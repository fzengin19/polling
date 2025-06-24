<?php

namespace Database\Factories;

use App\Models\SurveyPage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'page_id' => SurveyPage::factory(),
            'type' => fake()->randomElement(['text', 'number', 'email', 'url', 'tel', 'textarea', 'select', 'radio', 'checkbox', 'date', 'time', 'datetime-local']),
            'title' => fake()->sentence(3),
            'is_required' => fake()->boolean(),
            'help_text' => fake()->optional()->sentence(),
            'placeholder' => fake()->optional()->word(),
            'config' => [
                'min' => fake()->optional()->numberBetween(1, 10),
                'max' => fake()->optional()->numberBetween(10, 100),
                'options' => fake()->optional()->words(3),
            ],
            'order_index' => fake()->numberBetween(0, 10),
        ];
    }

    /**
     * Indicate that the question is required.
     */
    public function required(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_required' => true,
        ]);
    }

    /**
     * Indicate that the question is optional.
     */
    public function optional(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_required' => false,
        ]);
    }
} 