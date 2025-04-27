<?php

namespace Givebutter\LaravelCustomFields\Interfaces;

interface CustomFieldable
{
    /**
     * Get the custom fields belonging to this model.
     */
    public function customFields(): mixed;
}
