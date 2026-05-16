<?php

namespace App\Contact\Actions;

use App\Contact\Models\Contact;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteContact
{
    use AsAction;

    public function handle(Contact $contact): void
    {
        DB::transaction(fn () => $contact->delete());
    }

    public function asController(Contact $contact): Response
    {
        $this->handle($contact);

        return response()->noContent();
    }
}
