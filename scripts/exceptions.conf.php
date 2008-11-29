<?php
/**
 * Exceptions definition for Classes and Constants official version known
 *
 * PHP versions 5
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @since    File available since Release 1.9.0b1
 */

/* default version for each extension
   if not defined, then suppose its 5.0.0 */
$version_exceptions = array('standard' => '4.0.0');
/* if default version is not 4.0.0, then we can fix the right
   constant initial version here */
require_once 'constant_exceptions.php';
/* if default version is not 4.0.0, then we can fix the right
   predefined class initial version here */
require_once 'class_exceptions.php';

/**
 * Function that provides to return exceptions results
 *
 * @param string $extension Extension name
 * @param sting  $type      Type of exception (version | class | constant)
 *
 * @return mixed Return false if no exception exists for this $extension and $type
 */
function getExceptions($extension, $type)
{
    global $version_exceptions, $class_exceptions, $constant_exceptions;

    $exceptions = false;

    switch ($type) {
    case 'version' :
        if (isset($version_exceptions[$extension])) {
            $exceptions = $version_exceptions[$extension];
        }
        break;
    case 'class' :
        if (isset($class_exceptions[$extension])) {
            $exceptions = $class_exceptions[$extension];
        }
        break;
    case 'constant' :
        if (isset($constant_exceptions[$extension])) {
            $exceptions = $constant_exceptions[$extension];
        }
    }

    return $exceptions;
}
?>