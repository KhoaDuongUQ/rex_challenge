<?php

namespace App\Contact\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SearchContactsData extends Data
{
    public const FIELDS = ['name', 'phone', 'email_domain'];

    public function __construct(
        public string $q,
        public ?string $field = null,
    ) {}

    public static function rules(): array
    {
        return [
            'q' => ['required', 'string', 'min:1', 'max:255'],
            'field' => ['nullable', 'string', 'in:'.implode(',', self::FIELDS)],
        ];
    }
}
