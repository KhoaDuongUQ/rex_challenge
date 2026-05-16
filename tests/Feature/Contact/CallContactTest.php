<?php

namespace Tests\Feature\Contact;

use App\Contact\Actions\CallContact;
use App\Contact\Enums\CallOutcome;
use App\Contact\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CallContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_an_outcome_and_persists_a_call_row(): void
    {
        $contact = Contact::factory()->create();

        $result = CallContact::run($contact);

        $this->assertContains(
            $result->outcome,
            CallOutcome::cases(),
        );
        $this->assertSame($contact->id, $result->contactId);
        $this->assertDatabaseHas('calls', [
            'contact_id' => $contact->id,
            'outcome' => $result->outcome->value,
        ]);
        $this->assertNotEmpty($result->calledAt);
    }

    public function test_call_url_matches_provider_shape(): void
    {
        $contact = Contact::factory()->create();

        $result = CallContact::run($contact);

        $this->assertMatchesRegularExpression(
            '#^https://telephony\.example\.test/calls/[0-9A-HJKMNP-TV-Z]{26}$#',
            $result->callUrl,
        );
    }

    public function test_repeated_calls_have_distinct_urls(): void
    {
        $contact = Contact::factory()->create();

        $first = CallContact::run($contact);
        $second = CallContact::run($contact);

        $this->assertNotSame($first->callUrl, $second->callUrl);
        $this->assertSame(2, $contact->calls()->count());
    }

    public function test_http_returns_outcome(): void
    {
        $contact = Contact::factory()->create();

        $this->postJson("/api/contacts/{$contact->id}/call")
            ->assertSuccessful()
            ->assertJsonStructure(['contactId', 'outcome', 'callUrl', 'calledAt']);
    }

    public function test_http_returns_404_for_unknown_contact(): void
    {
        $this->postJson('/api/contacts/9999/call')
            ->assertStatus(404);
    }
}
