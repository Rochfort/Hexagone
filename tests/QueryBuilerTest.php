<?php

/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 03.10.17
 * Time: 16:31
 */
class QueryBuilerTest extends \PHPUnit_Framework_TestCase
{
    public function testSelect()
    {
        $pdoMock = new \MokObjects\MockPDO();

        $sql = 'SELECT * FROM table';
        $query = new \Hexagone\QueryBuilder();
        $query->table('table')->get($pdoMock);
        $this->assertEquals($sql, $query->getSql());


        $sql = 'SELECT * FROM table WHERE `a` = \'b\'';
        $query = new \Hexagone\QueryBuilder();
        $query->table('table')->where('a', 'b')->get($pdoMock);
        $this->assertEquals($sql, $query->getSql());


        $sql = 'SELECT `sss`,`yyy` FROM table WHERE `a` = \'b\'';
        $query = new \Hexagone\QueryBuilder();
        $query->select(['sss','yyy'])->table('table')->where('a', 'b')->get($pdoMock);
        $this->assertEquals($sql, $query->getSql());


        $sql = 'SELECT `sss`,`yyy` FROM table WHERE `a` = \'b\'';
        $query = new \Hexagone\QueryBuilder();
        $query->select('sss,yyy')->table('table')->where('a', 'b')->get($pdoMock);
        $this->assertEquals($sql, $query->getSql());


        $sql = 'SELECT * FROM table WHERE `a` = \'b\' AND `c` = \'d\'';
        $query = new \Hexagone\QueryBuilder();
        $query->table('table')->where(['a', 'c'], ['b', 'd'])->get($pdoMock);
        $this->assertEquals($sql, $query->getSql());


        $sql = 'SELECT * FROM table WHERE `a` = \'b\' AND `c` = \'d\'';
        $query = new \Hexagone\QueryBuilder();
        $query->table('table')->where(['a' => 'b', 'c' => 'd'])->get($pdoMock);
        $this->assertEquals($sql, $query->getSql());


        $sql = 'SELECT * FROM table WHERE `a` = \'b\' AND `c` > \'d\'';
        $query = new \Hexagone\QueryBuilder();
        $query->table('table')->where(['a', 'c'], ['b', 'd'], ['=', '>'])->get($pdoMock);
        $this->assertEquals($sql, $query->getSql());
    }
}