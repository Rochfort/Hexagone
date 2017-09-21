<?php
/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 24.05.17
 * Time: 18:03
 */

namespace Hexagone\Components;

/**
 * Trait AsStructs
 *
 * @package Components
 */
trait AsStructs
{
    /**
     * Get Array from entity
     *
     * @return array
     */
    public function asArray() {
        $array = [];
        foreach ($this->properties as $key => $property) {
            $array[$property] = $this->$property;
        }

        return $array;
    }

    /**
     * Get stdObject from entity
     *
     * @return \stdClass
     */
    public function asObject() {
        $object = new \stdClass();
        foreach ($this->properties as $key => $property) {
            $object->$property = $this->$property;
        }

        return $object;
    }

    /**
     * Autofill entity from array/object
     *
     * @param      $input
     * @param bool $requiredAllProps
     * @throws \Exception
     */
    public function loadFromObject($input, $requiredAllProps = false)
    {
        if (!is_object($input) && !is_array($input)) {
            throw new \Exception('incoming data is not object');
        }

        $newInput = new \stdClass();

        foreach ($input as $item => $value) {
            $val = ucwords($item, '_');
            $field = str_replace('_', '', $val);
            $prop  = lcfirst($field);

            $newInput->$prop = $value;
        }

        $input = $newInput;

        foreach ($this->properties as $key => $property) {
            if (!isset($input->$property)) {
                if ($requiredAllProps) {
                    throw new \Exception('not found item \'' . $property . '\' in array');
                } else {
                    continue;
                }
            }
            $this->$property = $input->$property;
        }
    }
}