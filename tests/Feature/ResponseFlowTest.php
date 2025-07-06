<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Template;
use App\Models\Survey;
use App\Models\SurveyPage;
use App\Models\Question;
use App\Models\Choice;
use App\Models\Response as SurveyResponse;
use App\Models\Answer;

class ResponseFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Survey $survey;
    protected Question $textQuestion;
    protected Question $choiceQuestion;
    protected Choice $choice1;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->survey = Survey::factory()->create(['status' => 'active', 'created_by' => $this->user->id]);
        $page = $this->survey->pages()->create();
        $this->textQuestion = Question::factory()->create(['page_id' => $page->id, 'type' => 'text']);
        $this->choiceQuestion = Question::factory()->create(['page_id' => $page->id, 'type' => 'multiple_choice', 'is_required' => true]);
        $this->choice1 = Choice::factory()->create(['question_id' => $this->choiceQuestion->id]);
    }

    public function test_can_start_response(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/responses', ['survey_id' => $this->survey->id]);
        $response->assertStatus(201)
            ->assertJson(['success' => true, 'message' => 'Response session started.'])
            ->assertJsonPath('data.survey_id', $this->survey->id);
    }

    public function test_can_submit_complete_response(): void
    {
        $responseModel = SurveyResponse::factory()->create(['survey_id' => $this->survey->id, 'user_id' => $this->user->id]);
        $answers = ['answers' => [['question_id' => $this->choiceQuestion->id, 'choice_id' => $this->choice1->id]]];
        $response = $this->postJson("/api/responses/{$responseModel->id}/submit", $answers);
        $response->assertOk()->assertJsonPath('data.is_complete', true);
    }

    public function test_can_submit_anonymous_response(): void
    {
        $responseId = SurveyResponse::factory()->create(['survey_id' => $this->survey->id, 'user_id' => null])->id;
        $answersData = [
            'answers' => [
                ['question_id' => $this->choiceQuestion->id, 'choice_id' => $this->choice1->id]
            ],
        ];
        $response = $this->postJson("/api/responses/{$responseId}/submit", $answersData);
        $response->assertStatus(200)->assertJsonPath('data.is_complete', true);
    }

    public function test_can_submit_authenticated_response(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $data = [
            'survey_id' => $this->survey->id,
        ];

        $response = $this->postJson('/api/responses', $data);
        $responseId = $response->json('data.id');

        $answersData = [
            'answers' => [
                [
                    'question_id' => $this->textQuestion->id,
                    'value' => 'Authenticated User',
                    'order_index' => 0,
                ],
            ],
        ];

        $response = $this->postJson("/api/responses/{$responseId}/submit", $answersData);
        
        $response->assertStatus(200);

        $this->assertDatabaseHas('responses', [
            'id' => $responseId,
            'user_id' => $this->user->id,
            'is_complete' => true,
        ]);
    }

    public function test_cannot_submit_to_inactive_survey(): void
    {
        $inactiveSurvey = Survey::factory()->create(['status' => 'draft']);
        $this->actingAs($this->user)->postJson('/api/responses', ['survey_id' => $inactiveSurvey->id])->assertStatus(422);
    }

    public function test_cannot_submit_to_expired_survey(): void
    {
        $expiredSurvey = Survey::factory()->create(['expires_at' => now()->subDay()]);
        $this->actingAs($this->user)->postJson('/api/responses', ['survey_id' => $expiredSurvey->id])->assertStatus(422);
    }

    public function test_cannot_exceed_max_responses(): void
    {
        $limitedSurvey = Survey::factory()->create(['max_responses' => 1]);
        SurveyResponse::factory()->create(['survey_id' => $limitedSurvey->id, 'is_complete' => true]);
        $this->actingAs($this->user)->postJson('/api/responses', ['survey_id' => $limitedSurvey->id])->assertStatus(422);
    }

    public function test_response_validation_rules(): void
    {
        // Test required survey_id
        $response = $this->postJson('/api/responses', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['survey_id'], 'data');

        // Test invalid survey_id
        $response = $this->postJson('/api/responses', ['survey_id' => 0]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['survey_id'], 'data');
    }

    public function test_answer_validation_rules(): void
    {
        $responseId = SurveyResponse::factory()->create(['survey_id' => $this->survey->id])->id;
        $this->postJson("/api/responses/{$responseId}/submit", [])->assertStatus(422)->assertJsonValidationErrors(['answers'], 'data');
    }

    public function test_can_get_response_statistics(): void
    {
        SurveyResponse::factory()->count(3)->create(['survey_id' => $this->survey->id, 'is_complete' => true]);
        $response = $this->actingAs($this->user)->getJson("/api/surveys/{$this->survey->id}/responses");
        
        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data' => ['total_responses', 'completed_responses']])
            ->assertJsonPath('data.completed_responses', 3);
    }

    public function test_can_get_response_details(): void
    {
        $responseModel = SurveyResponse::factory()->create(['survey_id' => $this->survey->id]);
        $response = $this->actingAs($this->user)->getJson("/api/responses/{$responseModel->id}");
        $response->assertOk()->assertJsonPath('data.id', $responseModel->id);
    }

    public function test_response_not_found(): void
    {
        $this->actingAs($this->user)->getJson('/api/responses/999999')->assertNotFound();
    }

    public function test_cannot_submit_to_nonexistent_response(): void
    {
        $answersData = ['answers' => [['question_id' => 999, 'value' => 'test']]];
        $this->postJson('/api/responses/999999/submit', $answersData)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['answers.0.question_id'], 'data');
    }
} 