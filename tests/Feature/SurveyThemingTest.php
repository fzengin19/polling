<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Survey;
use App\Models\Media;

class SurveyThemingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'sanctum');
    }

    private function createSurveyWithTheme(array $themeData)
    {
        return $this->postJson('/api/surveys', [
            'title' => 'Themed Survey',
            'status' => 'draft',
            'settings' => ['theme' => $themeData]
        ]);
    }

    public function test_it_rejects_invalid_hex_color()
    {
        $this->createSurveyWithTheme(['primary_color' => 'not-a-hex'])
             ->assertStatus(422)
             ->assertJsonStructure(['data' => ['settings.theme.primary_color']]);
    }

    public function test_it_rejects_invalid_font()
    {
        $this->createSurveyWithTheme(['font' => 'Comic Sans'])
             ->assertStatus(422)
             ->assertJsonStructure(['data' => ['settings.theme.font']]);
    }

    public function test_it_rejects_invalid_logo_media_id()
    {
        $this->createSurveyWithTheme(['logo_media_id' => 99999])
             ->assertStatus(422)
             ->assertJsonStructure(['data' => ['settings.theme.logo_media_id']]);
    }

    public function test_it_rejects_invalid_logo_placement()
    {
        $this->createSurveyWithTheme(['logo_placement' => 'middle-center'])
             ->assertStatus(422)
             ->assertJsonStructure(['data' => ['settings.theme.logo_placement']]);
    }

    public function test_it_accepts_and_saves_valid_theme_data_without_logo()
    {
        $themeData = [
            'primary_color' => '#10B981',
            'font' => 'Lato',
            'logo_placement' => 'top',
            'background_color' => '#FFFFFF'
        ];

        $response = $this->createSurveyWithTheme($themeData);
        
        $response->assertCreated();

        $survey = Survey::find($response->json('data.id'));
        $this->assertEquals($themeData, $survey->settings['theme']);
    }

    public function test_it_accepts_valid_logo_media_id()
    {
        // Önce bir medya oluştur
        $survey = Survey::factory()->create(['created_by' => $this->user->id]);
        
        // Gerçek PNG dosyası oluştur
        $pngData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
        
        $media = $survey->addMediaFromString($pngData)
            ->usingName('test-logo')
            ->usingFileName('test-logo.png')
            ->withCustomProperties(['mime_type' => 'image/png'])
            ->toMediaCollection('survey-logos');

        $themeData = [
            'primary_color' => '#10B981',
            'font' => 'Lato',
            'logo_media_id' => $media->id,
            'logo_placement' => 'top',
            'background_color' => '#FFFFFF'
        ];

        $response = $this->createSurveyWithTheme($themeData);
        
        $response->assertCreated();

        $newSurvey = Survey::find($response->json('data.id'));
        $this->assertEquals($themeData, $newSurvey->settings['theme']);
    }

    public function test_it_returns_logo_url_in_api_response()
    {
        // Önce bir medya oluştur
        $survey = Survey::factory()->create(['created_by' => $this->user->id]);
        
        // Gerçek PNG dosyası oluştur
        $pngData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
        
        $media = $survey->addMediaFromString($pngData)
            ->usingName('test-logo')
            ->usingFileName('test-logo.png')
            ->withCustomProperties(['mime_type' => 'image/png'])
            ->toMediaCollection('survey-logos');

        $themeData = [
            'primary_color' => '#10B981',
            'logo_media_id' => $media->id,
            'logo_placement' => 'top'
        ];

        $survey->update(['settings' => ['theme' => $themeData]]);

        $response = $this->getJson("/api/surveys/{$survey->id}");
        
        $response->assertOk()
                 ->assertJsonStructure(['data' => ['settings' => ['theme' => ['logo_url']]]]);
        
        $this->assertNotNull($response->json('data.settings.theme.logo_url'));
    }
}
