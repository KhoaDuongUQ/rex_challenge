<?php

namespace Tests\Feature\Contact;

use App\Contact\Actions\GetContact;
use App\Contact\Enums\CallOutcome;
use App\Contact\Models\Call;
use App\Contact\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_contact_data(): void
    {
        $contact = Contact::factory()->create([
            'name' => 'Ada',
            'phone' => '+61491570006',
            'email' => 'ada@example.com',
        ]);

        $result = GetContact::run($contact);

        $this->assertSame($contact->id, $result->id);
        $this->assertSame('Ada', $result->name);
        $this->assertSame('+61491570006', $result->phone);
    }

    public function test_http_returns_contact(): void
    {
        $contact = Contact::factory()->create();

        $this->getJson("/api/contacts/{$contact->id}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'id', 'name', 'phone', 'email', 'createdAt', 'updatedAt',
            ])
            ->assertJsonFragment(['id' => $contact->id]);
    }

    public function test_http_returns_404_for_unknown_id(): void
    {
        $this->getJson('/api/contacts/9999')
            ->assertStatus(404);
    }

    public function test_http_omits_calls_by_default(): void
    {
        $contact = Contact::factory()->create();
        Call::factory()->for($contact)->create();

        $this->getJson("/api/contacts/{$contact->id}")
            ->assertStatus(200)
            ->assertJsonMissingPath('calls');
    }

    public function test_http_includes_calls_when_requested(): void
    {
        $contact = Contact::factory()->create();
        $older = Call::factory()->for($contact)->state(['outcome' => CallOutcome::NoAnswer])->create([
            'created_at' => now()->subHour(),
        ]);
        $newer = Call::factory()->for($contact)->state(['outcome' => CallOutcome::Connected])->create([
            'created_at' => now(),
        ]);

        $response = $this->getJson("/api/contacts/{$contact->id}?include=calls")
            ->assertStatus(200);

        $response->assertJsonStructure([
            'id', 'name', 'phone', 'email', 'createdAt', 'updatedAt',
            'calls' => [
                ['id', 'contactId', 'outcome', 'calledAt'],
            ],
        ]);

        $calls = $response->json('calls');
        $this->assertCount(2, $calls);
        $this->assertSame($newer->id, $calls[0]['id']);
        $this->assertSame(CallOutcome::Connected->value, $calls[0]['outcome']);
        $this->assertSame($older->id, $calls[1]['id']);
    }
}
