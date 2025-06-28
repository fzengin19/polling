<?php

namespace Tests\Feature;

use App\Models\Choice;
use App\Models\Question;
use App\Models\Survey;
use App\Models\SurveyPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EnhancedMediaSystemTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Survey $survey;
    private SurveyPage $surveyPage;
    private Question $question;
    private Choice $choice;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
        
        $this->user = User::factory()->create();
        $this->survey = Survey::factory()->create(['created_by' => $this->user->id]);
        $this->surveyPage = SurveyPage::factory()->create(['survey_id' => $this->survey->id]);
        $this->question = Question::factory()->create(['page_id' => $this->surveyPage->id]);
        $this->choice = Choice::factory()->create(['question_id' => $this->question->id]);
    }

    public function test_can_upload_media_to_survey()
    {
        $file = UploadedFile::fake()->image('banner.jpg');

        $response = $this->actingAs($this->user)
            ->postJson("/api/enhanced-media/survey/{$this->survey->id}/upload", [
                'file' => $file,
                'collection' => 'survey-banners'
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'file_name',
                'disk',
                'conversions_disk',
                'collection_name',
                'mime_type',
                'size',
                'custom_properties',
                'generated_conversions',
                'responsive_images',
                'manipulations',
                'model_id',
                'model_type',
                'uuid',
                'order_column',
                'updated_at',
                'created_at',
                'original_url',
                'preview_url'
            ]);

        $this->assertDatabaseHas('media', [
            'model_type' => Survey::class,
            'model_id' => $this->survey->id,
            'collection_name' => 'survey-banners'
        ]);
    }

    public function test_can_upload_media_to_survey_page()
    {
        $file = UploadedFile::fake()->image('background.jpg');

        $response = $this->actingAs($this->user)
            ->postJson("/api/enhanced-media/survey-page/{$this->surveyPage->id}/upload", [
                'file' => $file,
                'collection' => 'page-backgrounds'
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('media', [
            'model_type' => SurveyPage::class,
            'model_id' => $this->surveyPage->id,
            'collection_name' => 'page-backgrounds'
        ]);
    }

    public function test_can_upload_media_to_question()
    {
        $file = UploadedFile::fake()->image('question.jpg');

        $response = $this->actingAs($this->user)
            ->postJson("/api/enhanced-media/question/{$this->question->id}/upload", [
                'file' => $file,
                'collection' => 'question-images'
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('media', [
            'model_type' => Question::class,
            'model_id' => $this->question->id,
            'collection_name' => 'question-images'
        ]);
    }

    public function test_can_upload_media_to_choice()
    {
        $file = UploadedFile::fake()->image('choice.png');

        $response = $this->actingAs($this->user)
            ->postJson("/api/enhanced-media/choice/{$this->choice->id}/upload", [
                'file' => $file,
                'collection' => 'choice-icons'
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('media', [
            'model_type' => Choice::class,
            'model_id' => $this->choice->id,
            'collection_name' => 'choice-icons'
        ]);
    }

    public function test_can_get_media_for_survey()
    {
        // Upload media first
        $file = UploadedFile::fake()->image('banner.jpg');
        $this->survey->addMedia($file)->toMediaCollection('survey-banners');

        $response = $this->actingAs($this->user)
            ->getJson("/api/enhanced-media/survey/{$this->survey->id}/media?collection=survey-banners");

        // Debug: dump response to see actual format
        dump($response->json());

        $response->assertStatus(200);
    }

    public function test_can_get_media_for_survey_page()
    {
        // Upload media first
        $file = UploadedFile::fake()->image('background.jpg');
        $this->surveyPage->addMedia($file)->toMediaCollection('page-backgrounds');

        $response = $this->actingAs($this->user)
            ->getJson("/api/enhanced-media/survey-page/{$this->surveyPage->id}/media?collection=page-backgrounds");

        $response->assertStatus(200);
    }

    public function test_can_get_media_for_question()
    {
        // Upload media first
        $file = UploadedFile::fake()->image('question.jpg');
        $this->question->addMedia($file)->toMediaCollection('question-images');

        $response = $this->actingAs($this->user)
            ->getJson("/api/enhanced-media/question/{$this->question->id}/media?collection=question-images");

        $response->assertStatus(200);
    }

    public function test_can_get_media_for_choice()
    {
        // Upload media first
        $file = UploadedFile::fake()->image('choice.png');
        $this->choice->addMedia($file)->toMediaCollection('choice-icons');

        $response = $this->actingAs($this->user)
            ->getJson("/api/enhanced-media/choice/{$this->choice->id}/media?collection=choice-icons");

        $response->assertStatus(200);
    }

    public function test_can_update_media_metadata()
    {
        // Upload media first
        $file = UploadedFile::fake()->image('banner.jpg');
        $media = $this->survey->addMedia($file)->toMediaCollection('survey-banners');

        $response = $this->actingAs($this->user)
            ->putJson("/api/enhanced-media/{$media->id}/metadata", [
                'alt_text' => 'Survey Banner',
                'caption' => 'This is a survey banner',
                'display_order' => 1
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'custom_properties'
            ]);

        $this->assertDatabaseHas('media', [
            'id' => $media->id,
            'custom_properties->alt_text' => 'Survey Banner',
            'custom_properties->caption' => 'This is a survey banner',
            'custom_properties->display_order' => 1
        ]);
    }

    public function test_can_delete_media()
    {
        // Upload media first
        $file = UploadedFile::fake()->image('banner.jpg');
        $media = $this->survey->addMedia($file)->toMediaCollection('survey-banners');

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/enhanced-media/{$media->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Media deleted successfully']);

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }

    public function test_can_get_collections_for_survey()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/enhanced-media/survey/collections');

        $response->assertStatus(200)
            ->assertJson(['survey-banners', 'survey-logos', 'survey-attachments']);
    }

    public function test_can_get_collections_for_survey_page()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/enhanced-media/survey-page/collections');

        $response->assertStatus(200)
            ->assertJson(['page-images', 'page-backgrounds']);
    }

    public function test_can_get_collections_for_question()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/enhanced-media/question/collections');

        $response->assertStatus(200)
            ->assertJson(['question-images', 'question-videos', 'question-documents']);
    }

    public function test_can_get_collections_for_choice()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/enhanced-media/choice/collections');

        $response->assertStatus(200)
            ->assertJson(['choice-images', 'choice-icons']);
    }

    public function test_returns_404_for_invalid_model()
    {
        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->actingAs($this->user)
            ->postJson('/api/enhanced-media/invalid/999/upload', [
                'file' => $file,
                'collection' => 'test'
            ]);

        $response->assertStatus(404)
            ->assertJson(['message' => 'Model not found']);
    }

    public function test_returns_400_when_no_file_provided()
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/enhanced-media/survey/{$this->survey->id}/upload", [
                'collection' => 'survey-banners'
            ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'No file provided']);
    }

    public function test_requires_authentication()
    {
        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->postJson("/api/enhanced-media/survey/{$this->survey->id}/upload", [
            'file' => $file,
            'collection' => 'survey-banners'
        ]);

        $response->assertStatus(401);
    }

    public function test_media_collections_are_specific_to_models()
    {
        // Test that each model has its own media collections
        $surveyFile = UploadedFile::fake()->image('survey.jpg');
        $questionFile = UploadedFile::fake()->image('question.jpg');
        $choiceFile = UploadedFile::fake()->image('choice.png');

        // Upload to different models
        $this->survey->addMedia($surveyFile)->toMediaCollection('survey-banners');
        $this->question->addMedia($questionFile)->toMediaCollection('question-images');
        $this->choice->addMedia($choiceFile)->toMediaCollection('choice-icons');

        // Verify they are stored separately
        $this->assertCount(1, $this->survey->getMedia('survey-banners'));
        $this->assertCount(1, $this->question->getMedia('question-images'));
        $this->assertCount(1, $this->choice->getMedia('choice-icons'));

        // Verify they don't interfere with each other
        $this->assertCount(0, $this->survey->getMedia('question-images'));
        $this->assertCount(0, $this->question->getMedia('survey-banners'));
    }
} 