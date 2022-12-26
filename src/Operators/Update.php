<?php

namespace Lacerta\Operators;

class Update
{
    private array $fields;
    private string $table;
    protected $connect;

    public function __construct($connect, $table, ...$fields)
    {
        $this->connect = $connect;
        $this->table = $table;
        $this->fields = $fields;
    }
}