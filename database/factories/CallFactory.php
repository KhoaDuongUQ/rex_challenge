<?php

namespace Database\Factories;

use App\Contact\Enums\CallOutcome;
use App\Contact\Models\Call;
use App\Contact\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Call>
 */
class CallFactory extends Factory
{
    protected $model = Call::class;

    public function definition(): array
    {
        return [
            'contact_id' => Contact::factory(),
            'outcome' => fake()->randomElement(CallOutcome::cases())->value,
        ];
    }
}
