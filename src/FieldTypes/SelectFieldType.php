<?php

namespace Givebutter\LaravelCustomFields\FieldTypes;

use Illuminate\Validation\Rule;

class SelectFieldType extends FieldType
{
    public function validationRules(array $attributes): array
    {
        return [
            $this->validationPrefix . $this->field->id => [
                $this->requiredRule($attributes['required']),
                'string',
                'max:255',
                Rule::in($this->field->answers),
            ],
        ];
    }
}
