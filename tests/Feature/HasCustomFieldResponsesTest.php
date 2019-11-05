<?php

namespace Givebutter\Tests\Feature;

use Givebutter\LaravelCustomFields\Models\CustomField;
use Givebutter\LaravelCustomFields\Models\CustomFieldResponse;
use Givebutter\Tests\Support\HasCustomFieldResponsesModel;
use Givebutter\Tests\Support\HasCustomFieldsModel;
use Givebutter\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HasCustomFieldResponsesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function custom_fields_responses_can_be_created_and_accessed_on_models_with_trait()
    {
        $customFieldModel = HasCustomFieldsModel::create();
        $customFieldResponseModel = HasCustomFieldResponsesModel::create();

        $customField = CustomField::make([
            'model_id' => $customFieldModel->id,
            'model_type' => get_class($customFieldModel),
            'type' => 'text',
            'subtype' => 'email',
            'required' => false,
            'answers' => json_encode(['Boom', 'Bap']),
            'title' => 'Tha Carter II',
            'description' => 'Lil Wayne',
            'order' => '1',
        ]);

        $customFieldModel->customFields()->save($customField);

        $customFieldResponse = CustomFieldResponse::make([
            'model_id' => $customFieldResponseModel->id,
            'model_type' => get_class($customFieldResponseModel),
            'field_id' => $customField->fresh()->id,
            'value_str' => 'Best Rapper Alive',
        ]);

        $customFieldResponseModel->customFieldResponses()->save($customFieldResponse);

        $this->assertCount(1, $customFieldResponseModel->fresh()->customFieldResponses);
        $this->assertEquals('Best Rapper Alive', $customFieldResponseModel->fresh()->customFieldResponses->first()->value_str);
    }
}
