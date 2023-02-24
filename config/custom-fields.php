<?php

return [
    'form_name' => env('CUSTOM_FIELDS_FORM_NAME', 'custom_fields'),

    'tables' => [
        'fields' => env('CUSTOM_FIELDS_TABLE', 'custom_fields'),
        'field-responses' => env('CUSTOM_FIELD_RESPONSES_TABLE', 'custom_field_responses'),
    ],

    /*
    | -------------------------------------------------------------------
    | Field Types
    | -------------------------------------------------------------------
    |
    | The list of all custom field types. You can register
    | your own custom field types here. Make sure to also
    | register the corresponding response type below.
    */
    'fields' => [
        'checkbox' => Givebutter\LaravelCustomFields\States\FieldType\CheckboxFieldType::class,
        'daterange' => Givebutter\LaravelCustomFields\States\FieldType\DateRangeFieldType::class,
        'number' => Givebutter\LaravelCustomFields\States\FieldType\NumberFieldType::class,
        'radio' => Givebutter\LaravelCustomFields\States\FieldType\RadioFieldType::class,
        'select' => Givebutter\LaravelCustomFields\States\FieldType\SelectFieldType::class,
        'textarea' => Givebutter\LaravelCustomFields\States\FieldType\TextareaFieldType::class,
        'text' => Givebutter\LaravelCustomFields\States\FieldType\TextFieldType::class,
    ],

    /*
    | -------------------------------------------------------------------
    | Response Types
    | -------------------------------------------------------------------
    |
    | The list of all custom field response types. You can register
    | your own custom field responses here. Make sure to also
    | register the corresponding field type above.
    */
    'responses' => [
        'checkbox' => Givebutter\LaravelCustomFields\States\ResponseType\CheckboxResponseType::class,
        'daterange' => Givebutter\LaravelCustomFields\States\ResponseType\DateRangeResponseType::class,
        'number' => Givebutter\LaravelCustomFields\States\ResponseType\NumberResponseType::class,
        'radio' => Givebutter\LaravelCustomFields\States\ResponseType\RadioResponseType::class,
        'select' => Givebutter\LaravelCustomFields\States\ResponseType\SelectResponseType::class,
        'textarea' => Givebutter\LaravelCustomFields\States\ResponseType\TextareaResponseType::class,
        'text' => Givebutter\LaravelCustomFields\States\ResponseType\TextResponseType::class,
    ],

    /*
    | -------------------------------------------------------------------
    | Value Fields
    | -------------------------------------------------------------------
    |
    | The list of all value columns that can hold a response value on the
    | custom_field_responses table.
    */
    'value-fields' => [
        'value_int',
        'value_str',
        'value_text',
    ],

];
