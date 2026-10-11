DROP TABLE IF EXISTS year_plans;

CREATE TABLE year_plans (
    id INTEGER PRIMARY KEY,
    name VARCHAR(255),
    student_year INTEGER NOT NULL,
    start_date TEXT NOT NULL,  -- YYYY-MM-DD (ISO 8601)
    end_date TEXT NOT NULL     -- YYYY-MM-DD
);

INSERT INTO year_plans (student_year, start_date, end_date)
VALUES (1, '2026-08-16', '2027-08-28');