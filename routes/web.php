<?php

use App\Actions\Ping;
use Illuminate\Support\Facades\Route;

Route::get('/api/ping', Ping::class);

Route::get('/{any?}', fn () => view('app'))->where('any', '.*');
