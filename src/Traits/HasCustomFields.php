<?php

namespace Givebutter\LaravelCustomFields\Traits;

use Givebutter\LaravelCustomFields\Exceptions\FieldDoesNotBelongToModelException;
use Givebutter\LaravelCustomFields\Exceptions\WrongNumberOfFieldsForOrderingException;
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
            return ['field_' . $field->id => $field->validationRules];
        })->toArray();

        $keyAdjustedFields = collect($request->get(config('custom-fields.form_name', 'custom_fields')))
            ->mapWithKeys(function ($field, $key) {
                return ["field_{$key}" => $field];
            })->toArray();

        return Validator::make($keyAdjustedFields, $validationRules);
    }

    public function order($fields)
    {
        // Allows us to pass in either an array or collection
        $fields = collect($fields);

        if ($fields->count() !== $this->customFields()->count()) {
            throw new WrongNumberOfFieldsForOrderingException(
                $fields->count(),
                $this->customFields()->count()
            );
        }

        $fields->each(function ($id, $index) {
            $customField = $this->customFields()->find($id);

            if (! $customField) {
                throw new FieldDoesNotBelongToModelException($id, $this);
            } 
            
            $customField->update(['order' => $index + 1]);
        });
    }
}
