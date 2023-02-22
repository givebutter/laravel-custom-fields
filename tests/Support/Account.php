<?php

namespace Givebutter\Tests\Support;

use Givebutter\LaravelCustomFields\Traits\HasCustomFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasCustomFields;

    public function surveys(): HasMany
    {
        return $this->hasMany(Survey::class);
    }
}
