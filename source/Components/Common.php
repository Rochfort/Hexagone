<?php
/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 24.05.17
 * Time: 14:19
 */

namespace Hexagone\Components;


trait Common
{
    /**
     * @return array
     * @throws \Exception
     */
    protected function getColumns()
    {
        if (empty($this->columns)) {
            throw new \Exception('Undefined column values');
        }

        $callback = function($value) {
            return '`' . $value . '`';
        };
        $columns = array_map($callback, $this->columns);
        return $columns;
    }

    /**
     * return array of prepared values with keys
     *
     * @return array
     * @throws \Exception
     */
    protected function getPreparedValues()
    {
        if (empty($this->properties)) {
            throw new \Exception('Undefined properties values');
        }

        $values = [];
        foreach ($this->properties as $key => $property) {
            $values[':' . $key] = $this->$property;
        }

        return $values;
    }

    /**
     * return array of keys for prepared values
     *
     * @return array
     * @throws \Exception
     */
    protected function getPreparedKeys() {

        if (empty($this->properties)) {
            throw new \Exception('Undefined properties values');
        }

        $keys = [];
        foreach ($this->properties as $key => $property) {
            $keys[] = ":$key";
        }
        return $keys;
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function getPreparedPrimaryKeys() {

        if (empty($this->pk)) {
            throw new \Exception('Undefined primary key in ' . self::class);
        }

        $keys = [];
        foreach ($this->pk as $item) {
            if (null === $this->{$item->property}) {
                $keys[] = '`' . $item->column . '` is null';
            } else {
                $keys[] = '`' . $item->column . '`=\'' . $this->{$item->property} . '\'';
            }
        }

        return $keys;
    }
}