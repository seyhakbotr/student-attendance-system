<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $majors = [
            ['name' => 'Computer Science', 'faculty_id' => 1],
            ['name' => 'Electrical Engineering', 'faculty_id' => 3],
            ['name' => 'English Literature', 'faculty_id' => 2],
            ['name' => 'Cryptocurrency','faculty_id' => 4],
            ['name' => 'Ace Attorney','faculty_id' => 5 ]
            // Add more majors as needed
        ];

        // Loop through the majors array and create records in the database
        foreach ($majors as $major) {
            Major::create($major);
        }
    }
}
