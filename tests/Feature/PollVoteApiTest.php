<?php

namespace Tests\Feature;

use App\Models\Option;
use App\Models\Poll;
use App\Models\PollVote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PollVoteApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Poll $poll;
    protected Option $option;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->poll = Poll::factory()->create(['user_id' => $this->user->id]);
        $this->option = Option::factory()->create(['option_id' => $this->option->id]);
    }

    public function test_index_returns_paginated_options()
    {
        Option::factory()->count(20)->create(['poll_id' => $this->poll->id]);

        $response = $this->getJson('/api/options');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data',
                     'total',
                     'per_page',
                     'current_page',
                 ])
                 ->assertJsonCount(15, 'data'); // paginate(15) varsayılan
    }

    public function test_store_creates_option()
    {
        $payload = [
            'poll_id' => $this->poll->id,
            'type' => 'text',
            'value' => 'Seçenek 1',
            'votes_count' => 0,
            'order' => 1,
        ];

        $response = $this->postJson('/api/options', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'poll_id' => $this->poll->id,
                     'type' => 'text',
                     'value' => 'Seçenek 1',
                     'votes_count' => 0,
                     'order' => 1,
                 ]);

        $this->assertDatabaseHas('options', [
            'poll_id' => $this->poll->id,
            'value' => 'Seçenek 1',
        ]);
    }

    public function test_show_returns_single_option()
    {
        $option = Option::factory()->create(['poll_id' => $this->poll->id]);

        $response = $this->getJson("/api/options/{$option->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $option->id,
                     'poll_id' => $this->poll->id,
                     'value' => $option->value,
                 ]);
    }

    public function test_update_modifies_option()
    {
        $option = Option::factory()->create(['poll_id' => $this->poll->id]);

        $updatePayload = [
            'value' => 'Güncellenmiş Seçenek',
            'order' => 2,
        ];

        $response = $this->putJson("/api/options/{$option->id}", $updatePayload);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $option->id,
                     'value' => 'Güncellenmiş Seçenek',
                     'order' => 2,
                 ]);

        $this->assertDatabaseHas('options', [
            'id' => $option->id,
            'value' => 'Güncellenmiş Seçenek',
            'order' => 2,
        ]);
    }

    public function test_destroy_deletes_option()
    {
        $option = Option::factory()->create(['poll_id' => $this->poll->id]);

        $response = $this->deleteJson("/api/options/{$option->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('options', [
            'id' => $option->id,
        ]);
    }
}
