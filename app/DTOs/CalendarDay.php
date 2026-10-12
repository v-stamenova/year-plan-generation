<?php

namespace App\DTOs;

use App\Models\PlanItem;

final class CalendarDay
{
    /** @param PlanItem[] $items */
    public function __construct(
        public readonly \DateTimeImmutable $date,
        public array $items = [],
    ) {}

    public function key(): string
    {
        return $this->date->format('d-m-Y');
    }
}