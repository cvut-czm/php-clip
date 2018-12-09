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

namespace clip\output;

use clip\Console;
use clip\PrintBuilder;

class ProgressBar {

    private $builder;
    private $isconsole;
    public function __construct(PrintBuilder $builder,bool $out=true) {
        $this->builder=$builder;
        $this->isconsole=$out;
    }

    public function printBuilder() : PrintBuilder {
        return $this->builder;
    }
    public function display(float $percentage,string $text,bool $deleteLastLine=true)
    {
        $o='[';
        for($i=0;$i<100;$i+=10) {
            if($i<$percentage)
                $o .= '%';
            else
                $o .= '␣';
        }
        $o.='] '.$text;
        if($deleteLastLine && !$this->isconsole)
            $this->builder->deleteLastLine();
        if(!$this->isconsole)
            $this->builder->writeln($o)->send();
    }
}