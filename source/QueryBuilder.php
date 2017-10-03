<?php

namespace Hexagone;

/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 03.10.17
 * Time: 14:47
 */
class QueryBuilder
{
    protected $fields = '*';
    protected $where  = '';
    protected $limit  = '';
    protected $table  = '';
    protected $sql    = '';

    /**
     * Задать получаемые поля
     *
     * @param array|string $fields
     * @return QueryBuilder
     */
    public function select($fields)
    {
        // Если строка пустая или *
        if (is_string($fields) && ($fields == '*' || empty($fields))) {
            return $this;
        }

        // Экранируем поля, если нужно
        if (is_string($fields)) {
            $fields = explode(',', $fields);
        }

        $callback = function($value) {
            return sprintf('`%s`', $value);
        };

        $fields = array_map($callback, $fields);
        $fields_str = implode(',', $fields);

        $this->fields = $fields_str;

        return $this;
    }

    /**
     * @param string $table
     * @return $this
     * @throws \Exception
     */
    public function table(string $table)
    {
        if (empty($table)) {
            $message = 'Table name cant be empty';
            throw new \Exception($message);
        }
        $this->table = $table;

        return $this;
    }

    /**
     * Задать условие where
     * В зависимости от типов значений каждого параметра получится разный результат
     * Возможные комбинации типов параметров:
     *  - string, string, string    (a = b)
     *  - array, array, string      (a = b and c = d)
     *  - array, array, array       (a = b and c > d)
     *  - array, string, string     (a = b and c = d)
     *
     * @param array|string $keys       - ключи посика
     * @param array|string $value      - значения поиска
     * @param array|string $comparison - тип сравнения
     * @return QueryBuilder
     * @throws \Exception
     */
    public function where($keys, $value = '', $comparison = '=')
    {
        if (is_string($keys) && is_string($value) && is_string($comparison)) {
            // одно условие, одно сравнение, все просто
            $format = "`%s` %s '%s'";
            $this->where = sprintf($format, $keys, $comparison, $value);
        } else if (is_array($keys) && is_array($value)) {
            // Набор условий где значения одного массива колонки таблицы,
            // а значения другого массива - значения полей
            $where = [];
            $format = "`%s` %s '%s'";

            if (is_string($comparison)) {
                // Если сравнение строка - значит один тип сравнения на все условия
                foreach ($keys as $key => $val) {
                    $where[] = sprintf($format, $val, $comparison, $value[$key]);
                }
            } else {
                // Если сравнение массив, значит каждому условию свой тип сравнения
                foreach ($keys as $key => $val) {
                    $where[] = sprintf($format, $val, $comparison[$key], $value[$key]);
                }
            }
            $this->where = implode(' AND ', $where);

        } else if (is_array($keys) && empty($value)) {
            // Набор условий в одном массиве, где ключ массива - название колонки в таблице,
            // а значение массива - значение условия фильтрации
            // На все условия одно сравнение
            $where = [];
            $format = "`%s` %s '%s'";
            foreach ($keys as $key => $val) {
                $where[] = sprintf($format, $key, $comparison, $val);
            }
            $this->where = implode(' AND ', $where);
        } else {
            $message = 'key and value count must be equivalent';
            throw new \Exception($message);
        }

        return $this;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return $this
     * @throws \Exception
     */
    public function limit($limit, $offset = 0)
    {
        if ($limit < 0 || $offset < 0) {
            $message = 'limit and offset cant be smaller 0';
            throw new \Exception($message);
        }

        $format = "limit %d, %d";
        $this->limit = sprintf($format, $offset, $limit);

        return $this;
    }

    /**
     * Получить из запроса объект типа HexagoneEntity
     * 
     * @param HexagoneEntity $object
     * @param \PDO           $pdo
     * @return HexagoneEntity[]
     */
    public function getObject($object, \PDO $pdo = null)
    {
        $stmt = $this->getStmt($pdo);
        $stmt->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE, $object);
        return $stmt->fetchAll();
    }

    /**
     * @param \PDO $pdo
     * @return array
     */
    public function get(\PDO $pdo = null)
    {
        $stmt = $this->getStmt($pdo);
        return $stmt->fetchAll();
    }

    /**
     * @param \PDO|null $pdo
     * @return \PDOStatement
     * @throws \Exception
     */
    protected function getStmt(\PDO $pdo = null): \PDOStatement
    {
        if (null == $pdo) {
            $pdo = ConnectionManager::getDbh();
        }

        $where = '';
        if (!empty($this->where)) {
            $where = " WHERE " . $this->where;
        }

        $limit = '';
        if (!empty($this->limit)) {
            $limit = " " . $this->where;
        }

        if (empty($this->table)) {
            $message = 'Table name cant be empty';
            throw new \Exception($message);
        }

        $this->sql  = "SELECT " . $this->fields . " FROM " . $this->table . $where . $limit;
        $stmt = $pdo->prepare($this->sql);

        return $stmt;
    }

    /**
     * @return string
     */
    public function getSql(): string
    {
        return $this->sql;
    }
}