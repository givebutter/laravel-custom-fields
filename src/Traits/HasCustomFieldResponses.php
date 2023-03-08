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
        $customFields = CustomField::findMany(array_keys($fields));

        $responses = collect($fields)
            ->filter(fn ($value, $key) => $customFields->contains('id', $key))
            ->map(fn ($value, $key) => new CustomFieldResponse([
                'value' => $value,
                'field_id' => $key,
            ]));

        $this->customFieldResponses()->saveMany($responses);
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
