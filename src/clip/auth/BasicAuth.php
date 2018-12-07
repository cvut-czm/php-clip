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

namespace clip\auth;

class BasicAuth implements IAuth {

    private $users = [];
    private $filename;

    public function __construct(string $filename) {
        $this->filename=$filename;
        $this->users = json_decode(file_get_contents($filename), true);
    }

    public function authorize(string $username, string $password): bool {
        if(count($this->users)==0)
        {
            $this->userAdd($username,$password);
            return true;
        }
        if (isset($this->users[$username])) {
            $pass = $this->users[$username]['password'];
        } else {
            $pass = "$2y$10$.vGA1O9wmRjrwAVXD98HNOgsNpDczlqm3Jq7KnEd1rVAGv3Fykk1a";
        }
        return password_verify($password, $pass);
    }

    public function userExist(string $username): bool {
        return isset($this->users[$username]);
    }

    public function users(): array {
        return array_keys($this->users);
    }

    public function userHasClaim(string $username, string $claim): bool {
        return true;
    }

    public function userAdd(string $username, string $password) {
        $this->users[$username] = ["password" => password_hash($password, PASSWORD_DEFAULT)];
        file_put_contents($this->filename, json_encode($this->users));
    }
}