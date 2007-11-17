<?php
/**
 * Test Extensions that appeared both as standard or PECL
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

xdebug_start_trace();

require_once 'PHP/CompatInfo.php';

/**
 * @ignore
 */
function test_extensions()
{
    $image = imagecreate(320, 240);
    imageantialias($image, true);
    return $image;
}

print_r(apache_get_modules());

if (!extension_loaded('sqlite')) {
    $prefix = (PHP_SHLIB_SUFFIX === 'dll') ? 'php_' : '';
    dl($prefix . 'sqlite.' . PHP_SHLIB_SUFFIX);
}

xdebug_stop_trace();

$info = new PHP_CompatInfo();

$file    = __FILE__;
$options = array('debug' => true);

var_dump($info->parseFile($file, $options));
?>