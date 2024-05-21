<?php

namespace Database\Seeders;

use App\Models\Classroom;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassroomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Manually assign unique room numbers
        $roomNumbers = range(1, 10); // Adjust the range as needed

        foreach ($roomNumbers as $roomNumber) {
            Classroom::factory()->make(['room_number' => $roomNumber])->save();
        }

        // Optionally, shuffle the rooms to distribute them randomly
        shuffle($roomNumbers);
    }}
