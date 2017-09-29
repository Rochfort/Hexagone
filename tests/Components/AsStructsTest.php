<?php
/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 29.09.17
 * Time: 15:22
 */

namespace Components;


use Entities\TestGoodEntity;

class AsStructsTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadStruct()
    {
        $ent = new TestGoodEntity();
        $obj = (object)[
            'id'            => 15,
            'name'          => 'my name',
            'secondName'    => 'my second name',
            'addr'          => 'city, street',
            'asdjasd'       => 'rtyuiop'
        ];

        $ent->loadFromObject($obj);

        $this->assertEquals($obj->id        , $ent->getId());
        $this->assertEquals($obj->name      , $ent->getName());
        $this->assertEquals($obj->secondName, $ent->getSecondName());
        $this->assertEquals($obj->addr      , $ent->getAddr());
    }

    public function testLoadStructRequired()
    {
        $ent = new TestGoodEntity();
        $obj = (object)[
            'id'            => 15,
            'name'          => 'my name',
            'secondName'    => 'my second name',
        ];

        $this->expectException(\Exception::class);

        $ent->loadFromObject($obj, true);
    }

    public function testGetObject()
    {
        $ent = new TestGoodEntity();
        $obj = (object)[
            'id'            => 15,
            'name'          => 'my name',
            'secondName'    => 'my second name',
            'addr'          => 'city, street',
        ];

        $ent->loadFromObject($obj);

        $this->assertEquals(true, $ent->asObject() instanceof \stdClass);
        $this->assertEquals($obj, $ent->asObject());
        $this->assertEquals(true, is_array($ent->asArray()));
        $this->assertEquals((array)$obj, $ent->asArray());
    }
}