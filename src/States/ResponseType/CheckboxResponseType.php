<?php

namespace Givebutter\LaravelCustomFields\States\ResponseType;

class CheckboxResponseType extends ResponseType
{
    public function getFormattedValue(mixed $value): mixed
    {
        return (bool) $value;
    }

    public function getValueFriendly(): mixed
    {
        return $this->response->value ? 'Checked' : 'Unchecked';
    }
}
