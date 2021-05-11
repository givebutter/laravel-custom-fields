<?php

namespace Givebutter\LaravelCustomFields\Models;

use Illuminate\Database\Eloquent\Model;

class CustomFieldResponse extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]|bool
     */
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'value',
    ];

    /**
     * @var string[]
     */
    const VALUE_FIELDS = [
        CustomField::TYPE_NUMBER => 'value_int',
        CustomField::TYPE_CHECKBOX => 'value_int',
        CustomField::TYPE_RADIO => 'value_str',
        CustomField::TYPE_SELECT => 'value_str',
        CustomField::TYPE_TEXT => 'value_str',
        CustomField::TYPE_TEXTAREA => 'value_text',
    ];

    /**
     * CustomFieldResponse constructor.
     *
     * @param array $attributes
     */
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

    /**
     * Get the morphable model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model()
    {
        return $this->morphTo();
    }

    /**
     * Get the field belonging to the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function field()
    {
        return $this->belongsTo(CustomField::class, 'field_id');
    }

    /**
     * Add a scope to return models that match the given value.
     *
     * @param $query
     * @param $value
     * @return mixed
     */
    public function scopeHasValue($query, $value)
    {
        return $query
            ->where('value_str', $value)
            ->orWhere('value_int', $value)
            ->orWhere('value_text', $value);
    }

    /**
     * @param $value
     * @return bool|mixed
     */
    public function formatValue($value)
    {
        // checkboxes send a default value of `on` so we need to booleanize it.
        if ($this->field->type === 'checkbox') {
            $value = ! ! $value;
        }

        return $value;
    }

    /**
     * @return bool|mixed
     */
    public function getValueAttribute()
    {
        return $this->formatValue(
            $this->attributes[$this->valueField()]
        );
    }

    /**
     * @return mixed|string
     */
    public function getValueFriendlyAttribute()
    {
        if ($this->field->type === 'checkbox') {
            return $this->value ? 'Checked' : 'Unchecked';
        }

        return $this->value;
    }

    /**
     * @param $value
     */
    public function setValueAttribute($value)
    {
        $this->attributes['value_int'] = null;
        $this->attributes['value_str'] = null;
        $this->attributes['value_text'] = null;
        unset($this->attributes['value']);

        $this->attributes[$this->valueField()] = $this->formatValue($value);
    }

    /**
     * @return string
     */
    protected function valueField()
    {
        return self::VALUE_FIELDS[$this->field->type];
    }
}
