<?php

require_once 'PHP/Requires.php';

$requires = new PHP_Requires;

$info = $requires->parseFile('Math/ComplexOp.php',true);

var_dump($info);

?>