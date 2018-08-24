<?php

class Test {

    public $set='joins';
    public static $test = 0;
    protected $str = 'rakesh';
    private $strgen = 'male';

    public static function get_count() {
        echo self::$test++;
        echo "******************" . "<br/>";
    }

    public function get_str() {
        echo $this->str;
        echo $this->strgen;
        echo self::$test;
    }

}

Test::get_count();
Test::get_count();
Test::get_count();
$ts = new Test();
//echo $ts->str;
//$ts->get_str();
echo $ts->set;
echo Test::$test;