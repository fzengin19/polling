<?php

namespace Tests\Feature;

use App\Models\Poll;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PollApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Testler için kullanıcı yaratıyoruz, çünkü user_id zorunlu
        $this->user = User::factory()->create();
    }

    public function test_index_returns_paginated_polls()
    {
        Poll::factory()->count(20)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/polls');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'total',
                'per_page',
                'current_page',
                'last_page',
                'from',
                'to',
            ])
            ->assertJsonCount(15, 'data');
    }

    public function test_store_creates_poll()
    {
        $payload = [
            'title' => 'Yeni Anket',
            'user_id' => $this->user->id,
            'max_votes_per_user' => 3,
            'starts_at' => now()->toDateTimeString(),
            'ends_at' => now()->addDay()->toDateTimeString(),
            'is_public' => true,
            'anon_id' => 'anon123',
        ];

        $response = $this->postJson('/api/polls', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'Yeni Anket',
                'user_id' => $this->user->id,
                'max_votes_per_user' => 3,
                'is_public' => true,
                'anon_id' => 'anon123',
            ]);

        $this->assertDatabaseHas('polls', [
            'title' => 'Yeni Anket',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_show_returns_single_poll()
    {
        $poll = Poll::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/polls/{$poll->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $poll->id,
                'title' => $poll->title,
                'user_id' => $this->user->id,
            ]);
    }

    public function test_update_modifies_poll()
    {
        $poll = Poll::factory()->create(['user_id' => $this->user->id]);

        $updatePayload = [
            'title' => 'Güncellenmiş Anket',
            'max_votes_per_user' => 5,
            'is_public' => false,
        ];

        $response = $this->putJson("/api/polls/{$poll->id}", $updatePayload);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $poll->id,
                'title' => 'Güncellenmiş Anket',
                'max_votes_per_user' => 5,
                'is_public' => false,
            ]);

        $this->assertDatabaseHas('polls', [
            'id' => $poll->id,
            'title' => 'Güncellenmiş Anket',
            'max_votes_per_user' => 5,
            'is_public' => false,
        ]);
    }

    public function test_destroy_deletes_poll()
    {
        $poll = Poll::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/polls/{$poll->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('polls', [
            'id' => $poll->id,
        ]);
    }
}
