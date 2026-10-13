<?php

namespace App\Models;

class YearTemplate
{
    public int $id;
    public ?string $name = null;
    public \DateTime $start_date;
    public \DateTime $end_date;
}