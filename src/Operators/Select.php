<?php

namespace Lacerta\Operators;

class Select
{
    protected array $fields = [];
    protected array $values = [];
    protected string $table = "";

    public function __construct($table, $args)
    {
    }

    public function getStr(): string
    {
        return 'Select';
    }
}