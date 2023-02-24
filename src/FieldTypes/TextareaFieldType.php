<?php

namespace Givebutter\LaravelCustomFields\FieldTypes;

class TextareaFieldType extends FieldType
{
    public function validationRules(array $attributes): array
    {
        return [
            'field_' . $this->field->id => [
                $this->requiredRule($attributes['required']),
                'string',
            ],
        ];
    }
}