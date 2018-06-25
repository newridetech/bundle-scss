<?php

namespace Newride\Scss\Providers;

use Illuminate\Support\ServiceProvider;
use Newride\Scss\ScssCompiler;

class ScssCompilerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('scss', ScssCompiler::class);
    }
}
