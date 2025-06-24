<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class CrudSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_template_survey_crud_flow(): void
    {
        // User registration
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user, 'sanctum');

        // Create Template
        $templateData = [
            'title' => 'Test Template',
            'description' => 'desc',
            'is_public' => true,
        ];
        $response = $this->postJson('/api/templates', $templateData);
        $response->assertStatus(201);
        $templateId = $response->json('id');

        // Create TemplateVersion
        $versionData = [
            'version' => '1.0.0',
            'snapshot' => ['foo' => 'bar'],
        ];
        $response = $this->postJson("/api/templates/{$templateId}/versions", $versionData);
        $response->assertStatus(201);
        $templateVersionId = $response->json('id');

        // Create Survey
        $surveyData = [
            'title' => 'Test Survey',
            'description' => 'desc',
            'status' => 'draft',
            'template_id' => $templateId,
            'template_version_id' => $templateVersionId,
        ];
        $response = $this->postJson('/api/surveys', $surveyData);
        $response->assertStatus(201);
        $surveyId = $response->json('id');

        // Create SurveyPage
        $pageData = [
            'survey_id' => $surveyId,
            'order_index' => 0,
            'title' => 'Page 1',
        ];
        $response = $this->postJson('/api/surveys/pages', $pageData);
        $response->assertStatus(201);
        $pageId = $response->json('id');

        // Create Question
        $questionData = [
            'page_id' => $pageId,
            'type' => 'text',
            'title' => 'Q1',
            'is_required' => true,
            'help_text' => 'help',
            'placeholder' => 'ph',
            'config' => ['min' => 1],
            'order_index' => 0,
        ];
        $response = $this->postJson('/api/survey-pages/'.$pageId.'/questions', $questionData);
        $response->assertStatus(201);
        $questionId = $response->json('id');

        // List Questions
        $response = $this->getJson('/api/survey-pages/'.$pageId.'/questions');
        $response->assertStatus(200);
        $this->assertTrue(count($response->json('data')) > 0);
    }
} 