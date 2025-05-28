<?php
namespace Database\Factories;

use App\Models\Poll;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Option>
 */
class OptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
            return [
            'poll_id' => Poll::factory(),
            'type' => $this->faker->randomElement(['text', 'image', 'video']),
            'value' => $this->faker->sentence(),
            'votes_count' => 0,
            'order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
