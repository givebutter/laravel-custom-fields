<?php

namespace Givebutter\LaravelCustomFields\FieldTypes;

class CheckboxFieldType extends FieldType
{
    public function validationRules(array $attributes): array
    {
        return [
            $this->validationPrefix . $this->field->id => [
                $this->requiredRule($attributes['required']),
                $attributes['required'] ? 'accepted' : function ($fail, $value, $attribute) {
                    if (! in_array($value, ['on', 'off', 'yes', 'no', 0, 1, '0', '1', true, false, 'true', 'false'])) {
                        $fail('The :attribute response cannot be understood.');
                    }
                },
            ],
        ];
    }
}
