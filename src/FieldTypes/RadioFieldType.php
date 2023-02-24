<?php

namespace Givebutter\LaravelCustomFields\FieldTypes;

use Illuminate\Validation\Rule;

class RadioFieldType extends FieldType
{
    public function validationRules(array $attributes): array
    {
        return [
            'field_' . $this->field->id => [
                $this->requiredRule($attributes['required']),
                'string',
                'max:255',
                Rule::in($this->field->answers),
            ],
        ];
    }
}
