<?php

namespace Givebutter\LaravelCustomFields\States\ResponseType;

use Givebutter\LaravelCustomFields\Enums\CustomFieldTypes;
use Givebutter\LaravelCustomFields\Enums\ValueFields;
use Givebutter\LaravelCustomFields\Models\CustomFieldResponse;

abstract class ResponseType
{
    public function __construct(
        protected CustomFieldResponse $response,
    ) {
        //
    }

    public function formatValue(mixed $value): mixed
    {
        return $value;
    }

    public function getValue(): mixed
    {
        return $this->formatValue(
            $this->response->getAttribute($this->valueField())
        );
    }

    public function getValueFriendly(): mixed
    {
        return $this->response->value;
    }

    public function setValue(mixed $value): void
    {
        $this->clearValues();

        $this->response->{$this->valueField()} = $this->formatValue($value);
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

    protected function valueField(): string
    {
        return CustomFieldTypes::from($this->response->field->type)->valueField();
    }
}
