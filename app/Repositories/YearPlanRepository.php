<?php

namespace App\Repositories;

use App\Models\YearPlan;
use Framework\Database;

use DateTime;

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

    public function findById(int $id): ?YearPlan
    {
        $row = $this->database->run(
            "SELECT * FROM year_plans WHERE id = ?",
            [$id]
        )->fetch();

        if (!$row) {
            return null;
        }

        return $this->fromDbRow($row);
    }

    public function insert(YearPlan $plan): int
    {
        $this->database->run(
            "INSERT INTO year_plans (name, student_year, start_date, end_date)
         VALUES (?, ?, ?, ?)",
            [
                $plan->name,
                $plan->student_year,
                $plan->start_date->format('Y-m-d'),
                $plan->end_date->format('Y-m-d'),
            ]
        );

        $id = (int) $this->database->getLastID();
        $plan->id = $id;

        return $id;
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