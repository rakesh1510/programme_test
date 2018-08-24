<?php

include './interface2.php';

class Filelogger implements Logger {

    private $handle;
    private $logfile;

    public function __construct($filename, $mode = 'a') {
        $this->logfile = $filename;
        $this->handle = fopen($filename, $mode);
    }

    public function log($msg) {
        $message = date("F j, Y, g:i a") . ': ' . $message . "\n";
        fwrite($this->handle, $message);
    }

    public function __destruct() {
        fclose($this->handle);
    }

}
