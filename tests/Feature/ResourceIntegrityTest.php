<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Survey;
use App\Models\SurveyPage;
use App\Models\Question;
use App\Models\Choice;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResourceIntegrityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that deleting a survey also deletes its related pages, questions, and choices.
     *
     * @return void
     */
    public function test_survey_deletion_cascades_to_related_models(): void
    {
        // 1. Setup
        $user = User::factory()->create();
        $survey = Survey::factory()->create(['created_by' => $user->id]);
        $page = SurveyPage::factory()->create(['survey_id' => $survey->id]);
        $question = Question::factory()->create(['page_id' => $page->id, 'type' => 'multiple_choice']);
        $choice = Choice::factory()->create(['question_id' => $question->id]);

        // 2. Assert initial state
        $this->assertDatabaseHas('surveys', ['id' => $survey->id]);
        $this->assertDatabaseHas('survey_pages', ['id' => $page->id]);
        $this->assertDatabaseHas('questions', ['id' => $question->id]);
        $this->assertDatabaseHas('choices', ['id' => $choice->id]);

        // 3. Action
        $this->actingAs($user, 'sanctum')->deleteJson("/api/surveys/{$survey->id}")->assertStatus(204);

        // 4. Assert final state
        $this->assertDatabaseMissing('surveys', ['id' => $survey->id]);
        $this->assertDatabaseMissing('survey_pages', ['id' => $page->id]);
        $this->assertDatabaseMissing('questions', ['id' => $question->id]);
        $this->assertDatabaseMissing('choices', ['id' => $choice->id]);
    }

    /**
     * Test that a user cannot update a survey they do not own.
     */
    public function test_user_cannot_update_others_survey(): void
    {
        $owner = User::factory()->create();
        $imposter = User::factory()->create();
        $survey = Survey::factory()->create(['created_by' => $owner->id]);

        $this->actingAs($imposter)->putJson("/api/surveys/{$survey->id}", ['title' => 'New Title'])
            ->assertForbidden();
    }

    /**
     * Test that a user cannot delete a survey they do not own.
     */
    public function test_user_cannot_delete_others_survey(): void
    {
        $owner = User::factory()->create();
        $imposter = User::factory()->create();
        $survey = Survey::factory()->create(['created_by' => $owner->id]);

        $this->actingAs($imposter)->deleteJson("/api/surveys/{$survey->id}")
            ->assertForbidden();
    }

    /**
     * Test that a user cannot update another user's template.
     */
    public function test_user_cannot_update_others_template(): void
    {
        $owner = User::factory()->create();
        $imposter = User::factory()->create();
        $template = Template::factory()->create(['created_by' => $owner->id]);
        
        $this->actingAs($imposter)->putJson("/api/templates/{$template->id}", ['title' => 'New Title'])
            ->assertForbidden();
    }
} 