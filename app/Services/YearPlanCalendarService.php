<?php

namespace App\Services;

use App\DTOs\CalendarDay;
use App\DTOs\CalendarWeek;
use App\DTOs\MultiDaySegment;
use App\Models\PlanItem;
use DateTimeImmutable;
use DateTimeInterface;

final class YearPlanCalendarService
{
    /**
     * @param PlanItem[] $planItems
     * @return array<int, CalendarWeek> keyed by ISO week number
     */
    public function buildWeeks(
        DateTimeInterface $start,
        DateTimeInterface $end,
        array $planItems
    ): array {
        $weeks = $this->createWeeks($this->toDay($start), $this->toDay($end));

        foreach ($planItems as $item) {
            if ($item->end_date === null) {
                $this->placeSingleDayItem($weeks, $item);
            } else {
                $this->placeMultiDayItem($weeks, $item);
            }
        }

        $this->assignBlockWeeks($weeks, $planItems);

        return $weeks;
    }

    /**
     * @return array<int, CalendarWeek>
     */
    private function createWeeks(DateTimeImmutable $start, DateTimeImmutable $end): array
    {
        // Sunday on or before start, Saturday on or after end.
        $calendarStart = $start->modify('-' . $start->format('w') . ' days');
        $calendarEnd   = $end->modify('+' . (6 - (int) $end->format('w')) . ' days');

        $weeks = [];

        for ($weekStart = $calendarStart; $weekStart <= $calendarEnd; $weekStart = $weekStart->modify('+7 days')) {
            // Monday determines the ISO week number.
            $weekNumber = (int) $weekStart->modify('+1 day')->format('W');

            $week = new CalendarWeek($weekNumber, $weekStart);

            for ($i = 0; $i < 7; $i++) {
                $day = new CalendarDay($weekStart->modify("+{$i} days"));
                $week->days[$day->key()] = $day;
            }

            $weeks[$weekNumber] = $week;
        }

        return $weeks;
    }

    /** @param array<int, CalendarWeek> $weeks */
    private function placeSingleDayItem(array $weeks, PlanItem $item): void
    {
        $key = $item->start_date->format('d-m-Y');

        foreach ($weeks as $week) {
            $day = $week->dayFor($key);

            if ($day !== null) {
                $day->items[] = $item;
                return;
            }
        }
    }

    /** @param array<int, CalendarWeek> $weeks */
    private function placeMultiDayItem(array $weeks, PlanItem $item): void
    {
        $start = $this->toDay($item->start_date);
        $end   = $this->toDay($item->end_date);

        foreach ($weeks as $week) {
            $weekEnd = $week->endDate();

            if ($start > $weekEnd || $end < $week->startDate) {
                continue;
            }

            $visibleStart = $start > $week->startDate ? $start : $week->startDate;
            $visibleEnd   = $end < $weekEnd ? $end : $weekEnd;

            $week->multiDaySegments[] = new MultiDaySegment(
                item: $item,
                startColumn: $this->daysBetween($week->startDate, $visibleStart) + 1,
                span: $this->daysBetween($visibleStart, $visibleEnd) + 1,
            );
        }
    }

    private function toDay(DateTimeInterface $date): DateTimeImmutable
    {
        return DateTimeImmutable::createFromInterface($date)->setTime(0, 0);
    }

    private function daysBetween(DateTimeImmutable $from, DateTimeImmutable $to): int
    {
        return (int) $from->diff($to)->days;
    }

    /** @param array<int, CalendarWeek> $weeks */
    private function assignBlockWeeks(array $weeks, array $planItems): void
    {
        $blockStarts = [];
        $breaks = [];

        foreach ($planItems as $item) {
            if ($item->type === 'block') {
                $blockStarts[] = $this->toDay($item->start_date);
            } elseif ($item->type === 'break') {
                $breakStart = $this->toDay($item->start_date);
                $breakEnd   = $item->end_date !== null
                    ? $this->toDay($item->end_date)
                    : $breakStart;

                $breaks[] = [$breakStart, $breakEnd];
            }
        }

        if ($blockStarts === []) {
            return;
        }

        usort($blockStarts, fn (DateTimeImmutable $a, DateTimeImmutable $b) => $a <=> $b);

        [$periodStarts, $periodEnds] = $this->blockPeriods($blockStarts);

        $counters = array_fill(0, count($periodStarts), 0);

        foreach ($weeks as $week) {
            $period = $this->periodIndexFor($week, $periodStarts, $periodEnds);

            if ($period === null || $this->isEntirelyBreak($week, $breaks)) {
                continue;
            }

            $counters[$period]++;
            $week->blockWeek = $counters[$period];
        }
    }

    /**
     * Each block period runs from its block start up to the next block start.
     * The last block has no next block, so it runs for 10 calendar weeks.
     *
     * @param DateTimeImmutable[] $blockStarts sorted ascending
     * @return array{0: DateTimeImmutable[], 1: DateTimeImmutable[]}
     */
    private function blockPeriods(array $blockStarts): array
    {
        $periodStarts = $blockStarts;
        $periodEnds   = [];

        $lastIndex = count($blockStarts) - 1;

        for ($i = 0; $i < $lastIndex; $i++) {
            $periodEnds[] = $blockStarts[$i + 1];
        }

        $periodEnds[] = $blockStarts[$lastIndex]->modify('+11 weeks');

        return [$periodStarts, $periodEnds];
    }

    /**
     * Returns the block period a week belongs to, or null.
     *
     * @param DateTimeImmutable[] $periodStarts
     * @param DateTimeImmutable[] $periodEnds   exclusive
     */
    private function periodIndexFor(CalendarWeek $week, array $periodStarts, array $periodEnds): ?int
    {
        $weekStart = $week->startDate;
        $weekEnd   = $week->endDate();

        foreach ($periodStarts as $i => $periodStart) {
            // Week overlaps the period: starts before the period ends and ends on or after it begins.
            if ($weekStart < $periodEnds[$i] && $weekEnd >= $periodStart) {
                return $i;
            }
        }

        return null;
    }

    /** @param array<int, array{0: DateTimeImmutable, 1: DateTimeImmutable}> $breaks */
    private function isEntirelyBreak(CalendarWeek $week, array $breaks): bool
    {
        foreach ($week->days as $day) {
            $covered = false;

            foreach ($breaks as [$breakStart, $breakEnd]) {
                if ($day->date >= $breakStart && $day->date <= $breakEnd) {
                    $covered = true;
                    break;
                }
            }

            if (! $covered) {
                return false;
            }
        }

        return true;
    }
}