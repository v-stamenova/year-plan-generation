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
    (1, "2026-09-08", 'CSB');

INSERT INTO plan_items (year_plan_id, start_date, name, colour) VALUES
    (1, "2026-09-07", "Programming basics (PBA)", "#00ff00"),
    (1, "2026-09-09", "Programming basics (PBA)", "#00ff00"),
    (1, "2026-09-09", "IT Studio (PBA)", "#0000ff"),
    (1, "2026-09-15", "Programming basics (PBA)", "#00ff00"),
    (1, "2026-09-16", "Programming basics (PBA)", "#00ff00"),
    (1, "2026-09-16", "IT Studio (PBA)", "#0000ff");

INSERT INTO plan_items (year_plan_id, start_date, end_date, name, colour) VALUES
    (1, "2026-09-07", "2026-09-11",  "Start Block 1", "#ff00ff"),
    (1, "2026-09-15", "2026-09-17",  "Start Block 2", "#fff0ff");
