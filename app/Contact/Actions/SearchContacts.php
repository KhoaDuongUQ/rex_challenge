<?php

namespace App\Contact\Actions;

use App\Contact\Data\ContactData;
use App\Contact\Data\SearchContactsData;
use App\Contact\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\LaravelData\DataCollection;

class SearchContacts
{
    use AsAction;

    public const LIMIT = 50;

    public function handle(SearchContactsData $params): DataCollection
    {
        $term = $params->q;
        $like = '%'.addcslashes($term, '%_\\').'%';

        $contacts = Contact::query()
            ->where(function (Builder $query) use ($params, $term, $like) {
                $field = $params->field;

                if ($field === 'name' || $field === null) {
                    $query->orWhere('name', 'LIKE', $like);
                }

                if ($field === 'phone' || $field === null) {
                    $query->orWhere('phone', 'LIKE', $like);
                }

                if ($field === 'email_domain') {
                    $domain = '%@'.addcslashes($term, '%_\\');
                    $query->orWhere('email', 'LIKE', $domain);
                } elseif ($field === null) {
                    // Default (no field) — match anywhere in email, so the
                    // single `q` works as a fuzzy across all columns.
                    $query->orWhere('email', 'LIKE', $like);
                }
            })
            ->orderByDesc('updated_at')
            ->limit(self::LIMIT)
            ->get();

        return ContactData::collect(
            $contacts->map(fn (Contact $c) => ContactData::fromModel($c))->all(),
            DataCollection::class,
        );
    }
}
