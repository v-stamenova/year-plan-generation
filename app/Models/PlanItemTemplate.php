<?php

namespace App\Models;

use DateTime;

class PlanItemTemplate
{
    public int $id;
    public ?string $name = null;
    public ?string $colour = null;
    public DateTime $start_date;
    public ?DateTime $end_date = null;
    public int $year_template_id;
    public ?Course $course = null;
    public ?string $course_id = null;
    public bool $is_main = false;
    public ?string $type = null;
}