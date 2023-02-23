<?php

namespace Givebutter\LaravelCustomFields\States\FieldType;

use Illuminate\Validation\Rule;

class SelectFieldType extends FieldType
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
//
}
