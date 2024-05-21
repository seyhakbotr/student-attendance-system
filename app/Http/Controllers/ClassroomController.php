<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $classrooms = Classroom::latest()->paginate(9);
        if ($request->wantsJson()) {
            return $classrooms;
        }
        return Inertia::render('Classroom/Index', [
            'classrooms' => $classrooms,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('dashboard')],
                ['label' => 'Classrooms', 'url' => route('classroom.index')],
            ],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom)
    {
        // Eager load the relationships
        $classroom->load(['students', 'courses']);

        // Map students with their attendance records
        $studentsWithAttendance = $classroom->students->map(function ($student) use ($classroom) {
            $attendanceRecords = $student->attendances()
                ->whereIn('course_id', $classroom->courses->pluck('id'))
                ->where('classroom_id', $classroom->id)
                ->get()
                ->map(function ($record) use ($classroom) {
                    $course = $classroom->courses->find($record->course_id);
                    return [
                        'course' => $course,
                        'status' => $record->attended,
                        'date' => $record->date,
                    ];
                });

            $student->attendance = $attendanceRecords;
            return $student;
        });

        return Inertia::render('Classroom/Show', [
            'classroom' => $classroom,
            'students' => $studentsWithAttendance,
            'courses' => $classroom->courses,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('dashboard')],
                ['label' => 'Classrooms', 'url' => route('classroom.index')],
                ['label' => $classroom->name, 'url' => route('classroom.show', $classroom->id)],
            ],
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Logic for creating a new classroom
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $this->validateData($request);
        Classroom::create($validatedData);
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Logic for editing a classroom
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, Classroom $classroom)
    {
        $validatedData = $this->validateData($request, $classroom->id);
        $classroom->update($validatedData);
        return redirect()->back()->with('success', 'Classroom updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom)
    {
        $classroom->delete();
        return redirect()->back();
    }

    /**
     * Mark attendance for a student in a classroom.
     */
    public function markAttendance(Request $request, Classroom $classroom)
    {
        $validatedData = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'classroom_id' => 'required|exists:classrooms,id',
        ]);

        Attendance::where('student_id', $validatedData['student_id'])
            ->where('course_id', $validatedData['course_id'])
            ->where('classroom_id', $validatedData['classroom_id'])
            ->update(['attended' => 'present']);

        return redirect()->route('classroom.attendance', $classroom->id)->with('success', 'Attendance marked successfully.');
    }

    /**
     * Show the attendance page for a classroom.
     */
    public function showAttendancePage(Classroom $classroom)
    {
        $classroom->load(['courses']);
        return Inertia::render('Classroom/Attendance', [
            'classroom' => $classroom,
            'courses' => $classroom->courses,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('dashboard')],
                ['label' => 'Classrooms', 'url' => route('classroom.index')],
                ['label' => $classroom->name, 'url' => route('classroom.show', $classroom->id)],
                ['label' => 'Attendance', 'url' => route('classroom.attendance', $classroom->id)],
            ],
        ]);
    }

    private function validateData(Request $request, $id = null)
    {
        return $request->validate([
            'name' => 'required|unique:classrooms,name,' . $id,
            'room_number' => 'required|unique:classrooms,room_number,' . $id,
        ]);
    }
}
