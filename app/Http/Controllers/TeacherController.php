<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use App\Models\Classroom;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:teachers,name',
            'classroom_id' => 'required|integer|exists:classrooms,id'
        ]);

        $classroomId = $validatedData['classroom_id'];

        $weekdays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $teacherScheduleCount = ClassSchedule::where('classroom_id', $classroomId)
            ->whereIn('day', $weekdays)
            ->groupBy('day')
            ->selectRaw('day, count(*) as count')
            ->get();

        if ($teacherScheduleCount->count() >= count($weekdays)) {
            return redirect()->back()->withErrors(['classroom_id' => 'The schedule for this classroom is already full for the weekdays.']);
        }

        $teacher = Teacher::create($validatedData);
        return redirect()->back()->with('success', 'Teacher has been successfully stored');
    }
    public function show($classroomId): Response
    {
        $classroom = Classroom::with(['teachers','major'])->findOrFail($classroomId);
        return Inertia::render("Classroom/Teacher", [
            "classroom" => $classroom,
            "teachers" => $classroom->teachers,
'breadcrumbs' => [
                    ['label' => 'Home', 'url' => route('dashboard')],
                    ['label' => 'Classrooms', 'url' => route('classroom.index')],
                    ['label' => $classroom->major->name, 'url' => route('classroom.show', $classroom->id)],
                    ['label' => 'Teacher of  ' . $classroom->major->name, 'url' => route('classroom.teachers', $classroom->id)],
                ],

        ]);

    }
    public function destroy(Teacher $teacher): RedirectResponse
    {
        $teacher->deleteOrFail();

        return redirect()->back()->with('success', 'Teacher deleted successfully!');
    }
    public function update(Request $request, Teacher $teacher)
    {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $teacher->update($validatedData);

        return redirect()->back()->with('success', 'Teacher updated successfully!');
    }
}
