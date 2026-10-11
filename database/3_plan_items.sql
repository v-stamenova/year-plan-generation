DROP TABLE IF EXISTS plan_items;

CREATE TABLE plan_items (
    id INTEGER PRIMARY KEY,
    year_plan_id INTEGER NOT NULL
        REFERENCES year_plans(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    start_date TEXT NOT NULL,
    end_date TEXT,
    name TEXT,
    colour TEXT,
    course_id TEXT
        REFERENCES courses(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);