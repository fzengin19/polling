<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrudSmokeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'sanctum');
    }

    public function test_user_template_survey_crud_flow(): void
    {
        // 1. Create Template
        $templateData = [
            'title' => 'My Smoke Test Template',
            'is_public' => true,
        ];
        $response = $this->postJson('/api/templates', $templateData);
        $response->assertStatus(201);
        $templateId = $response->json('data.id');
        $this->assertDatabaseHas('templates', ['id' => $templateId, 'title' => 'My Smoke Test Template']);

        // 2. Create Template Version
        $versionData = [
            'version' => '1.0.0',
            'snapshot' => ['foo' => 'bar'],
        ];
        $response = $this->postJson("/api/templates/{$templateId}/versions", $versionData);
        $response->assertStatus(201);
        $templateVersionId = $response->json('data.id');
        $this->assertDatabaseHas('template_versions', ['id' => $templateVersionId, 'template_id' => $templateId]);

        // 3. Create Survey from Template
        $surveyData = [
            'title' => 'My Smoke Test Survey',
            'status' => 'draft',
            'template_id' => $templateId,
            'template_version_id' => $templateVersionId,
        ];
        $response = $this->postJson('/api/surveys', $surveyData);
        $response->assertStatus(201);
        $surveyId = $response->json('data.id');
        $this->assertDatabaseHas('surveys', ['id' => $surveyId, 'template_id' => $templateId]);

        // 4. Create Survey Page
        $pageData = [
            'survey_id' => $surveyId,
            'title' => 'Page 1',
        ];
        $response = $this->postJson('/api/surveys/pages', $pageData);
        $response->assertStatus(201);
        $pageId = $response->json('data.id');
        $this->assertDatabaseHas('survey_pages', ['id' => $pageId, 'survey_id' => $surveyId]);

        // 5. Create Question
        $questionData = [
            'type' => 'text',
            'title' => 'What is your name?',
        ];
        $response = $this->postJson("/api/survey-pages/{$pageId}/questions", $questionData);
        $response->assertStatus(201);
        $questionId = $response->json('data.id');
        $this->assertDatabaseHas('questions', ['id' => $questionId, 'page_id' => $pageId]);
    }
} 