<?php

class constant {

    const PI = 3.14;

//self is used for constant bcs it belong to the class
    public function circle_area($r) {
        echo self::PI * $r * $r;
    }

}

$cons = new constant();
$cons->circle_area('10');
