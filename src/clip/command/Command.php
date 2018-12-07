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

namespace clip\command;


use clip\Console;
use clip\Context;
use clip\DiContainer;
use clip\PrintBuilder;

abstract class Command extends DiContainer {
    public function __construct(Console $console,Context $context) {
        parent::__construct(['console'=>$console,'context'=>$context]);
    }

    public abstract static function name() : string;
    public abstract static function description() : string;
    public abstract static function usage(PrintBuilder $builder);
    public static function autoComplete(Console $console, ?Context $context, Params $params) : ?string
    {
        return null;
    }
    public abstract function execute(Params $params) : ?WaitForInput;
}