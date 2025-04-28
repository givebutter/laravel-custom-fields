<?php

return [
    'form-name' => env('CUSTOM_FIELDS_FORM_NAME', 'custom_fields'),

    'tables' => [
        'fields' => env('CUSTOM_FIELDS_TABLE', 'custom_fields'),
        'field-responses' => env('CUSTOM_FIELD_RESPONSES_TABLE', 'custom_field_responses'),
    ],

    'models' => [
        'custom-field' => \Givebutter\LaravelCustomFields\Models\CustomField::class,
        'custom-field-response' => \Givebutter\LaravelCustomFields\Models\CustomFieldResponse::class,
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
        'checkbox' => \Givebutter\LaravelCustomFields\FieldTypes\CheckboxFieldType::class,
        'number' => \Givebutter\LaravelCustomFields\FieldTypes\NumberFieldType::class,
        'radio' => \Givebutter\LaravelCustomFields\FieldTypes\RadioFieldType::class,
        'select' => \Givebutter\LaravelCustomFields\FieldTypes\SelectFieldType::class,
        'textarea' => \Givebutter\LaravelCustomFields\FieldTypes\TextareaFieldType::class,
        'text' => \Givebutter\LaravelCustomFields\FieldTypes\TextFieldType::class,
        'multiselect' => \Givebutter\LaravelCustomFields\FieldTypes\MultiselectFieldType::class,
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
        'checkbox' => \Givebutter\LaravelCustomFields\ResponseTypes\CheckboxResponseType::class,
        'number' => \Givebutter\LaravelCustomFields\ResponseTypes\NumberResponseType::class,
        'radio' => \Givebutter\LaravelCustomFields\ResponseTypes\RadioResponseType::class,
        'select' => \Givebutter\LaravelCustomFields\ResponseTypes\SelectResponseType::class,
        'textarea' => \Givebutter\LaravelCustomFields\ResponseTypes\TextareaResponseType::class,
        'text' => \Givebutter\LaravelCustomFields\ResponseTypes\TextResponseType::class,
        'multiselect' => \Givebutter\LaravelCustomFields\ResponseTypes\MultiselectResponseType::class,
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
        'value_json',
    ],

];
