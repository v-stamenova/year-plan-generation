<?php

namespace App\Models;

use pq\DateTime;

class YearTemplate
{
    public int $id;
    public string $name;
    public DateTime $start_date;
    public DateTime $end_date;
}