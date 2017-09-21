<?php
/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 25.05.17
 * Time: 18:27
 */

namespace Hexagone\Components;

/**
 * Trait Where
 *
 * @package Components
 */
trait Where
{
    /**
     * @var string
     */
    private $where = "WHERE %s";

    /**
     * @return string
     */
    protected function getStringWhere()
    {
        $where = $this->getPreparedPrimaryKeys();
        $preparedKeys = implode(' AND ', $where);
        $where = sprintf($this->where, $preparedKeys);
        return $where;
    }
}