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
use Illuminate\Http\UploadedFile;

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
        $response->assertJsonValidationErrors(['name', 'email', 'password'], 'data');

        // Test email format
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email'], 'data');

        // Test password min length
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password'], 'data');

        // Test name max length
        $response = $this->postJson('/api/auth/register', [
            'name' => str_repeat('a', 256),
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name'], 'data');

        // Test email max length
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => str_repeat('a', 250) . '@example.com',
            'password' => 'password123',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email'], 'data');

        // Test password confirmation
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password1234',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password'], 'data');
    }

    public function test_login_validation_errors(): void
    {
        // Test required fields
        $response = $this->postJson('/api/auth/login', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password'], 'data');

        // Test email format
        $response = $this->postJson('/api/auth/login', [
            'email' => 'invalid-email',
            'password' => 'password',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email'], 'data');
    }

    public function test_google_login_validation_errors(): void
    {
        // Test required token
        $response = $this->postJson('/api/auth/google', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['google_access_token'], 'data');

        // Test token max length
        $response = $this->postJson('/api/auth/google', [
            'google_access_token' => str_repeat('a', 1001),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['google_access_token'], 'data');
    }

    // ==================== SURVEY VALIDATION TESTS ====================

    public function test_survey_create_validation_errors(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Test required fields
        $response = $this->postJson('/api/surveys', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'status'], 'data');

        // Test title max length
        $response = $this->postJson('/api/surveys', [
            'title' => str_repeat('a', 256),
            'status' => 'draft',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title'], 'data');

        // Test description max length
        $response = $this->postJson('/api/surveys', [
            'title' => 'Test Survey',
            'description' => str_repeat('a', 1001),
            'status' => 'draft',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['description'], 'data');

        // Test invalid status
        $response = $this->postJson('/api/surveys', [
            'title' => 'Test Survey',
            'status' => 'invalid_status',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['status'], 'data');

        // Test template_id min value
        $response = $this->postJson('/api/surveys', [
            'title' => 'Test Survey',
            'status' => 'draft',
            'template_id' => 0,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['template_id'], 'data');

        // Test max_responses min value
        $response = $this->postJson('/api/surveys', [
            'title' => 'Test Survey',
            'status' => 'draft',
            'max_responses' => 0,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['max_responses'], 'data');

        // Test max_responses max value
        $response = $this->postJson('/api/surveys', [
            'title' => 'Test Survey',
            'status' => 'draft',
            'max_responses' => 1000001,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['max_responses'], 'data');

        // Test expires_at future date
        $response = $this->postJson('/api/surveys', [
            'title' => 'Test Survey',
            'status' => 'draft',
            'expires_at' => now()->subDay(),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['expires_at'], 'data');
    }

    // ==================== QUESTION VALIDATION TESTS ====================

    public function test_question_create_validation_errors(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Test required fields
        $response = $this->postJson("/api/survey-pages/{$this->page->id}/questions", []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['type', 'title'], 'data');

        // Test type max length
        $response = $this->postJson("/api/survey-pages/{$this->page->id}/questions", [
            'type' => str_repeat('a', 51),
            'title' => 'Test Question',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['type'], 'data');

        // Test title max length
        $response = $this->postJson("/api/survey-pages/{$this->page->id}/questions", [
            'type' => 'text',
            'title' => str_repeat('a', 256),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title'], 'data');

        // Test help_text max length
        $response = $this->postJson("/api/survey-pages/{$this->page->id}/questions", [
            'type' => 'text',
            'title' => 'Test Question',
            'help_text' => str_repeat('a', 1001),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['help_text'], 'data');

        // Test placeholder max length
        $response = $this->postJson("/api/survey-pages/{$this->page->id}/questions", [
            'type' => 'text',
            'title' => 'Test Question',
            'placeholder' => str_repeat('a', 256),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['placeholder'], 'data');

        // Test order_index min value
        $response = $this->postJson("/api/survey-pages/{$this->page->id}/questions", [
            'type' => 'text',
            'title' => 'Test Question',
            'order_index' => -1,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['order_index'], 'data');
    }

    // ==================== CHOICE VALIDATION TESTS ====================

    public function test_choice_create_validation_errors(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Test required fields
        $response = $this->postJson("/api/questions/{$this->question->id}/choices", []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['label', 'value'], 'data');

        // Test label max length
        $response = $this->postJson("/api/questions/{$this->question->id}/choices", [
            'label' => str_repeat('a', 256),
            'value' => 'test',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['label'], 'data');

        // Test value max length
        $response = $this->postJson("/api/questions/{$this->question->id}/choices", [
            'label' => 'Test Choice',
            'value' => str_repeat('a', 256),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['value'], 'data');

        // Test order_index min value
        $response = $this->postJson("/api/questions/{$this->question->id}/choices", [
            'label' => 'Test Choice',
            'value' => 'test',
            'order_index' => -1,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['order_index'], 'data');
    }

    // ==================== SURVEY PAGE VALIDATION TESTS ====================

    public function test_survey_page_create_validation_errors(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Test required fields
        $response = $this->postJson('/api/surveys/pages', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['survey_id'], 'data');

        // Test survey_id min value
        $response = $this->postJson('/api/surveys/pages', [
            'survey_id' => 0,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['survey_id'], 'data');

        // Test title max length
        $response = $this->postJson('/api/surveys/pages', [
            'survey_id' => $this->survey->id,
            'title' => str_repeat('a', 256),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title'], 'data');

        // Test order_index min value
        $response = $this->postJson('/api/surveys/pages', [
            'survey_id' => $this->survey->id,
            'order_index' => -1,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['order_index'], 'data');
    }

    // ==================== TEMPLATE VALIDATION TESTS ====================

    public function test_template_create_validation_errors(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Test required fields
        $response = $this->postJson('/api/templates', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title'], 'data');

        // Test title max length
        $response = $this->postJson('/api/templates', [
            'title' => str_repeat('a', 256),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title'], 'data');

        // Test description max length
        $response = $this->postJson('/api/templates', [
            'title' => 'Test Template',
            'description' => str_repeat('a', 1001),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['description'], 'data');

        // Test forked_from_template_id min value
        $response = $this->postJson('/api/templates', [
            'title' => 'Test Template',
            'forked_from_template_id' => 0,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['forked_from_template_id'], 'data');
    }

    // ==================== MEDIA VALIDATION TESTS ====================

    public function test_media_upload_validation_errors(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Test required fields
        $response = $this->postJson('/api/media/upload', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'model_type',
            'model_id',
            'collection_name',
            'file'
        ], 'data');

        // Test model_type validation
        $response = $this->postJson('/api/media/upload', [
            'model_type' => 'invalid_model',
            'model_id' => 1,
            'collection_name' => 'images',
            'file' => UploadedFile::fake()->image('test.jpg')
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['model_type'], 'data');
    }

    // ==================== ROLE VALIDATION TESTS ====================

    public function test_role_assign_validation_errors(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Test required fields
        $response = $this->postJson('/api/roles/assign', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['role_name', 'model_type', 'model_id'], 'data');

        // Test invalid role_name
        $response = $this->postJson('/api/roles/assign', ['role_name' => 'nonexistent-role', 'model_type' => 'user', 'model_id' => 1]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('role_name', 'data');

        // Test invalid model_type
        $response = $this->postJson('/api/roles/assign', ['role_name' => 'editor', 'model_type' => 'invalid-type', 'model_id' => 1]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('model_type', 'data');
    }

    // ==================== RESPONSE VALIDATION TESTS ====================

    public function test_response_create_validation_errors(): void
    {
        // Test required fields
        $response = $this->postJson('/api/responses', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['survey_id'], 'data');

        // Test survey_id min value
        $response = $this->postJson('/api/responses', [
            'survey_id' => 0,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['survey_id'], 'data');
    }

    public function test_response_submit_validation_errors(): void
    {
        // Create a response first
        $response = $this->postJson("/api/responses", ['survey_id' => $this->survey->id]);
        $responseId = $response->json('data.id');

        // Test with a non-existent response ID should still trigger validation first
        $response = $this->postJson("/api/responses/999999/submit", []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['answers'], 'data');

        // Test empty answers array with a valid response ID
        $response = $this->postJson("/api/responses/{$responseId}/submit", ['answers' => []]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['answers'], 'data');

        // Test invalid answer structure (missing question_id)
        $response = $this->postJson("/api/responses/{$responseId}/submit", ['answers' => [
            ['value' => 'some answer']
        ]]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['answers.0.question_id'], 'data');
    }
} 