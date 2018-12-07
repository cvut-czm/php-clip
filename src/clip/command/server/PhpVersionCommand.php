<?php

namespace clip\command\server;

use clip\command\Command;
use clip\command\Params;
use clip\command\WaitForInput;
use clip\PrintBuilder;

class PhpVersionCommand extends Command {

    public static function name() : string {
        return 'php-version';
    }

    public static function description() : string {
        return 'Returns php version';
    }

    public static function usage(PrintBuilder $builder) {
        // TODO: Implement usage() method.
    }

    public function execute(Params $params) : ?WaitForInput {
        $this->printBuilder()->writeln('Current PHP version: '.phpversion())->sendInputLine();
        return null;
    }
}