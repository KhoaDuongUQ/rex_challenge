<?php

namespace Tests\Feature\Contact;

use App\Contact\Actions\GetContact;
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
}
