<?php

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;

$loader = new Loader();
$loader->registerNamespaces(
    [
        'MyApp\Models' => __DIR__ . '/../app/models/',
    ]
);
file_put_contents('test.log',var_export($loader,1));
$loader->register();

$container = new FactoryDefault();
$container->set(
    'db',
    function () {
        return new PdoMysql(
            [
                'host'     => 'localhost',
                'username' => 'docker',
                'password' => '1111',
                'dbname'   => 'test',
            ]
        );
    }
);


$app = new Micro($container);
// получение списка
$app->get(
    '/api/contacts',
    function () use ($app) {
        $phql = 'SELECT id, lastName, firstName, middleName '
            . 'FROM MyApp\Models\Contacts '
            . 'ORDER BY lastName'
        ;

        $robots = $app
            ->modelsManager
            ->executeQuery($phql)
        ;

        $data = [];

        foreach ($robots as $robot) {
            $data[] = [
                'id'   => $robot->id,
                'lastName' => $robot->lastName,
                'firstName' => $robot->firstName,
                'middleName' => $robot->middleName,
            ];
        }

        echo json_encode($data);
    }
);
// получение конкретной записи по id
$app->get(
    '/api/contacts',
    function ($id) use ($app) {
        $phql = 'SELECT * '
            . 'FROM MyApp\Models\Contacts '
            . 'WHERE id = :id: '
        ;

        $robots = $app
            ->modelsManager
            ->executeQuery(
                $phql,
                [
                    'id' =>   $id
                ]
            )
        ;

        $data = [];

        foreach ($robots as $robot) {
            $data[] = [
                'id'   => $robot->id,
                'lastName' => $robot->lastName,
                'firstName' => $robot->firstName,
                'middleName' => $robot->middleName,
            ];
        }

        echo json_encode($data);
    }
);
$app->handle(
    $_SERVER["REQUEST_URI"]
);