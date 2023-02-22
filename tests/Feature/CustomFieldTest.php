<?php

namespace Givebutter\Tests\Feature;

use Givebutter\LaravelCustomFields\Models\CustomField;
use Givebutter\Tests\Support\Survey;
use Givebutter\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomFieldTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_can_have_a_group()
    {
        $survey = Survey::create();

        $survey->customFields()->save(
            CustomField::factory()->make([
                'group' => 'foo',
            ])
        );

        $customField = $survey->customFields->first();

        $this->assertEquals('foo', $customField->group);
    }
}
