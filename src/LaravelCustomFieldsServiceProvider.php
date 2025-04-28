<?php

namespace Givebutter\LaravelCustomFields;

use Givebutter\LaravelCustomFields\FieldTypes\FieldType;
use Givebutter\LaravelCustomFields\ResponseTypes\ResponseType;
use Illuminate\Support\ServiceProvider;

class LaravelCustomFieldsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/custom-fields.php', 'custom-fields');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/custom-fields.php' => config_path('custom-fields.php'),
        ], 'custom-fields-config');

        if (! class_exists('CreateCustomFieldsTables')) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_custom_fields_tables.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_custom_fields_tables.php'),
            ], 'migrations');
        }

        $this->app->bind(FieldType::class, function ($app, $params) {
            $fieldTypeClass = config('custom-fields.fields.'.$params['type']);

            return new $fieldTypeClass($params['field']);
        });

        $this->app->bind(ResponseType::class, function ($app, $params) {
            $responseTypeClass = config('custom-fields.responses.'.$params['type']);

            return new $responseTypeClass($params['response']);
        });
    }
}
