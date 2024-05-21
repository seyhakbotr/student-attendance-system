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
            return redirect()->back()->with('success', 'Student added successfully');
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            // Log the error
            Log::error("Exception occurred: {$e->getMessage()} in file {$e->getFile()} on line {$e->getLine()}");
            // Return an error response with a meaningful message
            return redirect()->back()->with('error', 'Failed to add student: ' . $e->getMessage());
        }
    }
    public function index(Student $student)
    {
        // Load the associated classroom and courses for the student
        $student->load(['classroom', 'courses']);

        // Fetch the attendance records for the student within their classroom and courses
        $attendance = $student->attendances()
            ->whereIn('course_id', $student->courses->pluck('id'))
            ->where('classroom_id', $student->classroom->id)
            ->get()
            ->map(function ($record) {
                return [
                    'course' => $record->course,
                    'status' => $record->attended,
                    'date' => $record->date,
                ];
            });

        // Prepare data for rendering
        $data = [
            'student' => $student,
            'attendance' => $attendance,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('dashboard')],
                ['label' => 'Students', 'url' => route('students.index')],
                ['label' => $student->name, 'url' => route('students.show', $student->id)],
            ],
        ];

        // Render the student show view with data
        return Inertia::render('Classroom/Show', $data);
    }
}
