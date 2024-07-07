<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Major;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::withCount('classroom')->with('major')->get();
        $majors = Major::all();
        return Inertia::render(
            'Course/Index',
            [
                'courses' => $courses,
                'majors' => $majors,
                    'breadcrumbs' => [
                        ['label' => 'Home', 'url' => route('dashboard')],
                        ['label' => 'Courses', 'url' => route('course.index')],
                ],
            ]
        );
    }
    public function storeGlobally(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:courses',

            'major_id' => 'required|exists:majors,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
        ]);

        $course = Course::create($validatedData);
        return redirect()->back()->with('success', 'Course has been successfully stored');
    }

    public function edit($id): Response
    {
        $course = Course::findOrFail($id);
        $majors = Major::all();
        return Inertia::render('Course/Edit', [
            'course' => $course,
            'majors' => $majors,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('dashboard')],
                ['label' => 'Course', 'url' => route('course.index')],
                ['label' => 'Edit Course of ' . $course->name, 'url' => route('course.edit', $course->id)],
            ],
        ]);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'course_id' => 'required|integer|exists:courses,id',
            'classroom_id' => 'required|integer|exists:classrooms,id',
        ]);

        try {
            $courseCount = Course::where('classroom_id', $validatedData['classroom_id'])->count();

            if ($courseCount >= 5) {
                return redirect()->back()->withErrors(['courseLimit' => 'Cannot add more than 5 courses to this classroom.']);
            }

            DB::beginTransaction();

            $course = Course::find($validatedData['course_id']);
            $course->classroom_id = $validatedData['classroom_id'];
            $course->save();

            $classroomId = $validatedData['classroom_id'];
            $courseId = $validatedData['course_id']; // Changed from $course->id
            DB::insert("
        INSERT INTO attendances (student_id, course_id, classroom_id, attended, date, created_at, updated_at)
        SELECT cs.student_id, ?, ?, 'absent', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
        FROM classroom_student cs
        WHERE cs.classroom_id = ?
    ", [$courseId, $classroomId, $classroomId]);

            DB::commit();

            return redirect()->back()->with('success', 'Course assigned and attendance records created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Exception occurred: {$e->getMessage()} in file {$e->getFile()} on line {$e->getLine()}");

            return redirect()->back()->withErrors(['error' => 'Failed to assign course: ' . $e->getMessage()]);
        }
    }
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = $request->input('ids');
        Course::whereIn('id', $ids)->delete();
        return redirect()->back()->with('success', 'Selected Courses Deleted Successfully!');
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
                        'major_name' => $course->classroom->major->name,
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

    public function detachFromClassroom($courseId, $classroomId)
    {
        $course = Course::findOrFail($courseId);

        if ($course->classroom_id == $classroomId) {
            $course->classroom_id = null;
            $course->save();

            return redirect()->back()->with('success', 'Course detached Successfully!');
        }

        return redirect()->back()->withErrors(['message' => 'Course does not belong to the specified classroom.'], 404);
    }    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->back()->with('success', 'Course deleted successfully!');
    }

    public function update(Request $request, Course $course)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',

            'major_id' => 'required|exists:majors,id',
        ]);

        $course->update($validatedData);

        return redirect()->back()->with('success', 'Course updated successfully!');
    }
}
