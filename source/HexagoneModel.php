<?php

namespace Hexagone;

/**
 * Class HexagoneModel
 * Общий функционал для любой типовой модели
 *
 * Пока включает в себя
 * - заготовку и настройку для репозитория
 * - удобные setFetchClass
 *
 * @package Hexagone
 */
class HexagoneModel
{
    /**
     * @param \PDOStatement $stmt
     * @param               $obj
     */
    protected function setFetchClass(\PDOStatement $stmt, $obj)
    {
        $stmt->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE, $obj);
    }

    /**
     * @param \PDOStatement[] $stmts
     * @param                 $obj
     */
    protected function setsFetchClass(array $stmts, $obj)
    {
        foreach ($stmts as $stmt) {
            $stmt->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE, $obj);
        }
    }
}