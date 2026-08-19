<?php

namespace App\Providers;

use App\Services\Analysis\Contracts\DialogueReaderInterface;
use App\Services\Dialogue\Integration\ForAnalysis\DialogueReaderAdapter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DialogueReaderInterface::class, DialogueReaderAdapter::class);
    }

    public function boot(): void
    {
        //
    }
}
