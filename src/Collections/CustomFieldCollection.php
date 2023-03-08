<?php

namespace Givebutter\LaravelCustomFields\Collections;

use Givebutter\LaravelCustomFields\Models\CustomField;
use Illuminate\Database\Eloquent\Collection;

class CustomFieldCollection extends Collection
{
    public function toValidationRules(): array
    {
        return $this->map(fn (CustomField $field): array => $field->validation_rules)
            ->flatMap(fn (array $rules): array => $rules)
            ->toArray();
    }

    public function toValidationAttributes(): array
    {
        return $this->map(fn (CustomField $field): array => $field->validation_attributes)
            ->flatMap(fn (array $rules): array => $rules)
            ->toArray();
    }

    public function toValidationMessages(): array
    {
        return $this->map(fn (CustomField $field): array => $field->validation_messages)
            ->flatMap(fn (array $rules): array => $rules)
            ->toArray();
    }
}