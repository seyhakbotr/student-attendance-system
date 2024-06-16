<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CourseController extends Controller
{
   public function store(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255|unique:courses',
        'teacher_id' => 'required|integer|exists:teachers,id',
        'classroom_id' => 'required|integer|exists:classrooms,id',
    ]);

    try {
        // Check the number of courses in the specified classroom
        $courseCount = Course::where('classroom_id', $validatedData['classroom_id'])->count();

        // If the course count is 5 or more, return an error
        if ($courseCount >= 5) {
            return redirect()->back()->withErrors(['courseLimit' => 'Cannot add more than 5 courses to this classroom.']);
        }

        // Begin a transaction
        DB::beginTransaction();

        // Create the new course
        $course = Course::create($validatedData);

        // Insert attendance records using raw SQL
        $classroomId = $validatedData['classroom_id'];
        $courseId = $course->id;
        DB::insert("
            INSERT INTO attendances (student_id, course_id, classroom_id, attended, date, created_at, updated_at)
            SELECT cs.student_id, ?, ?, 'absent', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
            FROM classroom_student cs
            WHERE cs.classroom_id = ?
        ", [$courseId, $classroomId, $classroomId]);

        // Commit the transaction
        DB::commit();

        return redirect()->back()->with('success', 'Course and attendance records created successfully!');
    } catch (\Exception $e) {
        // Rollback the transaction on error
        DB::rollBack();

        // Log the error
        Log::error("Exception occurred: {$e->getMessage()} in file {$e->getFile()} on line {$e->getLine()}");

        // Return an error response
        return redirect()->back()->withErrors(['error' => 'Failed to add course: ' . $e->getMessage()]);
    }
    }
    public function getCoursesByClassroom($classroomId)
    {
        try {
            // Load the classroom with its courses
            $classroom = Classroom::with('courses')->findOrFail($classroomId);

            // Prepare the data to be sent to the Inertia view
            $data = [
                'classroom' => [
                    'id' => $classroom->id,
                    'name' => $classroom->name,
                ],
                'courses' => $classroom->courses->map(function ($course) {
                    return [
                        'id' => $course->id,
                        'name' => $course->name,
                    ];
                }),
                'breadcrumbs' => [
                    ['label' => 'Home', 'url' => route('dashboard')],
                    ['label' => 'Classrooms', 'url' => route('classroom.index')],
                    ['label' => $classroom->major->name, 'url' => route('classroom.show', $classroom->id)],
                    ['label' => 'Courses of ' . $classroom->major->name, 'url' => route('classroom.courses', $classroom->id)],
                ],
            ];

            // Render the Inertia view with the data
            return Inertia::render('Classroom/Course', $data);
        } catch (\Exception $e) {
            // Log the error
            Log::error("Exception occurred: {$e->getMessage()} in file {$e->getFile()} on line {$e->getLine()}");

            // Return an error response
            return redirect()->back()->withErrors(['error' => 'Failed to retrieve courses: ' . $e->getMessage()]);
        }
    }
    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->back()->with('success', 'Course deleted successfully!');
    }
    public function update(Request $request, Course $course)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $course->update($validatedData);

        return redirect()->back()->with('success', 'Course updated successfully!');
    }
}
