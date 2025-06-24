<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Template;
use App\Models\TemplateVersion;
use App\Models\Survey;
use App\Models\SurveyPage;
use App\Models\Question;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => bcrypt('password'),
        ]);
        
        $this->otherUser = User::factory()->create([
            'email' => 'otheruser@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    // ==================== AUTH ENDPOINTS ====================
    
    public function test_auth_register(): void
    {
        $data = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];
        
        $response = $this->postJson('/api/auth/register', $data);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'token',
            'user' => ['id', 'name', 'email', 'created_at', 'updated_at']
        ]);
    }

    public function test_auth_login(): void
    {
        $data = [
            'email' => 'testuser@example.com',
            'password' => 'password',
        ];
        
        $response = $this->postJson('/api/auth/login', $data);
        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
    }

    public function test_auth_me(): void
    {
        $this->actingAs($this->user, 'sanctum');
        
        $response = $this->getJson('/api/auth/me');
        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'name', 'email']);
    }

    public function test_auth_logout(): void
    {
        $this->actingAs($this->user, 'sanctum');
        
        $response = $this->postJson('/api/auth/logout');
        $response->assertStatus(200);
    }

    public function test_auth_google_login(): void
    {
        $data = [
            'access_token' => 'fake_google_token',
        ];
        
        $response = $this->postJson('/api/auth/google', $data);
        $response->assertStatus(422); // Invalid token should return validation error
    }

    // ==================== TEMPLATE ENDPOINTS ====================
    
    public function test_templates_index(): void
    {
        Template::factory()->count(3)->create();
        
        $response = $this->getJson('/api/templates');
        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    public function test_templates_store(): void
    {
        $this->actingAs($this->user, 'sanctum');
        
        $data = [
            'title' => 'Test Template',
            'description' => 'Test Description',
            'is_public' => true,
        ];
        
        $response = $this->postJson('/api/templates', $data);
        $response->assertStatus(201);
        $response->assertJsonStructure(['id', 'title', 'description', 'is_public', 'created_by']);
    }

    public function test_templates_show(): void
    {
        $template = Template::factory()->create(['is_public' => true]);
        
        $response = $this->getJson("/api/templates/{$template->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'title']);
    }

    public function test_templates_update(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $template = Template::factory()->create(['created_by' => $this->user->id]);
        
        $data = [
            'title' => 'Updated Template',
            'description' => 'Updated Description',
        ];
        
        $response = $this->putJson("/api/templates/{$template->id}", $data);
        $response->assertStatus(200);
        $response->assertJson(['title' => 'Updated Template']);
    }

    public function test_templates_destroy(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $template = Template::factory()->create(['created_by' => $this->user->id]);
        
        $response = $this->deleteJson("/api/templates/{$template->id}");
        $response->assertStatus(200);
    }

    public function test_templates_public(): void
    {
        Template::factory()->create(['is_public' => true]);
        Template::factory()->create(['is_public' => false]);
        
        $response = $this->getJson('/api/templates/public');
        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    public function test_templates_my(): void
    {
        $this->actingAs($this->user, 'sanctum');
        Template::factory()->create(['created_by' => $this->user->id]);
        Template::factory()->create(['created_by' => $this->otherUser->id]);
        
        $response = $this->getJson('/api/templates/my');
        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    public function test_templates_fork(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $template = Template::factory()->create(['is_public' => true]);
        
        $response = $this->postJson("/api/templates/{$template->id}/fork");
        $response->assertStatus(201);
        $response->assertJsonStructure(['id', 'title']);
    }

    public function test_templates_versions(): void
    {
        $template = Template::factory()->create(['is_public' => true]);
        TemplateVersion::factory()->create(['template_id' => $template->id]);
        
        $response = $this->getJson("/api/templates/{$template->id}/versions");
        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    public function test_templates_create_version(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $template = Template::factory()->create(['created_by' => $this->user->id]);
        
        $data = [
            'version' => '2.0.0',
            'snapshot' => ['new' => 'data'],
        ];
        
        $response = $this->postJson("/api/templates/{$template->id}/versions", $data);
        $response->assertStatus(201);
        $response->assertJsonStructure(['id', 'version']);
    }

    public function test_templates_restore_version(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $template = Template::factory()->create(['created_by' => $this->user->id]);
        $version = TemplateVersion::factory()->create(['template_id' => $template->id]);
        
        $response = $this->postJson("/api/templates/{$template->id}/versions/{$version->id}/restore");
        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'title']);
    }

    // ==================== SURVEY ENDPOINTS ====================
    
    public function test_surveys_index(): void
    {
        Survey::factory()->count(3)->create();
        
        $response = $this->getJson('/api/surveys');
        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    public function test_surveys_store(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $template = Template::factory()->create();
        $version = TemplateVersion::factory()->create(['template_id' => $template->id]);
        
        $data = [
            'title' => 'Test Survey',
            'description' => 'Test Description',
            'status' => 'draft',
            'template_id' => $template->id,
            'template_version_id' => $version->id,
        ];
        
        $response = $this->postJson('/api/surveys', $data);
        $response->assertStatus(201);
        $response->assertJsonStructure(['id', 'title', 'status']);
    }

    public function test_surveys_show(): void
    {
        $survey = Survey::factory()->create(['status' => 'active']);
        
        $response = $this->getJson("/api/surveys/{$survey->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'title']);
    }

    public function test_surveys_update(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $survey = Survey::factory()->create(['created_by' => $this->user->id]);
        
        $data = [
            'title' => 'Updated Survey',
            'description' => 'Updated Description',
        ];
        
        $response = $this->putJson("/api/surveys/{$survey->id}", $data);
        $response->assertStatus(200);
        $response->assertJson(['title' => 'Updated Survey']);
    }

    public function test_surveys_destroy(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $survey = Survey::factory()->create(['created_by' => $this->user->id]);
        
        $response = $this->deleteJson("/api/surveys/{$survey->id}");
        $response->assertStatus(200);
    }

    public function test_surveys_active(): void
    {
        Survey::factory()->create(['status' => 'active']);
        Survey::factory()->create(['status' => 'draft']);
        
        $response = $this->getJson('/api/surveys/active');
        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    public function test_surveys_my(): void
    {
        $this->actingAs($this->user, 'sanctum');
        Survey::factory()->create(['created_by' => $this->user->id]);
        Survey::factory()->create(['created_by' => $this->otherUser->id]);
        
        $response = $this->getJson('/api/surveys/my');
        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    public function test_surveys_by_status(): void
    {
        Survey::factory()->create(['status' => 'draft']);
        Survey::factory()->create(['status' => 'active']);
        
        $response = $this->getJson('/api/surveys/status/draft');
        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    public function test_surveys_by_template(): void
    {
        $template = Template::factory()->create();
        Survey::factory()->create(['template_id' => $template->id]);
        Survey::factory()->create(['template_id' => null]);
        
        $response = $this->getJson("/api/surveys/template/{$template->id}");
        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    public function test_surveys_publish(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $survey = Survey::factory()->create(['created_by' => $this->user->id, 'status' => 'draft']);
        
        $response = $this->postJson("/api/surveys/{$survey->id}/publish");
        $response->assertStatus(200);
    }

    public function test_surveys_archive(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $survey = Survey::factory()->create(['created_by' => $this->user->id, 'status' => 'active']);
        
        $response = $this->postJson("/api/surveys/{$survey->id}/archive");
        $response->assertStatus(200);
    }

    public function test_surveys_duplicate(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $survey = Survey::factory()->create(['created_by' => $this->user->id]);
        
        $response = $this->postJson("/api/surveys/{$survey->id}/duplicate");
        $response->assertStatus(201);
        $response->assertJsonStructure(['id', 'title']);
    }

    // ==================== SURVEY PAGE ENDPOINTS ====================
    
    public function test_survey_pages_index(): void
    {
        $survey = Survey::factory()->create(['status' => 'active']);
        SurveyPage::factory()->create(['survey_id' => $survey->id]);
        
        $response = $this->getJson("/api/surveys/{$survey->id}/pages");
        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    public function test_survey_pages_store(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $survey = Survey::factory()->create(['created_by' => $this->user->id]);
        
        $data = [
            'survey_id' => $survey->id,
            'title' => 'Test Page',
            'order_index' => 0,
        ];
        
        $response = $this->postJson('/api/surveys/pages', $data);
        $response->assertStatus(201);
        $response->assertJsonStructure(['id', 'title']);
    }

    public function test_survey_pages_show(): void
    {
        $survey = Survey::factory()->create(['status' => 'active']);
        $page = SurveyPage::factory()->create(['survey_id' => $survey->id]);
        
        $response = $this->getJson("/api/surveys/pages/{$page->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'title']);
    }

    public function test_survey_pages_update(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $survey = Survey::factory()->create(['created_by' => $this->user->id]);
        $page = SurveyPage::factory()->create(['survey_id' => $survey->id]);
        
        $data = [
            'title' => 'Updated Page',
            'order_index' => 1,
        ];
        
        $response = $this->putJson("/api/surveys/pages/{$page->id}", $data);
        $response->assertStatus(200);
        $response->assertJson(['title' => 'Updated Page']);
    }

    public function test_survey_pages_destroy(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $survey = Survey::factory()->create(['created_by' => $this->user->id]);
        $page = SurveyPage::factory()->create(['survey_id' => $survey->id]);
        
        $response = $this->deleteJson("/api/surveys/pages/{$page->id}");
        $response->assertStatus(200);
    }

    public function test_survey_pages_reorder(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $survey = Survey::factory()->create(['created_by' => $this->user->id]);
        $page1 = SurveyPage::factory()->create(['survey_id' => $survey->id, 'order_index' => 0]);
        $page2 = SurveyPage::factory()->create(['survey_id' => $survey->id, 'order_index' => 1]);

        $data = [
            'page_ids' => [$page2->id, $page1->id],
        ];

        $response = $this->postJson("/api/surveys/{$survey->id}/pages/reorder", $data);
        $response->assertStatus(200);
    }

    // ==================== QUESTION ENDPOINTS ====================
    
    public function test_questions_index(): void
    {
        $page = SurveyPage::factory()->create();
        Question::factory()->create(['page_id' => $page->id]);
        
        $response = $this->getJson("/api/survey-pages/{$page->id}/questions");
        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    public function test_questions_store(): void
    {
        $page = SurveyPage::factory()->create();
        $data = [
            'page_id' => $page->id,
            'type' => 'text',
            'title' => 'Test Question',
            'is_required' => true,
        ];
        
        $response = $this->postJson("/api/survey-pages/{$page->id}/questions", $data);
        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['id', 'title', 'type']]);
    }

    public function test_questions_show(): void
    {
        $question = Question::factory()->create();
        
        $response = $this->getJson("/api/questions/{$question->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['id', 'title']]);
    }

    public function test_questions_update(): void
    {
        $question = Question::factory()->create();
        $data = [
            'page_id' => $question->page_id,
            'type' => 'select',
            'title' => 'Updated Question',
            'is_required' => false,
            'placeholder' => 'dolor',
            'config' => [
                'min' => 6,
                'max' => null,
                'options' => null
            ],
            'order_index' => 2,
        ];
        
        $response = $this->putJson("/api/questions/{$question->id}", $data);
        $response->assertStatus(200);
        $response->assertJson(['data' => ['title' => 'Updated Question']]);
    }

    public function test_questions_destroy(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $survey = Survey::factory()->create(['created_by' => $this->user->id]);
        $page = SurveyPage::factory()->create(['survey_id' => $survey->id]);
        $question = Question::factory()->create(['page_id' => $page->id]);
        
        $response = $this->deleteJson("/api/questions/{$question->id}");
        $response->assertStatus(200);
    }

    public function test_questions_by_type(): void
    {
        $page = SurveyPage::factory()->create();
        Question::factory()->create(['page_id' => $page->id, 'type' => 'text']);
        Question::factory()->create(['page_id' => $page->id, 'type' => 'number']);
        
        $response = $this->getJson("/api/survey-pages/{$page->id}/questions/type/text");
        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    public function test_questions_reorder(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $survey = Survey::factory()->create(['created_by' => $this->user->id]);
        $page = SurveyPage::factory()->create(['survey_id' => $survey->id]);
        $question1 = Question::factory()->create(['page_id' => $page->id, 'order_index' => 0]);
        $question2 = Question::factory()->create(['page_id' => $page->id, 'order_index' => 1]);
        
        $data = [
            'question_ids' => [$question2->id, $question1->id],
        ];
        
        $response = $this->postJson("/api/survey-pages/{$page->id}/questions/reorder", $data);
        $response->assertStatus(200);
    }

    // ==================== ERROR CASES ====================
    
    public function test_unauthorized_access(): void
    {
        $template = Template::factory()->create();
        
        $response = $this->putJson("/api/templates/{$template->id}", ['title' => 'Test']);
        $response->assertStatus(401);
    }

    public function test_forbidden_access(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $template = Template::factory()->create(['created_by' => $this->otherUser->id]);
        
        $response = $this->putJson("/api/templates/{$template->id}", ['title' => 'Test']);
        $response->assertStatus(403);
    }

    public function test_not_found(): void
    {
        $response = $this->getJson('/api/templates/999999');
        $response->assertStatus(404);
    }

    public function test_validation_errors(): void
    {
        $this->actingAs($this->user, 'sanctum');
        
        $response = $this->postJson('/api/templates', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);
    }
} 