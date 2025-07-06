<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Survey;
use App\Models\SurveyPage;
use App\Models\Question;

class QuestionConfigValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private SurveyPage $page;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $survey = Survey::factory()->create(['created_by' => $this->user->id]);
        $this->page = SurveyPage::factory()->create(['survey_id' => $survey->id]);
        $this->actingAs($this->user, 'sanctum');
    }

    private function createQuestion(array $data)
    {
        return $this->postJson("/api/survey-pages/{$this->page->id}/questions", $data);
    }

    public function test_number_config_validation_fails_if_max_is_less_than_min()
    {
        $data = [
            'type' => 'number',
            'title' => 'Invalid Range Question',
            'config' => ['min' => 10, 'max' => 5]
        ];
        
        $this->createQuestion($data)
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['config.max']]);
    }

    public function test_linear_scale_config_validation_fails_if_labels_are_too_long()
    {
        $data = [
            'type' => 'linear_scale',
            'title' => 'Long Label Question',
            'config' => ['label_min' => str_repeat('a', 51)]
        ];

        $this->createQuestion($data)
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['config.label_min']]);
    }

    public function test_valid_number_config_is_accepted()
    {
        $data = [
            'type' => 'number',
            'title' => 'Valid Number Question',
            'config' => ['min' => 1, 'max' => 100]
        ];

        $this->createQuestion($data)->assertCreated();
    }

    public function test_valid_linear_scale_config_is_accepted()
    {
        $data = [
            'type' => 'linear_scale',
            'title' => 'Valid Linear Scale',
            'config' => [
                'min' => 1, 
                'max' => 5, 
                'label_min' => 'Bad', 
                'label_max' => 'Good'
            ]
        ];

        $this->createQuestion($data)->assertCreated();
    }
}
