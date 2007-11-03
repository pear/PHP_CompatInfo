<?php
/**
 * Get the Compatibility info for an entire folder (recursive)
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

$info = new PHP_CompatInfo();

$folder  = dirname(__FILE__);
$options = array(
    'file_ext' => array('php3', 'php'),
    'ignore_files' => array(__FILE__)
    );

var_dump($options);
var_dump($info->parseFolder($folder, $options));
?>