<?php

namespace App\Contact\Actions;

use App\Contact\Data\ContactData;
use App\Contact\Data\GetContactsData;
use App\Contact\Data\UpsertContactData;
use App\Contact\Models\Contact;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\LaravelData\DataCollection;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\search;
use function Laravel\Prompts\select;
use function Laravel\Prompts\table;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

class RunContactsConsole
{
    use AsAction;

    public string $commandSignature = 'contacts:console';

    public string $commandDescription = 'Interactive CLI for the Contact module.';

    public function handle(): void
    {
        info('Contacts Console — drives every Contact Action through Laravel Prompts.');

        while (true) {
            $choice = select(
                label: 'What would you like to do?',
                options: [
                    'list' => 'List contacts',
                    'show' => 'Show a contact',
                    'create' => 'Create a contact',
                    'update' => 'Update a contact',
                    'delete' => 'Delete a contact',
                    'call' => 'Call a contact',
                    'exit' => 'Exit',
                ],
                default: 'list',
            );

            try {
                match ($choice) {
                    'list' => $this->listContacts(),
                    'show' => $this->showContact(),
                    'create' => $this->createContact(),
                    'update' => $this->updateContact(),
                    'delete' => $this->deleteContact(),
                    'call' => $this->callContact(),
                    'exit' => null,
                };
            } catch (ValidationException $e) {
                foreach ($e->errors() as $messages) {
                    foreach ($messages as $message) {
                        error($message);
                    }
                }

                continue;
            } catch (ModelNotFoundException) {
                error('Contact not found.');

                continue;
            }

            if ($choice === 'exit') {
                info('Bye.');

                return;
            }
        }
    }

    public function asCommand(Command $command): int
    {
        $this->handle();

        return Command::SUCCESS;
    }

    private function listContacts(): void
    {
        $search = text(
            label: 'Search (optional — matches name / phone / email)',
            placeholder: 'leave blank to list everything',
        );

        /** @var DataCollection<int, ContactData> $contacts */
        $contacts = GetContacts::run(
            GetContactsData::validateAndCreate(['search' => $search !== '' ? $search : null]),
        );

        $rows = $contacts->toCollection()
            ->map(fn (ContactData $c): array => [
                $c->id,
                $c->name,
                $c->phone ?? '—',
                $c->email ?? '—',
                $c->updatedAt,
            ])
            ->all();

        if ($rows === []) {
            warning('No contacts found.');

            return;
        }

        table(['ID', 'Name', 'Phone', 'Email', 'Updated'], $rows);
    }

    private function showContact(): void
    {
        $contact = $this->pickContact('Pick a contact to show');

        $data = GetContact::run($contact)->include('calls');

        $payload = $data->toArray();
        $calls = $payload['calls'] ?? [];
        unset($payload['calls']);

        note(collect($payload)
            ->map(fn ($value, $key): string => sprintf('%-10s %s', $key.':', $this->stringify($value)))
            ->implode(PHP_EOL));

        if ($calls === []) {
            note('No calls recorded.');

            return;
        }

        table(
            ['Call ID', 'Outcome', 'Called At'],
            collect($calls)
                ->map(fn (array $c): array => [$c['id'], $c['outcome'], $c['calledAt']])
                ->all(),
        );
    }

    private function createContact(): void
    {
        $contact = UpsertContact::run(
            UpsertContactData::validateAndCreate($this->promptContactFields()),
        );

        info("Created contact #{$contact->id} — {$contact->name}.");
    }

    private function updateContact(): void
    {
        $existing = $this->pickContact('Pick a contact to update');

        $contact = UpsertContact::run(
            UpsertContactData::validateAndCreate($this->promptContactFields(
                id: $existing->id,
                defaultName: $existing->name,
                defaultPhone: $existing->phone?->formatE164(),
                defaultEmail: $existing->email,
            )),
        );

        info("Updated contact #{$contact->id} — {$contact->name}.");
    }

    private function deleteContact(): void
    {
        $contact = $this->pickContact('Pick a contact to delete');

        if (! confirm("Delete contact #{$contact->id} ({$contact->name})?", default: false)) {
            note('Cancelled.');

            return;
        }

        $id = $contact->id;
        $name = $contact->name;

        DeleteContact::run($contact);

        info("Deleted contact #{$id} — {$name}.");
    }

    private function callContact(): void
    {
        $contact = $this->pickContact('Pick a contact to call');

        $result = CallContact::run($contact);

        info(sprintf(
            'Outcome: %s — %s (at %s)',
            $result->outcome->value,
            $result->callUrl,
            $result->calledAt,
        ));
    }

    /**
     * Re-uses GetContacts so the search behaviour matches the HTTP layer.
     */
    private function pickContact(string $label): Contact
    {
        $id = search(
            label: $label,
            options: function (string $value): array {
                $params = GetContactsData::validateAndCreate(['search' => $value !== '' ? $value : null]);

                /** @var DataCollection<int, ContactData> $contacts */
                $contacts = GetContacts::run($params);

                return $contacts->toCollection()
                    ->mapWithKeys(fn (ContactData $c): array => [
                        $c->id => sprintf(
                            '#%d %s%s',
                            $c->id,
                            $c->name,
                            $c->phone ? " <{$c->phone}>" : ($c->email ? " <{$c->email}>" : ''),
                        ),
                    ])
                    ->all();
            },
            placeholder: 'Type to search by name / phone / email',
            scroll: 10,
        );

        return Contact::findOrFail($id);
    }

    /**
     * @return array{id: ?int, name: string, phone: ?string, email: ?string}
     */
    private function promptContactFields(
        ?int $id = null,
        ?string $defaultName = null,
        ?string $defaultPhone = null,
        ?string $defaultEmail = null,
    ): array {
        $name = text(label: 'Name', default: $defaultName ?? '', required: true);
        $phone = text(
            label: 'Phone (AU / NZ, E.164 — e.g. +61412345678)',
            default: $defaultPhone ?? '',
            hint: 'Leave blank to clear.',
        );
        $email = text(
            label: 'Email',
            default: $defaultEmail ?? '',
            hint: 'Leave blank to clear.',
        );

        return [
            'id' => $id,
            'name' => $name,
            'phone' => $phone !== '' ? $phone : null,
            'email' => $email !== '' ? $email : null,
        ];
    }

    private function stringify(mixed $value): string
    {
        return match (true) {
            $value === null => '—',
            is_scalar($value) => (string) $value,
            default => json_encode($value, JSON_UNESCAPED_SLASHES),
        };
    }
}
