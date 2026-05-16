<?php

namespace App\Contact\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class GetContactsData extends Data
{
    public function __construct(
        public ?string $search = null,
    ) {}

    public static function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
        ];
    }

    public static function messages(): array
    {
        return [
            'search.string' => 'The search field must be a string.',
            'search.max' => 'The search field must not exceed :max characters.',
        ];
    }
}
