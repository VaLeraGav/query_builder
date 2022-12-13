<?php

namespace App;

class DBException extends \Exception
{

}

class DB
{
    const DIRECTORY_SEPARATOR = "/";
    protected ?string $dsn = null;
    protected ?string $username = null;
    protected ?string $password = null;
    protected array $options;
    protected \PDO $pdo;
    private string $lang = 'en';

    protected array $error_messages = [
        'en' => [
            0 => '%s: no SQL query passed',
        ],
        'ru' => [
            4 => '%s: не передан SQL запрос',
        ],
    ];

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

    public function setErrorMessagesLang(string $lang): self
    {
//        if (!array_key_exists($lang, $this->error_messages)) {
//            throw new DBException(
//                sprintf(
//                    '%s: language "%s" is not supported, use any of: "%s". ' .
//                    "Make a pull request for this library, or derive a new class from class 'Mysql' and add the " .
//                    "internationalization language for your language to property self::\$exception_i18n_messages",
//                    __METHOD__,
//                    $lang,
//                    implode('", "', array_keys($this->error_messages))
//                )
//            );
//        }

        $this->lang = $lang;
        return $this;
    }

    public function __wakeup()
    {
        $this->connect();
    }

    public static function setup(
        $dsn = null,
        $username = null,
        $password = null,
        $options = array()
    ): DB {
        if (is_null($dsn)) {
            $dsn = 'sqlite:' . dirname(__FILE__) . DIRECTORY_SEPARATOR . 'tmp.db';
            print_r($dsn);
        }
        return new self($dsn, $username, $password, $options);
    }


}



DB::setup()->setErrorMessagesLang('ru');

