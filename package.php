<?php
/**
 * PHP_CompatInfo Package Script Generator
 *
 * Generate a new fresh version of package xml 2.0
 * built with PEAR_PackageFileManager 1.6.0+
 *
 * PHP versions 4 and 5
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD
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
    'ignore' => array(__FILE__,
       'funclist.txt', 'updateVersionInfo.php', 'version.xml')
    );

$p2 = &PEAR_PackageFileManager2::importOptions($packagefile, $options);
$p2->setPackageType('php');
$p2->generateContents();
$p2->addRelease();
$p2->setOSInstallCondition('windows');
$p2->addInstallAs('scripts/compatinfo.bat', 'pci.bat');
$p2->addInstallAs('scripts/pci.php', 'pci');
$p2->addRelease();
$p2->addIgnoreToRelease('scripts/compatinfo.bat');
$p2->addInstallAs('scripts/pci.php', 'pci');
//$p2->addReplacement('scripts/pci.php', 'pear-config', '@php_bin@', 'php_bin');
/*
$p2->addReplacement('CompatInfo/Parser.php', 'package-info', '@package_version@', 'version');
$p2->addReplacement('CompatInfo/Renderer.php', 'package-info', '@package_version@', 'version');
$p2->addReplacement('CompatInfo/Renderer/Array.php', 'package-info', '@package_version@', 'version');
$p2->addReplacement('CompatInfo/Renderer/Null.php', 'package-info', '@package_version@', 'version');
$p2->addReplacement('CompatInfo/Renderer/Text.php', 'package-info', '@package_version@', 'version');
$p2->addReplacement('CompatInfo/Renderer/Xml.php', 'package-info', '@package_version@', 'version');
$p2->addReplacement('CompatInfo/Renderer/Csv.php', 'package-info', '@package_version@', 'version');
$p2->addReplacement('CompatInfo/Renderer/Html.php', 'package-info', '@package_version@', 'version');
$p2->addReplacement('CompatInfo/Renderer/Html.php', 'package-info', '@package_name@', 'name');
$p2->addReplacement('CompatInfo/Renderer/Html.php', 'pear-config', '@data_dir@', 'data_dir');
*/
$p2->setReleaseVersion('1.8.1');
$p2->setAPIVersion('1.8.0');
$p2->setReleaseStability('stable');
$p2->setAPIStability('stable');
$p2->setNotes('
* bugs
- 14696 : PHP_CompatInfo fails to scan code line when not ended with ;

* changes
- getallheaders() is detected as sapi_apache rather than sapi_aolserver
');
//$p2->setLicense('BSD', 'http://www.opensource.org/licenses/bsd-license.php');
//$p2->setPearinstallerDep('1.5.4');
//$p2->setPhpDep('4.3.10');
//$p2->addPackageDepWithChannel('required', 'Event_Dispatcher', 'pear.php.net', '1.0.0');
//$p2->addPackageDepWithChannel('optional', 'PHPUnit', 'pear.phpunit.de', '3.2.0');
//$p2->addPackageDepWithChannel('optional', 'XML_Util', 'pear.php.net', '1.1.4');
//$p2->addPackageDepWithChannel('required', 'File_Find', 'pear.php.net', '1.3.0');
//$p2->addPackageDepWithChannel('optional', 'XML_Beautifier', 'pear.php.net', '1.1');
//$p2->addPackageDepWithChannel('optional', 'Console_ProgressBar', 'pear.php.net', '0.5.2beta');
//$p2->addPackageDepWithChannel('optional', 'Var_Dump', 'pear.php.net', '1.0.3');

if (isset($_GET['make']) ||
    (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $p2->writePackageFile();
} else {
    $p2->debugPackageFile();
}
?>