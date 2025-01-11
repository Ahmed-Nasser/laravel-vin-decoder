<?php

namespace Pureride\Vin;

use Illuminate\Support\ServiceProvider;

class PurerideServiceProvider extends ServiceProvider
{
    public function boot()
    {
        dd('vin is here');
        // Load routes, views, migrations, etc.
    }

    public function register()
    {
        // Register any package services
    }
}