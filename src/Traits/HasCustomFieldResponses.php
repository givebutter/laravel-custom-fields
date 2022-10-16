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
     * Save the custom fields to the model.
     *   This parameter accepts a list of fields for immediate saving & backwards compatibility.
     *
     * @param $fields
     */
    public function saveCustomFields($fields = [])
    {
        if (!empty($fields)) {
            $this->makeCustomFields($fields);
        }

        $this->customFieldResponses->each(function(CustomFieldResponse $field) {
            $field->model_id ??= $this->id; // set the ID now, if model did not exist when makeCustomFields() was called
            $field->save();
        });
    }

    /**
     * Make the given custom fields but do not save.
     *
     * @param $fields
     */
    public function makeCustomFields($fields)
    {
        foreach ($fields as $key => $value) {
            $customField = CustomField::find((int) $key);

            if (! $customField) {
                continue;
            }

            $customFieldResponse = new CustomFieldResponse([
                'value' => $value,
                'model_id' => $this->id,
                'field_id' => $customField->id,
                'model_type' => get_class($this),
            ]);

            $this->customFieldResponses->push($customFieldResponse);
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
        $query->whereHas('customFieldResponses', function ($query) use ($field, $value) {
            $query
                ->where('field_id', $field->id)
                ->where(function ($subQuery) use ($value) {
                    $subQuery->hasValue($value);
                });
        });
    }
}
