<?php

namespace Hexagone;


use Hexagone\Components\AsStructs;
use Hexagone\Components\Delete;
use Hexagone\Components\Limit;
use Hexagone\Components\Order;
use Hexagone\Components\QueryHistory;
use Hexagone\Components\Update;
use Hexagone\Components\Where;
use Hexagone\Components\Ignore;
use Hexagone\Components\Insert;
use Hexagone\Components\Common;
use stdClass;
use ReflectionClass;

/**
 * Class HexagoneEntity
 *
 * Абстрактная сущность от которой нужно наследовать сущности в пользовательском коде
 *
 * @package Hexagone
 */
class HexagoneEntity
{
    use AsStructs, QueryHistory;
    use Common, Ignore, Where;
    use Insert, Update, Delete;
    use Order, Limit;

    /**
     * @var \ReflectionClass $selfReflectionClass
     */
    private $selfReflectionClass;

    /**
     * Первичный ключ таблицы
     *
     * @var \stdClass[]
     */
    private $pk = [];

    /**
     * Название таблицы
     *
     * @var string
     */
    private $table;

    /**
     * Колонки таблицы
     *
     * @var array
     */
    private $columns;

    /**
     * Свойства класса
     *
     * @var array
     */
    private $properties;

    /**
     * AbstractReflectionEntity constructor.
     */
    public function __construct()
    {
        $this->entityReflection();
    }

    /**
     * С помощью ReflectionClass парсит текущую entity и собирает данные для работы с бд
     */
    private function entityReflection()
    {
        $this->selfReflectionClass = new ReflectionClass(static::class);
        $this->findColumns();
        $this->findTableName();
    }

    /**
     * Бегаем по свойствам и ищем поля таблицы
     */
    private function findColumns()
    {
        $properties = $this->selfReflectionClass->getProperties();
        foreach ($properties as $property) {
            $currentProperty = $this->selfReflectionClass->getProperty($property->name);
            $docBlock = $currentProperty->getDocComment();
            $this->inspectionColumn($docBlock, $property->name);
        }
    }

    /**
     * Колонка и название колонки
     *
     * @param $docBlock string
     * @param $name     string
     */
    private function inspectionColumn($docBlock, $name)
    {
        $columnName = $name;
        $regexpColumn = '/@Column\(?(name=([a-zA-Z0-9_]+))?\)?/mu';
        preg_match($regexpColumn, $docBlock, $result);
        if (!empty($result)) {
            $columnName = isset($result[2]) ? $result[2] : $name;
            $this->columns[] = $columnName;
            $this->properties[] = $name;
        }

        $regexpPk = '/@pk/mu';
        preg_match($regexpPk, $docBlock, $result);
        if (!empty($result)) {
//            $columnName = isset($result[2]) ? $result[2] : $name;
            $primary = new stdClass();
            $primary->column = $columnName;
            $primary->property = $name;
            $this->pk[] = $primary;
        }
    }

    /**
     * Парсим название таблицы
     */
    private function findTableName()
    {
        $regexp = '/@Table\(name=([a-zA-Z0-9_]+)\)/mu';

        $docBlock = $this->selfReflectionClass->getDocComment();

        preg_match($regexp, $docBlock, $result);

        if (2 == count($result)) {
            $this->table = $result[1];
        }
    }

    /**
     * Получить объект св-ва от рефлексии
     *
     * @param $name
     * @return \ReflectionProperty
     */
    private function getProperty($name)
    {
        $currentProperty = $this->selfReflectionClass->getProperty($name);
        if ($currentProperty->isPrivate() || $currentProperty->isProtected()) {
            $currentProperty->setAccessible(true);
        }
        return $currentProperty;
    }

    /**
     * Нужно для установки св-ва если св-во класса отличается от названия поля в бд
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (in_array($name, $this->columns)) {
            $key = array_search($name, $this->columns);
            $property = $this->getProperty($this->properties[$key]);
            $property->setValue($this, $value);
        }
    }
}