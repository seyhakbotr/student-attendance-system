<?php

use App\Models\Classroom;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
function resetAttendance(Classroom $classroom)
{
    $classroom->students()->each(function ($student) {
        $student->attendances()->delete();
    });

}


Schedule::command('model:prune')->daily();

Schedule::command('app:rotate-schedule')->daily();

Schedule::command('schedule-advance')->daily();
Schedule::call(function () {
    $classrooms = Classroom::all();

    foreach ($classrooms as $classroom) {
        if ($classroom) {
            resetAttendance($classroom);
        }
    }

})->weekly();
