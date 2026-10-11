DROP TABLE IF EXISTS courses;

CREATE TABLE courses (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    colour TEXT
);

INSERT INTO courses (id, name, colour) VALUES
    ('CSB', 'Computer Science Basics', '#0000ff');


