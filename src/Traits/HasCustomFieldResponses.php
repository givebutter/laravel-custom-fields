<?php

namespace Givebutter\LaravelCustomFields\Traits;

trait HasCustomFieldResponses
{
    public function customFieldResponses()
    {
        return $this->morphMany(config('custom-fields.models.custom_field_response'), 'model');
    }

    public function saveCustomFields($fields)
    {
        foreach ($fields as $key => $value) {
            $customField = config('custom-fields.models.custom_field')::find((int) $key);

            if (! $customField) {
                continue;
            }

            config('custom-fields.models.custom_field_response')::create([
                'value' => $value,
                'field_id' => $customField->id,
                'model_id' => $this->id,
                'model_type' => get_class($this),
            ]);
        }
    }

    public function scopeWhereField($query, $field, $value)
    {
        $query->whereHas('customFieldResponses', function ($subQuery) use ($field, $value) {
            $subQuery
                ->where('field_id', $field->id)
                ->hasValue($value);
        });
    }
}
