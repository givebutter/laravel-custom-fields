<?php

namespace Givebutter\LaravelCustomFields\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CustomFieldResponse extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]|bool
     */
    protected $guarded = [
        'id',
    ];

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
        CustomField::TYPE_TEXT => 'value_str',
        CustomField::TYPE_RADIO => 'value_str',
        CustomField::TYPE_SELECT => 'value_str',
        CustomField::TYPE_NUMBER => 'value_int',
        CustomField::TYPE_CHECKBOX => 'value_int',
        CustomField::TYPE_TEXTAREA => 'value_text',
    ];

    /**
     * CustomFieldResponse constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        /*
         * We have to do this because the `value` mutator
         * depends on `field_id` being set. If `value`
         * is declared earlier than `field_id` in a
         * create() array, the mutator will fail.
         */

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
        // Checkboxes send a default value of "on", so we need to booleanize the value
        if ($this->field->type === 'checkbox') {
            $value = ! ! $value;
        }

        return $value;
    }

    /**
     * Get/Set the `value` attribute.
     *
     * @return Attribute
     */
    public function value(): Attribute
    {
        return new Attribute(
            get: function ($value, $attributes) {
                return $this->formatValue(
                    $attributes[$this->valueField()]
                );
            },
            set: function ($value) {
                unset($this->attributes['value']);

                return [
                    'value_int' =>  $this->valueField() === 'value_int'  ? $this->formatValue($value) : null,
                    'value_str' =>  $this->valueField() === 'value_str'  ? $this->formatValue($value) : null,
                    'value_text' => $this->valueField() === 'value_text' ? $this->formatValue($value) : null,
                ];
            },
        );
    }

    /**
     * Get the `value_friendly` attribute.
     *
     * @return Attribute
     */
    public function valueFriendly(): Attribute
    {
        return Attribute::get(function () {
            if ($this->field->type === 'checkbox') {
                return $this->value ? 'Checked' : 'Unchecked';
            }

            return $this->value;
        });
    }

    /**
     * @return string
     */
    protected function valueField()
    {
        return self::VALUE_FIELDS[$this->field->type];
    }
}
