<?php

namespace App\Repositories;

use App\Models\Course;
use Framework\Database;

class CourseRepository
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @return Course[]
     */
    public function all(): array
    {
        $stmt = $this->database->run("SELECT * FROM courses")->fetchAll();
        $courses = [];
        foreach ($stmt as $row) {
            $course = $this->fromDbRow($row, false);
            $courses[] = $course;
        }
        return $courses;
    }

    private function fromDbRow(mixed $row): Course
    {
        $course = new Course();
        $course->id = $row->id;
        $course->name = $row->name;
        $course->colour = $row->colour;
        return $course;
    }
}