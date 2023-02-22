<?php

namespace Givebutter\LaravelCustomFields\States\ResponseType;

use Givebutter\LaravelCustomFields\Enums\ValueFields;
use Givebutter\LaravelCustomFields\Models\CustomFieldResponse;

abstract class ResponseType
{
    public function __construct(
        protected CustomFieldResponse $response,
    ) {
        //
    }

    public function getFormattedValue(mixed $value): mixed
    {
        return $value;
    }

    public function getValueFriendly(): mixed
    {
        return $this->response->value;
    }

    public function setValue(mixed $value): void
    {
        $this->clearValues();

        $this->response->{$this->response->valueField()} = $this->formatValue($value);
    }

    protected function formatValue(mixed $value): mixed
    {
        return $value;
    }

    protected function clearValues(): void
    {
        $attributes = $this->response->getAttributes();

        foreach (ValueFields::cases() as $valueField) {
            $attributes[$valueField->value] = null;
        }

        unset($attributes['value']);

        $this->response->setRawAttributes($attributes);
    }
}
