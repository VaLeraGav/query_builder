<?php

namespace Lacerta\Operators;

class Select
{
    private array $fields;
    private string $table;
    protected $connect;

    private $where = [];
    private $having = [];
    private $order = [];
    private $groupBy = [];
    private $join = [];
    private $limit = null;

    private $comparisonOperators = [
        '=',
        '!=',
        '<>',
        '>',
        '<',
        '>=',
        '<=',
        '!<',
        '!>'
    ];

    private $whereOperators = [
        'BETWEEN',
        'NOT IN',
        'IN',
        'NOT LIKE',
        'EXISTS'

    ];

    private $whereTypes = [
        'and',
        'or',
        '',
        'all',
        'any',
    ];

    public function __construct($connect, $table, ...$fields)
    {
        $this->connect = $connect;
        $this->table = $table;
        empty($fields) ? $this->fields = ['*'] : $this->fields = $fields;
    }

    protected function _where($field, $sign, $value, $union)
    {
        if (!in_array($sign, $this->comparisonOperators)) {
            throw new \Exception("Не правильный операторы сравнения $sign | where");
        }
        if (!in_array($union, $this->whereTypes)) {
            throw new \Exception("Передан неверный тип {$union} | where");
        }
        if (!is_integer($value) && $value[0] != ":" && $value[0] != "?") {
            $value = "'" . $value . "'";
        }
        return [$union, str_replace(".", "'.'", $field), $sign, $value];
    }

    public function where($field, $sign, $value, $union = null)
    {
        $this->where[] = $this->_where($field, $sign, $value, $union);
        return $this;
    }

    public function wh($field, $sign, $value = null)
    {
        if (func_num_args() === 2) {
            $value = $sign;
            $sign = '=';
        }
        $this->where[] = $this->_where($field, $sign, $value, $union = null);
        return $this;
    }

    public function whereGroup($type, callable $where)
    {
        if (!in_array($type, $this->whereTypes)) {
            throw new \Exception("Передан неверный тип {$type} | whereGroup");
        }
        $this->where[] = [" $type ("];
        $where($this);
        $this->where[] = [")"];
        return $this;
    }

    public function andWhere()
    {
        $this->where[] = ["AND", "first", "=", "second"];
        return $this;
    }

    public function orWhere()
    {
        $this->where[] = ["OR", "first", "=", "second"];
        return $this;
    }


    public function getStr(): string
    {
        $q = 'SELECT ' . implode(", ", $this->fields) . ' FROM ' . $this->table . ' ';

        if (!empty($this->join)) {
        }
        if (!empty($this->where)) {
            $q .= 'WHERE';
            foreach ($this->where as $where) {

                $q .= "{$where[0]}";
                if (count($where) > 1) {
                    $q .= "(`{$where[1]}` {$where[2]} {$where[3]})";
                }
                if (!isset($where)) {
                    $q .= "{$where[0]}";
                    if (count($where) > 1) {
                        $q .= "('{$where[1]}' {$where[2]} {$where[3]})";
                    }
                }
            }
        }
        if (!empty($this->group_bu)) {
        }
        if (!empty($this->having)) {
        }
        if (!empty($this->orders)) {
        }
        if (!empty($this->limit)) {
        }

        return $q;
    }

    public function getAll()
    {
        $sql = $this->getStr();
        return $this->connect->query($sql)->fetchAll();
    }
}