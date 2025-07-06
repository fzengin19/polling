<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Survey;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;
    protected Survey $survey;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles with the correct guard
        Role::findOrCreate('admin', 'api');
        Role::findOrCreate('editor', 'api');
        Role::findOrCreate('viewer', 'api');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        
        $this->user = User::factory()->create();
        $this->survey = Survey::factory()->create();
    }

    public function test_can_get_all_roles(): void
    {
        $response = $this->actingAs($this->admin)->getJson('/api/roles');
        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure(['success', 'message', 'data' => ['roles']]);
    }

    public function test_can_assign_role_to_user(): void
    {
        $data = ['model_type' => 'user', 'model_id' => $this->user->id, 'role_name' => 'editor'];
        $this->actingAs($this->admin)->postJson('/api/roles/assign', $data)
            ->assertOk()
            ->assertJson(['success' => true, 'message' => 'Role assigned successfully.']);
        $this->assertTrue($this->user->hasRole('editor'));
    }

    public function test_can_assign_role_to_survey(): void
    {
        $data = ['model_type' => 'survey', 'model_id' => $this->survey->id, 'role_name' => 'viewer'];
        $this->actingAs($this->admin)->postJson('/api/roles/assign', $data)
            ->assertOk()
            ->assertJson(['success' => true, 'message' => 'Role assigned successfully.']);
        $this->assertTrue($this->survey->hasRole('viewer'));
    }

    public function test_can_remove_role_from_user(): void
    {
        $this->user->assignRole('editor');
        $this->assertTrue($this->user->hasRole('editor'));
        
        $data = ['model_type' => 'user', 'model_id' => $this->user->id, 'role_name' => 'editor'];
        $this->actingAs($this->admin)->postJson('/api/roles/remove', $data)
            ->assertOk()
            ->assertJsonPath('message', 'Role removed successfully.');
        
        $this->assertFalse($this->user->fresh()->hasRole('editor'));
    }

    public function test_can_remove_role_from_survey(): void
    {
        $this->survey->assignRole('viewer');
        $this->assertTrue($this->survey->hasRole('viewer'));
        
        $data = ['model_type' => 'survey', 'model_id' => $this->survey->id, 'role_name' => 'viewer'];
        $this->actingAs($this->admin)->postJson('/api/roles/remove', $data)
            ->assertOk()
            ->assertJsonPath('message', 'Role removed successfully.');
        
        $this->assertFalse($this->survey->fresh()->hasRole('viewer'));
    }

    public function test_can_get_user_roles(): void
    {
        $this->user->assignRole(['editor', 'viewer']);
        
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/roles/users/{$this->user->id}");
        
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message', 'data' => ['roles']]);
        $this->assertCount(2, $response->json('data.roles'));
    }

    public function test_can_get_survey_roles(): void
    {
        $this->survey->assignRole(['admin', 'viewer']);
        
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/roles/surveys/{$this->survey->id}");
        
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message', 'data' => ['roles']]);
        $this->assertCount(2, $response->json('data.roles'));
    }

    public function test_can_check_user_has_role(): void
    {
        $this->user->assignRole('editor');
        
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/roles/users/{$this->user->id}/has/editor");
        
        $response->assertStatus(200);
        $response->assertJsonPath('data.has_role', true);
    }

    public function test_can_check_user_does_not_have_role(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/roles/users/{$this->user->id}/has/admin");
        
        $response->assertStatus(200);
        $response->assertJsonPath('data.has_role', false);
    }

    public function test_can_check_survey_has_role(): void
    {
        $this->survey->assignRole('viewer');
        
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/roles/surveys/{$this->survey->id}/has/viewer");
        
        $response->assertStatus(200);
        $response->assertJsonPath('data.has_role', true);
    }

    public function test_can_check_survey_does_not_have_role(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/roles/surveys/{$this->survey->id}/has/admin");
        
        $response->assertStatus(200);
        $response->assertJsonPath('data.has_role', false);
    }

    public function test_assign_role_requires_authentication(): void
    {
        $data = [
            'role_name' => 'editor',
            'model_type' => 'user',
            'model_id' => $this->user->id,
        ];

        $response = $this->postJson('/api/roles/assign', $data);
        $response->assertStatus(401);
    }

    public function test_assign_role_validates_role_exists(): void
    {
        $data = [
            'role_name' => 'nonexistent-role',
            'model_type' => 'user',
            'model_id' => $this->user->id,
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/roles/assign', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('role_name', 'data');
    }

    public function test_assign_role_validates_model_exists(): void
    {
        $data = ['model_type' => 'user', 'model_id' => 999, 'role_name' => 'editor'];
        $this->actingAs($this->admin)->postJson('/api/roles/assign', $data)
            ->assertNotFound();
    }

    public function test_returns_404_for_nonexistent_user(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/roles/users/99999');
        
        $response->assertStatus(404);
        $response->assertJson(['success' => false, 'message' => 'User not found.']);
    }

    public function test_returns_404_for_nonexistent_survey(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/roles/surveys/99999');
        
        $response->assertStatus(404);
    }
} 