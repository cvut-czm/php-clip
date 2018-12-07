<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * This class provide full control over sandboxes.
 *
 * @package local_personal_sandbox
 * @category core
 * @copyright 2018 CVUT CZM, Jiri Fryc
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace clip\command\core;

use clip\command\Command;
use clip\command\Params;
use clip\command\WaitForInput;
use clip\Console;
use clip\Context;
use clip\context\RootContext;
use clip\PrintBuilder;

class CdCommand extends Command {

    public function execute(Params $params): ?WaitForInput {
        $context=$this->context();
        $console=$this->console();

        if ($params->get(0) == '.') {
            $console->printBuilder()->sendInputLine();
            return null;
        }
        if ($params->get(0) == '..') {
            $console->popContext();
            $console->printBuilder()->sendInputLine();
            return null;
        }
        if ($params->get(0) == '/') {
            $console->setContext(new RootContext($console, []), true);
            $console->printBuilder()->sendInputLine();
            return null;
        }
        foreach ($context->childs() as $child) {
            if ($child::name() == $params->get(0)) {
                try {
                    $console->setContext(new $child($console, array_slice($params->getAll(), 1)));
                    $console->printBuilder()->writeln('Switched to context ' . $params->get(0) . '.')->sendInputLine();
                    return null;
                } catch (\Exception $e) {
                    $console->printBuilder()->writeln($e->getMessage())->sendInputLine();
                    return null;
                }
            }
        }
        $console->printBuilder()->writeln('Context ' . $params->get(0) . ' doesn´t exist.')->sendInputLine();
        return null;
    }

    public static function autoComplete(Console $console, ?Context $context, Params $params): ?string {
        $t = $params->get(0);
        if ($t == null || $t == '') {
            return null;
        }
        $valid_names = [];
        foreach ($context->childs() as $child) {
            if (strpos($child::name(), $t)===0) {
                $valid_names[] = $child::name();
            }
        }
        if (count($valid_names) == 0) {
            return null;
        }
        if (count($valid_names) == 1) {
            return 'cd ' . $valid_names[0];
        }
        $return = 'cd ';
        for ($i = 0; $i < strlen($valid_names[0]); $i++) {
            foreach ($valid_names as $valid_name) {
                if ($valid_name[$i] !== $valid_name[0] || strlen($valid_name[$i]) <= $i) {
                    return $return;
                }
            }
            $return .= $valid_names[0][$i];
        }
        return $return;
    }

    public static function name(): string {
        return 'cd';
    }

    public static function description(): string {
        return 'Switches context.';
    }

    public static function usage(PrintBuilder $builder) {
        $builder->writeln('Usage:')->send()
                ->writeln('cd ..        - Return one context higher')->send()
                ->writeln('cd /         - Return to root')->send()
                ->writeln('cd [context] - Move to context')->sendInputLine();
    }
}