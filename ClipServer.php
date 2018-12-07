<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use clip\Handler;

require dirname(__FILE__) . '/vendor/autoload.php';
/*
$server = IoServer::factory(
        new HttpServer(
                new WsServer(
                        new Handler()
                )
        ),
        8080
);*/

$server = IoServer::factory(
        new Handler(new \clip\auth\BasicAuth(__DIR__.'/authorized_users.json')),
        8080
);

$server->run();