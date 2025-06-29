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

class ValidationErrorTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Survey $survey;
    private SurveyPage $page;
    private Question $question;
    private Choice $choice;

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
        $this->question = Question::factory()->create(['page_id' => $this->page->id]);
        $this->choice = Choice::factory()->create(['question_id' => $this->question->id]);
    }

    // ==================== AUTH VALIDATION TESTS ====================

    public function test_register_validation_errors(): void
    {
        // Test required fields
        $response = $this->postJson('/api/auth/register', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password']);

        // Test email format
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);

        // Test password min length
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);

        // Test name max length
        $response = $this->postJson('/api/auth/register', [
            'name' => str_repeat('a', 256),
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);

        // Test email max length
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => str_repeat('a', 250) . '@example.com',
            'password' => 'password123',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_login_validation_errors(): void
    {
        // Test required fields
        $response = $this->postJson('/api/auth/login', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);

        // Test email format
        $response = $this->postJson('/api/auth/login', [
            'email' => 'invalid-email',
            'password' => 'password',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_google_login_validation_errors(): void
    {
        // Test required token
        $response = $this->postJson('/api/auth/google', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['google_access_token']);

        // Test token max length
        $response = $this->postJson('/api/auth/google', [
            'google_access_token' => str_repeat('a', 1001),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['google_access_token']);
    }

    // ==================== SURVEY VALIDATION TESTS ====================

    public function test_survey_create_validation_errors(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Test required fields
        $response = $this->postJson('/api/surveys', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'status']);

        // Test title max length
        $response = $this->postJson('/api/surveys', [
            'title' => str_repeat('a', 256),
            'status' => 'draft',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);

        // Test description max length
        $response = $this->postJson('/api/surveys', [
            'title' => 'Test Survey',
            'description' => str_repeat('a', 1001),
            'status' => 'draft',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['description']);

        // Test invalid status
        $response = $this->postJson('/api/surveys', [
            'title' => 'Test Survey',
            'status' => 'invalid_status',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['status']);

        // Test template_id min value
        $response = $this->postJson('/api/surveys', [
            'title' => 'Test Survey',
            'status' => 'draft',
            'template_id' => 0,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['template_id']);

        // Test max_responses min value
        $response = $this->postJson('/api/surveys', [
            'title' => 'Test Survey',
            'status' => 'draft',
            'max_responses' => 0,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['max_responses']);

        // Test max_responses max value
        $response = $this->postJson('/api/surveys', [
            'title' => 'Test Survey',
            'status' => 'draft',
            'max_responses' => 1000001,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['max_responses']);

        // Test expires_at future date
        $response = $this->postJson('/api/surveys', [
            'title' => 'Test Survey',
            'status' => 'draft',
            'expires_at' => now()->subDay(),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['expires_at']);
    }

    // ==================== QUESTION VALIDATION TESTS ====================

    public function test_question_create_validation_errors(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Test required fields
        $response = $this->postJson("/api/survey-pages/{$this->page->id}/questions", []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['type', 'title']);

        // Test type max length
        $response = $this->postJson("/api/survey-pages/{$this->page->id}/questions", [
            'type' => str_repeat('a', 51),
            'title' => 'Test Question',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['type']);

        // Test title max length
        $response = $this->postJson("/api/survey-pages/{$this->page->id}/questions", [
            'type' => 'text',
            'title' => str_repeat('a', 256),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);

        // Test help_text max length
        $response = $this->postJson("/api/survey-pages/{$this->page->id}/questions", [
            'type' => 'text',
            'title' => 'Test Question',
            'help_text' => str_repeat('a', 1001),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['help_text']);

        // Test placeholder max length
        $response = $this->postJson("/api/survey-pages/{$this->page->id}/questions", [
            'type' => 'text',
            'title' => 'Test Question',
            'placeholder' => str_repeat('a', 256),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['placeholder']);

        // Test order_index min value
        $response = $this->postJson("/api/survey-pages/{$this->page->id}/questions", [
            'type' => 'text',
            'title' => 'Test Question',
            'order_index' => -1,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['order_index']);
    }

    // ==================== CHOICE VALIDATION TESTS ====================

    public function test_choice_create_validation_errors(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Test required fields
        $response = $this->postJson("/api/questions/{$this->question->id}/choices", []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['label', 'value']);

        // Test label max length
        $response = $this->postJson("/api/questions/{$this->question->id}/choices", [
            'label' => str_repeat('a', 256),
            'value' => 'test',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['label']);

        // Test value max length
        $response = $this->postJson("/api/questions/{$this->question->id}/choices", [
            'label' => 'Test Choice',
            'value' => str_repeat('a', 256),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['value']);

        // Test order_index min value
        $response = $this->postJson("/api/questions/{$this->question->id}/choices", [
            'label' => 'Test Choice',
            'value' => 'test',
            'order_index' => -1,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['order_index']);
    }

    // ==================== SURVEY PAGE VALIDATION TESTS ====================

    public function test_survey_page_create_validation_errors(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Test required fields
        $response = $this->postJson('/api/surveys/pages', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['survey_id']);

        // Test survey_id min value
        $response = $this->postJson('/api/surveys/pages', [
            'survey_id' => 0,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['survey_id']);

        // Test title max length
        $response = $this->postJson('/api/surveys/pages', [
            'survey_id' => $this->survey->id,
            'title' => str_repeat('a', 256),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);

        // Test order_index min value
        $response = $this->postJson('/api/surveys/pages', [
            'survey_id' => $this->survey->id,
            'order_index' => -1,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['order_index']);
    }

    // ==================== TEMPLATE VALIDATION TESTS ====================

    public function test_template_create_validation_errors(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Test required fields
        $response = $this->postJson('/api/templates', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);

        // Test title max length
        $response = $this->postJson('/api/templates', [
            'title' => str_repeat('a', 256),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);

        // Test description max length
        $response = $this->postJson('/api/templates', [
            'title' => 'Test Template',
            'description' => str_repeat('a', 1001),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['description']);

        // Test forked_from_template_id min value
        $response = $this->postJson('/api/templates', [
            'title' => 'Test Template',
            'forked_from_template_id' => 0,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['forked_from_template_id']);
    }

    // ==================== MEDIA VALIDATION TESTS ====================

    public function test_media_upload_validation_errors(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Test required fields
        $response = $this->postJson('/api/media/upload', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['question_id', 'file']);

        // Test question_id min value
        $response = $this->postJson('/api/media/upload', [
            'question_id' => 0,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['question_id']);

        // Test alt_text max length
        $response = $this->postJson('/api/media/upload', [
            'question_id' => $this->question->id,
            'alt_text' => str_repeat('a', 256),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['alt_text']);

        // Test caption max length
        $response = $this->postJson('/api/media/upload', [
            'question_id' => $this->question->id,
            'caption' => str_repeat('a', 501),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['caption']);
    }

    // ==================== ROLE VALIDATION TESTS ====================

    public function test_role_assign_validation_errors(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Test required fields
        $response = $this->postJson('/api/roles/assign', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['role_name']);

        // Test role_name max length
        $response = $this->postJson('/api/roles/assign', [
            'role_name' => str_repeat('a', 256),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['role_name']);

        // Test user_id min value
        $response = $this->postJson('/api/roles/assign', [
            'role_name' => 'test_role',
            'user_id' => 0,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['user_id']);

        // Test survey_id min value
        $response = $this->postJson('/api/roles/assign', [
            'role_name' => 'test_role',
            'survey_id' => 0,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['survey_id']);
    }

    // ==================== RESPONSE VALIDATION TESTS ====================

    public function test_response_create_validation_errors(): void
    {
        // Test required fields
        $response = $this->postJson('/api/responses', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['survey_id']);

        // Test survey_id min value
        $response = $this->postJson('/api/responses', [
            'survey_id' => 0,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['survey_id']);
    }

    public function test_response_submit_validation_errors(): void
    {
        // Create a response first
        $responseData = ['survey_id' => $this->survey->id];
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

        // Test required question_id in answers
        $response = $this->postJson("/api/responses/{$responseId}/submit", [
            'answers' => [
                ['value' => 'test'],
            ],
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['answers.0.question_id']);

        // Test question_id min value
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
                    'question_id' => $this->question->id,
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
                    'question_id' => $this->question->id,
                    'value' => 'test',
                    'order_index' => -1,
                ],
            ],
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['answers.0.order_index']);
    }
} 