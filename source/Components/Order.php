<?php
/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 29.05.17
 * Time: 12:11
 */

namespace Hexagone\Components;


trait Order
{
    /**
     * @var bool|\stdClass
     */
    private $order = false;
    /**
     * @var string
     */
    private $orderString = "ORDER BY %s";

    /**
     * @return string
     */
    protected function getStringOrder() {
        $string = '';
        if (false !== $this->order) {
            $order = [];
            foreach ($this->order as $name => $orderDirect) {
                $order[] = $name . " " . $orderDirect;
            }



            $string = sprintf($this->orderString, implode(',',$order));
        }

        return $string;
    }

    /**
     * @param bool $order
     * @return $this
     */
    public function setOrder($order = false)
    {
        if (!is_array($order) || !is_object($order)) {
            $this->order = false;
        }

        if (is_array($order)) {
            $order = (object)$order;
        }

        $this->order = $order;

        return $this;
    }
}