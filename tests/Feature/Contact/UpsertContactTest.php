<?php

namespace Tests\Feature\Contact;

use App\Contact\Actions\UpsertContact;
use App\Contact\Data\UpsertContactData;
use App\Contact\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpsertContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_a_contact(): void
    {
        $data = UpsertContactData::from([
            'name' => 'Ada Lovelace',
            'phone' => '+61491570006',
            'email' => 'ada@example.com',
        ]);

        $result = UpsertContact::run($data);

        $this->assertSame('Ada Lovelace', $result->name);
        $this->assertSame('+61491570006', $result->phone);
        $this->assertSame('ada@example.com', $result->email);
        $this->assertDatabaseHas('contacts', [
            'id' => $result->id,
            'name' => 'Ada Lovelace',
            'phone' => '+61491570006',
            'email' => 'ada@example.com',
        ]);
    }

    public function test_updates_an_existing_contact_when_id_is_provided(): void
    {
        $existing = Contact::factory()->create(['name' => 'Original']);

        $result = UpsertContact::run(UpsertContactData::from([
            'id' => $existing->id,
            'name' => 'Updated',
            'phone' => '+61491570006',
        ]));

        $this->assertSame($existing->id, $result->id);
        $this->assertSame('Updated', $result->name);
        $this->assertSame(1, Contact::count());
    }

    public function test_normalises_phone_to_e164(): void
    {
        $result = UpsertContact::run(UpsertContactData::from([
            'name' => 'Ada',
            'phone' => '0491570006',  // local AU mobile, no country code
        ]));

        $this->assertSame('+61491570006', $result->phone);
    }

    public function test_rejects_non_au_nz_phone_via_http(): void
    {
        $this->postJson('/api/contacts', [
            'name' => 'Bad',
            'phone' => '+12025550100', // US number
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor('phone');
    }

    public function test_rejects_malformed_phone(): void
    {
        $this->postJson('/api/contacts', [
            'name' => 'Bad',
            'phone' => '+1234',
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor('phone');
    }

    public function test_rejects_invalid_email(): void
    {
        $this->postJson('/api/contacts', [
            'name' => 'Bad',
            'email' => 'not-an-email',
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor('email');
    }

    public function test_rejects_missing_name(): void
    {
        $this->postJson('/api/contacts', [
            'phone' => '+61491570006',
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor('name');
    }

    public function test_returns_contact_data_shape_via_http(): void
    {
        $this->postJson('/api/contacts', [
            'name' => 'Ada',
            'phone' => '+61491570006',
            'email' => 'ada@example.com',
        ])->assertSuccessful()
            ->assertJsonStructure([
                'id', 'name', 'phone', 'email', 'createdAt', 'updatedAt',
            ]);
    }
}
