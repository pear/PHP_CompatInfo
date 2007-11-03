<?php
/**
 * How to cutomize CLI output version. Requires at least version 1.3.1
 *
 * PHP versions 4 and 5
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @ignore
 */
require_once 'PHP/CompatInfo/Cli.php';

// split filename to 30 char. max and continuation char. is +
$cli = new PHP_CompatInfo_Cli(30, '+');
$cli->run();
?>