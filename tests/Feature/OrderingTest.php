<?php

namespace Givebutter\Tests\Feature;

use Givebutter\LaravelCustomFields\Models\CustomField;
use Givebutter\Tests\Support\Survey;
use Givebutter\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function new_fields_are_ordered_by_default()
    {
        $survey = Survey::create();
        $survey->customfields()->saveMany([
            factory(CustomField::class)->make([
                'title' => 'email',
                'type' => 'text',
            ]),
            factory(CustomField::class)->make([
                'title' => 'phone',
                'type' => 'text',
            ]),
        ]);

        $this->assertEquals(1, $survey->customfields->firstWhere('title', 'email')->order);
        $this->assertEquals(2, $survey->customfields->firstWhere('title', 'phone')->order);
    }
}
