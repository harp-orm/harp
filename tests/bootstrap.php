<?php

error_reporting(E_ALL);

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->addPsr4('Harp\\Db\\Test\\', __DIR__.'/src');
