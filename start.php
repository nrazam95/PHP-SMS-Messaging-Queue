<?php
require_once realpath(__DIR__ . '/vendor/autoload.php');
use Workerman\Worker;
use Workerman\WebServer;
use Workerman\Autoloader;
use PHPSocketIO\SocketIO;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

define('GLOBAL_START', true);

require_once __DIR__ . '/start_web.php';
require_once __DIR__ . '/start_io.php';

Worker::runAll();
