<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Faculty;
use Illuminate\Database\Seeder;

class ClassroomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $classrooms = [
            ['major_id' => 1, 'faculty_id' => 1, 'room_number' => 101],
            ['major_id' => 2, 'faculty_id' => 1, 'room_number' => 102],
            ['major_id' => 1, 'faculty_id' => 2, 'room_number' => 201],
            // Add more classrooms as needed
        ];

        foreach ($classrooms as $classroom) {
            Classroom::create($classroom);
        }
    }
}
