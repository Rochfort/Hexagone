<?php
/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 29.05.17
 * Time: 16:28
 */

namespace Hexagone\Commands;

use Hexagone\Generator\EntityGenerator;
use Hexagone\ConnectionManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class GenerateEntities extends Command
{
    protected function configure()
    {
        $this
            ->setName('ge')
            ->setDescription('Generate entities from database tables')
            ->addArgument(
                'test',
                InputArgument::OPTIONAL,
                'use test options'
            )
//            ->addOption(
//                'yell',
//                null,
//                InputOption::VALUE_NONE,
//                'If set, the task will yell in uppercase letters'
//            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>This script generate all entities and all models with selectByPrimaryMethod from database tables</info>");

        $helper = $this->getHelper('question');

        // Выясняем где конфиг подключения к базе
        $message  = "Enter path to configuration file (relative path from project path ):\n";
        $question = new Question($message);
        $question->setNormalizer(function ($value) {
            $string  = '';
            $pattern = "/^[-_\.\\a-zA-Z0-9]+$/";
            if (preg_match($pattern, $value)) {
                $string = trim($value);
            }

            return $string;
        });

        while (empty($fileConfig = $helper->ask($input, $output,$question))
            || !is_file(PROJECT_PATH . $fileConfig)) {
            if (empty($fileConfig)) {
                $output->writeln('<comment>Cant find file ' . PROJECT_PATH . $fileConfig . '</comment>');
            } else {
                $output->writeln('<comment>This file not looks like good, try one more time pls</comment>');
            }
        }

        $message  = "Enter the path where you want to create the entities/models (relative path from project path ) ?\n";
        $question = new Question($message);
        $question->setNormalizer(function ($value) {
            $string  = '';
            $pattern = "/^[-_\.\\a-zA-Z0-9]+$/";
            if (preg_match($pattern, $value)) {
                $string = trim($value);
            }

            return $string;
        });

        while (empty($path = $helper->ask($input, $output, $question))) {
            $output->writeln('<comment>Please, enter the path where you want to create the entities/models</comment>');
        }

        $message  = "Enter namespace for entities: \n";
        $callback = function ($value) {
            $string  = '';
            $pattern = "/^[-_\.\\a-zA-Z0-9]+$/";
            if (preg_match($pattern, $value)) { $string = trim($value); }
            return $string;
        };
        $question = new Question($message);
        $question->setNormalizer($callback);
        $namespace = $helper->ask($input, $output, $question);

        $message           = "Delete already exists entities/models and create new (y/n) ?\n";
        $question          = new ConfirmationQuestion($message, false);
        $deleteOldEntities = $helper->ask($input, $output, $question);

        $filesCreated = $this->createEntities($fileConfig, $path, $deleteOldEntities, $namespace);

        $out = array();
        $path = rtrim(PROJECT_PATH . $path, '/');
        exec(" ls -RGg " . $path . "/entity", $out);
        $output->writeln('<comment>Created ' . $filesCreated . ' entity files. ls:</comment>');
        $output->writeln(implode("\n",$out));
    }

    public static function toObject($array)
    {
        $result = new \stdClass();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result->{$key} = self::toObject($value);
            } else {
                $result->{$key} = $value;
            }
        }
        return $result;
    }

    /**
     * @param $fileConfig
     * @param $path
     * @param $deleteOldEntities
     * @param $namespace
     * @return int
     */
    protected function createEntities($fileConfig, $path, $deleteOldEntities, $namespace)
    {
        $data   = require_once(PROJECT_PATH . $fileConfig);
        $config = self::toObject($data);

        ConnectionManager::setSettings($config->databases);
        $pdo = ConnectionManager::getDbh();

        $entityGen    = new EntityGenerator();
//        $pdo, PROJECT_PATH . $path, $deleteOldEntities
        $entityGen->setPdo($pdo);
        $entityGen->setPath(PROJECT_PATH . $path);
        $entityGen->setNamespace($namespace);
        $entityGen->setDeleteOldEntities($deleteOldEntities);
        $filesCreated = $entityGen->execute();

        return $filesCreated;
    }
}