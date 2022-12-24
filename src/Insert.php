<?php

namespace Lacerta;

class Insert
{
    protected $fields = [];
    protected $values = [];
    protected $table = "";

    public function __construct($table, $args)
    {
        $this->table = $table;

        $firstKey = key($args);
        $this->fields = array_keys($args[$firstKey]);

        foreach ($args as $values) {
            $addQuotes = array_map(function ($v) {
                return (is_string($v)) ? "'" . $v . "'" : $v;
            }, $values);
            $this->values[] = "(" . implode(", ", $addQuotes) . ")";
        }
        print_r($this->values);

    }

    public function fields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }
    public function values(array $values)
    {
        $addQuotes = array_map(function ($v) {
            return (is_string($v)) ? "'" . $v . "'" : $v;
        }, $values);
        $this->values[] = "(" . implode(", ", $addQuotes) . ")";;
        return $this;
    }

    public function getStr(): string
    {
        $q = 'INSERT INTO ' . $this->table . ' '
            . '(' . implode(", ", $this->fields) . ')'
            . ' VALUES ';
        $q .= implode(",", $this->values);
        return $q ."\n";
    }
}