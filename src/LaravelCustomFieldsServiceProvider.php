<?php

namespace Givebutter\LaravelCustomFields;

use Illuminate\Support\ServiceProvider;

class LaravelCustomFieldsServiceProvider extends ServiceProvider
{

    protected const CONFIG_PATH = __DIR__.'/../config/custom-fields.php';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('custom-fields.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'custom_fields'
        );
    }
}
