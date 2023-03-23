<?php

namespace Givebutter\LaravelCustomFields\ResponseTypes;

class CheckboxResponseType extends ResponseType
{
    const VALUE_FIELD = 'value_int';

    public function formatValue(mixed $value): mixed
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function getValueFriendly(): mixed
    {
        return $this->response->value ? 'Checked' : 'Unchecked';
    }
}
