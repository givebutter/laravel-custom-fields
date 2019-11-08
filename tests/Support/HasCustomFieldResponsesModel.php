<?php

namespace Givebutter\Tests\Support;

use Givebutter\LaravelCustomFields\Traits\HasCustomFieldResponses;
use Illuminate\Database\Eloquent\Model;

class HasCustomFieldResponsesModel extends Model
{
    use HasCustomFieldResponses;
}
