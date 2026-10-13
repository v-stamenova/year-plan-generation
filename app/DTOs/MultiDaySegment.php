<?php

namespace App\DTOs;

use App\Models\PlanItem;
use App\Models\PlanItemTemplate;

final class MultiDaySegment
{
    public function __construct(
        public readonly PlanItem|PlanItemTemplate $item,
        public readonly int $startColumn, // 1 = Sunday, 7 = Saturday
        public readonly int $span,
    ) {}
}