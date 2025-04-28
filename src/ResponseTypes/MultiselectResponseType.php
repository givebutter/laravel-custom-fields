<?php

namespace Givebutter\LaravelCustomFields\ResponseTypes;

class MultiselectResponseType extends ResponseType
{
    const string VALUE_FIELD = 'value_json';

    public function getValueFriendly(): string
    {
        if (is_null($this->getValue())) {
            return '';
        }

        return implode(', ', $this->getValue());
    }
}
