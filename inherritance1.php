<?php

class Man {

    public $name;
    public $gender;

    public function __construct($name, $gender) {
        $this->name = $name;
        $this->gender = $gender;
        echo 'The class "', __CLASS__, '" was initiated!<br />';
    }

    public function set_name($name) {
        $this->name = $name;
    }

    protected function get_name() {
        return $this->name;
    }

    public function set_gender($gender) {
        $this->gender = $gender;
    }

    public function get_gender() {
        return $this->gender;
    }

}

class Employee extends Man {

    public function __construct($name, $gender) {
        parent::__construct($name, $gender);
    }

    public function get_name() {
        echo 'The class "', __CLASS__, '" was initiated!<br />';
    }
    
    public function call_emp() {
        echo $this->get_name();
    }

}
class Test extends Man {

    public function __construct($name, $gender) {
        parent::__construct($name, $gender);
    }

    public function get_name() {
        echo 'The class "', __CLASS__, '" was initiated!<br />';
    }

}
$emp = new Employee("emp", "male");
$emp->set_name("emp");
echo $emp->get_name();




$test = new test("test", "male");
$test->set_name("test");
echo $test->get_name();

$man = new Man("man", "male");
$man->set_name("man");
echo $man->get_name();

