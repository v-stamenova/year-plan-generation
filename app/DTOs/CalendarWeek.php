<?php

namespace App\DTOs;

final class CalendarWeek
{
    /**
     * @param array<string, CalendarDay> $days keyed by d-m-Y
     * @param MultiDaySegment[]          $multiDaySegments
     */
    public function __construct(
        public readonly int $weekNumber,
        public readonly \DateTimeImmutable $startDate, // Sunday
        public array $days = [],
        public array $multiDaySegments = [],
        public ?int $blockWeek = null, // 1-based position inside its block period, null if not a block week
    ) {}

    public function endDate(): \DateTimeImmutable
    {
        return $this->startDate->modify('+6 days');
    }

    public function dayFor(string $key): ?CalendarDay
    {
        return $this->days[$key] ?? null;
    }
}