<?php

namespace Givebutter\LaravelCustomFields\Models;

use Givebutter\LaravelCustomFields\Collections\CustomFieldCollection;
use Givebutter\LaravelCustomFields\FieldTypes\FieldType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class CustomField extends Model
{
    use HasFactory, SoftDeletes;

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
        'group',
        'type',
        'title',
        'description',
        'answers',
        'required',
        'default_value',
        'order',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'answers' => 'array',
        'archived_at' => 'datetime',
    ];

    /**
     * CustomField constructor.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('custom-fields.tables.fields', 'custom_fields');
    }

    /**
     * Bootstrap the model and its traits.
     */
    protected static function boot(): void
    {
        parent::boot();

        self::creating(static function ($field) {
            $lastFieldOnCurrentModel = $field->model
                ->customFields()
                ->reorder()
                ->orderByDesc('order')
                ->first();

            $field->order = ($lastFieldOnCurrentModel ? $lastFieldOnCurrentModel->order : 0) + 1;
        });
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function responses(): HasMany
    {
        return $this->hasMany(config('custom-fields.models.custom-field-response'), 'field_id');
    }

    public function archive(): self
    {
        $this->forceFill([
            'archived_at' => now(),
        ])->save();

        return $this;
    }

    public function unarchive(): self
    {
        $this->forceFill([
            'archived_at' => null,
        ])->save();

        return $this;
    }

    public function validationRules(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attributes) => $this->field_type->validationRules($attributes),
        );
    }

    public function validationAttributes(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attributes) => $this->field_type->validationAttributes(),
        );
    }

    public function validationMessages(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attributes) => $this->field_type->validationMessages(),
        );
    }

    public function fieldType(): Attribute
    {
        return Attribute::get(
            fn ($value, array $attributes) => App::makeWith(FieldType::class, [
                'type' => $attributes['type'],
                'field' => $this,
            ]),
        );
    }

    /**
     * Create a new Eloquent Collection instance.
     */
    public function newCollection(array $models = []): CustomFieldCollection
    {
        return new CustomFieldCollection($models);
    }
}
