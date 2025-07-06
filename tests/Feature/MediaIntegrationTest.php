<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Question;
use App\Models\Media;

class MediaIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Question $question;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->question = Question::factory()->create();
        $this->actingAs($this->user, 'sanctum');
    }

    public function test_media_reference_is_added_to_question_config_on_upload()
    {
        Storage::fake('media');

        $file = UploadedFile::fake()->image('test_image.jpg');

        $response = $this->postJson('/api/media/upload', [
            'file' => $file,
            'model_type' => 'question',
            'model_id' => $this->question->id,
            'collection_name' => 'question_images'
        ]);

        $response->assertStatus(201);
        $mediaId = $response->json('data.id');

        $this->question->refresh();

        $this->assertArrayHasKey('media_references', $this->question->config);
        $this->assertCount(1, $this->question->config['media_references']);
        $this->assertEquals($mediaId, $this->question->config['media_references'][0]['media_id']);
    }

    public function test_media_reference_is_removed_from_question_config_on_delete()
    {
        Storage::fake('media');
        $file = UploadedFile::fake()->image('test_image.jpg');

        // 1. Upload media
        $uploadResponse = $this->postJson('/api/media/upload', [
            'file' => $file,
            'model_type' => 'question',
            'model_id' => $this->question->id,
            'collection_name' => 'question_images'
        ]);
        $mediaId = $uploadResponse->json('data.id');
        $this->question->refresh();
        
        // Verify it was added
        $this->assertArrayHasKey('media_references', $this->question->config);
        $this->assertCount(1, $this->question->config['media_references']);
        
        // 2. Delete media
        $deleteResponse = $this->deleteJson("/api/media/{$mediaId}");
        $deleteResponse->assertNoContent();

        $this->question->refresh();
        
        // 3. Verify it was removed
        $this->assertArrayHasKey('media_references', $this->question->config);
        $this->assertCount(0, $this->question->config['media_references']);
    }
} 