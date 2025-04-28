<?php

namespace Givebutter\Tests;

use Givebutter\LaravelCustomFields\LaravelCustomFieldsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(function (string $modelName) {
            $namespace = 'Database\\Factories\\';

            $modelName = Str::afterLast($modelName, '\\');

            return $namespace.$modelName.'Factory';
        });

        $this->setUpDatabase($this->app);
        $this->withoutExceptionHandling();
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
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });

        $this->prepareDatabaseForHasCustomFieldsModel();
        $this->runMigrationStub();
    }

    protected function runMigrationStub()
    {
        (include __DIR__.'/../database/migrations/create_custom_fields_tables.php.stub')
            ->up();
    }

    protected function prepareDatabaseForHasCustomFieldsModel()
    {
        (include __DIR__.'/../tests/support/migrations/create_surveys_and_survey_responses_tables.php')
            ->up();
    }

    protected function resetDatabase()
    {
        // Drop all tables manually.
        $schema = $this->app['db']->connection()->getSchemaBuilder();
        foreach ($schema->getTables() as $table) {
            $schema->drop($table['name']);
        }

        $this->artisan('migrate:fresh');
        $this->runMigrationStub();
    }
}
