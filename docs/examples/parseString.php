<?php
/**
 * Get the Compatibility info for a string
 *
 * PHP versions 4 and 5
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Davey Shafik <davey@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @ignore
 */

require_once 'PHP/CompatInfo.php';

$info = new PHP_CompatInfo();

$res = $info->parseString('<?php $file = file_get_contents(__FILE__); $tokens = token_get_all($file); ?>');
var_dump($res);

?>