<?php

namespace Givebutter\Tests\Feature;

use Givebutter\LaravelCustomFields\Collections\CustomFieldCollection;
use Givebutter\LaravelCustomFields\Models\CustomField;
use Givebutter\Tests\Support\Survey;
use Givebutter\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomFieldTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_can_have_a_group(): void
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

    public function test_collection_validation_rules(): void
    {
        $survey = Survey::create();

        $cfs = CustomField::factory()->count(6)->sequence(
            ['title' => 'My Checkbox Field', 'type' => 'checkbox'],
            ['title' => 'My Integer Field', 'type' => 'number'],
            ['title' => 'My Radio Field', 'type' => 'radio'],
            ['title' => 'My Select Field', 'type' => 'select', 'answers' => ['foo', 'bar']],
            ['title' => 'My Text Field', 'type' => 'text'],
            ['title' => 'My Textarea Field', 'type' => 'textarea'],
            ['title' => 'My Multiselect Field', 'type' => 'multiselect'],
        )->create([
            'model_id' => $survey->id,
            'model_type' => $survey->getMorphClass(),
        ]);

        $this->assertInstanceOf(CustomFieldCollection::class, $cfs);

        $rules = $cfs->toValidationRules();

        $this->assertSame(
            $cfs->map(fn (CustomField $field) => 'custom_fields.field_'.$field->id)->toArray(),
            array_keys($rules),
        );
    }
}
