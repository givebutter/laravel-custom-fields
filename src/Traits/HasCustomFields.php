<?php

namespace Givebutter\LaravelCustomFields\Traits;

use Givebutter\LaravelCustomFields\Exceptions\FieldDoesNotBelongToModelException;
use Givebutter\LaravelCustomFields\Exceptions\WrongNumberOfFieldsForOrderingException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

trait HasCustomFields
{
    public function customFields(): MorphMany
    {
        return $this->morphMany(config('custom-fields.models.custom-field'), 'model')->orderBy('order');
    }

    public function validateCustomFields(Request|array|null $fields): Validator
    {
        if ($fields instanceof Request) {
            return $this->validateCustomFieldsRequest($fields);
        }

        $customFields = $this->customFields()
            ->whereNull('archived_at')
            ->get();

        return new Validator(
            app('translator'),
            $this->validationData($fields, $customFields),
            $this->validationRules($customFields),
            [],
            $this->validationAttributes($customFields),
        );
    }

    public function validateCustomFieldsRequest(Request $request): Validator
    {
        return $this->validateCustomFields($request->get(config('custom-fields.form-name', 'custom_fields')));
    }

    /**
     * Handle a request to order the fields.
     *
     * @throws FieldDoesNotBelongToModelException
     * @throws WrongNumberOfFieldsForOrderingException
     */
    public function order($fields): void
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

    protected function validationData(?array $fields, Collection $customFields): array
    {
        return collect($fields)
            ->intersectByKeys(array_flip($customFields->modelKeys()))
            ->mapWithKeys(fn ($v, $k) => ["field_$k" => $v])
            ->toArray();
    }

    protected function validationRules(Collection $fields): array
    {
        return $fields
            ->map(function ($field): array {
                $field->field_type->setValidationPrefix('field_');

                return $field->validation_rules;
            })
            ->flatMap(fn (array $ruleset): array => $ruleset)
            ->toArray();
    }

    protected function validationAttributes(Collection $fields): array
    {
        return $fields
            ->map(function ($field): array {
                $field->field_type->setValidationPrefix('field_');

                return $field->validation_attributes;
            })
            ->flatMap(fn (array $rules): array => $rules)
            ->toArray();
    }
}
