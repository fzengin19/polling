<?php

namespace Database\Factories;

use App\Models\Response;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResponseFactory extends Factory
{
    protected $model = Response::class;

    public function definition(): array
    {
        return [
            'survey_id' => Survey::factory(),
            'user_id' => User::factory(),
            'is_complete' => $this->faker->boolean,
            'submitted_at' => $this->faker->optional()->dateTime,
        ];
    }
} 