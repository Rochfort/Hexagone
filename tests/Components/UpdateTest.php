<?php
/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 26.05.17
 * Time: 16:14
 */

namespace Components;


use Entities\TestGoodEntity;
use Entities\TestGoodEntityMultiKey;
use MokObjects\MockPDO;

class UpdateTest extends \PHPUnit_Framework_TestCase
{
    public function testUpdateGood()
    {
        $ent = new TestGoodEntity();
        $pdoMock = new MockPDO();

        $ent->setLogState(true);
        $ent->setSecondName('awdawd')->setAddr('street')->setName('aqweqwe')->update($pdoMock);
        $ent->setOnlyNewValues()->setSecondName('iosduerjfiusejf')->update($pdoMock);

        try {
            $ent->setOrder(['aaa' => 'desc'])->update($pdoMock);
        } catch (\Throwable $t) {
            $message = 'Set string empty for update';
            $this->assertEquals($message, $t->getMessage());
        }

        $ent->setOnlyNewValues(false)->update($pdoMock);
        $ent->setOrder(['aaa' => 'desc', 'bbb' => ''])->setIgnore()->setLimit(12)->update($pdoMock);

        $actual = $ent->getLog();
        foreach ($actual as $key => $item) {
            unset($actual[$key]['time']);
        }

        $expected = [
            [
                'query' => "UPDATE   `crazy_table` SET `id`= Null,`fio`='aqweqwe',`fio2`='awdawd'," .
                    "`addr`='street' WHERE `id` is null  ",
                'values' => ''
            ],
            [
                'query' => "UPDATE   `crazy_table` SET `fio2`='iosduerjfiusejf' WHERE `id` is null  ",
                'values' => ''
            ],
            [
                'query' => "UPDATE   `crazy_table` SET `id`= Null,`fio`='aqweqwe'," .
                    "`fio2`='iosduerjfiusejf',`addr`='street' WHERE `id` is null ORDER BY aaa desc ",
                'values' => ''
            ],
            [
                'query' => "UPDATE  IGNORE `crazy_table` SET `id`= Null,`fio`='aqweqwe'," .
                    "`fio2`='iosduerjfiusejf',`addr`='street' WHERE `id` is null ORDER BY aaa desc,bbb  LIMIT 12",
                'values' => ''
            ]
        ];

        $this->assertEquals($expected, $actual);
        $this->assertCount(4,$actual);
    }

    public function testUpdateGoodultiKey()
    {
        $ent = new TestGoodEntityMultiKey();
        $pdoMock = new MockPDO();

        // simple save
        $ent->setSecondName('awdawd')->setAddr('street')->setName('aqweqwe')->setLogState(true);
        $ent->update($pdoMock);

        $ent->setOnlyNewValues()->setSecondName('iosduerjfiusejf')->update($pdoMock);

        try {
            $ent->setOrder(['aaa' => 'desc'])->update($pdoMock);
        } catch (\Throwable $t) {
            $message = 'Set string empty for update';
            $this->assertEquals($message, $t->getMessage());
        }

        $ent->setOnlyNewValues(false)->update($pdoMock);

        $actual = $ent->getLog();
        foreach ($actual as $key => $item) {
            unset($actual[$key]['time']);
        }

        $expected = [
            [
                'query' => "UPDATE   `crazy_table` SET `id`= Null,`fio`='aqweqwe',`fio2`='awdawd'," .
                    "`addr`='street' WHERE `id` is null AND `fio`='aqweqwe'  ",
                'values' => ''
            ],
            [
                'query' => "UPDATE   `crazy_table` SET `fio2`='iosduerjfiusejf' " .
                    "WHERE `id` is null AND `fio`='aqweqwe'  ",
                'values' => ''
            ],
            [
                'query' => "UPDATE   `crazy_table` SET `id`= Null,`fio`='aqweqwe',`fio2`='iosduerjfiusejf'," .
                    "`addr`='street' WHERE `id` is null AND `fio`='aqweqwe' ORDER BY aaa desc ",
                'values' => ''
            ]
        ];

        $this->assertEquals($expected, $actual);
        $this->assertCount(3,$actual);
        $expected = new \stdClass();
        $expected->id = null;
        $expected->name = 'aqweqwe';
        $expected->addr = 'street';
        $expected->secondName = 'iosduerjfiusejf';
        $this->assertEquals($expected, $ent->asObject());
    }
}