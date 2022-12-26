<?php

namespace Lacerta\Operators;

class Select
{
    private array $fields;
    private string $table;
    protected \PDO $connect;

    private array $where = [];
    private array $having = [];
    private array $order = [];
    private array $groupBy = [];
    private array $join = [];
    private array $limit = [];

    private array $comparisonOperators = [
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

    private array $whereOperators = [
        'BETWEEN',
        'NOT BETWEEN',
        'IN',
        'NOT IN',
        'LIKE',
        'NOT LIKE',
        'EXISTS',
        'NOT EXISTS'
    ];

    private array $whereTypes = [
        'and',
        'or',
        '',
        'all',
        'any',
        '!'
    ];

    public function __construct($connect, $table, ...$fields)
    {
        $this->connect = $connect;
        $this->table = $table;
        empty($fields) ? $this->fields = ['*'] : $this->fields = $fields;
    }

    protected function _where($field, $sign, $value, $union)
    {
        if (!in_array($sign, $this->comparisonOperators) && !in_array($sign, $this->whereOperators)) {
            throw new \Exception("Не правильный операторы $sign | where");
        }
        if (!in_array($union, $this->whereTypes)) {
            throw new \Exception("Передан неверный тип {$union} | where");
        }

        // TODO: не нравится
        if (!is_integer($value) && $value[0] != ":" && $value[0] != "?") {
            if (is_array($value)) {
                $addQuotes = array_map(function ($v) {
                    return (is_string($v)) ? "'" . $v . "'" : $v;
                }, $value);

                $value = match ($sign) {
                    'BETWEEN', 'NOT BETWEEN' => $addQuotes[0] . " AND " . $addQuotes[1],
                    'IN', 'NOT IN' => '(' . implode(", ", $addQuotes) . ')',
                    default => "!!!"
                };
            } else {
                $value = "'" . $value . "'";
            }
        }
        return [$union, str_replace(".", "'.'", $field), $sign, $value];
    }

    public function whereBetween($field, array $value, $union = null)
    {
        $this->where[] = $this->_where($field, $sign = 'BETWEEN', $value, $union);
        return $this;
    }

    public function whereNotIn($field, array $value, $union = null)
    {
        $this->where[] = $this->_where($field, $sign = 'NOT IN', $value, $union);
        return $this;
    }

    public function whereIn($field, array $value, $union = null)
    {
        $this->where[] = $this->_where($field, $sign = 'IN', $value, $union);
        return $this;
    }

    public function whereLike($field, $value, $union = null)
    {
        $this->where[] = $this->_where($field, $sign = 'LIKE', $value, $union);
        return $this;
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

    public function whereGroup($union, callable $where)
    {
        if (!in_array($union, $this->whereTypes)) {
            throw new \Exception("Передан неверный тип {$union} | whereGroup");
        }
        $this->where[] = [" $union ("];
        $where($this);
        $this->where[] = [")"];
        return $this;
    }


    public function select()
    {
        return new self($this->connect, $this->table, $this->fields);
    }

    // TODO: не работает как надо
    public function whereExists(callable $where)
    {
        $this->where[] = [" EXISTS ("];
        $where($this);
        $this->where[] = [")"];
        return $this->getStr();
    }

    public function andWhere($field, $sign, $value)
    {
        $this->where[] = $this->_where($field, $sign, $value, $union = 'and');;
        return $this;
    }

    public function orWhere($field, $sign, $value)
    {
        $this->where[] = $this->_where($field, $sign, $value, $union = 'or');
        return $this;
    }

    //------------join
    private function _join($table, $field, $sign, $linkTo = null, $alias = null, $type = "INNER")
    {
        if (!in_array($sign, $this->comparisonOperators)) {
            throw new \Exception("Не правильный операторы $sign | join");
        }
        if ($linkTo === null) {
            $linkTo = "{$this->table}'.'id";
        } else {
            $linkTo = str_replace(".", "'.'", $linkTo);
        }
        $on = (empty($alias) ? "'{$table}'" : "'{$alias}'") . ".'{$field}' $sign '{$linkTo}'";
        $this->join[] = [$type, $table, $alias, $on];
    }

    public function join($table, $field, $sign = "=", $linkTo = null, $alias = null)
    {
        $this->_join($table, $field, $sign, $linkTo, $alias);
        return $this;
    }

    public function leftJoin($table, $field, $sign, $linkTo = null, $alias = null)
    {
        $this->_join($table, $field, $sign, $linkTo, $alias, "LEFT");
        return $this;
    }

    public function rightJoin($table, $field, $sign, $linkTo = null, $alias = null)
    {
        $this->_join($table, $field, $sign, $linkTo, $alias, "RIGHT");
        return $this;
    }

    //------------groupBy
    public function groupBy($field)
    {
        $this->groupBy[] = str_replace(".", "'.'", $field);
        return $this;
    }

    // ---------having
    public function having($field, $sign, $value, $union = null)
    {
        $this->having[] = $this->_where($field, $sign, $value, $union);
        return $this;
    }

    public function andHaving($field, $sign, $value = null)
    {
        $this->having = $this->_where($field, $sign, $value, $union = 'AND');
        return $this;
    }

    public function orHaving($field, $sign, $value = null)
    {
        $this->having = $this->_where($field, $sign, $value, $union = 'OR');
        return $this;
    }

    public function havingGroup($union, callable $having)
    {
        if (!in_array($union, $this->whereTypes)) {
            throw new \Exception("Передан неверный тип {$union} | havingGroup");
        }
        $this->having[] = [" $union ("];
        $having($this);
        $this->having[] = [")"];
        return $this;
    }

    // ------------order
    public function asc($field = "id")
    {
        $this->order[] = [str_replace(".", "'.'", $field), "ASC"];
        return $this;
    }

    public function desc($field = "id")
    {
        $this->order[] = [str_replace(".", "'.'", $field), "DESC"];
        return $this;
    }

    // -----------limit
    public function limit(int $limit, int $offset = null)
    {
        $this->limit = [$limit, $offset];
        return $this;
    }

    public function getStr(): string
    {
        $q = 'SELECT ' . implode(", ", $this->fields) . ' FROM ' . $this->table . ' ';

        if (!empty($this->join)) {
            foreach ($this->join as $join) {
                $q .= " {$join[0]} JOIN '{$join[1]}'";
                if (!empty($join[2])) {
                    $q .= " AS '{$join[2]}'";
                }
                $q .= " ON ({$join[3]})";
            }
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

        if (!empty($this->groupBy)) {
            $q .= " GROUP BY ('" . implode("','", $this->groupBy) . "')";
        }

        if (!empty($this->having)) {
            $q .= " HAVING ";
            foreach ($this->having as $having) {
                $q .= "{$having[0]}";
                if (count($having) > 1) {
                    $q .= "('{$having[1]}' {$having[2]} {$having[3]} )";
                }
            }
        }

        if (!empty($this->order)) {
            $q .= " ORDER BY";
            $tmp = [];
            foreach ($this->order as $order) {
                $tmp[] = " '{$order[0]}' $order[1]";
            }
            $q .= implode(",", $tmp);
        }
        if (!empty($this->limit)) {
            $q .= " LIMIT {$this->limit[0]}";
            if (!empty($this->limit[1])) {
                $q .= " OFFSET {$this->limit[1]}";
            }
        }

        return $q . "\n";
    }

    public function getAll()
    {
        $sql = $this->getStr();
        return $this->connect->query($sql)->fetchAll();
    }
}