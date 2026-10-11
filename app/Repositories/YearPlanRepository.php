<?php

namespace App\Repositories;

use App\Models\YearPlan;
use Framework\Database;
use pq\DateTime;

class YearPlanRepository
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @return YearPlan[]
     */
    public function all(): array
    {
        $stmt = $this->database->run("SELECT * FROM year_plans")->fetchAll();
        $templates = [];
        foreach ($stmt as $row) {
            $plan = $this->fromDbRow($row);
            $templates[] = $plan;
        }
        return $templates;
    }

    private function fromDbRow(mixed $row): YearPlan
    {
        $plan = new YearPlan();
        $plan->id = $row->id;
        $plan->name = $row->name;
        $plan->student_year = $row->student_year;
        $plan->start_date = new DateTime($row->start_date);
        $plan->end_date = new DateTime($row->end_date);
        return $plan;
    }
}