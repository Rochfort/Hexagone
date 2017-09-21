<?php

namespace Entities;

use Hexagone\HexagoneEntity;

/**
 * Class TestEntity
 *
 * @package tests\entities
 * @Table(name=crazy_table)
 */
class TestGoodEntityMultiKey extends HexagoneEntity
{
    /**
     * @Column @pk
     */
    protected $id;
    /**
     * @Column(name=fio) @pk
     */
    protected $name;
    /**
     * @Column(name=fio2)
     */
    protected $secondName;
    /**
     * @Column(name=addr)
     */
    protected $addr;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return TestGoodEntity
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return TestGoodEntity
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSecondName()
    {
        return $this->secondName;
    }

    /**
     * @param mixed $secondName
     * @return TestGoodEntity
     */
    public function setSecondName($secondName)
    {
        $this->secondName = $secondName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddr()
    {
        return $this->addr;
    }

    /**
     * @param mixed $addr
     * @return TestGoodEntity
     */
    public function setAddr($addr)
    {
        $this->addr = $addr;

        return $this;
    }
}