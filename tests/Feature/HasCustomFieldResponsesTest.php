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

    /** @test */
    public function whereField_method_allows_filtering_responses()
    {
        $customFieldModel = HasCustomFieldsModel::create();
        $firstResponseModel = HasCustomFieldResponsesModel::create();
        $secondResponseModel = HasCustomFieldResponsesModel::create();

        $firstField = CustomField::create([
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

        $firstResponse = CustomFieldResponse::make([
            'model_id' => $firstResponseModel->id,
            'model_type' => get_class($firstResponseModel),
            'field_id' => $firstField->id,
            'value_str' => 'Hit Em Up',
        ]);

        $secondResponse = CustomFieldResponse::make([
            'model_id' => $secondResponseModel->id,
            'model_type' => get_class($secondResponseModel),
            'field_id' => $firstField->id,
            'value_str' => 'Best Rapper Alive',
        ]);

        $firstResponseModel->customFieldResponses()->save($firstResponse);
        $secondResponseModel->customFieldResponses()->save($secondResponse);

        $this->assertCount(1, CustomFieldResponseModel::whereField($firstField, 'Hit Em Up')->get());
        $this->assertEquals($firstResponse->id, CustomFieldResponseModel::whereField($firstField, 'Hit Em Up')->first()->id);

        $this->assertCount(1, CustomFieldResponseModel::whereField($firstField, 'Best Rapper Alive')->get());
        $this->assertEquals($secondResponse->id, CustomFieldResponseModel::whereField($firstField, 'Best Rapper Alive')->first()->id);
    }
}
