<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'prod-db',
    'database' => 'prod_db',
    'username' => 'root',
    'password' => 'root'
], 'prod');

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'vdev-db',
    'database' => 'vdev_db',
    'username' => 'root',
    'password' => 'root'
], 'vdev');

$capsule->setAsGlobal();
$capsule->bootEloquent();

return $capsule;
