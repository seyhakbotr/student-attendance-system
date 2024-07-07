<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\Student;
use Illuminate\Http\RedirectResponse as IlluminateRedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use PhpParser\Node\Stmt\TryCatch;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpFoundation\RedirectResponse;

class StudentController extends Controller
{
    public function store(Request $request)
    {
        $request->merge([
            'year_id' => (int)$request->input('year_id'),
            'semester_id' => (int)$request->input('semester_id'),
        ]);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'major_id' => 'required|exists:majors,id',
            'faculty_id' => 'required|exists:faculties,id',
            'year_id' => 'required|integer|min:1|max:4|exists:years,id',
            'semester_id' => 'required|integer|min:1|max:2|exists:semesters,id',
        ]);

        try {
            // Begin a transaction
            DB::beginTransaction();

            // Insert student record using raw SQL query
            $studentId = DB::table('students')->insertGetId([
                'name' => $validatedData['name'],
                'gender' => $validatedData['gender'],
                'major_id' => $validatedData['major_id'],
                'faculty_id' => $validatedData['faculty_id'],
                'year_id' => $validatedData['year_id'],
                'semester_id' => $validatedData['semester_id'],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Log for debugging
            Log::info("Student inserted with ID: {$studentId}");

            // Generate QR code data and image
            $qrCodeData = json_encode(['student_id' => $studentId, 'name' => $validatedData['name']]);
            $qrCode = QrCode::format('png')->size(300)->color(255, 0, 0)->margin(10)->errorCorrection('H')->generate($qrCodeData);
            $qrCodeFileName = uniqid() . '.png';
            $qrCodeFilePath = 'public/qrcodes/' . $qrCodeFileName;
            Storage::put($qrCodeFilePath, $qrCode);

            // Log for debugging
            Log::info("QR Code generated and stored at: {$qrCodeFilePath}");

            // Attach the student to the classroom
            if (!is_null($validatedData['classroom_id'])) {
                DB::table('classroom_student')->insert([
                    'classroom_id' => $validatedData['classroom_id'],
                    'student_id' => $studentId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Log for debugging
                Log::info("Student attached to classroom ID: {$validatedData['classroom_id']}");
            }

            // Update the student record with QR code data and image path
            DB::table('students')->where('id', $studentId)->update([
                'qr_code_data' => $qrCodeData,
                'qr_code_image_path' => $qrCodeFilePath,
                'updated_at' => now()
            ]);

            // Log for debugging
            Log::info("Student updated with QR Code data");

            // Get the classroom and its courses
            if (!is_null($validatedData['classroom_id'])) {
                $classroom = Classroom::with('courses')->findOrFail($validatedData['classroom_id']);

                // If the classroom has courses, create attendance records for the student
                if ($classroom->courses->isNotEmpty()) {
                    foreach ($classroom->courses as $course) {
                        DB::table('attendances')->insert([
                            'student_id' => $studentId,
                            'course_id' => $course->id,
                            'classroom_id' => $classroom->id,
                            'attended' => 'absent',
                            'date' => now(),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        // Log for debugging
                        Log::info("Attendance record created for student ID: {$studentId}, course ID: {$course->id}");
                    }
                }
            }

            // Commit the transaction
            DB::commit();

            // Redirect back with a success message
            return redirect()->back()->with("success", "Student added successfully!");
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();

            // Log the error
            Log::error("Exception occurred: {$e->getMessage()} in file {$e->getFile()} on line {$e->getLine()}");

            // Return an error response with a meaningful message
            return redirect()->back()->with('error', 'Failed to add student: ' . $e->getMessage());
        }
    }
    public function destroy(Classroom $classroom, Student $student)
    {
        try {
            // Detach the student from the classroom
            $classroom->students()->detach($student->id);

            return redirect()->back()->with('success', 'Student removed from classroom successfully');
        } catch (\Throwable $th) {
            // Log the error for debugging
            Log::error('Failed to remove student from classroom', ['error' => $th]);

            return response()->json(['message' => 'Failed to remove student from classroom'], 500);
        }
    }
    public function destroyGlobally(Student $student): RedirectResponse
    {
        $student->deleteOrFail();

        return redirect()->back()->with('success', 'Student deleted successfully!');
    }
    public function edited(Student $student, $classroomId): Response
    {
        // Load the student data with attendances filtered by the classroom
        $classroom = Classroom::with('courses')->findOrFail($classroomId);

        // Load the student's attendances filtered by the classroom and their related courses
        $student->load(['attendances' => function ($query) use ($classroomId) {
            $query->where('classroom_id', $classroomId)->with('course');
        }]);

        // Prepare the data to be sent to the Inertia view
        $data = [
        'student' => [
           'id' => $student->id,
           'name' => $student->name,
           'gender' => $student->gender,
           'attendances' => $student->attendances->map(function ($attendance) {
               return [
                   'course_id' => $attendance->course_id,
                   'course_name' => $attendance->course->name,
                   'attendance_status' => $attendance->attended,
                   'attendance_date' => $attendance->date,
               ];
           }),
        ],
        'courses' => $classroom->courses->map(function ($course) use ($student) {
            $attendance = $student->attendances->firstWhere('course_id', $course->id);
            return [
                'course_id' => $course->id,
                'course_name' => $course->name,
                'attendance_status' => $attendance ? $attendance->attended : 'absent',
                'attendance_date' => $attendance ? $attendance->date : null,
            ];
        }),
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('dashboard')],
            ['label' => 'Classroom of ' . $classroom->major->name, 'url' => route('classroom.show', $classroomId)],
            ['label' => $student->name, 'url' => route('student.edited', ['student' => $student->id, 'classroom' => $classroomId])]
        ],
    ];
        return Inertia::render('Classroom/Edit', $data);
    }
    public function update(Request $request, $student_id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'attendances' => 'required|array',
            'attendances.*.course_id' => 'required|exists:courses,id',
            'attendances.*.attended' => 'required|in:present,absent',
        ]);

        try {
            // Begin a transaction
            DB::beginTransaction();

            // Update student name and gender
            DB::table('students')->where('id', $student_id)->update([
                'name' => $validatedData['name'],
                'gender' => $validatedData['gender'],
                'updated_at' => now()
            ]);

            foreach ($validatedData['attendances'] as $attendanceData) {
                // Update the attendance record
                DB::table('attendances')
                    ->where('student_id', $student_id)
                    ->where('course_id', $attendanceData['course_id'])
                    ->update([
                        'attended' => $attendanceData['attended'],
                        'updated_at' => now()
                    ]);
            }

            // Commit the transaction
            DB::commit();

            // Redirect back with a success message
            /*  return redirect()->back()->with('success', 'Student updated successfully'); */
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            // Log the error
            Log::error("Exception occurred: {$e->getMessage()} in file {$e->getFile()} on line {$e->getLine()}");
            // Return an error response with a meaningful message
            return redirect()->back()->with('error', 'Failed to update student: ' . $e->getMessage());
        }
    }

    public function updateGlobally(Request $request, $student_id)
    {
        // Validate the incoming data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'major_id' => 'nullable|exists:majors,id',
            'faculty_id' => 'nullable|exists:faculties,id',
            'year_id' => 'nullable|exists:years,id',
            'semester_id' => 'nullable|exists:semesters,id',
        ]);

        // Find the student record
        $student = Student::findOrFail($student_id);

        // Update the student record using Eloquent
        $student->name = $validatedData['name'];
        $student->gender = $validatedData['gender'];
        $student->major_id = $validatedData['major_id'];
        $student->faculty_id = $validatedData['faculty_id'];
        $student->year_id = $validatedData['year_id'];
        $student->semester_id = $validatedData['semester_id'];

        // Save the updated student record
        $student->save();

        return redirect()->back()->with('success', 'Student Updated Successfully');
    }
    public function bulkDestroy(Request $request): IlluminateRedirectResponse
    {
        $ids = $request->input('ids');
        Student::whereIn('id', $ids)->delete();
        return redirect()->back()->with('success', 'Selected students deleted Successfully!');
    }

    public function index(): Response
    {
        $students = Student::with(['major.faculty'])
        ->withCount('classrooms')
            ->get();
        $classrooms = Classroom::all();
        $faculties = Faculty::all();
        $majors = Major::with(['classrooms:id,major_id,room_number']) // Select only necessary fields
                        ->get();
        return Inertia::render(
            'Student/Index',
            [
                'students' => $students,
                'classrooms' => $classrooms,
                'faculties' => $faculties,
                'majors' => $majors,
                'breadcrumbs' => [
                    ['label' => 'Home', 'url' => route('dashboard')],
                    ['label' => 'Student', 'url' => route('student.index')],
                ],
            ]
        );

    }


    public function edit($id): Response
    {
        $student = Student::with(['major', 'faculty'])->findOrFail($id);
        $faculties = Faculty::all();
        $majors = Major::all();
        $classrooms = Classroom::all();

        return Inertia::render('Student/Edit', [
            'student' => $student,
            'faculties' => $faculties,
            'majors' => $majors,
            'classrooms' => $classrooms,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('dashboard')],
                ['label' => 'Student', 'url' => route('student.index')],
                ['label' => 'Edit student', 'url' => route('student.edit', $student->id)],
            ],
        ]);
    }

    public function importStudent(Classroom $classroom): Response
    {
        $students = Student::query()->with('major.faculty')->get();
        $classroom->load('major');
        $classrooms = Classroom::all();
        $faculties = Faculty::all();
        $majors = Major::all();
        return Inertia::render(
            'Classroom/ImportStudent',
            [
                'students' => $students,
                'classrooms' => $classrooms,
                'faculties' => $faculties,
                'classroom' => $classroom,
                'majors' => $majors,
                'breadcrumbs' => [
                    ['label' => 'Home', 'url' => route('dashboard')],
                    ['label' => 'Classroom of ' . $classroom->major->name, 'url' => route('classroom.show', $classroom->id)],
                    ['label' => 'Import Students', 'url' => route('students.importStudent', $classroom->id)]
                ],
            ]
        );
    }

    public function handleBulkInsert(Request $request)
    {
        $validatedData = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id', // Validate each student ID
        ]);

        try {
            DB::beginTransaction();

            $classroom = Classroom::with('courses')->findOrFail($validatedData['classroom_id']);

            foreach ($validatedData['student_ids'] as $studentId) {
                $exists = DB::table('classroom_student')
                            ->where('classroom_id', $validatedData['classroom_id'])
                            ->where('student_id', $studentId)
                            ->exists();

                if (!$exists) {
                    DB::table('classroom_student')->insert([
                        'classroom_id' => $validatedData['classroom_id'],
                        'student_id' => $studentId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    foreach ($classroom->courses as $course) {
                        DB::table('attendances')->insert([
                            'student_id' => $studentId,
                            'course_id' => $course->id,
                            'classroom_id' => $classroom->id,
                            'attended' => 'absent', // Default attendance status
                            'date' => now(), // Current date as default attendance date
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }

            // Commit the transaction
            DB::commit();

            return response()->json(['message' => 'Students inserted into classroom successfully']);
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            Log::error("Exception occurred: {$e->getMessage()} in file {$e->getFile()} on line {$e->getLine()}");
            return response()->json(['error' => 'Failed to insert students into classroom: Internal server error'], 500);
        }
    }

}
