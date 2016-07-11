<?php
namespace jesusch\phpDuplicates\Command;

use Symfony\Component\Console\Command\Command;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

abstract class AbstractCommand extends Command
{

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        $paths = array(
            __DIR__ . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "Entity"
        );
        $isDevMode = false;

        // the connection configuration
        $dbParams = array(
            'driver' => 'pdo_mysql',
            'user' => 'root',
            'password' => '',
            'dbname' => 'phpDuplicates'
        );

        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
        return EntityManager::create($dbParams, $config);
    }
}