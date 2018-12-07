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

namespace clip\command\clip;

use clip\command\Command;
use clip\command\Params;
use clip\command\WaitForInput;
use clip\Console;
use clip\Context;
use clip\PrintBuilder;

class ChangePasswordCommand extends Command {

    public static function name(): string {
        return 'changepassword';
    }

    public function execute(Params $params): ?WaitForInput {
        $this->console()->printBuilder()->write('Enter old password: ')->send();
        return new WaitForInput([$this, 'processOldPassword']);
    }

    public function processOldPassword(Console $console, ?Context $context, Params $data) {
        if ($console->getAuth()->authorize($console->getUsername(), $data->raw())) {
            $console->printBuilder()->write('Enter new password: ')->send();
            return new WaitForInput([$this, 'processNewPassword']);
        }
        else
        {
            $console->printBuilder()->writeln('Wrong password.')->sendInputLine();
            return null;
        }
    }
    private $password_new;
    public function processNewPassword(Console $console, ?Context $context, Params $data) {
        $this->password_new=$data->raw();
        $console->printBuilder()->write('Enter new password again: ')->send();
        return new WaitForInput([$this, 'processNewPasswordAgain']);
    }
    public function processNewPasswordAgain(Console $console, ?Context $context, Params $data) {
        if ($this->password_new===$data->raw()) {
            $console->getAuth()->userAdd($console->getUsername(),$data->raw());
            $console->printBuilder()->writeln('Passwords changed.')->sendInputLine();
        }
        else
        {
            $console->printBuilder()->writeln('Passwords don´t match.')->sendInputLine();
        }
        return null;
    }

    public static function description(): string {
        return 'Change active user password';
    }

    public static function usage(PrintBuilder $builder) {
        $builder->writeln('Usage: changepassword')->sendInputLine();
    }
}