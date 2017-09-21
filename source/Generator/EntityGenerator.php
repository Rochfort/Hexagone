<?php
/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 30.05.17
 * Time: 13:48
 */

namespace Hexagone\Generator;


class EntityGenerator
{
    /**
     * @var \PDO
     */
    private $pdo;
    private $path = '';
    private $namespace;
    private $deleteOldEntities;

    public function __construct(\PDO $pdo = null, $path = null, $namespace = '', $deleteOldFiles = false)
    {
        if (null !== $pdo) {
            $this->pdo = $this->setPdo($pdo);
        }

        if (null !== $path) {
            $this->path = $this->setPath((string)$path);
        }

        if (null != $namespace) {
            $this->namespace = $this->setNamespace((bool)$namespace);
        } else {
            $this->namespace = 'entity';
        }

        $this->deleteOldEntities = $deleteOldFiles;
    }

    public function execute()
    {
        $this->createPath();

        $result = $this->pdo->query('show tables');
        $result->execute();
        $tables = $result->fetchAll(\PDO::FETCH_COLUMN);

        if (empty($tables)) {
            return False;
        }

        $separator = "_";

        $templateEntity = file_get_contents(dirname(__FILE__) . '/templates/entity.txt');
        $templateField = file_get_contents(dirname(__FILE__) . '/templates/fields.txt');
        $templateGet = file_get_contents(dirname(__FILE__) . '/templates/getter.txt');
        $templateSet = file_get_contents(dirname(__FILE__) . '/templates/setter.txt');

        $count = 0;

        foreach ($tables as $table) {

            $classname = str_replace($separator, '', ucwords($table, $separator));
            $entity = $this->path . "/entity/" . $classname . ".php";

            // Если не нужно удалять существующие сущности
            if (false === $this->deleteOldEntities && file_exists($entity)) {
                continue;
            }

            $columns = $this->pdo->query('DESCRIBE ' . $table);
            $result = $columns->fetchAll();

            $fields = "";
            $functions = "";
            foreach ($result as $column) {
                $pk = ($column->Key == "PRI") ? "@pk" : "";
                $function   = str_replace($separator, '', ucwords($column->Field, $separator));
                $fieldName  = lcfirst($function);
                $fields    .= sprintf($templateField, $column->Field, $pk, $fieldName);
                $functions .= sprintf($templateGet, $function, $fieldName);
                $functions .= sprintf($templateSet, $function, $fieldName);
            }

            $class = sprintf($templateEntity, $classname, $table, $fields, $functions, $this->namespace);

            file_put_contents($entity, $class . "\n");
            chmod($entity, 0774);

            $count++;
        }

        return $count;
    }

    public function createPath()
    {
        $newPath  = $this->path . '/entity';
        if (!file_exists($newPath)) {
            mkdir($newPath, 0777, true);
        }
    }

    /**
     * @param \PDO $pdo
     * @return EntityGenerator
     */
    public function setPdo(\PDO $pdo): EntityGenerator
    {
        $this->pdo = $pdo;

        return $this;
    }

    /**
     * @param string $path
     * @return EntityGenerator
     */
    public function setPath(string $path): EntityGenerator
    {
        $this->path = rtrim($path, '/');

        return $this;
    }

    /**
     * @param mixed $namespace
     * @return EntityGenerator
     */
    public function setNamespace($namespace)
    {
        if (!empty($namespace)) {
            $this->namespace = rtrim($namespace, '\\') . '\\entity';
        } else {
            $this->namespace = 'entity';
        }

        return $this;
    }

    /**
     * @param bool $deleteOldEntities
     * @return EntityGenerator
     */
    public function setDeleteOldEntities(bool $deleteOldEntities): EntityGenerator
    {
        $this->deleteOldEntities = $deleteOldEntities;

        return $this;
    }
}