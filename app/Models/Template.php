<?php

namespace App\Models;

class Template
{
    public int $id;
    public ?string $name = null;
    public \DateTime $start_date;
    public \DateTime $end_date;
}