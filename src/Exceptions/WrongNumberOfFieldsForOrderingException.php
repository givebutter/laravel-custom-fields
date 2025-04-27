<?php

namespace Givebutter\LaravelCustomFields\Exceptions;

use Exception;

class WrongNumberOfFieldsForOrderingException extends Exception
{
    /**
     * WrongNumberOfFieldsForOrderingException constructor.
     */
    public function __construct($given, $expected)
    {
        parent::__construct("Wrong number of fields passed for ordering. $given given, $expected expected.");
    }
}
