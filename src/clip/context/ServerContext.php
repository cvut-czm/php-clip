<?php

namespace clip\context;

use clip\command\server\PhpExtensionsCommand;
use clip\command\server\PhpVersionCommand;
use clip\command\server\UptimeCommand;
use clip\Context;

class ServerContext extends Context {

    public static function name() : string {
        return 'Server';
    }

    public static function description() : string {
        return 'Information about enviroment and server settings.';
    }

    protected function context_commands() : array {
        return [PhpExtensionsCommand::class, PhpVersionCommand::class,UptimeCommand::class];
    }

    protected function context_childs() : array {
        return [ClipContext::class];
    }
}