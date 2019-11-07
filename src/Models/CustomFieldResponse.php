<?php

namespace Givebutter\LaravelCustomFields\Models;

use Illuminate\Database\Eloquent\Model;

class CustomFieldResponse extends Model
{
    protected $guarded = ['id'];

    const VALUE_FIELDS = [
        'number'   => 'value_int',
        'checkbox' => 'value_int',
        'radio'    => 'value_str',
        'select'   => 'value_str',
        'text'     => 'value_str',
        'textarea' => 'value_text',
    ];

    public function __construct(array $attributes = [])
    {
        $this->bootIfNotBooted();

        $this->initializeTraits();

        $this->syncOriginal();

        $this->fill($attributes);
        $this->table = config('custom-fields.tables.field-responses', 'custom_field_responses');
    }

    public function model()
    {
        return $this->morphTo();
    }

    public function field()
    {
        return $this->belongsTo(CustomField::class, 'field_id');
    }

    public function scopeHasValue($query, $value)
    {
        return $query
            ->where('value_str', $value)
            ->orWhere('value_int', $value)
            ->orWhere('value_text', $value);
    }

    public function getValueAttribute()
    {
        return $this->attributes[self::VALUE_FIELDS[$this->field->type]];
    }

    public function setValueAttribute($value)
    {
        $this->attributes[self::VALUE_FIELDS[$this->field->type]] = $value;
    }
}
