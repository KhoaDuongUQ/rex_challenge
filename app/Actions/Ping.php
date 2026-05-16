<?php

namespace App\Actions;

use App\Data\PingData;
use Lorisleiva\Actions\Concerns\AsAction;

class Ping
{
    use AsAction;

    public function handle(): PingData
    {
        return new PingData(
            message: 'pong',
            app: config('app.name'),
            time: now()->toIso8601String(),
        );
    }
}
