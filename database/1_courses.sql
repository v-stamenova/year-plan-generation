DROP TABLE IF EXISTS courses;

CREATE TABLE courses (
    id VARCHAR(5) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    colour VARCHAR(7)
);

INSERT INTO courses (id, name, colour) VALUES
    ('CSB', 'Computer Science Basics', '#0000ff');


