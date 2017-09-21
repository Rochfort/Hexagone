<?php

namespace Hexagone;

use Hexagone\Database\Mysql;

/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 24.05.17
 * Time: 12:49
 */
class ConnectionManager
{
    /**
     * @var \PDO[]
     */
    protected static $dbhs = [];
    /**
     * @var \stdClass[]
     */
    protected static $sets = [];

    /**
     * @param $sets
     */
    public static function setSettings($sets)
    {
        self::$sets = (array) $sets;
    }

    /**
     * @param string $name
     * @return \PDO
     * @throws \Exception
     */
    public static function getDbh($name = 'default')
    {
        if (!isset(self::$sets[$name])) {
            throw new \Exception('Config ' . $name . ' not found');
        }

        if (!isset(self::$dbhs[$name])) {

            $sets = self::$sets[$name];
            // TODO: Сделать условие для разных бд (mysql, postgress, etc.)
            // Чтобы получать разные подключения в зависимотри от настройки
            $dbh = Mysql::initConnection($sets);

            if ($dbh instanceof \PDO) {
                self::$dbhs[$name] = $dbh;
            } else {
                throw new \Exception('Cant create ' . $name . ' connection');
            }
        }

        return self::$dbhs[$name];
    }
}