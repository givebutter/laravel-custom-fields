<?php

namespace Givebutter\Tests\Support;

use Givebutter\LaravelCustomFields\Traits\HasCustomFields;
use Illuminate\Database\Eloquent\Model;

class HasCustomFieldsModel extends Model
{
    use HasCustomFields;
}
