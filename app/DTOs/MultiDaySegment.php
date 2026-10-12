<?php

namespace App\DTOs;

use App\Models\PlanItem;

final class MultiDaySegment
{
    public function __construct(
        public readonly PlanItem $item,
        public readonly int $startColumn, // 1 = Sunday, 7 = Saturday
        public readonly int $span,
    ) {}
}