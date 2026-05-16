<?php

namespace App\Contact\Enums;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
enum CallOutcome: string
{
    case Connected = 'connected';
    case NoAnswer = 'no_answer';
    case Busy = 'busy';
    case Voicemail = 'voicemail';
    case Failed = 'failed';
}
