<?php
/**
 * PHP_CompatInfo Package Script Generator
 *
 * Generate a new fresh version of package xml 2.0 built with PEAR_PackageFileManager 1.6.0+
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   PHP
 * @package    PHP_CompatInfo
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/PHP_CompatInfo
 * @since      File available since Release 1.4.1
 * @ignore
 */

require_once 'PEAR/PackageFileManager2.php';

PEAR::setErrorHandling(PEAR_ERROR_DIE);

$packagefile = 'c:/php/pear/PHP_CompatInfo/package2.xml';

$options = array('filelistgenerator' => 'cvs',
    'packagefile' => 'package2.xml',
    'baseinstalldir' => 'PHP',
    'simpleoutput' => true,
    'clearcontents' => false,
    'changelogoldtonew' => false,
    'ignore' => array('package.php', 'index.htm')
    );

$p2 = &PEAR_PackageFileManager2::importOptions($packagefile, $options);
$p2->setPackageType('php');
$p2->addRelease();
$p2->generateContents();
$p2->setReleaseVersion('1.4.1');
$p2->setAPIVersion('1.4.0');
$p2->setReleaseStability('stable');
$p2->setAPIStability('stable');
$p2->setNotes('* changes
- upgrade license from PHP 3.0 to 3.01
- fixed wrong implementation of Console_Getargs feature fixed in version 1.33 (see bug #9252)
- improve PHP5 detection with a new fresh up-to-date func_array.php file
(that include the new function "spl_object_hash" came with PHP 5.2.0)

* QA
- Improve readability (hilighted source code) of User Guide 1.4.1 included in this release
that cover all versions 1.4.x
  see http://pear.laurent-laville.org/PHP_CompatInfo for more format to download.
');
$p2->setLicense('PHP License 3.01', 'http://www.php.net/license/3_01.txt');

if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $p2->writePackageFile();
} else {
    $p2->debugPackageFile();
}
?>