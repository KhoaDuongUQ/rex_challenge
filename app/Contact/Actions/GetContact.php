<?php

namespace App\Contact\Actions;

use App\Contact\Data\ContactData;
use App\Contact\Models\Contact;
use Lorisleiva\Actions\Concerns\AsAction;

class GetContact
{
    use AsAction;

    public function handle(Contact $contact): ContactData
    {
        return ContactData::fromModel($contact);
    }
}
