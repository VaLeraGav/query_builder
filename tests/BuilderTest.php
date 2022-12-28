<?php

namespace Tests;

use Lacerta\Builder;
use Lacerta\Connect;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{
    public $db;

    public function setUp(): void
    {
        try {
//            $builder = Connect::class;
//            $this->assertInstanceOf(Connect::class, $builder);

            $this->db = Connect::setup()->table("users");
        } catch (\PDOException $e) {
            print_r($e->getMessage());
        }
    }

    public function testWhere(): void
    {
        $this->assertEquals(
            "SELECT * FROM users WHERE ('name' = 'valery')",
            $this->db->select()->wh("name", "=", "valery")->getStr()
        );

        $this->assertEquals(
            "SELECT id FROM users WHERE ('name' = 'valery')or('name' = 'valery') ORDER BY 'id' ASC LIMIT 1 OFFSET 2",
            $this->db->select("id")->wh("name", "valery")->where("name", "=", "valery", "or")->asc()->limit(
                1,
                2
            )->getStr()
        );

        $this->assertEquals(
            "SELECT * FROM users WHERE ('name' = 'valery')and('name' = 'valery') ORDER BY 'sd' ASC LIMIT 1",
            $this->db->select()->wh("name", "valery")->where("name", "=", "valery", "and")->asc("sd")->limit(1)->getStr(
            )
        );

        $this->assertEquals(
            "SELECT * FROM users WHERE ('name' = 'valery') ORDER BY 'name' ASC",
            $this->db->select()->wh("name", "valery")->order("name", "ASC")->getStr()
        );
    }

    public function testOrder(): void
    {
        // проверка второго параметра, проходит не только asc и desc
        $this->assertEquals(
            "SELECT * FROM users  ORDER BY 'name' ASC",
            $this->db->select()->order('name', 'ASC')->getStr()
        );

        $this->assertEquals(
            "SELECT * FROM users  ORDER BY 'name' DESC",
            $this->db->select()->desc('name')->getStr()
        );
    }

    public function testJoin(): void
    {
        // убрать пробел перед INNER
        $this->assertEquals(
            "SELECT * FROM users  INNER JOIN 'posts' ON ('posts'.'user_id' = 'users'.'id') INNER JOIN 'images' ON ('images'.'post_id' = 'post'.'id')",
            $this->db->select()->join("posts", "user_id")->join("images", "post_id", "=", "post.id")->getStr()
        );

        $this->assertEquals(
            "SELECT * FROM users  LEFT JOIN 'posts' ON ('posts'.'id' = 'user_id')",
            $this->db->select()->leftJoin("posts", "id", "=", "user_id")->getStr()
        );
    }

    public function testWhereIn(): void
    {
        $this->assertEquals(
            "SELECT * FROM users WHERE ('name' IN (1, 2, 4))",
            $this->db->select()->whereIn("name", [1, 2, 4])->getStr()
        );
    }

    // проверку на то что вторым параметром идет строка
    public function testWhereLike(): void
    {
        $this->assertEquals(
            "SELECT * FROM users WHERE ('name' LIKE '%like%')",
            $this->db->select()->whereLike("name", "%like%")->getStr()
        );
    }

    public function testWhereBetween(): void
    {
        $this->assertEquals(
            "SELECT * FROM users WHERE ('name' BETWEEN 1 AND 2)",
            $this->db->select()->whereBetween("name", [1, 2])->getStr()
        );
    }

    // нехороший пробел после
    // wh("name", "name", "or") - не работает только wh("name", "name")
    public function testWhereGroup(): void
    {
        $this->assertEquals(
            "SELECT * FROM users WHERE ('name' = 'name') or(('id' = 4)or('id' = 5))and('id' <> 4) GROUP BY ('users'.'name')",
            $this->db->select()->wh("name", "name")
                ->whereGroup("or", function ($q) {
                    $q->where("id", '=', 4)->orWhere("id", '=', 5);
                })
                ->andWhere("id", "<>", 4)
                ->groupBy("users.name")
                ->getStr()
        );
    }

    public function testInsert(): void
    {
        $this->assertEquals(
            "INSERT INTO users (name, email, age) VALUES ('Vasya', 'vasya@gmail.com', 22),('Petya', 'petya@gmail.com', 24)",
            $this->db->insert()
                ->fields('name', 'email', 'age')
                ->values('Vasya', 'vasya@gmail.com', 22)
                ->values('Petya', 'petya@gmail.com', 24)
                ->getStr()
        );

        // сортировка нарушилась
//        $this->assertFalse(
//            "INSERT INTO users (name, email, age) VALUES ('Vasya', 'vasya@gmail.com', 22),('Petya', 'petya@gmail.com', 24)",
//            $this->db->insert(
//                [
//                    'name' => 'Valiery',
//                    'email' => 'name1@gmail.com',
//                    'age' => 11
//                ],
//                [
//                    'name' => 'Valiery',
//                    'age' => 22,
//                    'email' => 'name2@gmail.com',
//                ]
//            )->getStr()
//        );
    }

    // то что нужно реализовать
    public function Update(): void
    {
        $this->assertEquals(
            "INSERT INTO users (name, email, age)
                    VALUES ('Vasya', 'vasya@gmail.com', 22),
                           ('Petya', 'petya@gmail.com', 24)",
            $this->db->update()
                ->fields('name', 'subname')
                ->values('test', 'testus')
                ->getStr()
        );
    }

    public function UpdateWhere(): void
    {
        $this->assertEquals(
            "UPDATE users SET  'name' = 'subname', 'subnamed' = 'testus' 
             WHERE ('id' = 4) ORDER BY 'name' DESC LIMIT 10 OFFSET 20",
            $this->db->update()
                ->fields('name', 'subname')
                ->values('test', 'testus')
                ->where("id", "=", 4)
                ->order('name', 'desc')
                ->limit(10, 20, 'offset')
                ->getStr()
        );
    }

    public function WhereExists(): void
    {
        $this->assertEquals(
            "SELECT * WHERE EXISTS (select 'name' from orders where orders.user_id = users.id)",

            $this->db->select()->whereExists(function ($query) {
                $query->select()
                    ->from('orders')
                    ->whereColumn('orders.user_id', 'users.id');
            })->getStr()
        );
    }


}