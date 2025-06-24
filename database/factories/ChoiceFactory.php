<?php

namespace Database\Factories;

use App\Models\Choice;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Choice>
 */
class ChoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Choice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question_id' => Question::factory(),
            'label' => $this->faker->sentence(3),
            'value' => $this->faker->word(),
            'order_index' => $this->faker->numberBetween(0, 10),
        ];
    }

    /**
     * Indicate that the choice is required.
     */
    public function required(): static
    {
        return $this->state(fn (array $attributes) => [
            'order_index' => 0,
        ]);
    }

    /**
     * Indicate that the choice is optional.
     */
    public function optional(): static
    {
        return $this->state(fn (array $attributes) => [
            'order_index' => $this->faker->numberBetween(1, 10),
        ]);
    }
} 