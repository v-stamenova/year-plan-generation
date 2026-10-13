DROP TABLE IF EXISTS plan_items;

CREATE TABLE plan_items
(
    id           INTEGER PRIMARY KEY,
    year_plan_id INTEGER NOT NULL
        REFERENCES year_plans (id)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
    start_date   TEXT    NOT NULL,
    end_date     TEXT,
    name         TEXT,
    colour       TEXT,
    course_id    TEXT
        REFERENCES courses (id)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
    type         TEXT,
    is_main      INTEGER DEFAULT 0 -- Used to store TRUE (1) or FALSE (0)
);

--
-- INSERT INTO plan_items (year_plan_id, start_date, end_date, name, colour, type)
-- VALUES (1, "2026-08-30", "2026-09-05", "Start Block 1", "#B1A0C7", 'block'),
--        (1, "2026-11-08", "2026-11-14", "Start Block 2", "#B1A0C7", 'block'),
--        (1, "2027-01-31", "2027-02-06", "Start Block 3", "#B1A0C7", 'block'),
--        (1, "2027-04-18", "2027-04-24", "Start Block 4", "#B1A0C7", 'block'),
--        (1, "2026-10-25", "2026-10-31", "Exam week", "#ffff80", 'exam'),
--        (1, "2026-11-01", "2026-11-07", "Project week", "#3788d8", 'project'),
--        (1, "2027-01-10", "2027-01-30", "Exam week", "#ffff80", 'exam'),
--        (1, "2027-03-28", "2027-04-17", "Exam week", "#ffff80", 'exam'),
--        (1, "2027-06-13", "2027-07-03", "Exam week", "#ffff80", 'exam');
--
-- INSERT INTO plan_items (year_plan_id, start_date, end_date, name, colour, is_main, type)
-- VALUES (1, "2026-10-18", "2026-10-24", "Autumn Break", "#CCFFCC", 1, 'break'),
--        (1, "2026-12-20", "2027-01-02", "Winter Break", "#CCFFCC", 1, 'break'),
--        (1, "2027-02-07", "2027-02-13", "Spring Break", "#CCFFCC", 1, 'break'),
--        (1, "2027-04-25", "2027-05-01", "May Break", "#CCFFCC", 1, 'break');
--
-- INSERT INTO plan_items (year_plan_id, start_date, name, colour, is_main, type)
-- VALUES (1, "2027-05-05", "Liberation Day", "#CCFFCC", 1, 'break'),
--        (1, "2027-05-06",  "Ascension Day", "#CCFFCC", 1, 'break'),
--        (1, "2027-05-07",  "Lecture Free Day", "#CCFFCC", 1, 'break');