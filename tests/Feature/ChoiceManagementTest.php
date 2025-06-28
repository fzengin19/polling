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
            'question_id' => $this->question->id,
            'label' => 'Red',
            'value' => 'red',
            'order_index' => 0,
        ];

        $response = $this->postJson('/api/choices', $data);
        
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id', 'question_id', 'label', 'value', 'order_index', 'created_at', 'updated_at'
        ]);
        $response->assertJson([
            'label' => 'Red',
            'value' => 'red',
            'order_index' => 0,
        ]);

        $this->assertDatabaseHas('choices', [
            'question_id' => $this->question->id,
            'label' => 'Red',
            'value' => 'red',
        ]);
    }

    public function test_can_update_choice(): void
    {
        $this->actingAs($this->user, 'sanctum');
        
        $choice = Choice::factory()->create([
            'question_id' => $this->question->id,
            'label' => 'Old Label',
            'value' => 'old_value',
        ]);

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
            'value' => 'updated_value',
        ]);
    }

    public function test_can_delete_choice(): void
    {
        $this->actingAs($this->user, 'sanctum');
        
        $choice = Choice::factory()->create(['question_id' => $this->question->id]);

        $response = $this->deleteJson("/api/choices/{$choice->id}");
        
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Choice deleted successfully']);

        $this->assertDatabaseMissing('choices', ['id' => $choice->id]);
    }

    public function test_can_get_choices_by_question(): void
    {
        $this->actingAs($this->user, 'sanctum');
        
        Choice::factory()->count(3)->create(['question_id' => $this->question->id]);

        $response = $this->getJson("/api/questions/{$this->question->id}/choices");
        
        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    public function test_can_reorder_choices(): void
    {
        $this->actingAs($this->user, 'sanctum');
        
        $choice1 = Choice::factory()->create([
            'question_id' => $this->question->id,
            'order_index' => 0,
        ]);
        $choice2 = Choice::factory()->create([
            'question_id' => $this->question->id,
            'order_index' => 1,
        ]);

        $data = [
            'choices' => [
                ['id' => $choice2->id, 'order_index' => 0],
                ['id' => $choice1->id, 'order_index' => 1],
            ]
        ];

        $response = $this->postJson("/api/questions/{$this->question->id}/choices/reorder", $data);
        
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Choices reordered successfully']);

        $this->assertDatabaseHas('choices', [
            'id' => $choice2->id,
            'order_index' => 0,
        ]);
        $this->assertDatabaseHas('choices', [
            'id' => $choice1->id,
            'order_index' => 1,
        ]);
    }

    public function test_choice_validation_rules(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Test required fields
        $response = $this->postJson('/api/choices', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['question_id', 'label', 'value']);

        // Test invalid question_id
        $response = $this->postJson('/api/choices', [
            'question_id' => 0,
            'label' => 'Test',
            'value' => 'test',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['question_id']);

        // Test label max length
        $response = $this->postJson('/api/choices', [
            'question_id' => $this->question->id,
            'label' => str_repeat('a', 256),
            'value' => 'test',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['label']);

        // Test value max length
        $response = $this->postJson('/api/choices', [
            'question_id' => $this->question->id,
            'label' => 'Test',
            'value' => str_repeat('a', 256),
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['value']);

        // Test order_index min value
        $response = $this->postJson('/api/choices', [
            'question_id' => $this->question->id,
            'label' => 'Test',
            'value' => 'test',
            'order_index' => -1,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['order_index']);
    }

    public function test_choice_authorization(): void
    {
        $otherUser = User::factory()->create();
        $otherQuestion = Question::factory()->create([
            'page_id' => SurveyPage::factory()->create([
                'survey_id' => Survey::factory()->create(['created_by' => $otherUser->id])->id
            ])->id,
            'type' => 'multiple_choice'
        ]);

        $this->actingAs($this->user, 'sanctum');

        // Try to create choice for other user's question
        $data = [
            'question_id' => $otherQuestion->id,
            'label' => 'Test',
            'value' => 'test',
        ];

        $response = $this->postJson('/api/choices', $data);
        $response->assertStatus(403);
    }

    public function test_choice_not_found(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->getJson('/api/choices/999999');
        $response->assertStatus(404);
    }

    public function test_choice_belongs_to_multiple_choice_question(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Create a text question
        $textQuestion = Question::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'text'
        ]);

        $data = [
            'question_id' => $textQuestion->id,
            'label' => 'Test',
            'value' => 'test',
        ];

        $response = $this->postJson('/api/choices', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['question_id']);
    }
} 