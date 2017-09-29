<?php
/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 25.05.17
 * Time: 18:25
 */

namespace Hexagone\Components;

const DISABLE   = 0;
const LPRIORITY = 1;
const DELAYED   = 2;

/**
 * Trait Ignore
 *
 * @package Hexagone\Components
 */
trait Ignore
{
    /**
     * @var string
     */
    private $priority = '';
    /**
     * @var array
     */
    private $priorityStack = [
        0 => '',
        1 => 'LOW_PRIORITY',
        2 => 'DELAYED'
    ];

    /**
     * @var string
     */
    private $ignore = '';

    /**
     * @param int $level
     * @return $this
     */
    public function setPriority(int $level)
    {
        if (!isset($this->priorityStack[$level])) {
            $level = 0;
        }
        $this->priority = $this->priorityStack[$level];
        return $this;
    }

    /**
     * @param bool $isNeedSet
     * @return $this
     */
    public function setIgnore(bool $isNeedSet = true) {
        if ($isNeedSet) {
            $this->ignore = 'IGNORE';
        } else {
            $this->ignore = '';
        }
        return $this;
    }
}