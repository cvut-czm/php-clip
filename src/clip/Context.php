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

namespace clip;

use clip\command\Command;
use clip\command\core\CommandsCommand;
use clip\command\core\CdCommand;
use clip\command\core\LsCommand;
use clip\command\core\ExitCommand;
use clip\command\Params;
use clip\context\ClipContext;
use clip\context\RootContext;

abstract class Context extends DiContainer {

    public static function getContext(Console $console, string $name, array $options = []) : Context {
        return new self::$contexts[$name]($console, $options);
    }

    protected $options;
    protected $cached = [];
    private $console;

    public function cacheAdd(string $name, $value) {
        $this->cached[$name] = $value;
    }

    public function cacheGet(string $name, $default = null) {
        if (!isset($this->cached[$name])) {
            return $default;
        }
        return $this->cached[$name];
    }

    public function __construct(Console $console, array $options) {
        parent::__construct(['console' => $console, 'context' => $this]);
        $this->options = $options;
        $this->console = $console;
    }

    public abstract static function name() : string;

    public function custom_name() : string {
        return static::name();
    }

    private $parent = null;

    public function setParent(?Context $context) {
        $this->parent = $context;
    }

    public function getParent() : ?Context {
        return $this->parent;
    }

    public abstract static function description() : string;

    public function commands() : array {
        $arr = [
                CommandsCommand::class,
                CdCommand::class,
                LsCommand::class,
                ExitCommand::class
        ];
        return array_merge($arr, $this->context_commands());
    }

    public function childs() : array {
        return $this->context_childs();
    }

    protected abstract function context_commands() : array;

    protected abstract function context_childs() : array;

    private $waiting_command = null;

    public function handle($data) {
        if (strlen($data) > 0 && ord($data[0]) === 9) {
            $params = new Params(substr($data, 1));
            if ($params->get(0) !== null) {
                foreach ($this->commands() as $command) {
                    if (strtolower($command::name()) == $params->command()) {
                        $ac = $command::autoComplete($this->console, $this, $params);
                        if ($ac != null) {
                            $this->console->rawOut(chr(9) . $ac);
                            return;
                        }
                    }
                }
            } else {
                $valid_commands = [];
                foreach ($this->commands() as $command) {
                    if (strpos(strtolower($command::name()), $params->command()) === 0) {
                        $valid_commands[] = $command::name();
                    }
                }
                if (count($valid_commands) == 0) {
                    return;
                }
                if (count($valid_commands) == 1) {
                    $this->console->rawOut(chr(9) . $valid_commands[0]);
                    return;
                }
                if (count($valid_commands) > 1) {
                    $b = $this->printBuilder();
                    foreach ($valid_commands as $valid_command) {
                        $b->write($valid_command . ' ');
                    }
                    $b->writeln('')->send();
                }

                $return = '';
                for ($i = 0; $i < strlen($valid_commands[0]); $i++) {
                    foreach ($valid_commands as $valid_command) {
                        if (strlen($valid_command) <= $i || $valid_commands[0][$i] !== $valid_command[$i]) {

                            $this->console->rawOut(chr(9) . $return);
                            return;
                        }
                    }
                    $return .= $valid_commands[0][$i];
                }
                $this->console->rawOut(chr(9) . $return);
            }
            return;
        }
        if ($this->waiting_command !== null) {
            $params = new Params('null ' . $data);
            $return = $this->waiting_command->input($this->console, $this, $params);
            $this->waiting_command = $return;
            return;
        }
        $params = new Params($data);
        /** @var Command $command */
        foreach ($this->commands() as $command) {
            if (strtolower($command::name()) == $params->command()) {
                $cmd = new $command($this->console, $this);
                $return = $cmd->execute($params);
                if ($return !== null) {
                    $this->waiting_command = $return;
                }
                return;
            }
        }
        $this->console->printBuilder()->writeln('Unknown command. Type \'cmd\' for help.')->sendInputLine();
    }
}