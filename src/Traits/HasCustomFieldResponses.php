<?php

namespace Givebutter\LaravelCustomFields\Traits;

use Givebutter\LaravelCustomFields\Models\CustomFieldResponse;

trait HasCustomFieldResponses
{
    public function customFieldResponses()
    {
        return $this->morphMany(CustomFieldResponse::class, 'model');
    }
}
