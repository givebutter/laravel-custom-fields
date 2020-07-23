<?php

namespace Givebutter\LaravelCustomFields\Validators;

use Givebutter\LaravelCustomFields\Models\CustomField;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class CustomFieldValidator extends Validator
{
    public function __construct($data, $rules)
    {
        parent::__construct(
            app('translator'),
            $data,
            $rules
        );
    }

    protected function getCustomFieldModel()
    {
        return config(
            'custom-fields.models.CustomField',
            CustomField::class
        );
    }

    protected function replaceAttributePlaceholder($message, $value)
    {
        $fieldId = (int) Str::after($value, 'field ');
        $fieldTitle = $this->getCustomFieldModel()::find($fieldId)->title;
        $replacementString = "`{$fieldTitle}` field";

        return str_replace(
            [':attribute', ':ATTRIBUTE', ':Attribute'],
            [$replacementString, Str::upper($replacementString), Str::ucfirst($replacementString)],
            $message
        );
    }
}
