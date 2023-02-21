<?php

class Address {

    public $ip = '';
    public $port = 19132;

    public function __construct($ip, $port) {
        $this->ip = $ip;
        $this->port = $port;
    }
    public function getPort() {
        return $this->port;
    }
    public function getIp() {
        return $this->ip;
    }
    public function asString() {
        return $this->ip.':'.$this->port;
    }
}
