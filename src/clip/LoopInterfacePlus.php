<?php

namespace clip;

use React\EventLoop\LoopInterface;

interface LoopInterfacePlus extends LoopInterface {

    public function executeTick();
}