<?php
/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 24.05.17
 * Time: 17:15
 */

namespace MokObjects;


class MockStatement extends \PDOStatement
{
    public function __construct() {}

    public function execute($input_parameters = null)
    {
        return true;
    }

}