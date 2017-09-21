<?php

namespace Hexagone\Components;

use Hexagone\ConnectionManager;


/**
 * Trait Insert
 *
 * @package Hexagone\Components
 */
trait Insert
{
    private $insert = "INSERT %s %s INTO `%s` (%s) VALUES (%s)";
    private $lastId = Null;

    /**
     * Generate command string
     *
     * @return string
     * @throws \Exception
     */
    private function getStringInsert()
    {
        if (empty($this->table)) {
            throw new \Exception('Undefined tbl_name for save');
        }

        $columns = implode(',', $this->getColumns());
        $keys = implode(',', $this->getPreparedKeys());
        $string = sprintf($this->insert, $this->priority, $this->ignore, $this->table, $columns, $keys);
        return $string;
    }

    /**
     * Execute statement in database
     *
     * @param \PDO $pdo
     * @throws \Exception
     */
    public function save(\PDO $pdo = null)
    {
        if (null == $pdo) {
            $pdo = ConnectionManager::getDbh();
        }

        $sql = $this->getStringInsert();
        $values = $this->getPreparedValues();
        $stmt = $pdo->prepare($sql);

        $this->addQuery($sql, $values);

        if ($stmt instanceof \PDOStatement) {
            $stmt->execute($values);
            $this->lastId = $pdo->lastInsertId();
            if (count($this->pk) == 1) {
                $this->{$this->pk[0]->property} = $this->lastId;
            }
            $this->setLastState();
        } else {
            throw new \Exception('PDO prepare return false in save');
        }
    }

    /**
     * @return mixed
     */
    public function getLastInsertId()
    {
        return $this->lastId;
    }
}