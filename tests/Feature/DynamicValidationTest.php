<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Survey;
use App\Models\Question;
use App\Models\Response;

class DynamicValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Survey $survey;
    private Response $response;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->survey = Survey::factory()->create(['status' => 'active']);
        $this->response = Response::factory()->create(['survey_id' => $this->survey->id]);
        $this->actingAs($this->user, 'sanctum');
    }

    private function submitAnswer(Question $question, $value)
    {
        return $this->postJson("/api/responses/{$this->response->id}/submit", [
            'answers' => [
                ['question_id' => $question->id, 'value' => $value]
            ]
        ]);
    }

    public function test_email_validation_works()
    {
        $question = Question::factory()->create(['type' => 'email']);
        
        $this->submitAnswer($question, 'invalid-email')->assertStatus(422)->assertJsonStructure(['data' => ['answers.0.value']]);
        $this->submitAnswer($question, 'valid@example.com')->assertOk();
    }

    public function test_url_validation_works()
    {
        $question = Question::factory()->create(['type' => 'url']);

        $this->submitAnswer($question, 'not-a-url')->assertStatus(422);
        $this->submitAnswer($question, 'http://example.com')->assertOk();
    }

    public function test_number_validation_works()
    {
        $question = Question::factory()->create([
            'type' => 'number',
            'config' => ['min' => 1, 'max' => 200]
        ]);
        
        $this->submitAnswer($question, 'not-a-number')->assertStatus(422);
        $this->submitAnswer($question, '123')->assertOk();
    }

    public function test_number_min_max_validation_works()
    {
        $question = Question::factory()->create([
            'type' => 'number',
            'config' => ['min' => 10, 'max' => 20]
        ]);

        $this->submitAnswer($question, '5')->assertStatus(422);  // Below min
        $this->submitAnswer($question, '25')->assertStatus(422); // Above max
        $this->submitAnswer($question, '15')->assertOk();      // Within range
    }

    public function test_is_required_validation_works()
    {
        $question = Question::factory()->create([
            'type' => 'text',
            'is_required' => true
        ]);

        $this->submitAnswer($question, null)->assertStatus(422);
        $this->submitAnswer($question, '')->assertStatus(422);
        $this->submitAnswer($question, 'Some value')->assertOk();
    }

    public function test_phone_validation_works()
    {
        $question = Question::factory()->create(['type' => 'phone']);

        $this->submitAnswer($question, 'just-text')->assertStatus(422);
        $this->submitAnswer($question, '+1 555-123-4567')->assertOk();
        $this->submitAnswer($question, '05551234567')->assertOk();
    }

    public function test_checkbox_validation_works()
    {
        $question = Question::factory()->hasChoices(3)->create(['type' => 'checkbox']);
        $choices = $question->choices;

        // Test valid multiple choices
        $this->submitAnswer($question, [$choices[0]->id, $choices[1]->id])->assertOk();

        // Test invalid data type
        $this->submitAnswer($question, 'not-an-array')->assertStatus(422);

        // Test empty array
        $this->submitAnswer($question, [])->assertStatus(422);

        // Test with a non-existent choice ID
        $this->submitAnswer($question, [$choices[0]->id, 9999])->assertStatus(422);

        // Test with a choice ID from another question
        $otherQuestion = Question::factory()->hasChoices(1)->create();
        $this->submitAnswer($question, [$choices[0]->id, $otherQuestion->choices->first()->id])->assertStatus(422);
    }

    public function test_dropdown_validation_works()
    {
        $question = Question::factory()->hasChoices(2)->create(['type' => 'dropdown']);
        $choice = $question->choices->first();

        // Test valid single choice
        $this->submitAnswer($question, $choice->id)->assertOk();

        // Test invalid data type (array)
        $this->submitAnswer($question, [$choice->id])->assertStatus(422);

        // Test non-existent choice ID
        $this->submitAnswer($question, 9999)->assertStatus(422);
    }

    public function test_linear_scale_validation_works()
    {
        $question = Question::factory()->create([
            'type' => 'linear_scale',
            'config' => ['min' => 1, 'max' => 5]
        ]);

        // Test values within range
        $this->submitAnswer($question, 1)->assertOk();
        $this->submitAnswer($question, 3)->assertOk();
        $this->submitAnswer($question, 5)->assertOk();

        // Test values out of range
        $this->submitAnswer($question, 0)->assertStatus(422);
        $this->submitAnswer($question, 6)->assertStatus(422);

        // Test invalid data type
        $this->submitAnswer($question, '3')->assertOk(); // Laravel considers numeric strings valid for 'integer' rule
        $this->submitAnswer($question, 'a')->assertStatus(422);
    }

    public function test_date_validation_works()
    {
        $question = Question::factory()->create(['type' => 'date']);

        $this->submitAnswer($question, 'not-a-date')->assertStatus(422);
        $this->submitAnswer($question, '2024-01-01')->assertOk();
    }

    public function test_time_validation_works()
    {
        $question = Question::factory()->create(['type' => 'time']);

        $this->submitAnswer($question, 'not-a-time')->assertStatus(422);
        $this->submitAnswer($question, '23:59:59')->assertOk();
        $this->submitAnswer($question, '12:30')->assertStatus(422); // Must be H:i:s
    }

    public function test_boolean_validation_works()
    {
        $question = Question::factory()->create(['type' => 'boolean']);

        $this->submitAnswer($question, true)->assertOk();
        $this->submitAnswer($question, false)->assertOk();
        $this->submitAnswer($question, 1)->assertOk();
        $this->submitAnswer($question, 0)->assertOk();

        $this->submitAnswer($question, 'not-a-boolean')->assertStatus(422);
        $this->submitAnswer($question, 123)->assertStatus(422);
    }
}
