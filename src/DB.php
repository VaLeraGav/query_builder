<?php

namespace Lacerta;

use Lacerta\DBException;

class DB
{
    const DIRECTORY_SEPARATOR = "/";
    protected ?string $dsn = null;
    protected ?string $username = null;
    protected ?string $password = null;
    protected array $options;
    protected \PDO $pdo;

    private function __construct(
        $dsn = null,
        $username = null,
        $password = null,
        $options = array()
    ) {
        $this->dsn = $dsn;
        $this->username = $username;
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
        $this->pdo = new \PDO($this->dsn, $this->username, $this->password);
        print_r($this->pdo);
        if ($this->pdo->errorCode()) {
            throw new DBException(
                sprintf(
                    "\n" . '%s: database connection error: %s',
                    __METHOD__,
                    $this->pdo->errorCode()
                )
            );
        }
        return $this;
    }


    public static function setup(
        $dsn = null,
        $username = null,
        $password = null,
        $options = array()
    ): DB {
        if (is_null($dsn)) {
            $dsn = 'sqlite:' . dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Database/identifier.sqlite';
        }
        return new self($dsn, $username, $password, $options);
    }

    /*
     * Executes an SQL statement, returning a result set as a Array
     */
    public function query(string $sql, $params = [])
    {
        $connect = $this->pdo->query($sql);
        $result = $connect->execute($params);

        if (!$result) {
            throw new \Exception('не верный запрос');
        }
        $matches = $connect->fetchAll(\PDO::FETCH_ASSOC);
        if ($matches === false) {
            throw new \Exception('Expect array, boolean given');
        }
        return $matches;
    }

    /*
     * Execute an SQL statement
     */
    public function exec(string $sql)
    {
        $this->pdo->exec($sql);
    }

    public function __wakeup()
    {
        $this->connect();
    }
}

