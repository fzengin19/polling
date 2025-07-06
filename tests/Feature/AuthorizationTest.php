<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Survey;
use App\Models\Template;
use App\Models\SurveyPage;
use App\Models\Question;
use App\Models\Choice;
use App\Models\TemplateVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $imposter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create();
        $this->imposter = User::factory()->create();
    }

    // Survey Child Resources
    public function test_user_cannot_update_child_page_of_others_survey(): void
    {
        $survey = Survey::factory()->create(['created_by' => $this->owner->id]);
        $page = SurveyPage::factory()->create(['survey_id' => $survey->id]);

        $this->actingAs($this->imposter)
            ->putJson("/api/surveys/pages/{$page->id}", ['title' => 'Imposter Title'])
            ->assertStatus(403);
    }

    public function test_user_cannot_delete_child_question_of_others_survey(): void
    {
        $survey = Survey::factory()->create(['created_by' => $this->owner->id]);
        $page = SurveyPage::factory()->create(['survey_id' => $survey->id]);
        $question = Question::factory()->create(['page_id' => $page->id]);

        $this->actingAs($this->imposter)
            ->deleteJson("/api/questions/{$question->id}")
            ->assertStatus(403);
    }

    // Public vs Private Resource Viewing
    public function test_guest_can_view_public_survey(): void
    {
        $survey = Survey::factory()->create(['created_by' => $this->owner->id, 'status' => 'active']);
        $this->getJson("/api/surveys/{$survey->id}")->assertStatus(200);
    }

    public function test_guest_cannot_view_private_survey(): void
    {
        $survey = Survey::factory()->create(['created_by' => $this->owner->id, 'status' => 'draft']);
        $this->getJson("/api/surveys/{$survey->id}")->assertStatus(403);
    }
    
    public function test_user_can_view_own_private_survey(): void
    {
        $survey = Survey::factory()->create(['created_by' => $this->owner->id, 'status' => 'draft']);
        $this->actingAs($this->owner)->getJson("/api/surveys/{$survey->id}")->assertStatus(200);
    }

    // Specific Actions
    public function test_user_cannot_publish_others_survey(): void
    {
        $survey = Survey::factory()->create(['created_by' => $this->owner->id, 'status' => 'draft']);
        $this->actingAs($this->imposter)
            ->postJson("/api/surveys/{$survey->id}/publish")
            ->assertStatus(403);
    }

    public function test_user_cannot_fork_private_template_of_others(): void
    {
        $template = Template::factory()->create(['created_by' => $this->owner->id, 'is_public' => false]);
        $this->actingAs($this->imposter)
            ->postJson("/api/templates/{$template->id}/fork")
            ->assertStatus(403);
    }

    public function test_user_can_fork_public_template_of_others(): void
    {
        $template = Template::factory()->create(['created_by' => $this->owner->id, 'is_public' => true]);
        // A version is required to fork
        TemplateVersion::factory()->create(['template_id' => $template->id]);

        $this->actingAs($this->imposter)
            ->postJson("/api/templates/{$template->id}/fork")
            ->assertStatus(201);
    }
} 