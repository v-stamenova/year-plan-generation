<?php

namespace App\Models;

use DateTime;

class YearPlan
{
    public int $id;
    public ?string $name;
    public int $student_year;
    public DateTime $start_date;
    public DateTime $end_date;
}