<?php

namespace Givebutter\LaravelCustomFields\States\FieldType;

class NumberFieldType extends FieldType
{
    public function validationRules(array $attributes): array
    {
        return [
            'field_' . $this->field->id => [
                $this->requiredRule($attributes['required']),
                'integer',
            ],
        ];
    }
}
