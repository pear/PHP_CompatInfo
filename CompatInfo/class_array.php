<?php
/**
 * Class dictionnary for PHP_CompatInfo 1.9.0a1 or better
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
 * @since    version 1.9.0a1 (2008-11-23)
 * @ignore
 */

require_once 'PHP/CompatInfo/date_class_array.php';
require_once 'PHP/CompatInfo/standard_class_array.php';

/**
 * Predefined Classes
 *
 * > Standard Defined Classes
 *   These classes are defined in the standard set of functions included in
 *   the PHP build.
 * - Directory
 * - stdClass
 * -  __PHP_Incomplete_Class
 *
 * > Predefined classes as of PHP 5
 *   These additional predefined classes were introduced in PHP 5.0.0
 * - Exception
 * - php_user_filter
 *
 * > Miscellaneous extensions
 *   define other classes which are described in their reference.
 *
 * @link http://www.php.net/manual/en/function.get-declared-classes.php
 * @link http://www.php.net/manual/en/reserved.classes.php
 * @global array $GLOBALS['_PHP_COMPATINFO_CLASS']
 */

$GLOBALS['_PHP_COMPATINFO_CLASS'] = array_merge(
    $GLOBALS['_PHP_COMPATINFO_CLASS_DATE'],
    $GLOBALS['_PHP_COMPATINFO_CLASS_STANDARD']
    );
?>