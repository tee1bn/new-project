<?php

//composer autoloader
require_once '../vendor/autoload.php';


$dotenv = Dotenv\Dotenv::createImmutable(realpath('../'));
$dotenv->load();



require_once 'database.php';
require_once 'core/app.php';
require_once 'core/controller.php';
require_once 'core/operations.php';
