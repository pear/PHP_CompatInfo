<?php
/**
 * Test Constants that appeared in >= 4.3.0
 *
 * PHP versions 4 and 5
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Davey Shafik <davey@php.net>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @ignore
 */

require_once 'PHP/CompatInfo.php';

/**
 * @ignore
 */
function test_constants()
{
    return __FUNCTION__;
}

$info = new PHP_CompatInfo();

$file = __FILE__;

var_dump($info->parseFile($file));

?>