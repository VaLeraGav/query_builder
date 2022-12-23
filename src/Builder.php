<?php

namespace Lacerta;

class Builder
{
    private $where = [];
    private $having = [];
    private $order = [];
    private $groupBy = [];
    private $join = [];
    private $limit = null;


    private function _select(
        $table,
        $wheres = [],
        $joins = null,
        $orders = null,
        $fields = "*",
        $group_bu = null,
        $havings = null,
        $limit = null
    ) {
        $q = "SELECT {$fields} FROM '{$table}'";

        if (!empty($joins)) {
            foreach ($joins as $join) {
                $q .= " {$join[0]} JOIN '{$join[1]}'";
                if (!empty($join[2])) {
                    $q .= " AS '{$join[2]}'";
                }
                $q .= " ON ({$join[3]})";
            }
        }

        if (!empty($wheres)) {
            $q .= " WHERE ";
            foreach ($wheres as $where) {
                $q .= "{$where[0]}";
                if (count($where) > 1) {
                    $q .= "({$where[1]} {$where[2]} {$where[3]})";
                }
                if (!isset($where)) {
                    $q .= " {$where[0]} ";
                    if (count($where) > 1) {
                        $q .= " ('{$where[1]}' {$where[2]} {$where[3]}) ";
                    }
                }
            }
        }

        if (!empty($group_bu)) {
            $q .= " GROUP BY ('" . implode("','", $group_bu) . "')";
        }

        if (!empty($havings)) {
            $q .= " HAVING";
            foreach ($havings as $having) {
                $q .= " {$having[0]}";
                if (count($having) > 1) {
                    $q .= "( '{$having[1]}' {$having[2]} {$having[3]} ) ";
                }
            }
        }

        if (!empty($orders)) {
            $q .= " ORDER BY";
            $tmp = [];
            foreach ($orders as $order) {
                $tmp[] = " '{$order[0]}' $order[1]";
            }
            $q .= implode(",", $tmp);
        }

        if (!empty($limit)) {
            $q .= " LIMIT {$limit[0]}";
            if (!empty($limit[1])) {
                $q .= " OFFSET {$limit[1]}";
            }
        }
        return $q;
    }

}