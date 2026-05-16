<?php

namespace App\Contact\Data;

use App\Contact\Models\Contact;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ContactData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $phone,
        public ?string $email,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function fromModel(Contact $contact): self
    {
        return new self(
            id: $contact->id,
            name: $contact->name,
            phone: $contact->phone?->formatE164(),
            email: $contact->email,
            createdAt: $contact->created_at->toIso8601String(),
            updatedAt: $contact->updated_at->toIso8601String(),
        );
    }
}
