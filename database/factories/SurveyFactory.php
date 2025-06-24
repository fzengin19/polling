<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Template;
use App\Models\TemplateVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Survey>
 */
class SurveyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['draft', 'active', 'archived']),
            'created_by' => User::factory(),
            'template_id' => Template::factory(),
            'template_version_id' => TemplateVersion::factory(),
            'settings' => [
                'allow_anonymous' => fake()->boolean(),
                'require_login' => fake()->boolean(),
            ],
            'expires_at' => fake()->optional()->dateTimeBetween('now', '+1 year'),
            'max_responses' => fake()->optional()->numberBetween(10, 1000),
        ];
    }

    /**
     * Indicate that the survey is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the survey is draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate that the survey is archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
        ]);
    }
} 