<?php

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Http\Response;
use \Phalcon\Security\Random;
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

        $contacts = $app
            ->modelsManager
            ->executeQuery($phql)
        ;

        $data = [];

        foreach ($contacts as $contact) {
            $data[] = [
                'id'   => $contact->id,
                'lastName' => $contact->lastName,
                'firstName' => $contact->firstName,
                'middleName' => $contact->middleName,
            ];
        }

        echo json_encode($data);
    }
);
// получение конкретной записи по id
$app->get(
    '/api/contact/{id}',
    function ($id) use ($app) {
        $phql = 'SELECT * '
            . 'FROM MyApp\Models\Contacts '
            . 'WHERE id = :id: '
        ;

        $contacts = $app
            ->modelsManager
            ->executeQuery(
                $phql,
                [
                    'id' =>   $id
                ]
            )
        ;

        $data = [];

        foreach ($contacts as $contact) {
            $data[] = [
                'id'   => $contact->id,
                'lastName' => $contact->lastName,
                'firstName' => $contact->firstName,
                'middleName' => $contact->middleName,
            ];
        }

        echo json_encode($data);
    }
);

//запись данных в таблицу
// проверка curl -i -X POST -d '{"lastName":"Sysykin","firstName":"Sysyck","middleName":"Sysykovich"}'     http://0.0.0.0/api/contactadd
$app->post(
    '/api/contactadd',
    function () use ($app) {
        $random = new \Phalcon\Security\Random();
        $guid=$random->uuid();
        $contact = $app->request->getJsonRawBody();
        var_export($contact);
        $phql  = 'INSERT INTO MyApp\Models\Contacts '
            . '(id, lastName, firstName, middleName) '
            . 'VALUES '
            . '(\''.$guid.'\', :lastName:, :firstName:, :middleName:)'
        ;

        $status = $app
            ->modelsManager
            ->executeQuery(
                $phql,
                [
                    'id'   => $guid,
                    'lastName' => $contact->lastName,
                    'firstName' => $contact->firstName,
                    'middleName' => $contact->middleName,
                ]
            )
        ;

        $response = new Response();

        if ($status->success() === true) {
            $response->setStatusCode(201, 'Created');

            $contact->id = $status->getModel()->id;

            $response->setJsonContent(
                [
                    'status' => 'OK',
                    'data'   => $contact,
                ]
            );
        } else {
            $response->setStatusCode(409, 'Conflict');

            $errors = [];
            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    'status'   => 'ERROR',
                    'messages' => $errors,
                ]
            );
        }

        return $response;
    }
);

// Изменение записей в таблице
// curl -i -X PUT -d '{"lastName":"Testov","firstName":"Test","middleName":"Testovich"}'  http://0.0.0.0/api/contact/ef553cb7-f796-11ea-8fca-0242ac110002
$app->put(
    '/api/contact/{id}',
    function ($id) use ($app) {
        $contact = $app->request->getJsonRawBody();
        $set_arr=[];
        foreach ($contact as $key=>$val){
            if($key != 'id') {
                $set_arr[] = "$key = :{$key}:";
            }
        }
        $set=" SET ". implode(',',$set_arr)." ";

        $phql  = 'UPDATE MyApp\Models\Contacts '
            . $set
            . 'WHERE id = :id:';
        echo $phql;
        $status = $app
            ->modelsManager
            ->executeQuery(
                $phql,
                [
                    'id' => $id,
                    'lastName' => $contact->lastName,
                    'firstName' => $contact->firstName,
                    'middleName' => $contact->middleName,
                ]
            )
        ;

        $response = new Response();

        if ($status->success() === true) {
            $response->setJsonContent(
                [
                    'status' => 'OK'
                ]
            );
        } else {
            $response->setStatusCode(409, 'Conflict');

            $errors = [];
            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    'status'   => 'ERROR',
                    'messages' => $errors,
                ]
            );
        }

        return $response;
    }
);

// Удаление curl -i -X DELETE http://0.0.0.0/api/contact/ef553cb7-f796-11ea-8fca-0242ac110002

$app->delete(
    '/api/contact/{id}',
    function ($id) use ($app) {
        $phql = 'DELETE '
            . 'FROM MyApp\Models\Contacts '
            . 'WHERE id = :id:';

        $status = $app
            ->modelsManager
            ->executeQuery(
                $phql,
                [
                    'id' => $id,
                ]
            )
        ;

        $response = new Response();

        if ($status->success() === true) {
            $response->setJsonContent(
                [
                    'status' => 'OK'
                ]
            );
        } else {
            $response->setStatusCode(409, 'Conflict');

            $errors = [];
            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    'status'   => 'ERROR',
                    'messages' => $errors,
                ]
            );
        }

        return $response;
    }
);

$app->handle(
    $_SERVER["REQUEST_URI"]
);