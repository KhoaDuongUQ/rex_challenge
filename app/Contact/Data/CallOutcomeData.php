<?php

namespace App\Contact\Data;

use App\Contact\Enums\CallOutcome;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CallOutcomeData extends Data
{
    public function __construct(
        public int $contactId,
        public CallOutcome $outcome,
        public string $callUrl,
        public string $calledAt,
    ) {}
}
