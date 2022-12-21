create table if not exists versions
(
    id      INTEGER      not null primary key autoincrement,
    name    varchar(255) not null,
    created timestamp default current_timestamp
--     created varchar(255) not null
)