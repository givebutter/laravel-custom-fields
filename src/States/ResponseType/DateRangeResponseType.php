<?php

namespace Givebutter\LaravelCustomFields\States\ResponseType;

use Carbon\CarbonImmutable;
use Givebutter\LaravelCustomFields\ValueObjects\DateRange;
use InvalidArgumentException;

class DateRangeResponseType extends ResponseType
{
    public function formatValue(mixed $value): mixed
    {
        return DateRange::fromArray(
            array_map(fn ($date) => CarbonImmutable::parse($date), $value),
        );
    }

    public function getValue(): mixed
    {
        return $this->formatValue([
            $this->response->value_datetime_start,
            $this->response->value_datetime_end,
        ]);
    }

    public function getValueFriendly(): mixed
    {
        $dateRange = $this->response->value;

        return $dateRange->start->format('n/j/Y') . ' - ' . $dateRange->end->format('n/j/Y');
    }

    public function setValue(mixed $value): void
    {
        if (! is_array($value)) {
            throw new InvalidArgumentException('Value must be an array.');
        }

        [$start, $end] = $value;

        $this->clearValues();

        $this->response->value_datetime_start = $start;
        $this->response->value_datetime_end = $end;
    }
}
