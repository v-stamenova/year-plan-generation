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
        ON UPDATE CASCADE,
    is_main INTEGER DEFAULT 0 -- Used to store TRUE (1) or FALSE (0)
);

INSERT INTO plan_items (year_plan_id, start_date, end_date, name, colour) VALUES
    (1, "2026-08-30", "2026-09-05",  "Start Block 1", "#B1A0C7");

INSERT INTO plan_items (year_plan_id, start_date, end_date, name, colour, is_main) VALUES
    (1, "2026-10-18", "2026-10-24",  "Autumn Break", "#CCFFCC", 1);