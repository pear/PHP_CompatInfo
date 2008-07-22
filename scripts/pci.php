#!@php_bin@
<?php
/**
 * Get the Compatibility info from PHP CLI
 *
 * PHP versions 4 and 5
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Davey Shafik <davey@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @access   public
 */

require_once 'PHP/CompatInfo/Cli.php';

$cli = new PHP_CompatInfo_Cli();
$cli->run();

?>