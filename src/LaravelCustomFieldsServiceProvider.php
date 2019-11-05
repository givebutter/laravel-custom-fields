<?php

namespace Givebutter\LaravelCustomFields;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class LaravelCustomFieldsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/laravel-custom-fields.php' => config_path('laravel-custom-fields.php'),
        ]);

        if (!class_exists('CreateCustomFieldsTables')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_custom_fields_tables.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_custom_fields_tables.php'),
            ], 'migrations');
        }
    }
}
