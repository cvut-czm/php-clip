<?php

namespace clip;

use clip\auth\IAuth;
use clip\context\RootContext;
use Ratchet\ConnectionInterface;
use React\EventLoop\LoopInterface;

class Console {

    private $con;
    private $isconsole=false;
    private $auth;
    private $loop;

    public function executeFuture(callable $callback,float $future=0.5)
    {
        $this->loop->addTimer($future,$callback);
    }

    public function __construct(ConnectionInterface $connection, IAuth $auth, LoopInterfacePlus $loop, array $rootContextes = [], array $rootCommands = []) {
        $this->con = $connection;
        if($connection instanceof CliConnection)
            $this->isconsole=true;
        $this->printBuilder()
                ->writeln('==============================')->send()
                ->writeln('   Context level interface    ')->send()
                ->writeln('==============================')->send()
                ->write('Username: ')->send();
        $this->context = new RootContext($this, [$rootContextes, $rootCommands]);
        $this->auth = $auth;
        $this->loop=$loop;
    }

    private $username = null;
    private $logged = false;

    public function getUsername(): string {
        return $this->username;
    }

    public function getAuth(): IAuth {
        return $this->auth;
    }

    private $context;
    private $context_old = [];

    public function popContext() {
        if ($this->context->getParent() != null) {
            $this->context = $this->context->getParent();
        }
    }

    public function setContext(Context $context, bool $clear = false) {
        $context->setParent($this->context);
        $this->context = $context;
        if ($clear) {
            $this->context->setParent(null);
        }
    }

    public function getContext(): Context {
        return $this->context;
    }

    private $bash = true;

    public function setBash(bool $value) {
        return $this->bash = $value;
    }

    public function isBash(): bool {
        return $this->bash;
    }

    private function handleLogin(string $data) {
        if ($this->logged == false) {
            if ($this->username == null) {
                if ($data == '') {
                    return false;
                }
                $this->username = $data;
                $this->printBuilder()->write('Password: ')->send();
            } else {
                if ($this->auth->authorize($this->username, $data)) {
                    $this->logged = true;
                    $this->printBuilder()->deleteLastLine()->deleteLastLine()->writeln('Logged in.')->sendInputLine();
                } else {
                    $this->username = null;
                    $this->printBuilder()->deleteLastLine()->deleteLastLine()->writeln('Wrong username or password.')->send()->write('Username: ')->send();
                }
            }
        }
        return $this->logged;
    }

    public function handle(string $data) {
        if (!$this->handleLogin($data)) {
            return;
        }
        $this->getContext()->handle($data);
    }

    public function rawOut(string $data) {
        $this->con->send($data);
    }

    public function close() {
        $this->con->close();
    }

    public function printBuilder(): PrintBuilder {
        return new PrintBuilder($this,$this->isconsole);
    }
}