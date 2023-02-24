<?php

namespace Givebutter\LaravelCustomFields\Models;

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
    use SoftDeletes, HasFactory;

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
     *
     * @var array
     */
    protected $casts = [
        'answers' => 'array',
        'archived_at' => 'datetime',
    ];

    /**
     * CustomField constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('custom-fields.tables.fields', 'custom_fields');
    }

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        self::creating(function ($field) {
            $lastFieldOnCurrentModel = $field->model->customFields()->orderByDesc('order')->first();

            $field->order = ($lastFieldOnCurrentModel ? $lastFieldOnCurrentModel->order : 0) + 1;
        });
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function responses(): HasMany
    {
        return $this->hasMany(CustomFieldResponse::class, 'field_id');
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
            get: fn ($value, $attributes) => $this->fieldType->validationRules($attributes),
        );
    }

    public function validationAttributes(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attributes) => $this->fieldType->validationAttributes(),
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
}
