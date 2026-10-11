<?php

namespace App\Models;

use DateTime;

class PlanItem
{
    public int $id;
    public ?string $name;
    public ?string $colour;
    public DateTime $start_date;
    public ?DateTime $end_date;
    public int $year_plan_id;
    public ?string $course_id;
}