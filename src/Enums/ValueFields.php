<?php

namespace Givebutter\LaravelCustomFields\Enums;

enum ValueFields: string
{
    case INT = 'value_int';
    case STR = 'value_str';
    case TEXT = 'value_text';
    case JSON = 'value_json';
}
