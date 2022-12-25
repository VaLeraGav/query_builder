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
$db = $conect->table("users");

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

$table3 = $conect->table("users")
    ->deleteTable()
    ->where("id", "<>", 4)
    ->andWhere("name", "sergey24");
//    DELETE FROM `users`
//        WHERE (`id` <> 4) AND  WHERE (`name` = sergey24)

$post = $db->where("id", 4)->delete();
//    DELETE FROM `users`
//        WHERE (`id` = 5)

$table4 = $db->droup()
// DROP TABLE users;

$table5 = $db->insert([
    [
        'name' => 'Valiery',
        'email' => 'name1@gmail.com',
        'age' => 11
    ],
    [
        'name' => 'Valiery',
        'age' => 22
        'email' => 'name2@gmail.com',
     ]   
]);
//    INSERT INTO `users`
//        (age, email, name)
//        VALUES
//            (11, 'name1@gmail.com', 'Valiery')
//            (22, 'name2@gmail.com', 'Valiery')

$table5 = $db->insert()
    ->fields('name', 'email', 'age')
    ->values('Vasya', 'vasya@gmail.com', 22)
    ->values('Petya', 'petya@gmail.com', 24);
//    INSERT INTO `users`
//        (age, email, name)
//        VALUES
//            (11, 'name1@gmail.com', 'Valiery')
//            (22, 'name2@gmail.com', 'Valiery')
    

$table5 = $db->update([
    'user' => 11,
    'post' => 345,
    'text' => 'Text'
])
    ->where("id", 4);
//    UPDATE `users`
//        SET
//            `user` = 11,
//            `post` = 345,
//            `text` = Text
//       WHERE (id = 4)
```