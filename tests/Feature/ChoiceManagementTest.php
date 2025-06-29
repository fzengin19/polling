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

class ChoiceManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Survey $survey;
    private SurveyPage $page;
    private Question $question;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $template = Template::factory()->create(['created_by' => $this->user->id]);
        $this->survey = Survey::factory()->create([
            'created_by' => $this->user->id,
            'template_id' => $template->id
        ]);
        $this->page = SurveyPage::factory()->create(['survey_id' => $this->survey->id]);
        $this->question = Question::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'multiple_choice'
        ]);
    }

    public function test_can_create_choice(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $data = [
            'label' => 'New Choice Label',
            'value' => 'new_choice_value',
        ];

        $response = $this->postJson("/api/questions/{$this->question->id}/choices", $data);
        
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id', 'question_id', 'label', 'value', 'order_index', 'created_at', 'updated_at'
        ]);
        $response->assertJson([
            'label' => 'New Choice Label',
            'value' => 'new_choice_value'
        ]);

        $this->assertDatabaseHas('choices', [
            'question_id' => $this->question->id,
            'label' => 'New Choice Label',
            'value' => 'new_choice_value',
        ]);
    }

    public function test_can_update_choice(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $choice = Choice::factory()->create(['question_id' => $this->question->id]);
        $data = [
            'label' => 'Updated Label',
            'value' => 'updated_value',
            'order_index' => 1,
        ];

        $response = $this->putJson("/api/choices/{$choice->id}", $data);
        
        $response->assertStatus(200);
        $response->assertJson([
            'label' => 'Updated Label',
            'value' => 'updated_value',
            'order_index' => 1,
        ]);

        $this->assertDatabaseHas('choices', [
            'id' => $choice->id,
            'label' => 'Updated Label',
        ]);
    }

    public function test_can_delete_choice(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $choice = Choice::factory()->create(['question_id' => $this->question->id]);

        $response = $this->deleteJson("/api/choices/{$choice->id}");
        
        $response->assertStatus(204);

        $this->assertDatabaseMissing('choices', ['id' => $choice->id]);
    }

    public function test_can_get_choices_by_question(): void
    {
        $this->actingAs($this->user, 'sanctum');
        
        Choice::factory()->count(3)->create(['question_id' => $this->question->id]);

        $response = $this->getJson("/api/questions/{$this->question->id}/choices");
        
        $response->assertStatus(200);
        $this->assertCount(3, $response->json());
    }

    public function test_can_reorder_choices(): void
    {
        $this->actingAs($this->user, 'sanctum');
        
        $choice1 = Choice::factory()->create(['question_id' => $this->question->id, 'order_index' => 0]);
        $choice2 = Choice::factory()->create(['question_id' => $this->question->id, 'order_index' => 1]);

        $data = [
            'choices' => [$choice2->id, $choice1->id]
        ];

        $response = $this->postJson("/api/questions/{$this->question->id}/choices/reorder", $data);
        
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Choices reordered successfully.']);

        $this->assertDatabaseHas('choices', [
            'id' => $choice1->id,
            'order_index' => 1
        ]);
        $this->assertDatabaseHas('choices', [
            'id' => $choice2->id,
            'order_index' => 0
        ]);
    }

    public function test_choice_validation_rules(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Test required fields
        $response = $this->postJson("/api/questions/{$this->question->id}/choices", []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['label', 'value']);

        // Test invalid value for order_index (must be an integer)
        $response = $this->postJson("/api/questions/{$this->question->id}/choices", [
            'label' => 'Test',
            'value' => 'test',
            'order_index' => 'not-an-integer',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['order_index']);
    }

    public function test_choice_authorization(): void
    {
        // Create a survey belonging to another user
        $otherUser = User::factory()->create();
        $otherSurvey = Survey::factory()->create(['created_by' => $otherUser->id]);
        $otherPage = SurveyPage::factory()->create(['survey_id' => $otherSurvey->id]);
        $otherQuestion = Question::factory()->create(['page_id' => $otherPage->id, 'type' => 'multiple_choice']);

        $this->actingAs($this->user, 'sanctum');

        $data = [
            'label' => 'Test',
            'value' => 'test',
        ];

        // Try to create choice on another user's question
        $response = $this->postJson("/api/questions/{$otherQuestion->id}/choices", $data);
        $response->assertStatus(403);

        // Try to update another user's choice
        $choice = Choice::factory()->create(['question_id' => $otherQuestion->id]);
        $response = $this->putJson("/api/choices/{$choice->id}", ['label' => 'updated']);
        $response->assertStatus(403);

        // Try to delete another user's choice
        $response = $this->deleteJson("/api/choices/{$choice->id}");
        $response->assertStatus(403);
    }

    public function test_choice_not_found(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $response = $this->getJson('/api/choices/9999');
        $response->assertStatus(404);
    }

    public function test_choice_belongs_to_multiple_choice_question(): void
    {
        $this->actingAs($this->user, 'sanctum');
        $textQuestion = Question::factory()->create(['page_id' => $this->page->id, 'type' => 'text']);
        
        $data = [
            'label' => 'Test',
            'value' => 'test',
        ];

        $response = $this->postJson("/api/questions/{$textQuestion->id}/choices", $data);
        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Choices can only be added to multiple_choice questions.']);
    }
} 