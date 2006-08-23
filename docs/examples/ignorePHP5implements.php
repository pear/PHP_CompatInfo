<?php
/**
 * Exclude all PHP5 functions when calculating the version needed.
 *
 * @version   $Id$
 * @author    Laurent Laville <pear@laurent-laville.org>
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