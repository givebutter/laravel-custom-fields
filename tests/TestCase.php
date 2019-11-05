<?php

namespace Givebutter\Tests;

use CreateHasCustomFieldsModelAndCustomFieldResponsesModelTables;
use Givebutter\LaravelCustomFields\LaravelCustomFieldsServiceProvider;
use Givebutter\Tests\Support\HasCustomFieldsModel;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase($this->app);
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelCustomFieldsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });

        $this->prepareDatabaseForHasCustomFieldsModel();
        HasCustomFieldsModel::create();

        $this->runMigrationStub();
    }

    protected function runMigrationStub()
    {
        include_once __DIR__ . '/../database/migrations/create_custom_fields_tables.php.stub';
        (new \CreateCustomFieldsTables())->up();
    }

    protected function prepareDatabaseForHasCustomFieldsModel()
    {
        include_once __DIR__ . '/../tests/support/migrations/create_has_custom_fields_model_and_custom_field_responses_model_tables.php';
        (new CreateHasCustomFieldsModelAndCustomFieldResponsesModelTables())->up();
    }

    protected function resetDatabase()
    {
        $this->artisan('migrate:fresh');
        $this->runMigrationStub();
    }
}
