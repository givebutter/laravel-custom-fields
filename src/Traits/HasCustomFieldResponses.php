<?php

namespace Givebutter\LaravelCustomFields\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasCustomFieldResponses
{
    /**
     * Get the custom field responses for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function customFieldResponses()
    {
        return $this->morphMany(config('custom-fields.models.custom-field-response'), 'model');
    }

    /**
     * Save the given custom fields to the model.
     *
     * @param $fields
     */
    public function saveCustomFields($fields)
    {
        $customFieldClass = config('custom-fields.models.custom-field');
        $customFieldResponseClass = config('custom-fields.models.custom-field-response');

        $customFields = $customFieldClass::findMany(array_keys($fields));

        $responses = collect($fields)
            ->filter(fn ($value, $key) => $customFields->contains('id', $key))
            ->map(function ($value, $key) use ($customFieldResponseClass) {
                $response = $customFieldResponseClass::firstOrNew([
                    'field_id' => $key,
                    'model_id' => $this->id,
                    'model_type' => $this->getMorphClass(),
                ]);

                if (! $response->id) {
                    $response->field()->associate($key);
                    $response->model()->associate($this);
                }

                $response->value = $value;

                return $response;
            });

        $this->customFieldResponses()->saveMany($responses);
    }

    /**
     * Add a scope to return only models which match the given field and value.
     *
     * @param $query
     * @param Model $field
     * @param mixed $value
     */
    public function scopeWhereField($query, Model $field, mixed $value)
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
