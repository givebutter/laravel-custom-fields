<?php

namespace Givebutter\Tests\Feature;

use Givebutter\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class ScaffoldingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function both_tables_are_created_by_migrations()
    {
        $tables = array_map(
            function ($table) {
                return $table->name;
            },
            DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;") // sqlite only for now
        );

        $this->assertContains('custom_fields', $tables);
        $this->assertContains('custom_field_responses', $tables);
    }

    /** @test */
    public function table_names_are_customizable_by_config()
    {
        config([
            'custom-fields' => [
                'tables' => [
                    'fields' => 'Boom',
                    'field_responses' => 'Bap',
                ],
            ],
        ]);

        $this->resetDatabase();

        $tables = array_map(
            function ($table) {
                return $table->name;
            },
            DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;") // sqlite only for now
        );

        $this->assertContains('Boom', $tables);
        $this->assertContains('Bap', $tables);
    }

    /** @test */
    public function default_table_names_are_not_used_if_there_is_custom_config()
    {
        config([
            'custom-fields' => [
                'tables' => [
                    'fields' => 'Boom',
                    'field_responses' => 'Bap',
                ],
            ],
        ]);

        $this->resetDatabase();

        $tables = array_map(
            function ($table) {
                return $table->name;
            },
            DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;") // sqlite only for now
        );

        $this->assertNotContains('custom_fields', $tables);
        $this->assertNotContains('custom_field_responses', $tables);
    }
}
