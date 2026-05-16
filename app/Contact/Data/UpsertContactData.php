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
            'email' => ['nullable', 'email:rfc'],
        ];
    }
}
