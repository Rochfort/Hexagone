<?php
/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 25.05.17
 * Time: 18:31
 */

namespace Hexagone\Components;


use Hexagone\ConnectionManager;

trait Delete
{
    /**
     * @var string
     */
    private $delete = "DELETE FROM `%s` %s";

    /**
     * @return string
     */
    private function getStringDelete() {
        $table = $this->table;
        $where = $this->getStringWhere();

        return sprintf($this->delete, $table, $where);
    }

    /**
     * @param \PDO $pdo
     */
    public function delete(\PDO $pdo = null)
    {
        if (null == $pdo) {
            $pdo = ConnectionManager::getDbh();
        }

        $sql = $this->getStringDelete();
        $pdo->exec($sql);
        $this->addQuery($sql);
    }
}