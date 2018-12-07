<?php

namespace clip\command\server;

use clip\command\Command;
use clip\command\Params;
use clip\command\WaitForInput;
use clip\PrintBuilder;

class PhpExtensionsCommand extends Command {

    public static function name() : string {
        return 'php-extensions';
    }

    public static function description() : string {
        return 'Returns loaded php extensions.';
    }

    public static function usage(PrintBuilder $builder) {
        // TODO: Implement usage() method.
    }

    public function execute(Params $params) : ?WaitForInput {
        $builder = $this->printBuilder()->write('Loaded extensions: ');
        $first = true;
        foreach (get_loaded_extensions() as $extension) {
            $builder->write(($first ? '' : ', ') . $extension);
            $first = false;
        }
        $builder->writeln('')->sendInputLine();
        return null;
    }
}