<?php

namespace Givebutter\Tests\Support;

use Givebutter\LaravelCustomFields\Traits\HasCustomFields;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasCustomFields;
}
