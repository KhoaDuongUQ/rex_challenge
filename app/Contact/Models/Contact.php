<?php

namespace App\Contact\Models;

use Database\Factories\ContactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Propaganistas\LaravelPhone\Casts\E164PhoneNumberCast;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
    ];

    protected $casts = [
        'phone' => E164PhoneNumberCast::class.':AU,NZ',
    ];

    public function calls(): HasMany
    {
        return $this->hasMany(Call::class);
    }

    protected static function newFactory(): ContactFactory
    {
        return ContactFactory::new();
    }
}
