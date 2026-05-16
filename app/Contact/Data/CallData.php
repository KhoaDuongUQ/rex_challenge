<?php

namespace App\Contact\Data;

use App\Contact\Enums\CallOutcome;
use App\Contact\Models\Call;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CallData extends Data
{
    public function __construct(
        public int $id,
        public int $contactId,
        public CallOutcome $outcome,
        public string $calledAt,
    ) {}

    public static function fromModel(Call $call): self
    {
        return new self(
            id: $call->id,
            contactId: $call->contact_id,
            outcome: $call->outcome,
            calledAt: $call->created_at->toIso8601String(),
        );
    }
}
