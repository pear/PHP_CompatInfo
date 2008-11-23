<?php
/**
 * Constant dictionnary for PHP_CompatInfo 1.1.1 or better
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
 * @since    version 1.1.1 (2006-07-27)
 * @ignore
 */

require_once 'PHP/CompatInfo/date_const_array.php';
require_once 'PHP/CompatInfo/standard_const_array.php';

/**
 * Predefined Constants
 *
 * @link http://www.php.net/manual/en/reserved.constants.php
 * @global array $GLOBALS['_PHP_COMPATINFO_CONST']
 */

$GLOBALS['_PHP_COMPATINFO_CONST'] = array_merge(
    $GLOBALS['_PHP_COMPATINFO_CONST_DATE'],
    $GLOBALS['_PHP_COMPATINFO_CONST_STANDARD']
    );
?>