<?php
namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Database\Seeder;

class ClassroomStudentSeeder extends Seeder
{
    public function run(): void
    {
        $classrooms = Classroom::all();
        $students = Student::all();

        foreach ($classrooms as $classroom) {
            $classroom->students()->attach(
                $students->random(rand(1, 10))->pluck('id')->toArray()
            );
        }
    }
}
