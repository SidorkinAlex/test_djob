<?php
/**
 * Created by PhpStorm.
 * User: seedteam
 * Date: 21.09.20
 * Time: 15:35
 */
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
$settings =
    [
        'host' => 'localhost',
        'username' => 'docker',
        'password' => '1111',
        'dbname' => 'test',
    ];

return new PdoMysql($settings);