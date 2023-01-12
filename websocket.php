<?php
require_once(__DIR__ . '/server/socket_server.php');

$socketServer = new SocketServer();

$socketServer->run();