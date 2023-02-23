<?php

namespace Givebutter\LaravelCustomFields\ValueObjects;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;

class DateRange implements Arrayable
{
    private function __construct(
        public readonly CarbonImmutable $start,
        public readonly CarbonImmutable $end,
    ) {
    }

    public static function make(CarbonImmutable $start, CarbonImmutable $end): self
    {
        if ($start->greaterThan($end)) {
            throw new InvalidArgumentException('Start date must be before end date.');
        }

        return new static($start, $end);
    }

    public static function fromArray(array $array): self
    {
        return static::make(...$array);
    }

    public function toArray(): array
    {
        return [$this->start, $this->end];
    }

    public function equals(self $other): bool
    {
        return $this->start->equalTo($other->start) && $this->end->equalTo($other->end);
    }
}
