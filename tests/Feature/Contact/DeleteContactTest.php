<?php

namespace Tests\Feature\Contact;

use App\Contact\Actions\DeleteContact;
use App\Contact\Models\Call;
use App\Contact\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_deletes_a_contact(): void
    {
        $contact = Contact::factory()->create();

        DeleteContact::run($contact);

        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }

    public function test_cascades_to_calls(): void
    {
        $contact = Contact::factory()
            ->has(Call::factory()->count(3))
            ->create();

        DeleteContact::run($contact);

        $this->assertSame(0, Call::where('contact_id', $contact->id)->count());
    }

    public function test_http_returns_204(): void
    {
        $contact = Contact::factory()->create();

        $this->deleteJson("/api/contacts/{$contact->id}")
            ->assertStatus(204);
    }

    public function test_http_returns_404_for_unknown_id(): void
    {
        $this->deleteJson('/api/contacts/9999')
            ->assertStatus(404);
    }
}
