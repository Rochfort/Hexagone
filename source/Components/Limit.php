<?php
/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 29.05.17
 * Time: 12:11
 */

namespace Hexagone\Components;


trait Limit
{
    /**
     * @var string
     */
    private $limit = "LIMIT %d";
    /**
     * @var null
     */
    private $limitCount = Null;

    /**
     * @param $limit
     * @return $this
     */
    public function setLimit($limit) {
        $this->limitCount = (int)$limit;

        return $this;
    }

    /**
     * @return string
     */
    protected function getStringLimit() {
        $string = '';
        if (null !== $this->limitCount && 0 < $this->limitCount) {
            $string = sprintf($this->limit, $this->limitCount);
        }

        return $string;
    }

}