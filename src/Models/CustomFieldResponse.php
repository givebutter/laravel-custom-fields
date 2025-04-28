<?php

namespace Givebutter\LaravelCustomFields\Models;

use Givebutter\LaravelCustomFields\ResponseTypes\ResponseType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\App;

class CustomFieldResponse extends Model
{
    /**
     * The attributes that aren't mass-assignable.
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that are mass-assignable.
     */
    protected $fillable = [
        'value',
    ];

    protected $casts = [
        'value_json' => 'array',
    ];

    // TODO: find a better way to do this. This breaks all other forms of creating the model.
    public function __construct(array $attributes = [])
    {
        /*
         * We have to do this because the `value` mutator
         * depends on `field_id` being set. If `value`
         * is declared earlier than `field_id` in a
         * create() array, the mutator will fail.
         */

        $this->attributes = $attributes;

        parent::__construct($attributes);

        $this->table = config('custom-fields.tables.field-responses', 'custom_field_responses');
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(config('custom-fields.models.custom-field'), 'field_id');
    }

    public function scopeHasValue(Builder $query, mixed $value): Builder
    {
        return $query->where(function (Builder $query) use ($value) {
            array_map(
                static fn (string $field) => $query->orWhere($field, $value),
                config('custom-fields.value-fields'),
            );
        });
    }

    public function formatValue(mixed $value): mixed
    {
        return $this->response_type->formatValue($value);
    }

    public function getValueAttribute(): mixed
    {
        return $this->response_type->getValue();
    }

    public function setValueAttribute(mixed $value): void
    {
        $this->response_type->setValue($value);
    }

    public function getValueFriendlyAttribute(): mixed
    {
        return $this->response_type->getValueFriendly();
    }

    public function responseType(): Attribute
    {
        return Attribute::get(
            fn (mixed $value, array $attributes) => App::makeWith(ResponseType::class, [
                'type' => $this->field->type,
                'response' => $this,
            ]),
        );
    }
}
