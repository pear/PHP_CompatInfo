<?php
/**
 * Exclude all PHP5 functions when calculating the version needed.
 *
 * PHP versions 4 and 5
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @ignore
 */

require_once 'PHP/CompatInfo.php';

$info = new PHP_CompatInfo();

$dir = 'C:\PEAR\Tools and utilities\PhpDocumentor-1.3.0';
$options = array(
    'debug' => true,
    'ignore_functions' => PHP_CompatInfo::loadVersion('5.0.0'),
    'ignore_constants' => array('clone', 'public')
    );

$res = $info->parseFolder($dir, $options);
var_dump($res);

?>