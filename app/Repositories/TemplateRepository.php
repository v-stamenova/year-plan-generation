<?php

namespace App\Repositories;

use App\Models\Template;
use Framework\Database;
use DateTime;

class TemplateRepository
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @return Template[]
     */
    public function all(): array
    {
        $stmt = $this->database->run("SELECT * FROM templates")->fetchAll();
        $templates = [];
        foreach ($stmt as $row) {
            $course = $this->fromDbRow($row);
            $templates[] = $course;
        }
        return $templates;
    }

    public function findById(int $id): ?Template
    {
        $row = $this->database->run(
            "SELECT * FROM templates WHERE id = ?",
            [$id]
        )->fetch();

        if (!$row) {
            return null;
        }

        return $this->fromDbRow($row);
    }

    private function fromDbRow(mixed $row): Template
    {
        $template = new Template();
        $template->id = $row->id;
        $template->name = $row->name;
        $template->start_date = new DateTime($row->start_date);
        $template->end_date = new DateTime($row->end_date);
        return $template;
    }
}