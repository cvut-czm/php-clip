<?php

namespace clip;

use clip\auth\IAuth;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use React\EventLoop\LoopInterface;
use Thread;

class Handler implements MessageComponentInterface {
    protected $connections;
    protected $auth;
    protected $rootContextes=[];
    protected $rootCommands=[];
    protected $loop;


    public function __construct(IAuth $authHandle,LoopInterfacePlus $loop,array $rootContextes=[], array $rootCommands=[]) {
        $this->connections = new \SplObjectStorage;
        $this->auth = $authHandle;
        $this->rootCommands=$rootCommands;
        $this->rootContextes=$rootContextes;
        $this->loop=$loop;
        echo 'Created';
    }

    function onOpen(ConnectionInterface $conn) {
        echo 'Opened';
        $this->connections->attach($conn, new Console($conn, $this->auth,$this->loop,$this->rootContextes,$this->rootCommands));
    }

    function onClose(ConnectionInterface $conn) {
        $this->connections->detach($conn);
    }

    function onError(ConnectionInterface $conn, \Exception $e) {
        // TODO: Implement onError() method.
    }

    function onMessage(ConnectionInterface $from, $msg) {
        if(strlen($msg)>0) {
            if (ord($msg[0]) == 255) {
                return;
            }
            if (strrpos($msg, "\n") == strlen($msg) - 1) {
                $msg = substr($msg, 0, -1);
            }
            if (strrpos($msg, "\r") == strlen($msg) - 1) {
                $msg = substr($msg, 0, -1);
            }
        }
        $this->connections[$from]->handle($msg);
    }
}