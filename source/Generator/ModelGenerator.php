<?php
/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 30.05.17
 * Time: 13:48
 */

namespace Hexagone\Generator;


class ModelGenerator
{
    private $pdo;
    private $path;
    public function __construct(\PDO $pdo, $path)
    {
        $this->pdo = $pdo;
        $this->path = rtrim($path,'/');
        $this->createPath();
    }

    public function execute()
    {


        return true;
    }

    public function createPath()
    {
        $newPath  = $this->path . '/models';
        if (!file_exists($newPath)) {
            mkdir($newPath, 0777, true);
        }
    }
}