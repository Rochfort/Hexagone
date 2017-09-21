<?php
/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 25.05.17
 * Time: 17:33
 */

namespace Hexagone\Components;


use Hexagone\ConnectionManager;

trait Update
{
    /**
     * @var string
     */
    private $update = "UPDATE %s %s `%s` SET %s %s %s %s";
    /**
     * @var bool
     */
    private $lastState = false;
    /**
     * @var bool
     */
    private $disableNulls = false;
    /**
     * @var bool
     */
    private $onlyNewValues = false;

    /**
     * @return string
     */
    private function getStringUpdate() {
        $where = $this->getStringWhere();
        $sets = implode(',', $this->getSets());
        $order = $this->getStringOrder();
        $limit = $this->getStringLimit();
        $update = sprintf($this->update, $this->priority, $this->ignore, $this->table, $sets, $where, $order, $limit);
        return $update;
    }

    /**
     * @throws \Exception
     */
    private function setLastState() {
        if (empty($this->properties)) {
            throw new \Exception('Cant fing properties to set update');
        }
        $this->lastState = new \stdClass();
        foreach ($this->properties as $property) {
            $this->lastState->$property = $this->$property;
        }
    }

    private function generateSetStatement($column, $value) {
        if (null === $value) {
            $string = '`' . $column . '`= Null';
        } else {
            $string = '`' . $column . '`=\'' . $value . '\'';
        }

        return $string;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getSets() {
        // need set only changed values
        $update = [];
        if ($this->onlyNewValues) {
            if (false === $this->lastState) {
                throw new \Exception('Cant find last entity state');
            }

            // Бежим по всем колонкам
            foreach ($this->columns as $key => $column) {
                // Получем название св-ва
                $propName = $this->properties[$key];
                // Если значение этого свойства изменилось с последнего состояния
                if ($this->lastState->$propName !== $this->$propName) {
                    // Генерируем строку на обновление
                    $update[] = $this->generateSetStatement($column, $this->$propName);
//                    $update[] = '`' . $column . '`=\'' . $this->$propName . '\'';
                }
            }
        } elseif ($this->disableNulls) {
            foreach ($this->columns as $key => $column) {
                // Получем название св-ва
                $propName = $this->properties[$key];
                // Если значение этого свойства изменилось с последнего состояния
                if (Null !== $this->$propName) {
                    // Генерируем строку на обновление
                    $update[] = $this->generateSetStatement($column, $this->$propName);
//                    $update[] = '`' . $column . '`=\'' . $this->$propName . '\'';
                }
            }
        } else {
            foreach ($this->columns as $key => $column) {
                // Получаем название св-ва
                $propName = $this->properties[$key];
                // Генерируем строку на обновление
                $update[] = $this->generateSetStatement($column, $this->$propName);
//                $update[] = '`' . $column . '`=\'' . $this->$propName . '\'';
            }
        }

        if (empty($update)) {
            throw new \Exception('Set string empty for update');
        }

        return $update;
    }

    /**
     * @param bool $only
     * @return $this
     */
    public function setOnlyNewValues($only = true)
    {
        if ($only) {
            $this->onlyNewValues = true;
        } else {
            $this->onlyNewValues = false;
        }

        return $this;
    }

    /**
     * @param bool $withNulls
     * @return $this
     */
    public function setDisableNulls($withNulls = true)
    {
        if ($withNulls) {
            $this->disableNulls = true;
        } else {
            $this->disableNulls = false;
        }

        return $this;
    }

    /**
     * @param \PDO|null $pdo
     * @throws \Exception
     */
    public function update(\PDO $pdo = null)
    {
        if (null == $pdo) {
            $pdo = ConnectionManager::getDbh();
        }

        $sql = $this->getStringUpdate();
        $stmt = $pdo->prepare($sql);

        if ($stmt instanceof \PDOStatement) {
            $this->addQuery($sql);
            $stmt->execute();
            $this->setLastState();
        } else {
            throw new \Exception('PDO prepare return false in update');
        }
    }
}