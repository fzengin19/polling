<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Poll>
 */
class PollFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'user_id' => null, // Testte kendin belirleyeceksin
            'max_votes_per_user' => $this->faker->numberBetween(1, 5),
            'starts_at' => $this->faker->dateTimeBetween('-1 week', '+1 week'),
            'ends_at' => $this->faker->dateTimeBetween('+1 week', '+2 weeks'),
            'is_public' => $this->faker->boolean,
            'uuid' => Str::random(32),
            'anon_id' => $this->faker->optional()->word,
        ];
    }
}
