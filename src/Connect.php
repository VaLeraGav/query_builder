<?php

namespace Lacerta;

use Lacerta\Operators\{
    Insert,
    Select,
    Update,
    Delete
};
use mysql_xdevapi\Table;


/**
 * @method Select select(...$args)
 * @method Update update(...$args)
 * @method Insert insert(...$args)
 * @method Delete delete()
 */
class Connect
{
    const DIRECTORY_SEPARATOR = "/";
    protected ?string $dbName = null;
    protected ?string $userName = null;
    protected ?string $password = null;
    protected array $options;

    protected \PDO $connect;
    private static $instance = null; //object instance

    private $tables = []; // all table names
    private $_t; // name table

    private function __construct(
        $dbName = null,
        $userName = null,
        $password = null,
        $options = array()
    ) {
        $this->dbName = $dbName;
        $this->userName = $userName;
        $this->password = $password;

        if (empty($options)) {
            $this->options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC // Устанавливает режим выборки по умолчанию
            ];
        }
        $this->connect();
    }

    private function connect()
    {
        try {
            $this->connect = new \PDO($this->dbName, $this->userName, $this->password, $this->options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
        return $this;
    }

    public static function setup(
        $dbName = null,
        $userName = null,
        $password = null,
        $options = array()
    ): Connect {
        // singleton
        if (self::$instance === null) {
            if (is_null($dbName)) {
                $dbName = 'sqlite:' . dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Database/identifier.sqlite';
            }
            self::$instance = new self($dbName, $userName, $password, $options);
        }
        return self::$instance;
    }

    /**
     * Executes an SQL statement, returning a result set as an Array
     * @param string $sql
     * @param array $params
     */
    public function query(string $sql, array $params = null)
    {
        if (!$params) {
            return $this->connect->query($sql);
        }
        $connect = $this->connect->prepare($sql);
        $result = $connect->execute($params);

        if (!$result) {
            throw new \Exception('не верный запрос');
        }
        return $connect;
    }

    /**
     * Execute an SQL statement
     */
    public function exec(string $sql)
    {
        $this->connect->exec($sql);
        return null;
    }

    public function __wakeup()
    {
        $this->connect();
    }

    public function table($name)
    {
        $this->tables = $this->connect->query("SELECT name FROM sqlite_master")->fetchAll(\PDO::FETCH_COLUMN);
        //убрал для тестов
        if (!in_array($name, $this->tables)) {
            throw new \Exception("Table not found");
        }
        $this->_t = $name;
        return $this;
    }

//    public function __call($method, $args)
//    {
//        return call_user_func_array(array($this->connect, $method), $args);
//    }

    public function droup()
    {
        $sql = 'DROP TABLE ' . $this->_t;
        return $this->exec($sql);
    }

    public function __call(string $name, array $args)
    {
        return match ($name) {
            'insert' => new Insert($this->connect, $this->_t, ...$args),
            'select' => new Select($this->connect, $this->_t, ...$args),
            'delete' => new Delete($this->connect, $this->_t, ...$args),
            'update' => new Update($this->connect, $this->_t, ...$args),

            default => throw new \Exception('Undefined class ' . $name . ' | Connect call |')
        };
    }
}

