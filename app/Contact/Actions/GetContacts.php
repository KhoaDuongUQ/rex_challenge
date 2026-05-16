<?php

namespace App\Contact\Actions;

use App\Contact\Data\ContactData;
use App\Contact\Data\GetContactsData;
use App\Contact\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\LaravelData\DataCollection;

class GetContacts
{
    use AsAction;

    public const LIMIT = 50;

    public function handle(GetContactsData $params): DataCollection
    {
        $contacts = Contact::query()
            ->when($params->search, function (Builder $query, string $search) {
                $like = '%'.addcslashes($search, '%_\\').'%';

                $query->where(function (Builder $q) use ($like) {
                    $q->where('name', 'LIKE', $like)
                        ->orWhere('phone', 'LIKE', $like)
                        ->orWhere('email', 'LIKE', $like);
                });
            })
            ->orderByDesc('updated_at')
            ->limit(self::LIMIT)
            ->get();

        return ContactData::collect(
            $contacts->map(fn (Contact $c) => ContactData::fromModel($c))->all(),
            DataCollection::class,
        );
    }

    public function asController(GetContactsData $params): DataCollection
    {
        return $this->handle($params);
    }
}
