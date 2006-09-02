<?php
/**
 * How to cutomize CLI output version. Requires at least version 1.3.1
 *
 * @version    $Id$
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @package    PHP_CompatInfo
 * @access     public
 * @ignore
 */
require_once 'PHP/CompatInfo/Cli.php';

// split filename to 30 char. max and continuation char. is +
$cli = new PHP_CompatInfo_Cli(30, '+');
$cli->run();
?>