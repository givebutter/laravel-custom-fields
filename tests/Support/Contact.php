<?php

namespace Givebutter\Tests\Support;

use Givebutter\LaravelCustomFields\Traits\HasCustomFieldResponses;
use Givebutter\LaravelCustomFields\Traits\HasCustomFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasCustomFields;
    use HasCustomFieldResponses;

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
