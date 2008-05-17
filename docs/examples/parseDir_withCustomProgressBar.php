<?php
/**
 * Show a progress bar when parsing a directory from CLI
 * Do not display any progress bar from other SAPI
 *
 * To run on Windows platform, do:
 * (path to PHP cli)\php.exe -f (this script) -- -d (the dir you want to parse)
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
if (php_sapi_name() == 'cli') {
    include_once 'PHP/CompatInfo/Cli.php';
    /*
      Display a progress bar like this one:

     [)))))                                          ]    8% -  45/563 files

     */
    $pbar = array('formatString' => '[%bar%] %percent% - %fraction% files',
                  'barfill' => ')',
                  'prefill' => ' ',
                  'options' => array('percent_precision' => 0));

    $cli = new PHP_CompatInfo_Cli($pbar);
    $cli->run();
} else {
    include_once 'PHP/CompatInfo.php';

    $info = new PHP_CompatInfo();

    $dir  = 'C:\php\pear\HTML_Progress2';
    var_dump($info->parseDir($dir));
}
?>