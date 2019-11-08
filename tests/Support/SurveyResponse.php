<?php

namespace Givebutter\Tests\Support;

use Givebutter\LaravelCustomFields\Traits\HasCustomFieldResponses;
use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    use HasCustomFieldResponses;
}
