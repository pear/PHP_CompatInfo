<?php
/**
 * Constant dictionary for PHP_CompatInfo 1.1.1 or better
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
 */

require_once 'PHP/CompatInfo/calendar_const_array.php';
require_once 'PHP/CompatInfo/com_dotnet_const_array.php';
require_once 'PHP/CompatInfo/date_const_array.php';
require_once 'PHP/CompatInfo/dom_const_array.php';
require_once 'PHP/CompatInfo/filter_const_array.php';
require_once 'PHP/CompatInfo/ftp_const_array.php';
require_once 'PHP/CompatInfo/hash_const_array.php';
require_once 'PHP/CompatInfo/iconv_const_array.php';
require_once 'PHP/CompatInfo/internal_const_array.php';
require_once 'PHP/CompatInfo/libxml_const_array.php';
require_once 'PHP/CompatInfo/odbc_const_array.php';
require_once 'PHP/CompatInfo/pcre_const_array.php';
require_once 'PHP/CompatInfo/standard_const_array.php';
require_once 'PHP/CompatInfo/tokenizer_const_array.php';
require_once 'PHP/CompatInfo/xdebug_const_array.php';
require_once 'PHP/CompatInfo/xml_const_array.php';
require_once 'PHP/CompatInfo/zlib_const_array.php';

/**
 * Predefined Constants
 *
 * @link http://www.php.net/manual/en/reserved.constants.php
 * @global array $GLOBALS['_PHP_COMPATINFO_CONST']
 */

$GLOBALS['_PHP_COMPATINFO_CONST'] = array_merge(
    $GLOBALS['_PHP_COMPATINFO_CONST_CALENDAR'],
    $GLOBALS['_PHP_COMPATINFO_CONST_COM_DOTNET'],
    $GLOBALS['_PHP_COMPATINFO_CONST_DATE'],
    $GLOBALS['_PHP_COMPATINFO_CONST_DOM'],
    $GLOBALS['_PHP_COMPATINFO_CONST_FILTER'],
    $GLOBALS['_PHP_COMPATINFO_CONST_FTP'],
    $GLOBALS['_PHP_COMPATINFO_CONST_HASH'],
    $GLOBALS['_PHP_COMPATINFO_CONST_ICONV'],
    $GLOBALS['_PHP_COMPATINFO_CONST_INTERNAL'],
    $GLOBALS['_PHP_COMPATINFO_CONST_LIBXML'],
    $GLOBALS['_PHP_COMPATINFO_CONST_ODBC'],
    $GLOBALS['_PHP_COMPATINFO_CONST_PCRE'],
    $GLOBALS['_PHP_COMPATINFO_CONST_STANDARD'],
    $GLOBALS['_PHP_COMPATINFO_CONST_TOKENIZER'],
    $GLOBALS['_PHP_COMPATINFO_CONST_XDEBUG'],
    $GLOBALS['_PHP_COMPATINFO_CONST_XML'],
    $GLOBALS['_PHP_COMPATINFO_CONST_ZLIB']
    );
?>