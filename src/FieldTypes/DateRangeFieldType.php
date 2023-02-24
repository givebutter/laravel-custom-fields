<?php

namespace Givebutter\LaravelCustomFields\FieldTypes;

class DateRangeFieldType extends FieldType
{
    public function validationRules(array $attributes): array
    {
        return [
            'field_' . $this->field->id => [
                $this->requiredRule($attributes['required']),
                'array',
                'size:2',
            ],
            'field_' . $this->field->id . '.0' => [
                'date',
                'before:field_' . $this->field->id . '.1',
            ],
            'field_' . $this->field->id . '.1' => [
                'date',
            ],
        ];
    }

    public function validationAttributes(): array
    {
        $id = $this->field->id;

        return [
            "field_{$id}" => $this->field->title,
            "field_{$id}.0" => "{$this->field->title}'s Start Date",
            "field_{$id}.1" => "{$this->field->title}'s End Date",
        ];
    }
}
