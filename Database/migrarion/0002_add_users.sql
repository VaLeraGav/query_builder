CREATE TABLE users1
(
    id         INTEGER not null primary key autoincrement,
    name       VARCHAR(255),
    number     VARCHAR(255),
    email      EMAIL,
    password   VARCHAR(255),
    created_at TIMESTAMP
)