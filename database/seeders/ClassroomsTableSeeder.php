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
    public function run(): void
    {
        // Ensure faculties exist before seeding classrooms
        if (Faculty::count() === 0) {
            $this->call(FacultiesTableSeeder::class);
        }

        // Manually assign unique room numbers
        $roomNumbers = range(1, 10); // Adjust the range as needed

        foreach ($roomNumbers as $roomNumber) {
            Classroom::factory()->create(['room_number' => $roomNumber]);
        }
    }
}

