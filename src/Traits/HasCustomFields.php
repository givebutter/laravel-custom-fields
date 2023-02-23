<?php

namespace Givebutter\LaravelCustomFields\Traits;

use Givebutter\LaravelCustomFields\Exceptions\FieldDoesNotBelongToModelException;
use Givebutter\LaravelCustomFields\Exceptions\WrongNumberOfFieldsForOrderingException;
use Givebutter\LaravelCustomFields\Models\CustomField;
use Givebutter\LaravelCustomFields\Validators\CustomFieldValidator;
use Illuminate\Http\Request;

trait HasCustomFields
{
    /**
     * Get the custom fields belonging to this model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function customFields()
    {
        return $this->morphMany(CustomField::class, 'model')->orderBy('order');
    }

    /**
     * Validate the given custom fields.
     *
     * @param $fields
     * @return CustomFieldValidator
     */
    public function validateCustomFields($fields)
    {
        if ($fields instanceof Request) {
            return $this->validateCustomFieldsRequest($fields);
        }

        $customFields = $this->customFields()
            ->whereNull('archived_at')
            ->get();

        $validationRules = $customFields
            ->map(fn ($field) => $field->validation_rules)
            ->flatMap(fn ($rules) => $rules)
            ->toArray();

        $validationAttributes = $customFields
            ->map(fn ($field) => $field->validation_attributes)
            ->flatMap(fn ($rules) => $rules)
            ->toArray();

        $keyAdjustedFields = collect($fields)
            ->mapWithKeys(function ($field, $key) use ($customFields) {
                $id = $customFields->firstOrFail('id', $key)->id;

                return ["field_{$id}" => $field];
            })->toArray();

        return new CustomFieldValidator($keyAdjustedFields, $validationRules, $validationAttributes);
    }

    /**
     * Validate the given custom field request.
     *
     * @param Request $request
     * @return CustomFieldValidator
     */
    public function validateCustomFieldsRequest(Request $request)
    {
        return $this->validateCustomFields($request->get(config('custom-fields.form_name', 'custom_fields')));
    }

    /**
     * Handle a request to order the fields.
     *
     * @param $fields
     * @throws FieldDoesNotBelongToModelException
     * @throws WrongNumberOfFieldsForOrderingException
     */
    public function order($fields)
    {
        $fields = collect($fields);

        if ($fields->count() !== $this->customFields()->count()) {
            throw new WrongNumberOfFieldsForOrderingException($fields->count(), $this->customFields()->count());
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
