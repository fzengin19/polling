<?php

namespace Database\Factories;

use App\Models\Template;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TemplateVersion>
 */
class TemplateVersionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'template_id' => Template::factory(),
            'version' => fake()->semver(),
            'snapshot' => [
                'title' => fake()->sentence(3),
                'description' => fake()->paragraph(),
                'is_public' => fake()->boolean(),
            ],
        ];
    }
} 