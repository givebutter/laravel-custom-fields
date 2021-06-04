<?php

namespace Givebutter\LaravelCustomFields\Validators;

use Givebutter\LaravelCustomFields\Models\CustomField;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class CustomFieldValidator extends Validator
{
    /**
     * Create a new Validator instance.
     *
     * @param array $data
     * @param array $rules
     */
    public function __construct(array $data, array $rules)
    {
        parent::__construct(
            app('translator'),
            $data,
            $rules
        );
    }

    /**
     * Replace the :attribute placeholder in the given message.
     *
     * @param string $message
     * @param string $value
     * @return string
     */
    protected function replaceAttributePlaceholder($message, $value)
    {
        $fieldId = (int) Str::after($value, 'field ');
        $fieldTitle = CustomField::find($fieldId)->title;
        $replacementString = "`{$fieldTitle}` field";

        return str_replace(
            [':attribute', ':ATTRIBUTE', ':Attribute'],
            [$replacementString, Str::upper($replacementString), Str::ucfirst($replacementString)],
            $message
        );
    }
}
