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
        // Validate the request data
        $validatedData = $request->validate([
            'teacher_id' => 'required|integer|exists:teachers,id',
            'classroom_id' => 'required|integer|exists:classrooms,id'
        ]);

        $classroomId = $validatedData['classroom_id'];
        $teacherId = $validatedData['teacher_id'];

        $classroom = Classroom::findOrFail($classroomId);

        if ($classroom->teachers()->where('id', $teacherId)->exists()) {
            return redirect()->back()->withErrors(['alreadyExist' => 'This teacher is already assigned to the selected classroom.']);
        }

        $teacher = Teacher::findOrFail($teacherId);

        $teacher->classroom_id = $classroomId;
        $teacher->save();

        return redirect()->back()->with('success', 'Teacher has been successfully assigned to the classroom.');
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
    public function update(Request $request, Teacher $teacher): RedirectResponse
    {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'classroom_id' => 'nullable|exists:classrooms,id',
        ]);
        $teacher->update($validatedData);

        return redirect()->back()->with('success', 'Teacher updated successfully!');
    }
    public function index(): Response
    {
        $teachers = Teacher::withCount('classroom')->get();
        $classrooms = Classroom::all();
        return Inertia::render(
            'Teacher/Index',
            [
                    'teachers' => $teachers,
                    'breadcrumbs' => [
                        ['label' => 'Home', 'url' => route('dashboard')],
                        ['label' => 'Teachers', 'url' => route('teacher.index')],
                ],
                'classrooms' => $classrooms
                ]
        );
    }
    public function storeGlobally(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
                  'name' => 'required|string|max:255|unique:teachers,name',
                  'classroom_id' => 'nullable|exists:classrooms,id',
        ]);
        $teacher = Teacher::create($validatedData);
        return redirect()->back()->with('success', 'Teacher has been successfully stored');

    }
    public function detachFromClassroom($teacherId, $classroomId)
    {
        $teacher = Teacher::findOrFail($teacherId);

        // Check if the teacher belongs to the classroom
        if ($teacher->classroom_id == $classroomId) {
            // Detach the teacher from the classroom
            $teacher->classroom_id = null;
            $teacher->save();

            return redirect()->back()->with('success', 'Teachers detached Successfully!');
        }

        return redirect()->back()->withErrors(['message' => 'Teacher does not belong to the specified classroom.'], 404);
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = $request->input('ids');
        Teacher::whereIn('id', $ids)->delete();
        return redirect()->back()->with('success', 'Selected Teachers deleted Successfully!');
    }
    public function edit($id): Response
    {
        $teacher = Teacher::with('classroom')->findOrFail($id);
        $classrooms = Classroom::all();
        return Inertia::render('Teacher/Edit', [
            'teacher' => $teacher,
            'classrooms' => $classrooms,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('dashboard')],
                ['label' => 'Teacher', 'url' => route('teacher.index')],
                ['label' => 'Edit Teacher ' . $teacher->name, 'url' => route('teacher.edit', $teacher->id)],
            ],
        ]);
    }


}
