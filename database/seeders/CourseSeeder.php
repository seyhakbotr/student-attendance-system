<?php

namespace Database\Seeders;

use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define classroom IDs
        $classroomIds = [1, 2, 3]; // Assuming there are three classrooms

        // Define course data for each classroom
        foreach ($classroomIds as $classroomId) {
            $courses = [
                [
                    'name' => 'Course 1',
                    'classroom_id' => $classroomId,
                    'teacher_id' => 1, // Assuming the same teacher for all courses
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name' => 'Course 2',
                    'classroom_id' => $classroomId,
                    'teacher_id' => 2, // Assuming the same teacher for all courses
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name' => 'Course 3',
                    'classroom_id' => $classroomId,
                    'teacher_id' => 3, // Assuming the same teacher for all courses
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name' => 'Course 4',
                    'classroom_id' => $classroomId,
                    'teacher_id' => 4, // Assuming the same teacher for all courses
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name' => 'Course 5',
                    'classroom_id' => $classroomId,
                    'teacher_id' => 5, // Assuming the same teacher for all courses
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ];

            // Insert courses into the database for this classroom
            foreach ($courses as $courseData) {
                Course::create($courseData);
            }
        }
    }
}
