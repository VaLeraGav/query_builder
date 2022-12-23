# query_builder

## Применение:
```php
$conect = DB::setup();

// Выполните инструкцию SQL, возвращает null
$conect->exec(
    "CREATE TABLE users (
       id INTEGER not null primary key autoincrement,
       name VARCHAR(255),
       email EMAIL,
       age VARCHAR(255),
       created_at TIMESTAMP
    )"
);

// Выполняет инструкцию SQL, возвращающую результирующий набор в виде массива
$table0 = $conect->query(
    "SELECT * FROM users", null, 3
);

// Устанавливает таблицу
$db->table("users");

$table1 = $db->select("name_field");
//  SELECT name_field FROM users

$table2 = $db->select(['count' => '*|count']);
//    SELECT COUNT(*) AS `count`
//        FROM `users`

$table6 = $db->select()
    ->join("posts", "user_id")
    ->join("images", "post_id", "post.id")
    ->whereGroup(function ($q) {
        $q->where("id", 4)->orWhere("id", 5);
    })
    ->andWhere("id", "<>", 4)
    ->groupBy("users.name")
    ->asc();
//    SELECT * FROM `users` 
//        INNER JOIN `posts` 
//            ON (`posts`.`user_id` = `users`.`id`) 
//        INNER JOIN `images`
//            ON (`images`.`post_id` = `post`.`id`) 
//            WHERE ((id = 4) OR (id = 5))AND(id <> 4) 
//        GROUP BY  (`users`.`name`) 
//        ORDER BY `id` ASC

$table3 = $db->delete(5);
//    DELETE FROM `users`
//        WHERE (`id` = 5)


$table4 = $db->delete(15, 'userid');
//    DELETE FROM `users`
//        WHERE (`userid` = 15)


$table4 = $db->insert()
    ->fields(['name', 'email', 'age'])
    ->values(['Vasya', 'name1@gmail.com', 22])
    ->values(['Petya', 'name2@gmail.com', 33]);
//    INSERT INTO `users`
//        (`name`,`email`,`age`)
//        VALUES
//            (Vasya, name1@gmail.com, 22),
//            (Petya, name2@gmail.com, 33)


$table5 = $db->insert([
    'name' => 'Valiery',
    'email' => 'name@gmail.com',
    'age' => 33
]);
//    INSERT INTO `users`
//        (`name`,`email`,`age`)
//        VALUES
//            (Oleg, name@gmail.com, 33)

```