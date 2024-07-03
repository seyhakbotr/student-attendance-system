<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Classroom;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['Math', 'English', 'Science', 'Khmer', 'Japanese']),
            'classroom_id' => function () {
                return Classroom::factory()->create()->id;
            },
        ];
    }
}
