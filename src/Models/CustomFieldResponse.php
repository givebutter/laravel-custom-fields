<?php

namespace Givebutter\LaravelCustomFields\Models;

use Givebutter\LaravelCustomFields\Enums\CustomFieldTypes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(CustomField::class, 'field_id');
    }

    public function scopeHasValue(Builder $query, mixed $value): mixed
    {
        return $query
            ->where('value_str', $value)
            ->orWhere('value_int', $value)
            ->orWhere('value_text', $value);
    }

    public function formatValue($value): mixed
    {
        return $this->responseType->getFormattedValue($value);
    }

    public function getValueAttribute(): mixed
    {
        return $this->formatValue(
            $this->attributes[$this->valueField()]
        );
    }

    public function setValueAttribute(mixed $value): void
    {
        $this->responseType->setValue($value);
    }

     public function getValueFriendlyAttribute(): mixed
     {
         return $this->responseType->getValueFriendly();
     }

    public function responseType(): Attribute
    {
        return Attribute::get(
            fn (mixed $value, array $attributes) => CustomFieldTypes::from($this->field->type)
                ->createResponseType($this),
        );
    }

    public function valueField(): string
    {
        return CustomFieldTypes::from($this->field->type)->valueField();
    }
}
