<?php

namespace Database\Seeders;

use App\Models\ClassSchedule;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define classroom IDs
        $classroomIds = [1, 2, 3]; // Assuming there are three classrooms

        // Define days of the week
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        // Define class schedule data for each classroom
        foreach ($classroomIds as $classroomId) {
            foreach ($daysOfWeek as $day) {
                $startTime = '08:00:00'; // Example start time
                $endTime = '10:00:00'; // Example end time

                // Assuming same teacher for all classrooms and courses for simplicity
                $teacherId = 1;
                $courseId = $classroomId; // Assuming course IDs match classroom IDs

                // Create class schedule for this classroom, day, and time
                ClassSchedule::create([
                    'course_id' => $courseId,
                    'classroom_id' => $classroomId,
                    'teacher_id' => $teacherId,
                    'day' => $day,
                    'time_in' => $startTime,
                    'time_out' => $endTime,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
