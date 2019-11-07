<?php

namespace Givebutter\LaravelCustomFields\Models;

use Illuminate\Database\Eloquent\Model;

class CustomFieldResponse extends Model
{
    protected $guarded = ['id'];

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
        return $this->value_int
            ?? $this->value_str
            ?? $this->value_text;
    }

    public function setValueAttribute($value)
    {
        if (gettype($value) === 'string') {
            if (strlen($value) > 255) {
                return $this->update([
                    'value_text' => $value,
                    'value_str' => null,
                    'value_int' => null,
                ]);
            }

            return $this->update([
                'value_text' => null,
                'value_str' => $value,
                'value_int' => null,
            ]);
        }

        return $this->update([
            'value_text' => null,
            'value_str' => null,
            'value_int' => $value,
        ]);;
    }
}
