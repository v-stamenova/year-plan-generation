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

INSERT INTO plan_items (year_plan_id, start_date, course_id) VALUES
    (1, "2026-09-06", 'CSB');

INSERT INTO plan_items (year_plan_id, start_date, name, colour) VALUES
    (1, "2026-09-07", "Programming basics (PBA)", "#00ff00");