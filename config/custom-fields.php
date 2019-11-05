<?php

return [
    'form_name' => env('CUSTOM_FIELDS_FORM_NAME', 'custom_fields'),
    'tables' => [
        'fields' => env('CUSTOM_FIELDS_TABLE', 'custom_fields'),
        'field-responses' => env('CUSTOM_FIELD_RESPONSES_TABLE', 'custom_field_responses'),
    ],
];
