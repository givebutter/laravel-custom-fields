<?php

namespace Givebutter\LaravelCustomFields\Models;

use Illuminate\Database\Eloquent\Model;

class CustomFieldResponse extends Model
{
    protected $guarded = ['id'];
    
    protected $fillable = [
        'value',
    ];

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
        // We have to do this because the `value` mutator depends on
        // `field_id` being set. If `value` is declared earlie than `field_id`
        // in a create() array, the mutator will blow up.
        $this->attributes = $attributes;

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
        return $this->belongsTo(config('custom-fields.models.custom_field'), 'field_id');
    }

    public function scopeHasValue($query, $value)
    {
        return $query
            ->where('value_str', $value)
            ->orWhere('value_int', $value)
            ->orWhere('value_text', $value);
    }

    private function valueField()
    {
        return self::VALUE_FIELDS[$this->field->type];
    }

    public function getValueAttribute()
    {
        return $this->formatValue(
            $this->attributes[$this->valueField()]
        );
    }

    public function getValueFriendlyAttribute()
    {
        if ($this->field->type === 'checkbox') {
            return $this->value ? 'Checked' : 'Unchecked';
        }

        return $this->value;
    }

    public function formatValue($value)
    {
        // checkboxes send a default value of `on` so we need to booleanize it.
        if ($this->field->type === 'checkbox') {
            $value = !!$value;
        }

        return $value;
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value_int'] = null;
        $this->attributes['value_str'] = null;
        $this->attributes['value_text'] = null;
        unset($this->attributes['value']);

        $this->attributes[$this->valueField()] = $this->formatValue($value);
    }
}
