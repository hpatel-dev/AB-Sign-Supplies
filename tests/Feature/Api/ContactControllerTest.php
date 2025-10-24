<?php

namespace Tests\Feature\Api;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_persists_contact_message_and_returns_confirmation(): void
    {
        $payload = [
            'name' => 'Jordan Smith',
            'email' => 'jordan@example.com',
            'phone' => '+1 (800) 123-4567',
            'product' => 'Outdoor LED Display',
            'message' => 'Looking for pricing information.',
        ];

        $response = $this->postJson('/api/contact', $payload);

        $response->assertCreated()
            ->assertJsonPath('message', 'Thank you for reaching out. We will respond shortly.')
            ->assertJsonStructure(['message', 'data' => ['id']]);

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Jordan Smith',
            'email' => 'jordan@example.com',
            'phone' => '+1 (800) 123-4567',
            'product' => 'Outdoor LED Display',
            'message' => 'Looking for pricing information.',
        ]);
    }

    public function test_store_requires_mandatory_fields(): void
    {
        $response = $this->postJson('/api/contact', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'phone', 'message']);
    }

    public function test_store_rejects_invalid_phone_numbers(): void
    {
        $payload = [
            'name' => 'Jordan Smith',
            'email' => 'jordan@example.com',
            'phone' => 'abc123',
            'message' => 'Interested in products.',
        ];

        $response = $this->postJson('/api/contact', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('phone');
    }
}

