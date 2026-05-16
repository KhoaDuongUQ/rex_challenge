<?php

namespace App\Contact\Actions;

use App\Contact\Data\CallOutcomeData;
use App\Contact\Enums\CallOutcome;
use App\Contact\Models\Call;
use App\Contact\Models\Contact;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class CallContact
{
    use AsAction;

    public const PROVIDER_BASE = 'https://telephony.example.test';

    public function handle(Contact $contact): CallOutcomeData
    {
        return DB::transaction(function () use ($contact) {
            $outcome = Arr::random(CallOutcome::cases());

            $call = Call::create([
                'contact_id' => $contact->id,
                'outcome' => $outcome->value,
            ]);

            return new CallOutcomeData(
                contactId: $contact->id,
                outcome: $outcome,
                callUrl: self::PROVIDER_BASE.'/calls/'.Str::ulid(),
                calledAt: $call->created_at->toIso8601String(),
            );
        });
    }

    public function asController(Contact $contact): CallOutcomeData
    {
        return $this->handle($contact);
    }
}
