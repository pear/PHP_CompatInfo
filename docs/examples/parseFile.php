<?php

/* Get the Compatibility info for a single file */

require_once 'PHP/CompatInfo.php';

$info = new PHP_CompatInfo;

$file = __FILE__;

var_dump($info->parseFile($file));
?>