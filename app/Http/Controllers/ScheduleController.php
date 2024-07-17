<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            "course_id" => "required|exists:courses,id",
            "teacher_id" => "required|exists:teachers,id",
            'classroom_id' => 'required|exists:classrooms,id',
            'day' => 'required|string',
          'time_in' => 'required|date_format:H:i',
'time_out' => 'required|date_format:H:i',
        ]);
        /*$existingSchedule = ClassSchedule::where('teacher_id', $validatedData['teacher_id'])*/
        /*    ->where('day', $validatedData['day'])*/
        /*    ->first();*/
        /*if ($existingSchedule) {*/
        /*    return redirect()->back()->withErrors(['scheduleExist' => 'Schedule already exists for this teacher on the given day.']);*/
        /**/
        /*}*/
        $schedule = ClassSchedule::create($validatedData);
        return redirect()->back()->with("success", "Schedule added succesfully");
    }
    public function update(Request $request, ClassSchedule $schedule): RedirectResponse
    {
        $validatedData = $request->validate([
            "course_id" => "required|exists:courses,id",
            "teacher_id" => "required|exists:teachers,id",
            'classroom_id' => 'required|exists:classrooms,id',
            'day' => 'required|string',
  'time_in' => 'required|date_format:H:i',
'time_out' => 'required|date_format:H:i',

        ]);

        $schedule->update($validatedData);

        return redirect()->back()->with("success", "Schedule updated successfully");
    }
    public function destroy(ClassSchedule $schedule)
    {
        $schedule->deleteOrFail();

        return redirect()->back()->with("success", "Schedule deleted successfully");
    }
}
