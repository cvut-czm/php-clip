<?php

namespace clip;

use clip\output\ProgressBar;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class DiContainer implements ContainerInterface {

    private $_dependencies;
    public function __construct(array $dependencies) {
        $this->_dependencies=$dependencies;
    }

    public function context() : Context
    {
        return $this->get('context');
    }
    public function printBuilder() : PrintBuilder {
        return $this->console()->printBuilder();
    }
    public function progressBar() : ProgressBar {
        return $this->console()->printBuilder()->progressBar();
    }
    public function console() : Console {
        return $this->get('console');
    }
    public function get($id) {
        return $this->_dependencies[$id];
    }

    public function has($id) {
        return isset($this->_dependencies[$id]);
    }
}