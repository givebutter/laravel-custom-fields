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

    public function scopeWhereField($query, CustomField $field, $value)
    {
        return $query->whereHas('responses', function ($subQuery) use ($field, $value) {
            $subQuery
                ->where('field_id', $field->id)
                ->whereHasValue($value);
        });
    }
}
