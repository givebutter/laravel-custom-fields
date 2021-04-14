<?php

namespace Givebutter\LaravelCustomFields\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class CustomField extends Model
{
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_NUMBER = 'number';
    const TYPE_RADIO = 'radio';
    const TYPE_SELECT = 'select';
    const TYPE_TEXT = 'text';
    const TYPE_TEXTAREA = 'textarea';

	use SoftDeletes, HasFactory;
	
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
        parent::__construct($attributes);

        $this->table = config('custom-fields.tables.fields', 'custom_fields');
    }

    private function fieldValidationRules($required)
    {
        return [
            self::TYPE_CHECKBOX => $required ? ['accepted','in:0,1'] : ['in:0,1'],
            self::TYPE_NUMBER => [
                'integer',
            ],
            self::TYPE_SELECT => [
                'string',
                'max:255',
                Rule::in($this->answers),
            ],
            self::TYPE_RADIO => [
                'string',
                'max:255',
                Rule::in($this->answers),
            ],
            self::TYPE_TEXT => [
                'string',
                'max:255',
            ],
            self::TYPE_TEXTAREA => [
                'string',
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
