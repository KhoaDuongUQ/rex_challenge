<?php

namespace Database\Factories;

use App\Contact\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    protected $model = Contact::class;

    /**
     * libphonenumber's documented example numbers — guaranteed parseable and
     * tagged as AU. Using a fixed pool avoids random sequences that pass the
     * `phone:AU,NZ` rule but fail to map to a country on read.
     */
    private const AU_NZ_EXAMPLES = [
        '+61491570006',
        '+61491570156',
        '+61491570157',
        '+61491570158',
        '+64211234567',
        '+64212345678',
    ];

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->randomElement(self::AU_NZ_EXAMPLES),
            'email' => fake()->unique()->safeEmail(),
        ];
    }

    public function withoutContactDetails(): self
    {
        return $this->state(fn () => ['phone' => null, 'email' => null]);
    }
}
