<?php

namespace Tests\Feature\Contact;

use App\Contact\Actions\GetContacts;
use App\Contact\Data\GetContactsData;
use App\Contact\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetContactsTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_all_contacts_when_no_search(): void
    {
        Contact::factory()->count(3)->create();

        $results = GetContacts::run(GetContactsData::from([]));

        $this->assertCount(3, $results);
    }

    public function test_searches_by_name_partial(): void
    {
        Contact::factory()->create(['name' => 'Ada Lovelace']);
        Contact::factory()->create(['name' => 'Grace Hopper']);

        $results = GetContacts::run(GetContactsData::from(['search' => 'Ada']));

        $this->assertCount(1, $results);
        $this->assertSame('Ada Lovelace', $results[0]->name);
    }

    public function test_searches_by_phone_partial(): void
    {
        Contact::factory()->create(['phone' => '+61491570006']);
        Contact::factory()->create(['phone' => '+64211234567']);

        $results = GetContacts::run(GetContactsData::from(['search' => '91570006']));

        $this->assertCount(1, $results);
        $this->assertSame('+61491570006', $results[0]->phone);
    }

    public function test_searches_by_email_partial(): void
    {
        Contact::factory()->create(['email' => 'ada@example.com']);
        Contact::factory()->create(['email' => 'grace@other.org']);

        $results = GetContacts::run(GetContactsData::from(['search' => 'example.com']));

        $this->assertCount(1, $results);
        $this->assertSame('ada@example.com', $results[0]->email);
    }

    public function test_search_matches_across_all_fields(): void
    {
        $byName = Contact::factory()->create(['name' => 'foo person']);
        $byEmail = Contact::factory()->create(['email' => 'someone@foo.com']);
        Contact::factory()->create(['name' => 'unrelated', 'email' => 'nothing@bar.com']);

        $results = GetContacts::run(GetContactsData::from(['search' => 'foo']));

        $ids = collect($results->toArray())->pluck('id')->all();
        $this->assertContains($byName->id, $ids);
        $this->assertContains($byEmail->id, $ids);
        $this->assertCount(2, $results);
    }

    public function test_http_list_endpoint_returns_all_contacts(): void
    {
        Contact::factory()->count(2)->create();

        $this->getJson('/api/contacts')
            ->assertSuccessful()
            ->assertJsonCount(2);
    }

    public function test_http_list_endpoint_accepts_optional_search(): void
    {
        Contact::factory()->create(['name' => 'Ada']);
        Contact::factory()->create(['name' => 'Grace']);

        $this->getJson('/api/contacts?search=Ada')
            ->assertSuccessful()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Ada']);
    }
}
