<?php
/**
 * Get the Compatibility info for an array
 *
 * @version    $Id$
 * @author     Davey Shafik <davey@php.net>
 * @package    PHP_CompatInfo
 * @access     public
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