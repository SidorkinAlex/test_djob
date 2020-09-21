<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Mvc\Micro;
use Phalcon\Http\Response;
use \Phalcon\Security\Random;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
//db connection
$configPath = APP_PATH . '/config/config.php';
$db=include $configPath;

// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
    ]
);

$loader->register();

$loader->registerNamespaces(
    [
        'MyApp\Models' => __DIR__ . '/../app/models/',
    ]
);
$container = new FactoryDefault();

$container->set(
    'db',
    function () {
        global $db;
        return $db;
    }
);

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

$app = new Application($container);

try {
    // Handle the request
    $response = $app->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {

    echo 'Exception: ', $e->getMessage();
}