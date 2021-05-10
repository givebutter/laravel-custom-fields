<?php

namespace Givebutter\LaravelCustomFields\Traits;

use Givebutter\LaravelCustomFields\Models\CustomField;
use Givebutter\LaravelCustomFields\Models\CustomFieldResponse;

trait HasCustomFieldResponses
{
    /**
     * Get the custom field responses for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function customFieldResponses()
    {
        return $this->morphMany(CustomFieldResponse::class, 'model');
    }

    /**
     * Save the given custom fields to the model.
     *
     * @param $fields
     */
    public function saveCustomFields($fields)
    {
        foreach ($fields as $key => $value) {
            $customField = CustomField::find((int) $key);

            if (! $customField) {
                continue;
            }

            CustomFieldResponse::create([
                'value' => $value,
                'field_id' => $customField->id,
                'model_id' => $this->id,
                'model_type' => get_class($this),
            ]);
        }
    }

    /**
     * Add a scope to return only models which match the given field and value.
     *
     * @param $query
     * @param CustomField $field
     * @param $value
     */
    public function scopeWhereField($query, CustomField $field, $value)
    {
        $query->whereHas('customFieldResponses', function ($subQuery) use ($field, $value) {
            $subQuery
                ->where('field_id', $field->id)
                ->hasValue($value);
        });
    }
}
