<?php
/**
 * Get the Compatibility info for an array
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
require_once 'PEAR.php';

$info = new PHP_CompatInfo();

$files   = get_included_files();
$options = array(
    'debug' => false,
    'ignore_files' => array($files[0]),
    'ignore_functions' => array('debug_backtrace')
    );

var_dump($info->parseArray($files, $options));
?>