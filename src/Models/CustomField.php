<?php

namespace Givebutter\LaravelCustomFields\Models;

use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        $this->bootIfNotBooted();

        $this->initializeTraits();

        $this->syncOriginal();

        $this->fill($attributes);
        $this->table = config('custom-fields.tables.fields', 'custom_fields');
    }

    public function model()
    {
        return $this->morphTo();
    }

    public function responses()
    {
        return $this->hasMany(CustomFieldResponse::class, 'field_id');
    }
}
