<?php
/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 24.05.17
 * Time: 17:15
 */

namespace MokObjects;


class MockPDO extends \PDO
{
    public function __construct() {}

    public function prepare($statement, $driver_options = Null)
    {
        return new MockStatement();
    }

    public function exec($str)
    {
        return true;
    }

    public function lastInsertId($name = null)
    {
        return 321;
    }


}