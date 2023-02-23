<?php

namespace Givebutter\LaravelCustomFields\States\FieldType;

use Givebutter\LaravelCustomFields\Enums\CustomFieldTypes;
use Givebutter\LaravelCustomFields\Models\CustomField;

abstract class FieldType
{
    public function __construct(
        protected CustomField $field,
    ) {
        //
    }

    public function validationRules(array $attributes): array
    {
        return CustomFieldTypes::from($attributes['type'])
            ->getFieldValidationRules($this->field, $attributes['required']);
    }

    public function validationAttributes(): array
    {
        return [
            'field_' . $this->field->id => $this->field->title,
        ];
    }

    protected function requiredRule(bool $required): string
    {
        return $required ? 'required' : 'nullable';
    }
}
