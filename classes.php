<?php

class Test {

    public $tv;
    public $channel;
    protected $screen;
    private $vol;
    


    public function test() {
        $this->tv = 'videocon';
        echo $this->tv;
    }
    
    public function tvtype() {
        $this->channel="sport";
        $this->screen='14 Inch';
        $this->tv;
        $arrStr=array($this->channel,$this->screen, $this->tv);
        return $arrStr;
    }

}

$testObj=new Test();

print_r($testObj->tvtype());
echo $testObj->tv;
echo $testObj->screen;
