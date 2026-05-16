<?php

namespace App\Contact\Actions;

use App\Contact\Data\ContactData;
use App\Contact\Data\UpsertContactData;
use App\Contact\Models\Contact;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class UpsertContact
{
    use AsAction;

    public function handle(UpsertContactData $data): ContactData
    {
        $contact = DB::transaction(fn () => Contact::updateOrCreate(
            ['id' => $data->id],
            [
                'name' => $data->name,
                'phone' => $data->phone,
                'email' => $data->email,
            ],
        ));

        return ContactData::fromModel($contact->refresh());
    }
}
