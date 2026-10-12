DROP TABLE IF EXISTS courses;

CREATE TABLE courses (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    colour TEXT
);

INSERT INTO courses (id, name, colour) VALUES
    ('CSB', 'Computer Science Basics', '#FCD5B4'),
    ('PBA', 'Programming Basics', '#EBF1DE'),
    ('PCO', 'Programme & Career Orientation', '#FFC000'),
    ('ITS', 'IT Studio', '#F79646')


