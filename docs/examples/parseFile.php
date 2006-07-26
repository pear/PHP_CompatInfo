<?php
/**
 * Get the Compatibility info for a single file
 *
 * @version    $Id$
 * @author     Davey Shafik <davey@php.net>
 * @package    PHP_CompatInfo
 * @access     public
 * @ignore
 */

require_once 'PHP/CompatInfo.php';

$info = new PHP_CompatInfo();
$file = __FILE__;

var_dump($info->parseFile($file));
?>