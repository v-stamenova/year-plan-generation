<?php

namespace App\Repositories;

use App\Models\PlanItemTemplate;
use Framework\Database;
use DateTime;

class PlanItemTemplateRepository
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @return PlanItemTemplate[]
     */
    public function all(): array
    {
        $stmt = $this->database->run(
            "SELECT * FROM plan_item_templates"
        )->fetchAll();

        $items = [];

        foreach ($stmt as $row) {
            $items[] = $this->fromDbRow($row);
        }

        return $items;
    }

    /**
     * @return PlanItemTemplate[]
     */
    public function findByYearPlanId(int $yearPlanId): array
    {
        $stmt = $this->database->run(
            "SELECT
            pi.*,
            c.name AS course_name,
            c.colour AS course_colour
            FROM plan_item_templates pi
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

    public function findById(int $id): ?PlanItemTemplate
    {
        $row = $this->database->run(
            "SELECT
            pi.*,
            c.name AS course_name,
            c.colour AS course_colour
            FROM plan_item_templates pi
            LEFT JOIN courses c ON c.id = pi.course_id
            WHERE pi.id = ?",
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
    public function create(PlanItemTemplate $item): int
    {
        $this->database->run(
            "INSERT INTO plan_items (
                year_template_id,
                start_date,
                end_date,
                name,
                colour,
                course_id,
                is_main,
                type
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $item->year_template_id,
                $item->start_date->format('Y-m-d'),
                $item->end_date?->format('Y-m-d'),
                $item->name,
                $item->colour,
                $item->course_id,
                $item->is_main,
                $item->type,
            ]
        );

        return $this->database->getLastID();
    }

    public function delete(PlanItemTemplate $item): bool
    {
        $stmt = $this->database->run("DELETE FROM plan_items WHERE id = :id", ["id" => $item->id]);

        return $stmt->rowCount() > 0;
    }

    private function fromDbRow(mixed $row): PlanItemTemplate
    {
        $item = new PlanItemTemplate();

        $item->id = (int)$row->id;
        $item->name = $row->course_id ? $row->course_name : $row->name;
        $item->colour = $row->course_id ? $row->course_colour : $row->colour;

        $item->start_date = (new DateTime($row->start_date))->setTime(0, 0);

        $item->end_date = $row->end_date
            ? (new DateTime($row->end_date))->setTime(0, 0)
            : null;

        $item->year_template_id = (int)$row->year_template_id;
        $item->course_id = $row->course_id;
        $item->is_main = (bool)$row->is_main;
        $item->type = $row->type;

        return $item;
    }
}