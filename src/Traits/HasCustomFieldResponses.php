<?php

namespace Givebutter\LaravelCustomFields\Traits;

use Givebutter\LaravelCustomFields\Models\CustomField;
use Givebutter\LaravelCustomFields\Models\CustomFieldResponse;

trait HasCustomFieldResponses
{

    protected function getCustomFieldResponseModel()
    {
        return config(
            'custom-fields.models.CustomFieldResponse',
            CustomFieldResponse::class
        );
    }

    protected function getCustomFieldModel()
    {
        return config(
            'custom-fields.models.CustomField',
            CustomField::class
        );
    }

    public function customFieldResponses()
    {
        return $this->morphMany($this->getCustomFieldResponseModel(), 'model');
    }

    public function saveCustomFields($fields)
    {
        foreach ($fields as $key => $value) {
            $this->getCustomFieldResponseModel()::create([
                'value'      => $value,
                'field_id'   => $this->getCustomFieldModel()::find((int) $key)->id,
                'model_id'   => $this->id,
                'model_type' => get_class($this),
            ]);
        }
    }

    public function scopeWhereField($query, CustomField $field, $value)
    {
        $query->whereHas('customFieldResponses', function ($subQuery) use ($field, $value) {
            $subQuery
                ->where('field_id', $field->id)
                ->hasValue($value);
        });
    }
}
