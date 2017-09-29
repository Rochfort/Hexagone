<?php
/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 29.05.17
 * Time: 13:35
 */

namespace Components;


use Entities\TestGoodEntity;
use MokObjects\MockPDO;

class DeleteTest extends \PHPUnit_Framework_TestCase
{
    public function testDeleteEntity()
    {
        $ent = new TestGoodEntity();
        $pdoMock = new MockPDO();

        $ent->setLogState(true)->setId(12)->delete($pdoMock);

        $actual = $ent->getLog();
        foreach ($actual as $key => $item) {
            unset($actual[$key]['time']);
        }

        $expected = [
            [
                'query' => "DELETE FROM `crazy_table` WHERE `id`='12'",
                'values' => ''
            ],
        ];

        $this->assertEquals($expected, $actual);
        $this->assertCount(1,$actual);
    }
}