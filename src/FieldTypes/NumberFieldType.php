<?php

namespace Givebutter\LaravelCustomFields\FieldTypes;

class NumberFieldType extends FieldType
{
    public function validationRules(array $attributes): array
    {
        return [
            $this->validationPrefix.$this->field->id => [
                $this->requiredRule($attributes['required']),
                'integer',
            ],
        ];
    }
}
