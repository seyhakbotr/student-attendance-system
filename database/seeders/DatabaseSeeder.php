<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        //
        // Student::factory(10)->create();
        $this->call(ClassroomsTableSeeder::class);
        $this->call(StudentsTableSeeder::class);

        // Then assign students to classrooms
        $this->call(ClassroomStudentSeeder::class);

        // Finally, create courses for each classroom
        $this->call(CoursesTableSeeder::class);

        // Optionally seed attendance records
        $this->call(AttendanceTableSeeder::class);
    }
}
