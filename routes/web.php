<?php

use App\Actions\Ping;
use App\Contact\Actions\CallContact;
use App\Contact\Actions\DeleteContact;
use App\Contact\Actions\GetContact;
use App\Contact\Actions\GetContacts;
use App\Contact\Actions\UpsertContact;
use Illuminate\Support\Facades\Route;

Route::get('/api/ping', Ping::class);

Route::prefix('api/contacts')->group(function () {
    Route::get('/', GetContacts::class);
    Route::post('/', UpsertContact::class);
    Route::get('/{contact}', GetContact::class);
    Route::delete('/{contact}', DeleteContact::class);
    Route::post('/{contact}/call', CallContact::class);
});

Route::get('/{any?}', fn () => view('app'))->where('any', '.*');
