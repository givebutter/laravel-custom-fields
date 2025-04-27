<?php

namespace Givebutter\Tests\Feature;

use Givebutter\LaravelCustomFields\Models\CustomField;
use Givebutter\Tests\Support\Survey;
use Givebutter\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HasCustomFieldsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function custom_fields_can_be_created_and_accessed_on_models_with_trait(): void
    {
        $model = Survey::create();

        $customField = CustomField::factory()->make([
            'model_id' => $model->id,
            'model_type' => get_class($model),
            'description' => 'Lil Wayne',
        ]);

        $model->customFields()->save($customField);

        $this->assertCount(1, $model->fresh()->customFields);
        $this->assertEquals('Lil Wayne', $model->fresh()->customFields->first()->description);
    }

    /** @test */
    public function test_validating_unowned_custom_field_ids_are_ignored(): void
    {
        $model = Survey::create();

        $customField = CustomField::factory()->make([
            'model_id' => $model->id,
            'model_type' => get_class($model),
            'type' => 'text',
        ]);

        $validator = $model->validateCustomFields([
            $customField->id + 1 => 'foo',
        ]);

        $this->assertTrue($validator->passes());
    }
}
