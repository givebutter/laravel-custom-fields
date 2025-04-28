<?php

namespace Givebutter\LaravelCustomFields\ResponseTypes;

class MultiCheckboxResponseType extends ResponseType
{
    const VALUE_FIELD = 'value_json';

    public function formatValue(mixed $value): mixed
    {
        if (! is_array($value)) {
            return [$value];
        }

        return $value;
    }

    public function getValue(): mixed
    {
        return $this->formatValue(
            $this->response->getAttribute($this::VALUE_FIELD)
        );
    }

    public function getValueFriendly(): mixed
    {
        $answers = $this->response->field->answers;
        $values = $this->response->value;
        $list = [];

        if (! is_array($values)) {
            $values = [$values];
        }

        foreach ($values as $value) {
            if (isset($answers[$value])) {
                $list[] = $answers[$value];
            }
        }

        return implode(', ', $list);
    }
}
