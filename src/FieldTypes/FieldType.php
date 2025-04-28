<?php

namespace Givebutter\LaravelCustomFields\FieldTypes;

use Givebutter\LaravelCustomFields\Models\CustomField;

abstract class FieldType
{
    protected string $validationPrefix = 'custom_fields.field_';

    protected CustomField $field;

    public function __construct(CustomField $field)
    {
        $this->field = $field;
    }

    public function setValidationPrefix(string $prefix): self
    {
        $this->validationPrefix = $prefix;

        return $this;
    }

    public function validationRules(array $attributes): array
    {
        return [
            $this->validationPrefix.$this->field->id => ['required'],
        ];
    }

    public function validationAttributes(): array
    {
        return [
            $this->validationPrefix.$this->field->id => $this->field->title,
        ];
    }

    public function validationMessages(): array
    {
        return [];
    }

    protected function requiredRule(bool $required): string
    {
        return $required ? 'required' : 'nullable';
    }
}
