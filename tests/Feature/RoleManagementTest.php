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
    protected Role $adminRole;
    protected Role $editorRole;
    protected Role $viewerRole;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        $this->adminRole = Role::create(['name' => 'admin', 'guard_name' => 'api']);
        $this->editorRole = Role::create(['name' => 'editor', 'guard_name' => 'api']);
        $this->viewerRole = Role::create(['name' => 'viewer', 'guard_name' => 'api']);
        
        // Create users
        $this->user = User::factory()->create();
        $this->admin = User::factory()->create();
        $this->admin->assignRole($this->adminRole);
        
        // Create survey
        $this->survey = Survey::factory()->create(['created_by' => $this->user->id]);
    }

    public function test_can_get_all_roles(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/roles');
        
        $response->assertStatus(200);
        $response->assertJsonStructure(['roles']);
        $this->assertCount(3, $response->json('roles'));
    }

    public function test_can_assign_role_to_user(): void
    {
        $data = [
            'role_name' => 'editor',
            'user_id' => $this->user->id,
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/roles/assign', $data);
        
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Role assigned successfully']);
        
        $this->assertTrue($this->user->hasRole('editor'));
    }

    public function test_can_assign_role_to_survey(): void
    {
        $data = [
            'role_name' => 'viewer',
            'survey_id' => $this->survey->id,
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/roles/assign', $data);
        
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Role assigned successfully']);
        
        $this->assertTrue($this->survey->hasRole('viewer'));
    }

    public function test_can_remove_role_from_user(): void
    {
        $this->user->assignRole('editor');
        
        $data = [
            'role_name' => 'editor',
            'user_id' => $this->user->id,
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/roles/remove', $data);
        
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Role removed successfully']);
        
        $this->assertFalse($this->user->hasRole('editor'));
    }

    public function test_can_remove_role_from_survey(): void
    {
        $this->survey->assignRole('viewer');
        
        $data = [
            'role_name' => 'viewer',
            'survey_id' => $this->survey->id,
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/roles/remove', $data);
        
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Role removed successfully']);
        
        $this->assertFalse($this->survey->hasRole('viewer'));
    }

    public function test_can_get_user_roles(): void
    {
        $this->user->assignRole(['editor', 'viewer']);
        
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/roles/users/{$this->user->id}");
        
        $response->assertStatus(200);
        $response->assertJsonStructure(['roles']);
        $this->assertCount(2, $response->json('roles'));
    }

    public function test_can_get_survey_roles(): void
    {
        $this->survey->assignRole(['admin', 'viewer']);
        
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/roles/surveys/{$this->survey->id}");
        
        $response->assertStatus(200);
        $response->assertJsonStructure(['roles']);
        $this->assertCount(2, $response->json('roles'));
    }

    public function test_can_check_user_has_role(): void
    {
        $this->user->assignRole('editor');
        
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/roles/users/{$this->user->id}/has/editor");
        
        $response->assertStatus(200);
        $response->assertJson(['has_role' => true]);
    }

    public function test_can_check_user_does_not_have_role(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/roles/users/{$this->user->id}/has/admin");
        
        $response->assertStatus(200);
        $response->assertJson(['has_role' => false]);
    }

    public function test_can_check_survey_has_role(): void
    {
        $this->survey->assignRole('viewer');
        
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/roles/surveys/{$this->survey->id}/has/viewer");
        
        $response->assertStatus(200);
        $response->assertJson(['has_role' => true]);
    }

    public function test_can_check_survey_does_not_have_role(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/roles/surveys/{$this->survey->id}/has/admin");
        
        $response->assertStatus(200);
        $response->assertJson(['has_role' => false]);
    }

    public function test_assign_role_requires_authentication(): void
    {
        $data = [
            'role_name' => 'editor',
            'user_id' => $this->user->id,
        ];

        $response = $this->postJson('/api/roles/assign', $data);
        $response->assertStatus(401);
    }

    public function test_assign_role_validates_role_exists(): void
    {
        $data = [
            'role_name' => 'non-existent-role',
            'user_id' => $this->user->id,
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/roles/assign', $data);
        
        $response->assertStatus(422);
    }

    public function test_assign_role_validates_user_exists(): void
    {
        $data = [
            'role_name' => 'editor',
            'user_id' => 99999,
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/roles/assign', $data);
        
        $response->assertStatus(422);
    }

    public function test_assign_role_validates_survey_exists(): void
    {
        $data = [
            'role_name' => 'viewer',
            'survey_id' => 99999,
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/roles/assign', $data);
        
        $response->assertStatus(422);
    }

    public function test_returns_404_for_nonexistent_user(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/roles/users/99999');
        
        $response->assertStatus(404);
    }

    public function test_returns_404_for_nonexistent_survey(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/roles/surveys/99999');
        
        $response->assertStatus(404);
    }
} 