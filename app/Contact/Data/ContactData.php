<?php

namespace App\Contact\Data;

use App\Contact\Models\Call;
use App\Contact\Models\Contact;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Lazy;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
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
        #[DataCollectionOf(CallData::class)]
        #[LiteralTypeScriptType('Array<App.Contact.Data.CallData> | undefined')]
        public Lazy|DataCollection $calls,
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
            calls: Lazy::create(fn () => CallData::collect(
                $contact->calls()
                    ->orderByDesc('created_at')
                    ->orderByDesc('id')
                    ->limit(50)
                    ->get()
                    ->map(fn (Call $c) => CallData::fromModel($c))
                    ->all(),
                DataCollection::class,
            )),
        );
    }

    public static function allowedRequestIncludes(): array
    {
        return ['calls'];
    }
}
