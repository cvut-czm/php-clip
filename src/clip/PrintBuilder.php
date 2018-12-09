<?php

namespace clip;

use clip\output\ProgressBar;

class PrintBuilder {

    /** @var Console */
    private $console;

    /** @var string */
    private $msg='';

    private $opened = [];

    private $isconsole=false;

    public function __construct(Console $console,bool $isconsole=false) {
        $this->console = $console;
        $this->isconsole=$isconsole;
    }

    public function write(string $string) : PrintBuilder
    {
        $this->msg.=$string;
        return $this;
    }

    public function writeln(string $line) : PrintBuilder
    {
        return $this->write($line)->endln();
    }
    public function endln() : PrintBuilder {
        $this->msg.=$this->console->isBash()?"\r\n":'<br/>'."\r\n";
        return $this;
    }
    public function send() : PrintBuilder {
        //$this->formatReset()->foregroundDefault();
        $this->console->rawOut($this->msg);
        $this->msg='';
        $this->opened=[];
        return $this;
    }
    public function deleteLastLine() : PrintBuilder
    {
        $this->console->rawOut(chr(127));
        return $this;
    }
    public function progressBar() : ProgressBar
    {
        return new ProgressBar($this,$this->isconsole);
    }
    public function sendInputLine() : PrintBuilder
    {
        $this->send();
        $this->console->rawOut('<span class="ctx_hdl" style="color: sienna">'.$this->console->getUsername().'@'.$this->console->getContext()->custom_name().': '.'</span>');
        return $this;
    }

    public function formatBold(): PrintBuilder {
        $this->opened[] = '</b>';
        $this->msg .= $this->console->isBash() ? '\e[1m' : '<b>';
        return $this;
    }

    public function formatUnderline(): PrintBuilder {
        $this->opened[] = '</u>';
        $this->msg .= $this->console->isBash() ? '\e[4m' : '<u>';
        return $this;
    }

    public function formatHidden(): PrintBuilder {
        $this->opened[] = '-->';
        $this->msg .= $this->console->isBash() ? '\e[8m' : '<!--';
        return $this;
    }

    public function formatReset(): PrintBuilder {
        if ($this->console->isBash()) {
            $this->msg .= '\e[0m';
        } else {
            $o = [];
            for ($i = count($this->opened) - 1; $i >= 0; $i--) {
                switch ($this->opened[$i]) {
                    case 'b':
                    case 'u':
                    case '-->':
                        $this->msg .= $this->opened[$i];
                        break;
                    default:
                        $o[] = $this->opened[$i];
                        break;
                        $this->opened = array_reverse($o);
                }
            }
        }
        return $this;
    }

    public function foregroundDefault(): PrintBuilder {
        if ($this->console->isBash()) {
            $this->msg .= '\e[8m';
        } else {
            foreach ($this->opened as $open) {
                if ($open == '</span>') {
                    $this->msg .= $open;
                }
            }
        }
        return $this;
    }


    public function foregroundRed() : PrintBuilder {
        $this->opened[]='</span>';
        $this->msg=$this->console->isBash()?'\e[31m':'<span style="color: red;">';
        return $this;
    }
}