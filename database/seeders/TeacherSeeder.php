<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();
        $classrooms = [1, 2, 3]; // Array of classroom IDs

        foreach ($classrooms as $classroomId) {
            // Create 5 teachers for each classroom
            for ($i = 1; $i <= 5; $i++) {
                $name = $faker->name;
                Teacher::create([
                    'name' => $name,
                    'classroom_id' => $classroomId,
                ]);
            }
        }
    }
}
