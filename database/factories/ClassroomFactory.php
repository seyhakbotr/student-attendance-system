<?php

namespace Database\Factories;

use App\Models\Faculty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class ClassroomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'major_name' => $this->faker->word, // Generate a random major name
            'room_number' => $this->faker->unique()->numberBetween(1, 100), // Ensure unique room numbers
            'faculty_id' => Faculty::inRandomOrder()->first()->id, // Randomly select a faculty
        ];
    }
}

