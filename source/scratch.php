<?php
/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 24.05.17
 * Time: 12:57
 */

$string = '/home/rochfort/PhpstormProjects/Hexagone/bin/../vendor/autoload.php';
echo realpath($string);
echo "\n";
echo realpath(dirname($string) . '/../');
die;

$string = '/home/rochfort/qqq.txt';
$pattern = "/^[-_\.\\a-zA-Z0-9]+$/";
var_dump(preg_match($pattern, $string));
die;


/**
 * Обновление данных в таблице
 *
 * @return bool
 */
function update(\PDO $pdo)
{
    if (empty($this->pk)) {
        return false;
    }

    $values = $this->prepareValuesGenerator(false);

    $onUpdate = [];
    foreach ($this->columns as $key => $column) {
        if (isset($values[':' . $key])) {
            $onUpdate[] = "`" . $column . "`=:" . $key;
        }
    }

    $onUpdateWhere = $this->onUpdateGenerator();

    $sql = "UPDATE `" . $this->table . "` SET " . join(',', $onUpdate) . " WHERE " . $onUpdateWhere . ";";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);

    return true;
}

/**
 * Удаление текущей записи
 *
 * @return bool
 */
function delete(\PDO $pdo)
{
    if (empty($this->pk)) {
        return false;
    }

    $onUpdateWhere = $this->onUpdateGenerator();
    $sql = "DELETE FROM `" . $this->table . "` WHERE " . $onUpdateWhere . ";";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return true;
}

/**
 * Получить now
 *
 * @return string
 */
function currentDateTime()
{
    return date('Y-m-d H:i:s');
}



















/**
 * Генерируем массив подготовленых значений
 *
 * @return array $keys
 */
    function keyGenerator()
{
    $countFields = count($this->columns);

    $keys = [];
    for ($i = 0; $i < $countFields; $i++) {
        $keys[] = ':' . $i;
    }

    return $keys;
}

    /**
     * Генерируем массив where primary key для кейса UPDATE
     *
     * @return string
     */
    function onUpdateGenerator()
{
    $where = [];
    foreach ($this->pk as $primary) {
        $property = $primary->property;
        $currentProperty = $this->getProperty($property);
        $pkValue = $currentProperty->getValue($this);
        $where[] = "`" . $primary->column . "` = '" . $pkValue . "'";
    }

    return join(" AND ", $where);
}

    /**
     * Генерируем массив значений для вставки в запрос
     *
     * @param bool $all собирать пустые значения и первичный ключ
     * @return array $values
     */
    function prepareValuesGenerator($all = true)
{
    $values = [];

    $pkProperties = [];
    if (!empty($this->pk)) {
        foreach ($this->pk as $primary) {
            $pkProperties[] = $primary->property;
        }
    }

    foreach ($this->properties as $key => $property) {
        $currentProperty = $this->getProperty($property);
        $value = $currentProperty->getValue($this);
        if (false === $all && (null === $value || in_array($property, $pkProperties))) {
            continue;
        }
        $values[':' . $key] = $value;
    }

    return $values;
}

    /**
     * Установка lastInsertId
     *
     * @param $id
     * @return bool
     */
    function setLastInsertId($id)
{
    if (empty($this->pk)) {
        return false;
    }

    $property = $this->pk[0]->property;
    $currentProperty = $this->getProperty($property);
    $currentProperty->setValue($this, $id);
    return true;
}

