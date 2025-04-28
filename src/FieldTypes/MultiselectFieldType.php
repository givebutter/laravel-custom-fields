<?php

namespace Givebutter\LaravelCustomFields\FieldTypes;

use Illuminate\Validation\Rule;

class MultiselectFieldType extends FieldType
{
    public function validationRules(array $attributes): array
    {
        return [
            $this->validationPrefix.$this->field->id => array_filter([
                $this->requiredRule($attributes['required']),
                'array',
                $attributes['required'] ? 'min:1' : null,
            ]),
            $this->validationPrefix.$this->field->id.'.*' => [
                'required',
                'string',
                'max:255',
                Rule::in($this->field->answers),
            ],
        ];
    }
}
