<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use PhpParser\Node\Stmt\TryCatch;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StudentController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'classroom_id' => 'required|exists:classrooms,id' // Validate classroom ID
        ]);

        try {
            // Begin a transaction
            DB::beginTransaction();

            // Insert student record using raw SQL query
            $studentId = DB::table('students')->insertGetId([
                'name' => $validatedData['name'],
                'gender' => $validatedData['gender'],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Generate QR code data and image
            $qrCodeData = json_encode(['student_id' => $studentId, 'name' => $validatedData['name']]);
            $qrCode = QrCode::format('png')->size(300)->color(255, 0, 0)->margin(10)->errorCorrection('H')->generate($qrCodeData);
            $qrCodeFileName = uniqid() . '.png';
            $qrCodeFilePath = 'public/qrcodes/' . $qrCodeFileName;
            Storage::put($qrCodeFilePath, $qrCode);

            // Attach the student to the classroom
            DB::table('classroom_student')->insert([
                'classroom_id' => $validatedData['classroom_id'],
                'student_id' => $studentId,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Update the student record with QR code data and image path
            DB::table('students')->where('id', $studentId)->update([
                'qr_code_data' => $qrCodeData,
                'qr_code_image_path' => $qrCodeFilePath,
                'updated_at' => now()
            ]);

            // Get the classroom and its courses
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
                }
            }

            // Commit the transaction
            DB::commit();

            // Redirect back with a success message
            return redirect()->back()->with("success","success!");
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            // Log the error
            Log::error("Exception occurred: {$e->getMessage()} in file {$e->getFile()} on line {$e->getLine()}");
            // Return an error response with a meaningful message
            return redirect()->back()->with('error', 'Failed to add student: ' . $e->getMessage());
        }
    }

    public function destroy(Student $student)
    {
        try {
            $student->deleteOrFail();
            return redirect()->back()->with('success','Student Deleted successfully');
        } catch (\Throwable $th) {
            // Log the error for debugging
            Log::error('Failed to delete student', ['error' => $th]);

            return response()->json(['message' => 'Failed to delete student'], 500);
        }
    }
    public function edited(Student $student, $classroomId)
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
}
