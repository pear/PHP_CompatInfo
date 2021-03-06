<?php
/**
 * iconv extension Function Exceptions dictionary
 * for PHP_CompatInfo 1.9.0b2 or better
 *
 * PHP versions 4 and 5
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Davey Shafik <davey@php.net>
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @since    version 1.9.0 (2009-01-19)
 */

if (!isset($function_exceptions['iconv'])) {
    $function_exceptions['iconv'] = array();
}

$function_exceptions['iconv'] = array_merge($function_exceptions['iconv'], array(
  'ob_iconv_handler' =>
  array (
    'init' => '4.0.5',
    'ext' => 'iconv',
    'pecl' => false,
  ))
);
?>