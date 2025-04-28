<?php

namespace Givebutter\LaravelCustomFields\Enums;

enum CustomFieldType: string
{
    case CHECKBOX = 'checkbox';
    case NUMBER = 'number';
    case RADIO = 'radio';
    case SELECT = 'select';
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case MULTISELECT = 'multiselect';

    public function requiresAnswers(): bool
    {
        return in_array($this, [
            self::RADIO,
            self::SELECT,
            self::MULTISELECT,
        ]);
    }
}
