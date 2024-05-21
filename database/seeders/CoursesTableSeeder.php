<?php




namespace Database\Seeders;

use App\Models\Course;
use App\Models\Classroom;
use Illuminate\Database\Seeder;

class CoursesTableSeeder extends Seeder
{
    public function run(): void
    {
        $courseNames = ['Math', 'English', 'Science', 'Khmer', 'Japanese'];

        // Ensure classrooms exist before creating courses
        $classrooms = Classroom::all();
        if ($classrooms->isEmpty()) {
            $classrooms = Classroom::factory()->count(5)->create();
        }

        foreach ($classrooms as $classroom) {
            foreach ($courseNames as $courseName) {
                Course::create([
                    'name' => $courseName,
                    'classroom_id' => $classroom->id,
                ]);
            }
        }
    }
}

