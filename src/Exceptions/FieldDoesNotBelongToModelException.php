<?php

namespace Givebutter\LaravelCustomFields\Exceptions;

use Exception;

class FieldDoesNotBelongToModelException extends Exception
{
    /**
     * FieldDoesNotBelongToModelException constructor.
     */
    public function __construct($field, $model)
    {
        $class = get_class($model);

        parent::__construct("Field $field does not belong to $class with id $model->id.");
    }
}
