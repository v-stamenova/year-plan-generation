DROP TABLE IF EXISTS year_templates;

CREATE TABLE year_templates (
    id INTEGER PRIMARY KEY,
    name VARCHAR(255),
    start_date TEXT NOT NULL,  -- YYYY-MM-DD (ISO 8601)
    end_date TEXT NOT NULL     -- YYYY-MM-DD
);

INSERT INTO year_templates (start_date, end_date)
VALUES ('2026-08-16', '2027-08-28');