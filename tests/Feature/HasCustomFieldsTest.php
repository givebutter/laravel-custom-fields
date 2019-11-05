<?php

namespace Givebutter\Tests\Feature;

use Givebutter\LaravelCustomFields\Models\CustomField;
use Givebutter\Tests\Support\HasCustomFieldsModel;
use Givebutter\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HasCustomFieldsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function custom_fields_can_be_created_and_accessed_on_models_with_trait()
    {
        $model = HasCustomFieldsModel::create();

        $customField = CustomField::make([
            'model_id' => $model->id,
            'model_type' => get_class($model),
            'type' => 'text',
            'subtype' => 'email',
            'required' => false,
            'answers' => json_encode(['Boom', 'Bap']),
            'title' => 'Liquid Swords',
            'description' => 'Wu Tang',
            'order' => '1',
        ]);

        $model->customFields()->save($customField);

        $this->assertCount(1, $model->fresh()->customFields);
        $this->assertEquals('Wu Tang', $model->fresh()->customFields->first()->description);
    }
}
