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
    ('ITS', 'IT Studio', '#F79646'),
    ('OOP', 'Object Oriented Programming', '#DAEEF3'),
    ('PRF', 'Project: Foundations', '#92CDDC'),
    ('ATM', 'Agile & Team Management', '#00B0F0'),
    ('SEB', 'Software Engineering Basics (SEB)', '#DA9694'),
    ('FOD', 'Foundations of Data', '#95B3D7'),
    ('REM', 'Requirements Modelling', '#C4D79B'),
    ('WAD', 'Web Application Development', '#FF5050'),
    ('DSB', 'Data Science Basics', '#8064A2'),
    ('ACS', 'Project: Applied Client Solutions', '#92CDDC');

