<?php

namespace App\Contact\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UpsertContactData extends Data
{
    public function __construct(
        public ?int $id,
        public string $name,
        public ?string $phone,
        public ?string $email,
    ) {}

    public static function rules(): array
    {
        return [
            'id' => ['nullable', 'integer', 'exists:contacts,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'phone:AU,NZ'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
        ];
    }

    public static function messages(): array
    {
        return [
            'id.integer' => 'The id field must be an integer.',
            'id.exists' => 'No contact found with the provided id.',
            'name.required' => 'The name field is required.',
            'name.string' => 'The name field must be a string.',
            'name.max' => 'The name field must not exceed :max characters.',
            'phone.phone' => 'The phone field must be a valid Australian (AU) or New Zealand (NZ) phone number in E.164 format (e.g. +61412345678).',
            'email.email' => 'The email field must be a valid RFC-compliant email address (e.g. user@example.com).',
            'email.max' => 'The email field must not exceed :max characters.',
        ];
    }
}
