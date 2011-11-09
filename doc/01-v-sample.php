<?php
include dirname(__DIR__) . '/src/v.php';

$data = array(1, 2, array('fruit' => 'banana'));
$name = "Ray";

v($data, $name);