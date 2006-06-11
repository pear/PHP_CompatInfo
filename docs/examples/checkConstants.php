<?php
/**
 * Test Constants that appeared in >= 4.3.0
 *
 * @version    $Id$
 * @author     Davey Shafik <davey@php.net>
 * @package    PHP_CompatInfo
 * @access     public
 * @ignore
 */

require_once 'PHP/CompatInfo.php';

function test_constants()
{
    return __FUNCTION__;
}

$info = new PHP_CompatInfo();

$file = __FILE__;

var_dump($info->parseFile($file));
?>