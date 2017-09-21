<?php

namespace Components;

use Entities\TestBadEntity;
use Entities\TestBadEntity2;
use Entities\TestGoodEntity;
use const Hexagone\Components\LPRIORITY;
use MokObjects\MockPDO;

/**
 * Class InsertTest
 *
 * @package source\Components
 */
class InsertTest extends \PHPUnit_Framework_TestCase
{
    public function testGoodEntity() {
        $ent = new TestGoodEntity();
        $pdoMock = new MockPDO();

        // simple save
        $ent->setSecondName('awdawd')->setAddr('street')->setName('aqweqwe');
        $ent->save($pdoMock);

        // insert ingnore
        $ent->setIgnore()->setAddr('street222');
        $ent->save($pdoMock);

        // insert ingnore
        $ent->setPriority(LPRIORITY);
        $ent->save($pdoMock);

        $actual = $ent->getLog();
        foreach ($actual as $key => $item) {
            unset($actual[$key]['time']);
        }

        $expected = [
            [
                'query' => "INSERT   INTO `crazy_table` (`id`,`fio`,`fio2`,`addr`) VALUES (:0,:1,:2,:3)",
                'values' => [
                    ':0' => Null,
                    ':1' => 'aqweqwe',
                    ':2' => 'awdawd',
                    ':3' => 'street',
                ]
            ],
            [
                'query' => "INSERT  IGNORE INTO `crazy_table` (`id`,`fio`,`fio2`,`addr`) VALUES (:0,:1,:2,:3)",
                'values' => [
                    ':0' => 321,
                    ':1' => 'aqweqwe',
                    ':2' => 'awdawd',
                    ':3' => 'street222',
                ]
            ],
            [
                'query' => "INSERT LOW_PRIORITY IGNORE INTO `crazy_table` (`id`,`fio`,`fio2`,`addr`) " .
                    "VALUES (:0,:1,:2,:3)",
                'values' => [
                    ':0' => 321,
                    ':1' => 'aqweqwe',
                    ':2' => 'awdawd',
                    ':3' => 'street222',
                ]
            ],
        ];

        $this->assertEquals($expected, $actual);
        $this->assertCount(3,$actual);
        $this->assertEquals($ent->asObject(), (object)$ent->asArray());
    }

    public function testBadEntityExceptionTblName()
    {
        $ent = new TestBadEntity();
        $pdoMock = new MockPDO();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Undefined tbl_name for save');

        // simple save
        $ent->save($pdoMock);
    }

    public function testBadEntityExceptionColumn()
    {
        $ent = new TestBadEntity2();
        $pdoMock = new MockPDO();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Undefined column values');

        // simple save
        $ent->save($pdoMock);
    }
}