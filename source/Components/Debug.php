<?php
/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 08.12.17
 * Time: 17:01
 */

namespace Hexagone\Components;


trait Debug
{
    public function __debugInfo()
    {
        return $this->asArray();
    }
}