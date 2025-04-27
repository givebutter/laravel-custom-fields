<?php

namespace Givebutter\LaravelCustomFields\ResponseTypes;

class CheckboxResponseType extends ResponseType
{
    const string VALUE_FIELD = 'value_int';

    public function formatValue(mixed $value): mixed
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function getValueFriendly(): string
    {
        return $this->response->value ? 'Checked' : 'Unchecked';
    }
}
