# Contact module

A self-contained module for managing contacts and the (simulated) phone calls made to them. Every use case is a single-purpose Action; the HTTP API, web SPA, and interactive CLI all funnel through the same Actions, so behaviour stays consistent across surfaces.

## What lives here

```
app/Contact/
├── Actions/
│   ├── GetContacts.php          # list + search (name / phone / email)
│   ├── GetContact.php           # fetch one contact (optional `calls` include)
│   ├── UpsertContact.php        # create or update
│   ├── DeleteContact.php        # delete
│   ├── CallContact.php          # simulate placing a call, records a Call row
│   └── RunContactsConsole.php   # the `contacts:console` Artisan command
├── Data/
│   ├── ContactData.php          # contact payload (exposed to TS as App.Data.ContactData)
│   ├── GetContactsData.php      # query params for listing
│   ├── UpsertContactData.php    # validated input for create/update
│   ├── CallData.php             # a single call record
│   └── CallOutcomeData.php      # result of CallContact
├── Enums/
│   └── CallOutcome.php          # answered / voicemail / no_answer / busy / failed
└── Models/
    ├── Contact.php              # phone is cast to E.164 (AU/NZ) via laravel-phone
    └── Call.php
```

Business rules to know:

- **Phone numbers** are validated and stored as E.164 for AU/NZ via `propaganistas/laravel-phone` (`E164PhoneNumberCast:AU,NZ`). Accepted inputs include `+61412345678`, `0412 345 678`, etc. — they all normalise to E.164.
- **`CallContact`** doesn't dial anyone — it picks a random `CallOutcome`, writes a `Call` row inside a transaction, and returns a fake provider URL. It exists to exercise the full Action / Data / HTTP / CLI pipeline.
- **`GetContact`** supports a `calls` include so the detail page and CLI can show call history without an N+1.

## HTTP API

Routes are defined in [routes/web.php](../../routes/web.php) and dispatch straight to the Actions:

| Method   | Path                          | Action            |
| -------- | ----------------------------- | ----------------- |
| `GET`    | `/api/contacts`               | `GetContacts`     |
| `POST`   | `/api/contacts`               | `UpsertContact`   |
| `GET`    | `/api/contacts/{contact}`     | `GetContact`      |
| `DELETE` | `/api/contacts/{contact}`     | `DeleteContact`   |
| `POST`   | `/api/contacts/{contact}/call`| `CallContact`     |

`GetContacts` accepts a `search` query param matching against name / phone / email.

## Viewing the pages

The frontend is a React 19 SPA wired through `react-router-dom` in [resources/js/Root.tsx](../../resources/js/Root.tsx). With the Docker stack up and Vite running, open <http://localhost:8080> and use the in-app nav, or jump to a route directly:

| Page          | URL                          | Component                                                                         |
| ------------- | ---------------------------- | --------------------------------------------------------------------------------- |
| List          | `/contacts`                  | [ContactsListPage.tsx](../../resources/js/pages/contacts/ContactsListPage.tsx)    |
| Create        | `/contacts/new`              | [ContactFormPage.tsx](../../resources/js/pages/contacts/ContactFormPage.tsx)      |
| Detail        | `/contacts/:id`              | [ContactDetailPage.tsx](../../resources/js/pages/contacts/ContactDetailPage.tsx)  |
| Edit          | `/contacts/:id/edit`         | [ContactFormPage.tsx](../../resources/js/pages/contacts/ContactFormPage.tsx)      |

To run them:

```bash
# from the repo root
docker compose up -d                          # backend stack (app, nginx, mysql, redis)
npm run dev                                   # on the host — Vite dev server
```

Then open <http://localhost:8080/contacts>. If you've never built before, also run `docker compose exec app php artisan typescript:transform` so `resources/js/types/generated.d.ts` is populated with the `App.Data.*` types the pages consume.

## Running the CLI

`contacts:console` is the interactive Laravel-Prompts UI implemented by [RunContactsConsole.php](Actions/RunContactsConsole.php). It exercises every Action through the same validation rules as HTTP, which makes it handy for sanity-checking behaviour without opening the browser.

```bash
docker compose exec app php artisan contacts:console
```

Menu options:

- **List contacts** — optional search, prints a table.
- **Show a contact** — searchable picker, then a key/value dump plus the call history.
- **Create a contact** — prompts for name (required), phone (AU/NZ, optional), email (optional).
- **Update a contact** — same fields prefilled from the current record.
- **Delete a contact** — confirmation prompt.
- **Call a contact** — invokes `CallContact`, prints the random outcome + fake call URL.
- **Exit** — quits the loop.

Validation errors (e.g. invalid phone, duplicate email) are caught and printed inline so you stay in the menu loop. Use `Ctrl+C` to bail out at any prompt.

## Tests

Module tests live under [tests/Feature/Contact](../../tests/Feature/Contact). Run just the Contact tests with:

```bash
docker compose exec app php artisan test --compact --filter=Contact
```
