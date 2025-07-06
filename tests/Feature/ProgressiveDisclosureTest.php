<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Survey;
use App\Models\SurveyPage;

class ProgressiveDisclosureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_their_experience_level()
    {
        $user = User::factory()->create(['experience_level' => 'basic']);

        $this->actingAs($user, 'sanctum');

        $response = $this->putJson('/api/me', [
            'experience_level' => 'advanced',
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['experience_level' => 'advanced']);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'experience_level' => 'advanced',
        ]);
    }

    public function test_user_receives_validation_error_for_invalid_experience_level()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->putJson('/api/me', [
            'experience_level' => 'expert', // Invalid value
        ]);

        $response->assertStatus(422);
        
        // Check that the 'data' key contains an 'experience_level' error.
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'experience_level'
            ]
        ]);
        
        // Check that the error message is correct.
        $this->assertStringContainsString(
            'The selected experience level is invalid.', 
            $response->json('data.experience_level.0')
        );
    }

    public function test_survey_ui_complexity_level_can_be_set()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $surveyData = [
            'title' => 'Test Survey',
            'status' => 'draft',
            'settings' => [
                'anonymous' => false,
                'ui_complexity_level' => 'intermediate',
            ]
        ];

        $response = $this->postJson('/api/surveys', $surveyData);

        $response->assertCreated();
        $surveyId = $response->json('data.id');
        $survey = Survey::find($surveyId);
        
        $this->assertEquals('intermediate', $survey->settings['ui_complexity_level']);
    }

    public function test_question_complexity_and_category_can_be_set()
    {
        $user = User::factory()->create();
        $survey = Survey::factory()->create(['created_by' => $user->id]);
        $page = SurveyPage::factory()->create(['survey_id' => $survey->id]);
        
        $this->actingAs($user, 'sanctum');

        $questionData = [
            'title' => 'How complex is this?',
            'type' => 'text',
            'config' => [
                'complexity_level' => 'advanced',
                'category' => 'Technical',
            ],
        ];

        $response = $this->postJson("/api/survey-pages/{$page->id}/questions", $questionData);

        $response->assertCreated();
        $response->assertJsonPath('data.config.complexity_level', 'advanced');
        $response->assertJsonPath('data.config.category', 'Technical');
    }
}
