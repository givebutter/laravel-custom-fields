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
            DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;")
        );

        $this->assertContains('fields', $tables);
    }
}
