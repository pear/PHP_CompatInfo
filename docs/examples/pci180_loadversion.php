<?php
/**
 * Get PHP functions and constants history from a range of version,
 * group by version number
 *
 * This example show the new options|features available with API 1.8.0
 *
 * PHP versions 4 and 5
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @since    version 1.8.0 (2008-08-01)
 * @ignore
 */

require_once 'PHP/CompatInfo.php';

$compatInfo = new PHP_CompatInfo();

$r = $compatInfo->loadVersion('4.3.2', '4.4.0', true, true);
var_export($r);
?>