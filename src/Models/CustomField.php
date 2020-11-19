<?php

namespace Givebutter\LaravelCustomFields\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class CustomField extends Model
{
	
	use SoftDeletes;
	
    protected $guarded = ['id'];

    protected $fillable = [
        'type', 
        'title', 
        'description', 
        'answers', 
        'required', 
        'default_value', 
        'order',
    ];

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

    private function fieldValidationRules($required)
    {
        return [
            'text' => [
                'string',
                'max:255',
            ],
            'textarea' => [
                'string',
            ],
            'select' => [
                'string',
                'max:255',
                Rule::in($this->answers),
            ],
            'number' => [
                'integer',
            ],
            'checkbox' => $required ? ['accepted','in:0,1'] : ['in:0,1'],
            'radio' => [
                'string',
                'max:255',
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
        return $this->hasMany(config('custom-fields.models.custom_field_response'), 'field_id');
    }

    public function getValidationRulesAttribute()
    {
        $typeRules = $this->fieldValidationRules($this->required)[$this->type];
        array_unshift($typeRules, $this->required ? 'required' : 'nullable');
 
        return $typeRules;
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($field) {
            $lastFieldOnCurrentModel = $field->model->customFields()->orderBy('order', 'desc')->first();
            $field->order = ($lastFieldOnCurrentModel ? $lastFieldOnCurrentModel->order : 0) + 1;
        });
    }
}
