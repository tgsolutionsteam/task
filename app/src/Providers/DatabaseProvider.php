<?php

namespace App\Providers;

use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Di\DiInterface;

class DatabaseProvider implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        $di->set('db', function (): PdoMysql {

                return new PdoMysql(
                    [
                        'host' => array_key_exists('MYSQL_HOST', $_SERVER)
                            ? $_SERVER['MYSQL_HOST'] : 'db',
                        'username' => array_key_exists('MYSQL_USER', $_SERVER)
                            ? $_SERVER['MYSQL_USER'] : 'root',
                        'password' => array_key_exists(
                            'MYSQL_PASSWORD',
                            $_SERVER
                        ) ? $_SERVER['MYSQL_PASSWORD'] : 'root',
                        'dbname' => array_key_exists('MYSQL_DATABASE', $_SERVER)
                            ? $_SERVER['MYSQL_DATABASE'] : 'task',
                    ]
                );
        });
    }
}
