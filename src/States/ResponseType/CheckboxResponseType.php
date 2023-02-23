<?php

namespace Givebutter\LaravelCustomFields\States\ResponseType;

class CheckboxResponseType extends ResponseType
{
    public function formatValue(mixed $value): mixed
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function getValueFriendly(): mixed
    {
        return $this->response->value ? 'Checked' : 'Unchecked';
    }
}
