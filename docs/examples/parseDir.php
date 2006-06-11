<?php
/**
 * Get the Compatibility info for an entire folder (recursive)
 *
 * @version    $Id$
 * @author     Davey Shafik <davey@php.net>
 * @package    PHP_CompatInfo
 * @access     public
 * @ignore
 */

require_once 'PHP/CompatInfo.php';

$info = new PHP_CompatInfo();

$folder = dirname(__FILE__);

$options = array(
    'file_ext' => array('php3', 'php'),
    'ignore_files' => array(__FILE__)
    );

var_dump($options);
var_dump($info->parseFolder($folder, $options));
?>