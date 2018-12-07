<?php

namespace clip\command\server;

use clip\command\Command;
use clip\command\Params;
use clip\command\WaitForInput;
use clip\PrintBuilder;

class UptimeCommand extends Command {

    public static function name() : string {
        return 'uptime';
    }

    public static function description() : string {
        return 'Server uptime';
    }

    public static function usage(PrintBuilder $builder) {
        // TODO: Implement usage() method.
    }

    public function execute(Params $params) : ?WaitForInput {
        $num=$this->getLinux();
        if($num==null)
        {
            $this->printBuilder()->writeln('Uptime works only under Linux systems')->sendInputLine();
            return null;
        }
        $secs  = fmod($num, 60); $num = intdiv($num, 60);
        $mins  = $num % 60;      $num = intdiv($num, 60);
        $hours = $num % 24;      $num = intdiv($num, 24);
        $days  = $num;
        $val='';
        if($days>0)
            $val.=$days.' days ';
        if($hours>0)
            $val.=$hours.' hours ';
        if($mins>0)
            $val.=$mins.' minutes ';
        if($secs>0)
            $val.=$secs.' seconds ';
        $this->printBuilder()->writeln('Up for '.$val)->sendInputLine();
        return null;
    }

    private function getWindows() : ?float {
        try {
            $uptime=(time()-filemtime('c:\pagefile.sys'));
            return floatval($uptime);
        }
        catch (\Exception $exception)
        {
            return null;
        }
    }
    private function getLinux() : ?float
    {
        try {
            $str   = @file_get_contents('/proc/uptime');
            return floatval($str);
        } catch (\Exception $exception)
        {
            return null;
        }
    }
}