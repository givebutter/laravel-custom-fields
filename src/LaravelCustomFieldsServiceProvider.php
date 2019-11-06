<?php

namespace Givebutter\LaravelCustomFields;

use Illuminate\Support\ServiceProvider;

class LaravelCustomFieldsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/custom-fields.php' => config_path('custom-fields.php'),
        ]);

        if (!class_exists('CreateCustomFieldsTables')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_custom_fields_tables.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_custom_fields_tables.php'),
            ], 'migrations');
        }
    }
}
