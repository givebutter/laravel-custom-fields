<?php

namespace Givebutter\LaravelCustomFields\Traits;

use Givebutter\LaravelCustomFields\Models\CustomField;
use Givebutter\LaravelCustomFields\Models\CustomFieldResponse;

trait HasCustomFieldResponses
{
    public function customFieldResponses()
    {
        return $this->morphMany(CustomFieldResponse::class, 'model');
    }

    public function saveCustomFields($fields)
    {
        foreach ($fields as $key => $value) {
            CustomFieldResponse::create([
                'value' => $value,
                'field_id' => CustomField::find((int) $key)->id,
                'model_id' => $this->id,
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
