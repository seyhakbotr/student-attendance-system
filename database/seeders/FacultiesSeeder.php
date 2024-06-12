<?php

namespace Database\Seeders;

use App\Models\Faculty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FacultiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faculties = ['Science', 'Arts', 'Engineering', 'Business', 'Law'];

        foreach ($faculties as $facultyName) {
            Faculty::create(['name' => $facultyName]);
        }
    }
}
