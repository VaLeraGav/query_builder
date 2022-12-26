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
```
```php
// Устанавливает таблицу
$db = $conect->table("users");

$table1 = $db->select("name_field");
//  SELECT name_field FROM users

$table2 = $db->select('count(*)');
//    SELECT count(*)
//        FROM `users`

$table5 = $db->select("table_name")
    ->wh("name", "valery")->andWhere("name","=","sergey24");
//    SELECT table_name
//        FROM `users`
//          WHERE ('name' = 'valery')
//          AND WHERE ('name' = 'sergey24')

$table5 = $db->select("table_name")
    ->where("name","=","valery")
    ->where("id","<",3, "or")
    ->whereGroup('and', function ($q) {
        $q->where("id",'=', 4)->orWhere("id", 5);
    })
//    SELECT table_name
//        FROM `users`
//          WHERE ('name' = 'valery')
//          OR ('name' < 3)
//          AND ((id = 4) OR (id = 5))
    
    
$db = $conect->table("users")->select()
    ->join("posts", "user_id")
    ->join("images", "post_id", "=", "post.id")
    ->whereGroup("",function ($q) {
        $q->where("id",'=', 4)->orWhere("id", '=', 5);
    })
    ->andWhere("id", "<>", 4)
    ->groupBy("users.name")
    ->asc();
//    SELECT * FROM `users` 
//        INNER JOIN `posts` 
//            ON (`posts`.`user_id` = `users`.`id`) 
//        INNER JOIN `images`
//            ON (`images`.`post_id` = `post`.`id`) 
//            WHERE ! ((id = 4) OR (id = 5))AND(id <> 4) 
//        GROUP BY  (`users`.`name`) 
//        ORDER BY `id` ASC

//     SELECT * FROM users
//        INNER JOIN 'posts'
//          ON ('posts'.'user_id' = 'users'.'id')
//        INNER JOIN 'images'
//           ON ('images'.'post_id' = 'post'.'id')
//           WHERE ! ((`id` = 4)OR(`id` = 5))AND(`id` <> 4)
//        GROUP BY ('users'.'name')
//        ORDER BY 'id' ASC

$table5 = $db->select("table_name")
    ->where("name", "=", "valery")
    ->andWhere("age", "12")
    ->whereLike("name", '%we%')
    ->whereNotIn('category_id', [223, 15], 'or')
    ->andWhereBetween("column_name", [10, 12])
//      SELECT table_name FROM users
//          WHERE 'name' = 'valery'
//          AND 'age' = 12
//          OR 'category_id' NOT IN (223, 15)
//          AND 'column_name' BETWEEN 10 AND 12
```
```php
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

```
```php
$table5 = $db->update([
    'user' => 11,
    'post' => 345,
    'text' => 'Text'
])->where("id", 4);

//    UPDATE `users`
//        SET
//            `user` = 11,
//            `post` = 345,
//            `text` = Text
//       WHERE (id = 4)

echo $builder->update()
    ->fields('name', 'subname')
    ->values('test', 'testus')
    ->where("id","=", 4)
    ->order('name', 'desc')
    ->limit(10, 20, 'offset');


//    UPDATE `users`
//        SET
//            `name` = 'subname',
//            `subname` = 'testus'
//        WHERE ('id' = 4)
//        ORDER BY `name` DESC
//        LIMIT 10 OFFSET 20
```

## DROUP and DELETE
```php
$table4 = $db->droup()
// DROP TABLE users;

$table3 = $conect->table("users")
    ->deleteTable()
    ->where("id", "<>", 4)
    ->andWhere("name", "sergey24");
//    DELETE FROM `users`
//        WHERE (`id` <> 4)
//        AND (`name` = sergey24)

$post = $db->where("id", 4)->delete();
//    DELETE FROM `users`
//        WHERE (`id` = 5)
```





```php


// laravel
Products::whereIn('id', function($query){
    $query->select('paper_type_id')
    ->from(with(new ProductCategory)->getTable())
    ->whereIn('category_id', ['223', '15'])
    ->where('active', 1);
})->get();
//SELECT 
//    `p`.`id`,
//    `p`.`name`,  
//FROM `products` p 
//WHERE `p`.`id` IN (
//    SELECT 
//        `product_id` 
//    FROM `product_category`
//    WHERE `category_id` IN ('223', '15')
//)
//AND `p`.`active`=1
```