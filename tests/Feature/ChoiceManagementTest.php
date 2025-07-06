<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Survey;
use App\Models\SurveyPage;
use App\Models\Question;
use App\Models\Choice;
use App\Models\TemplateVersion;

class ChoiceManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Question $question;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $survey = Survey::factory()->create(['created_by' => $this->user->id]);
        $page = SurveyPage::factory()->create(['survey_id' => $survey->id]);
        $this->question = Question::factory()->create(['page_id' => $page->id, 'type' => 'multiple_choice']);
    }

    public function test_can_create_choice(): void
    {
        $data = ['label' => 'New Choice', 'value' => 'new_choice'];
        $this->actingAs($this->user)->postJson("/api/questions/{$this->question->id}/choices", $data)
            ->assertCreated()
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data' => ['id', 'label', 'value']]);
    }

    public function test_can_update_choice(): void
    {
        $choice = Choice::factory()->create(['question_id' => $this->question->id]);
        $data = ['label' => 'Updated Label', 'value' => $choice->value];
        $this->actingAs($this->user)->putJson("/api/choices/{$choice->id}", $data)
            ->assertOk()
            ->assertJsonPath('data.label', 'Updated Label');
    }

    public function test_can_delete_choice(): void
    {
        $choice = Choice::factory()->create(['question_id' => $this->question->id]);
        $this->actingAs($this->user)->deleteJson("/api/choices/{$choice->id}")
            ->assertNoContent();
        $this->assertDatabaseMissing('choices', ['id' => $choice->id]);
    }

    public function test_can_get_choices_by_question(): void
    {
        Choice::factory()->count(3)->create(['question_id' => $this->question->id]);
        $this->actingAs($this->user)->getJson("/api/questions/{$this->question->id}/choices")
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_can_reorder_choices(): void
    {
        $choice1 = Choice::factory()->create(['question_id' => $this->question->id, 'order_index' => 0]);
        $choice2 = Choice::factory()->create(['question_id' => $this->question->id, 'order_index' => 1]);
        $data = ['choice_ids' => [$choice2->id, $choice1->id]];
        $this->actingAs($this->user)->postJson("/api/questions/{$this->question->id}/choices/reorder", $data)
            ->assertOk()
            ->assertJson(['success' => true]);
    }
} 