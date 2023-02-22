<?php

namespace Givebutter\LaravelCustomFields\Enums;

use Givebutter\LaravelCustomFields\Models\CustomField;
use Givebutter\LaravelCustomFields\Models\CustomFieldResponse;
use Givebutter\LaravelCustomFields\States\FieldType;
use Givebutter\LaravelCustomFields\States\ResponseType;
use Illuminate\Validation\Rule;

enum CustomFieldTypes: string
{
    case CHECKBOX = 'checkbox';
    case NUMBER = 'number';
    case RADIO = 'radio';
    case SELECT = 'select';
    case TEXT = 'text';
    case TEXTAREA = 'textarea';

    public function valueField(): string
    {
        return match ($this) {
            self::TEXT, self::RADIO, self::SELECT => ValueFields::STR->value,
            self::NUMBER, self::CHECKBOX => ValueFields::INT->value,
            self::TEXTAREA => ValueFields::TEXT->value,
        };
    }

    public function createFieldType(CustomField $field): FieldType\FieldType
    {
        return match ($this) {
            self::CHECKBOX => new FieldType\CheckboxFieldType($field),
            self::NUMBER => new FieldType\NumberFieldType($field),
            self::RADIO => new FieldType\RadioFieldType($field),
            self::SELECT => new FieldType\SelectFieldType($field),
            self::TEXT => new FieldType\TextFieldType($field),
            self::TEXTAREA => new FieldType\TextareaFieldType($field),
        };
    }

    public function createResponseType(CustomFieldResponse $response): ResponseType\ResponseType
    {
        return match ($this) {
            self::CHECKBOX => new ResponseType\CheckboxResponseType($response),
            self::NUMBER => new ResponseType\NumberResponseType($response),
            self::RADIO => new ResponseType\RadioResponseType($response),
            self::SELECT => new ResponseType\SelectResponseType($response),
            self::TEXT => new ResponseType\TextResponseType($response),
            self::TEXTAREA => new ResponseType\TextareaResponseType($response),
        };
    }

    public function getFieldValidationRules(CustomField $field, bool $required): array
    {
        return match ($this) {
            self::CHECKBOX => $required ? ['accepted', 'in:0,1'] : ['in:0,1'],
            self::NUMBER => ['integer'],
            self::SELECT => ['string', 'max:255', Rule::in($field->answers)],
            self::RADIO => ['string', 'max:255', Rule::in($field->answers)],
            self::TEXT => ['string', 'max:255'],
            self::TEXTAREA => ['string'],
        };
    }
}
