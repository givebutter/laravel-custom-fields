<?php

namespace Givebutter\Tests\Feature;

use Givebutter\LaravelCustomFields\Models\CustomField;
use Givebutter\LaravelCustomFields\Models\CustomFieldResponse;
use Givebutter\Tests\Support\Account;
use Givebutter\Tests\Support\Contact;
use Givebutter\Tests\Support\Survey;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Givebutter\Tests\TestCase;

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
