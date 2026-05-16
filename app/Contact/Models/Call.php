<?php

namespace App\Contact\Models;

use App\Contact\Enums\CallOutcome;
use Database\Factories\CallFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Call extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'outcome',
    ];

    protected $casts = [
        'outcome' => CallOutcome::class,
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    protected static function newFactory(): CallFactory
    {
        return CallFactory::new();
    }
}
