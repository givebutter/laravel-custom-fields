<?php

namespace Givebutter\LaravelCustomFields\Traits;

use Givebutter\LaravelCustomFields\Models\CustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait HasCustomFields
{
    public function customFields()
    {
        return $this->morphMany(CustomField::class, 'model');
    }

    public function validateCustomFields(Request $request)
    {
        $validationRules = $this->customFields->mapWithKeys(function ($field) {
            return [$field->title => $field->validationRules];
        })->toArray();

        return Validator::make($request->get(config('custom-fields.form_name', 'custom_fields')), $validationRules);
    }
}
