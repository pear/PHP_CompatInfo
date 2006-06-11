<?php
/**
 * Get the Compatibility info for a string
 *
 * @version    $Id$
 * @author     Davey Shafik <davey@php.net>
 * @package    PHP_CompatInfo
 * @access     public
 * @ignore
 */

require_once 'PHP/CompatInfo.php';

$info = new PHP_CompatInfo();

var_dump($info->parseString('<?php $file = file_get_contents(__FILE__); $tokens = token_get_all($file); ?>'));
?>