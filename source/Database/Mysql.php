<?php

namespace Hexagone\Database;

/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 24.05.17
 * Time: 12:47
 */
class Mysql
{
    public static function initConnection($sets) {

        try {
            $dbh = new \PDO(
                "mysql:dbname={$sets->dbname};host={$sets->dbhost};port={$sets->dbport}",
                $sets->dbuser,
                $sets->dbpass,
                array(
                    \PDO::ATTR_PERSISTENT => false,
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8, SQL_MODE=\'\'',
                )
            );
            $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $dbh->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);

            return $dbh;

        }catch (\PDOException $e) {
            error_log($e->getMessage());
            throw new \Exception("PDO: ". $e->getMessage());
        }
    }
}