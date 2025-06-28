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
use App\Models\Response;
use App\Models\Answer;

class ResponseFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Survey $survey;
    private SurveyPage $page;
    private Question $textQuestion;
    private Question $choiceQuestion;
    private Choice $choice1;
    private Choice $choice2;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $template = Template::factory()->create(['created_by' => $this->user->id]);
        $this->survey = Survey::factory()->create([
            'created_by' => $this->user->id,
            'template_id' => $template->id,
            'status' => 'active'
        ]);
        $this->page = SurveyPage::factory()->create(['survey_id' => $this->survey->id]);
        
        // Create text question
        $this->textQuestion = Question::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'text',
            'title' => 'What is your name?',
            'is_required' => true,
        ]);
        
        // Create multiple choice question
        $this->choiceQuestion = Question::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'multiple_choice',
            'title' => 'What is your favorite color?',
            'is_required' => true,
        ]);
        
        $this->choice1 = Choice::factory()->create([
            'question_id' => $this->choiceQuestion->id,
            'label' => 'Red',
            'value' => 'red',
        ]);
        
        $this->choice2 = Choice::factory()->create([
            'question_id' => $this->choiceQuestion->id,
            'label' => 'Blue',
            'value' => 'blue',
        ]);
    }

    public function test_can_start_response(): void
    {
        $data = [
            'survey_id' => $this->survey->id,
            'metadata' => [
                'ip' => '192.168.1.1',
                'user_agent' => 'Mozilla/5.0...',
            ],
        ];

        $response = $this->postJson('/api/responses', $data);
        
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id', 'survey_id', 'user_id', 'started_at', 'is_complete', 'created_at'
        ]);
        $response->assertJson([
            'survey_id' => $this->survey->id,
            'is_complete' => false,
        ]);

        $this->assertDatabaseHas('responses', [
            'survey_id' => $this->survey->id,
            'is_complete' => false,
        ]);
    }

    public function test_can_submit_complete_response(): void
    {
        // Start response
        $responseData = [
            'survey_id' => $this->survey->id,
        ];
        $response = $this->postJson('/api/responses', $responseData);
        $responseId = $response->json('id');

        // Submit answers
        $answersData = [
            'answers' => [
                [
                    'question_id' => $this->textQuestion->id,
                    'value' => 'John Doe',
                    'order_index' => 0,
                ],
                [
                    'question_id' => $this->choiceQuestion->id,
                    'choice_id' => $this->choice1->id,
                    'order_index' => 0,
                ],
            ],
        ];

        $response = $this->postJson("/api/responses/{$responseId}/submit", $answersData);
        
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Response submitted successfully']);

        // Check response is marked as complete
        $this->assertDatabaseHas('responses', [
            'id' => $responseId,
            'is_complete' => true,
        ]);

        // Check answers are saved
        $this->assertDatabaseHas('answers', [
            'response_id' => $responseId,
            'question_id' => $this->textQuestion->id,
            'value' => 'John Doe',
        ]);

        $this->assertDatabaseHas('answers', [
            'response_id' => $responseId,
            'question_id' => $this->choiceQuestion->id,
            'choice_id' => $this->choice1->id,
        ]);
    }

    public function test_can_submit_anonymous_response(): void
    {
        $data = [
            'survey_id' => $this->survey->id,
        ];

        $response = $this->postJson('/api/responses', $data);
        $responseId = $response->json('id');

        $answersData = [
            'answers' => [
                [
                    'question_id' => $this->textQuestion->id,
                    'value' => 'Anonymous User',
                    'order_index' => 0,
                ],
            ],
        ];

        $response = $this->postJson("/api/responses/{$responseId}/submit", $answersData);
        
        $response->assertStatus(200);

        $this->assertDatabaseHas('responses', [
            'id' => $responseId,
            'user_id' => null,
            'is_complete' => true,
        ]);
    }

    public function test_can_submit_authenticated_response(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $data = [
            'survey_id' => $this->survey->id,
        ];

        $response = $this->postJson('/api/responses', $data);
        $responseId = $response->json('id');

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
        $inactiveSurvey = Survey::factory()->create([
            'created_by' => $this->user->id,
            'status' => 'draft'
        ]);

        $data = [
            'survey_id' => $inactiveSurvey->id,
        ];

        $response = $this->postJson('/api/responses', $data);
        $response->assertStatus(422);
    }

    public function test_cannot_submit_to_expired_survey(): void
    {
        $expiredSurvey = Survey::factory()->create([
            'created_by' => $this->user->id,
            'status' => 'active',
            'expires_at' => now()->subDay(),
        ]);

        $data = [
            'survey_id' => $expiredSurvey->id,
        ];

        $response = $this->postJson('/api/responses', $data);
        $response->assertStatus(422);
    }

    public function test_cannot_exceed_max_responses(): void
    {
        $limitedSurvey = Survey::factory()->create([
            'created_by' => $this->user->id,
            'status' => 'active',
            'max_responses' => 1,
        ]);

        // First response
        $data = ['survey_id' => $limitedSurvey->id];
        $response = $this->postJson('/api/responses', $data);
        $responseId = $response->json('id');

        $answersData = [
            'answers' => [
                [
                    'question_id' => $this->textQuestion->id,
                    'value' => 'First Response',
                    'order_index' => 0,
                ],
            ],
        ];
        $this->postJson("/api/responses/{$responseId}/submit", $answersData);

        // Second response should fail
        $response = $this->postJson('/api/responses', $data);
        $response->assertStatus(422);
    }

    public function test_response_validation_rules(): void
    {
        // Test required survey_id
        $response = $this->postJson('/api/responses', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['survey_id']);

        // Test invalid survey_id
        $response = $this->postJson('/api/responses', ['survey_id' => 0]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['survey_id']);
    }

    public function test_answer_validation_rules(): void
    {
        $responseData = [
            'survey_id' => $this->survey->id,
        ];
        $response = $this->postJson('/api/responses', $responseData);
        $responseId = $response->json('id');

        // Test required answers
        $response = $this->postJson("/api/responses/{$responseId}/submit", []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['answers']);

        // Test empty answers array
        $response = $this->postJson("/api/responses/{$responseId}/submit", ['answers' => []]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['answers']);

        // Test required question_id
        $response = $this->postJson("/api/responses/{$responseId}/submit", [
            'answers' => [
                ['value' => 'test'],
            ],
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['answers.0.question_id']);

        // Test invalid question_id
        $response = $this->postJson("/api/responses/{$responseId}/submit", [
            'answers' => [
                [
                    'question_id' => 0,
                    'value' => 'test',
                ],
            ],
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['answers.0.question_id']);

        // Test value max length
        $response = $this->postJson("/api/responses/{$responseId}/submit", [
            'answers' => [
                [
                    'question_id' => $this->textQuestion->id,
                    'value' => str_repeat('a', 1001),
                ],
            ],
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['answers.0.value']);

        // Test order_index min value
        $response = $this->postJson("/api/responses/{$responseId}/submit", [
            'answers' => [
                [
                    'question_id' => $this->textQuestion->id,
                    'value' => 'test',
                    'order_index' => -1,
                ],
            ],
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['answers.0.order_index']);
    }

    public function test_can_get_response_statistics(): void
    {
        // Create multiple responses
        for ($i = 0; $i < 3; $i++) {
            $responseData = ['survey_id' => $this->survey->id];
            $response = $this->postJson('/api/responses', $responseData);
            $responseId = $response->json('id');

            $answersData = [
                'answers' => [
                    [
                        'question_id' => $this->textQuestion->id,
                        'value' => "User {$i}",
                        'order_index' => 0,
                    ],
                ],
            ];
            $this->postJson("/api/responses/{$responseId}/submit", $answersData);
        }

        $response = $this->getJson("/api/surveys/{$this->survey->id}/responses");
        
        $response->assertStatus(200);
        $response->assertJsonStructure(['total_responses', 'completed_responses', 'incomplete_responses']);
        $response->assertJson([
            'total_responses' => 3,
            'completed_responses' => 3,
            'incomplete_responses' => 0,
        ]);
    }

    public function test_can_get_response_details(): void
    {
        $responseData = ['survey_id' => $this->survey->id];
        $response = $this->postJson('/api/responses', $responseData);
        $responseId = $response->json('id');

        $answersData = [
            'answers' => [
                [
                    'question_id' => $this->textQuestion->id,
                    'value' => 'Test Answer',
                    'order_index' => 0,
                ],
            ],
        ];
        $this->postJson("/api/responses/{$responseId}/submit", $answersData);

        $response = $this->getJson("/api/responses/{$responseId}");
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id', 'survey_id', 'user_id', 'started_at', 'submitted_at', 
            'is_complete', 'metadata', 'answers'
        ]);
        $response->assertJson([
            'id' => $responseId,
            'is_complete' => true,
        ]);
    }

    public function test_response_not_found(): void
    {
        $response = $this->getJson('/api/responses/999999');
        $response->assertStatus(404);
    }

    public function test_cannot_submit_to_nonexistent_response(): void
    {
        $answersData = [
            'answers' => [
                [
                    'question_id' => $this->textQuestion->id,
                    'value' => 'Test',
                    'order_index' => 0,
                ],
            ],
        ];

        $response = $this->postJson('/api/responses/999999/submit', $answersData);
        $response->assertStatus(404);
    }
} 