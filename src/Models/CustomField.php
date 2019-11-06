<?php

namespace Givebutter\LaravelCustomFields\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomField extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'answers' => 'array',
    ];

    const FIELD_VALIDATION_RULES = [
        'text' => 'string|max:255',
        'textarea' => 'string',
        'select' => 'string|max:255',
        'number' => 'integer',
        'checkbox' => 'boolean',
        'radio' => 'string:max:255'
    ];

    public function __construct(array $attributes = [])
    {
        $this->bootIfNotBooted();

        $this->initializeTraits();

        $this->syncOriginal();

        $this->fill($attributes);
        $this->table = config('custom-fields.tables.fields', 'custom_fields');
    }

    public function model()
    {
        return $this->morphTo();
    }

    public function responses()
    {
        return $this->hasMany(CustomFieldResponse::class, 'field_id');
    }

    public function getValidationRulesAttribute()
    {
        $required = $this->required ? 'required|' : '';
        $typeRules = CustomField::FIELD_VALIDATION_RULES[$this->type];

        return $required . $typeRules;
    }
}
