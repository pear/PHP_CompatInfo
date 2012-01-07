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

$packagefile = dirname(__FILE__) . '/package.xml';

$options = array('filelistgenerator' => 'svn',
    'packagefile' => 'package.xml',
    'baseinstalldir' => 'PHP',
    'simpleoutput' => true,
    'clearcontents' => false,
    'changelogoldtonew' => false,
    'ignore' => array(__FILE__, 'funclist.txt', 'version.xml')
    );

$p2 = &PEAR_PackageFileManager2::importOptions($packagefile, $options);
$p2->setPackageType('php');
$p2->generateContents();
$p2->addRelease();
$p2->setOSInstallCondition('windows');
$p2->addInstallAs('scripts/compatinfo.bat', 'pci.bat');
$p2->addInstallAs('scripts/pci.php', 'pci');
$p2->addInstallAs('scripts/pciconf.bat', 'pciconf.bat');
$p2->addInstallAs('scripts/configure.php', 'pciconf');
$p2->addRelease();
$p2->addIgnoreToRelease('scripts/compatinfo.bat');
$p2->addIgnoreToRelease('scripts/pciconf.bat');
$p2->addInstallAs('scripts/pci.php', 'pci');
$p2->addInstallAs('scripts/configure.php', 'pciconf');
/*
$p2->addReplacement('scripts/pci.php', 'pear-config', '@php_bin@', 'php_bin');
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
$p2->setReleaseVersion('1.9.0RC1');
$p2->setAPIVersion('1.9.0');
$p2->setReleaseStability('beta');
$p2->setAPIStability('stable');
$p2->setNotes('
* IMPORTANT
- if you are PHP5 user only:
  use the pciconf script to build your own extension support list
- if you are PHP4 user only:
  pciconf script required at least PHP5 to run, so to build your own support list,
  do it by hand. All major extensions and their PCI dictionaries are available
  on PEAR CVS [http://cvs.php.net/viewvc.cgi/pear/PHP_CompatInfo/CompatInfo/]
- A more comprehensive guide will be available for final stable release, ready for
  PhD system and new PEAR Manual.

* changes since beta2
- pciconf script did not used anymore the monolithic versions.xml and funclist.txt
data sources. Version information about extensions came from specific extension version.xml
file that are installed into PEAR/data/PHP_CompatInfo/phpdocref

* bugs fixed since beta2
- CSV, HTML and XML renderers did not provided expected result
  due to new classes result-key entry
- lost partial functions list information when parsing multiple data sources
with debug mode

* news since beta2
- add function getSummary() to print only summary when parsing a directory
or multiple data sources at once

You are welcome to read my presentation about the new API at
http://pear.laurent-laville.org/pepr/PHP_CompatInfo/api190/
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
