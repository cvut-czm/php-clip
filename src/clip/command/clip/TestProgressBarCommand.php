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

class TestProgressBarCommand extends Command {

    public static function name(): string {
        return 'test-progress-bar';
    }

    public static function description(): string {
        return '';
    }

    public static function usage(PrintBuilder $builder) {
        // TODO: Implement usage() method.
    }

    public function execute(Console $console, ?Context $context, Params $params): ?WaitForInput {
        $bar=$console->printBuilder()->progressBar();
        $bar->display(0,'Loading..');
        $console->executeFuture(1,function() use ($console,$bar){
            $bar->printBuilder()->deleteLastLine()->writeln('Loaded 2500 students.')->send();
            $bar->display(40,'Loading..',false);

            $console->executeFuture(1,function() use ($console,$bar){
                $bar->display(80,'Loading..');

                $console->executeFuture(1,function() use ($console,$bar){
                    $bar->display(100,'Loading..');
                    $console->printBuilder()->sendInputLine();
                });
            });
        });
        /*
        $bar=$console->printBuilder()->writeln('test')->send()->progressBar();
        echo 'test';
        $bar->basicProgressBar(0,'Loading...');
        sleep(5);
        echo 'test2';
        $bar->basicProgressBar(10,'Loading...');
        sleep(5);
        $bar->basicProgressBar(15,'Loading...');
        sleep(5);
        $bar->basicProgressBar(50,'Loading...');
        $console->printBuilder()->sendInputLine();*/
        return null;
    }
}