<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassSchedule;
use App\Models\Classroom;
use App\Models\Faculty;
use App\Models\Major;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Eager load classrooms with majors and faculties
        $classrooms = Classroom::with('major.faculty')->latest()->paginate(9);

        // Fetch all majors and faculties
        $majors = Major::all();
        $faculties = Faculty::all();

        // Get the current day of the week
        $currentDay = Carbon::now()->format('l');

        // Fetch class schedules for the current day
        $schedules = ClassSchedule::with(['course', 'classroom.major.faculty'])
                                  ->where('day', $currentDay)
                                  ->get();


        // Return the data for rendering with Inertia
        return Inertia::render('Classroom/Index', [
            'classrooms' => $classrooms,
            'majors' => $majors,
            'faculties' => $faculties,
            'currentDay' => $currentDay,
            'schedules' => $schedules,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('dashboard')],
                ['label' => 'Classrooms', 'url' => route('classroom.index')],
            ],
        ]);
    }    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom)
    {
        // Eager load the relationships
        $classroom->load(['students.attendances.course', 'courses', 'major']);

        // Fetch class schedules for all weekdays
        $schedules = ClassSchedule::with(['course', 'classroom.major.faculty', 'teacher'])
            ->whereIn('day', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'])
            ->where('classroom_id', $classroom->id)
            ->get();
        $currentDateTime = Carbon::now()->englishDayOfWeek;
        // Return the view with the necessary data
        return Inertia::render('Classroom/Show', [
            'classroom' => $classroom,
            'students' => $classroom->students,
                           'courses' => $classroom->courses,
            'schedules' => $schedules,
            'teachers' => $classroom->teachers,
            "currentDay" => $currentDateTime,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('dashboard')],
                ['label' => 'Classrooms', 'url' => route('classroom.index')],
                ['label' => $classroom->major->name, 'url' => route('classroom.show', $classroom->id)],
            ],
        ]);
    }
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
        $faculty = Faculty::findOrFail($validatedData['faculty_id']);

        if (!$faculty->majors->contains('id', $validatedData['major_id'])) {
            return redirect()->back()->withErrors([
                'create' => 'The selected major does not belong to the selected faculty.'
            ]);
        }

        Classroom::create($validatedData);
        return redirect()->back()->with('success', 'Classroom created successfully.');
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

        // Check if the student is associated with the specified classroom
        $isInClassroom = DB::table('classroom_student')
                            ->where('classroom_id', $validatedData['classroom_id'])
                            ->where('student_id', $validatedData['student_id'])
                            ->exists();

        if (!$isInClassroom) {
            return redirect()->back()->with('qrcode', 'Student is not in this class.');
        }

        // If student is in the class, update attendance
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
        // Get the current day of the week
        $currentDay = Carbon::now()->format('l');

        // Fetch the current day's schedule for the classroom
        $classroomSchedule = ClassSchedule::where('classroom_id', $classroom->id)
                                          ->where('day', $currentDay)
                                          ->with(['course', 'teacher'])
                                          ->first();
        // Pass the schedule data to the view
        return Inertia::render('Classroom/Attendance', [
            'classroom' => $classroom,
            'classroomSchedule' => $classroomSchedule,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('dashboard')],
                ['label' => 'Classrooms', 'url' => route('classroom.index')],
                ['label' => $classroom->major->name, 'url' => route('classroom.show', $classroom->id)],
                ['label' => 'Attendance', 'url' => route('classroom.attendance', $classroom->id)],
            ],
        ]);
    }
    private function validateData(Request $request, $id = null)
    {
        return $request->validate([
            'room_number' => 'required|unique:classrooms,room_number,' . $id,
            'major_id' => 'required|exists:majors,id',
            'faculty_id' => 'required|exists:faculties,id',
        ]);
    }
}
