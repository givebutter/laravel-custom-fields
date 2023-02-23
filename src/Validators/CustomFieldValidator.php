<?php

namespace Givebutter\LaravelCustomFields\Validators;

use Illuminate\Validation\Validator;

class CustomFieldValidator extends Validator
{
    /**
     * Create a new Validator instance.
     *
     * @param array $data
     * @param array $rules
     */
    public function __construct(array $data, array $rules, array $attributes)
    {
        parent::__construct(
            app('translator'),
            $data,
            $rules,
            attributes: $attributes,
        );
    }
}
