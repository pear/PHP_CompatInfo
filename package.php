<?php
/**
 * PHP_CompatInfo Package Script Generator
 *
 * Generate a new fresh version of package xml 2.0
 * built with PEAR_PackageFileManager 1.6.0+
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @since    File available since Release 1.4.1
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
    'ignore' => array(__FILE__, 'pcicmd.php',
        'funclist.txt', 'updateVersionInfo.php', 'version.xml')
    );

$p2 = &PEAR_PackageFileManager2::importOptions($packagefile, $options);
$p2->setPackageType('php');
$p2->generateContents();
$p2->addRelease();
$p2->setOSInstallCondition('windows');
$p2->addInstallAs('scripts/compatinfo.bat', 'pci.bat');
$p2->addInstallAs('scripts/pci.php', 'pci.php');
$p2->addRelease();
$p2->addIgnoreToRelease('scripts/compatinfo.bat');
$p2->addInstallAs('scripts/pci.php', 'pci.php');
//$p2->addReplacement('scripts/pci.php', 'pear-config', '@php_bin@', 'php_bin');
$p2->setReleaseVersion('1.7.0a1');
$p2->setAPIVersion('1.7.0');
$p2->setReleaseStability('alpha');
$p2->setAPIStability('stable');
$p2->setNotes('* bugs
- bug #13137 : Standard test suite failed under *nix

* news
- req #12857 : Add the option to locally mask exceptions
  see new api 1.7.0 options:
  ignore_functions_match, ignore_extensions_match, ignore_constants_match.
- req #13138 : separate constants and tokens in results
- req #13147 : CLI: add filter file extension option on parsing directory
');
//$p2->setPearinstallerDep('1.5.4');
//$p2->setPhpDep('4.3.10');
//$p2->addPackageDepWithChannel('optional', 'PHPUnit', 'pear.phpunit.de', '3.2.0');
//$p2->addPackageDepWithChannel('optional', 'XML_Util', 'pear.php.net', '1.1.4');

if (isset($_GET['make']) ||
    (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $p2->writePackageFile();
} else {
    $p2->debugPackageFile();
}
?>