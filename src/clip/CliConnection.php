<?php

namespace clip;

use Ratchet\ConnectionInterface;

class CliConnection implements ConnectionInterface {

    /**
     * Send data to the connection
     *
     * @param  string $data
     * @return \Ratchet\ConnectionInterface
     */
    function send($data) {
        echo strip_tags($data);
        return $this;
    }

    /**
     * Close the connection
     */
    function close() {
        die();
}}