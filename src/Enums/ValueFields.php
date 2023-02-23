<?php

namespace Givebutter\LaravelCustomFields\Enums;

enum ValueFields: string
{
    case INT = 'value_int';
    case STR = 'value_str';
    case TEXT = 'value_text';
    case DATE_START = 'value_datetime_start';
    case DATE_END = 'value_datetime_end';
}
