<?php

namespace Tests\Feature\Contact;

use App\Contact\Actions\SearchContacts;
use App\Contact\Data\SearchContactsData;
use App\Contact\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchContactsTest extends TestCase
{
    use RefreshDatabase;

    public function test_searches_by_name_partial(): void
    {
        Contact::factory()->create(['name' => 'Ada Lovelace']);
        Contact::factory()->create(['name' => 'Grace Hopper']);

        $results = SearchContacts::run(SearchContactsData::from([
            'q' => 'Ada',
            'field' => 'name',
        ]));

        $this->assertCount(1, $results);
        $this->assertSame('Ada Lovelace', $results[0]->name);
    }

    public function test_searches_by_phone_partial(): void
    {
        Contact::factory()->create(['phone' => '+61491570006']);
        Contact::factory()->create(['phone' => '+61400000001']);

        $results = SearchContacts::run(SearchContactsData::from([
            'q' => '91570006',
            'field' => 'phone',
        ]));

        $this->assertCount(1, $results);
        $this->assertSame('+61491570006', $results[0]->phone);
    }

    public function test_searches_by_email_domain(): void
    {
        Contact::factory()->create(['email' => 'ada@example.com']);
        Contact::factory()->create(['email' => 'grace@other.org']);

        $results = SearchContacts::run(SearchContactsData::from([
            'q' => 'example.com',
            'field' => 'email_domain',
        ]));

        $this->assertCount(1, $results);
        $this->assertSame('ada@example.com', $results[0]->email);
    }

    public function test_default_search_matches_across_all_fields(): void
    {
        $byName = Contact::factory()->create(['name' => 'foo person']);
        $byEmail = Contact::factory()->create(['email' => 'someone@foo.com']);

        $results = SearchContacts::run(SearchContactsData::from(['q' => 'foo']));

        $ids = collect($results->toArray())->pluck('id')->all();
        $this->assertContains($byName->id, $ids);
        $this->assertContains($byEmail->id, $ids);
    }

    public function test_http_rejects_missing_q(): void
    {
        $this->getJson('/api/contacts/search')
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('q');
    }

    public function test_http_rejects_unknown_field(): void
    {
        $this->getJson('/api/contacts/search?q=foo&field=bogus')
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('field');
    }
}
