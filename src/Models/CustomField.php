<?php

namespace Givebutter\LaravelCustomFields\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class CustomField extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'answers' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        $this->bootIfNotBooted();
        $this->initializeTraits();
        $this->syncOriginal();
        $this->fill($attributes);

        $this->table = config('custom-fields.tables.fields', 'custom_fields');
    }

    private function fieldValidationRules()
    {
        return [
            'text' => [
                "string",
                "max:255",
            ],
            'textarea' => [
                "string",
            ],
            'select' => [
                "string",
                "max:255",
                Rule::in($this->answers),
            ],
            'number' => [
                "integer",
            ],
            'checkbox' => [
                "boolean",
            ],
            'radio' => [
                "string",
                "max:255",
                Rule::in($this->answers),
            ],
        ];
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
        $typeRules = $this->fieldValidationRules()[$this->type];

        if ($this->required) {
            array_push($typeRules, 'required');
        }

        return $typeRules;
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($field) {
            $field->order = $field->model->customFields()->count() + 1;
        });
    }
}
