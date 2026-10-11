<?php

namespace App\Repositories;

use App\Models\PlanItem;
use Framework\Database;
use \DateTime;

class PlanItemRepository
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @return PlanItem[]
     */
    public function all(): array
    {
        $stmt = $this->database->run(
            "SELECT * FROM plan_items"
        )->fetchAll();

        $items = [];

        foreach ($stmt as $row) {
            $items[] = $this->fromDbRow($row);
        }

        return $items;
    }

    /**
     * @return PlanItem[]
     */
    public function findByYearPlanId(int $yearPlanId): array
    {
        $stmt = $this->database->run(
            "SELECT
            pi.*,
            c.name AS course_name,
            c.colour AS course_colour
            FROM plan_items pi
            LEFT JOIN courses c ON c.id = pi.course_id
            WHERE pi.year_plan_id = ?",
            [$yearPlanId]
        )->fetchAll();

        $items = [];

        foreach ($stmt as $row) {
            $items[] = $this->fromDbRow($row);
        }

        return $items;
    }

    public function findById(int $id): ?PlanItem
    {
        $row = $this->database->run(
            "SELECT * FROM plan_items WHERE id = ?",
            [$id]
        )->fetch();

        if (!$row) {
            return null;
        }

        return $this->fromDbRow($row);
    }

    /**
     * Create a plan item.
     */
    public function create(PlanItem $item): int
    {
        $this->database->run(
            "INSERT INTO plan_items (
                year_plan_id,
                start_date,
                end_date,
                name,
                colour,
                course_id
            ) VALUES (?, ?, ?, ?, ?, ?)",
            [
                $item->year_plan_id,
                $item->start_date->format('Y-m-d'),
                $item->end_date?->format('Y-m-d'),
                $item->name,
                $item->colour,
                $item->course_id
            ]
        );

        return $this->database->getLastID();
    }

    private function fromDbRow(mixed $row): PlanItem
    {
        $item = new PlanItem();

        $item->id = (int)$row->id;
        $item->name = $row->course_id ? $row->course_name : $row->name;
        $item->colour = $row->course_id ? $row->course_colour : $row->colour;

        $item->start_date = (new DateTime($row->start_date))->setTime(0, 0);

        $item->end_date = $row->end_date
            ? (new DateTime($row->end_date))->setTime(0, 0)
            : null;

        $item->year_plan_id = (int)$row->year_plan_id;
        $item->course_id = $row->course_id;

        return $item;
    }
}