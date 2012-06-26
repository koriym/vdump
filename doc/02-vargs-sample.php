<?php
include dirname(__DIR__) . '/src/v.php';

$con = new PHP_Vdaump_Controller;
$con->indexAction();

class PHP_Vdaump_Controller
{
    public function indexAction()
    {
        $model = new PHP_Vdaump_Test_Model;
        $namae = "ray";
        $model->create($namae, array($namae, 'fruit' => 'banna'));
    }
}

class PHP_Vdaump_Test_Model
{
    public function create($name, array $food)
    {
        // print how called.
        vargs();
    }
}
