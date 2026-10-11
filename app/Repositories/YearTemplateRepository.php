<?php

namespace App\Repositories;

use App\Models\YearTemplate;
use Framework\Database;
use pq\DateTime;

class YearTemplateRepository
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @return YearTemplate[]
     */
    public function all(): array
    {
        $stmt = $this->database->run("SELECT * FROM year_templates")->fetchAll();
        $templates = [];
        foreach ($stmt as $row) {
            $course = $this->fromDbRow($row);
            $templates[] = $course;
        }
        return $templates;
    }

    private function fromDbRow(mixed $row): YearTemplate
    {
        $template = new YearTemplate();
        $template->id = $row->id;
        $template->name = $row->name;
        $template->start_date = new DateTime($row->start_date);
        $template->end_date = new DateTime($row->end_date);
        return $template;
    }
}