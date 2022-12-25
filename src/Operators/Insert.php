<?php

namespace Lacerta\Operators;

class Insert
{
    protected array $fields = [];
    protected array $values = [];
    protected string $table = "";

    public function __construct($table, $args)
    {
        // sorting array and values
        if (!empty($args)) {
            if (!is_array(reset($args))) {
                ksort($args);
                $args = [$args];
            } else {
                foreach ($args as $key => $value) {
                    ksort($value);
                    $args[$key] = $value;
                }
            }
        }

        $this->table = $table;

        if (count($args) > 0) {
            $keysOfFirstArray = array_keys(current($args));
            $this->fields($keysOfFirstArray);

            $this->values($args);
        }
        return $this;
    }

    public function fields(...$fields): static
    {
        if (is_array($fields[0])) {
            $this->fields = array_shift($fields);
        } else {
            // asort($fields);
            $this->fields = $fields;
        }
        return $this;
    }

    public function values(...$args): static
    {
        if (!is_array($args[0])) {
            $addQuotes = array_map(function ($v) {
                return (is_string($v)) ? "'" . $v . "'" : $v;
            }, $args);
            $this->values[] = "(" . implode(", ", $addQuotes) . ")";
        } else {
            foreach ($args[0] as $values) {
                $addQuotes = array_map(function ($v) {
                    return (is_string($v)) ? "'" . $v . "'" : $v;
                }, $values);
                $this->values[] = "(" . implode(", ", $addQuotes) . ")";
            }
        }
        return $this;
    }

    public function getStr(): string
    {
        return 'INSERT INTO ' . $this->table. ' '
            . '(' . implode(", ", $this->fields) . ')'
            . ' VALUES ' . implode(",", $this->values);
    }
}