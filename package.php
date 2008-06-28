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
$p2->setReleaseVersion('1.8.0RC1');
$p2->setAPIVersion('1.8.0');
$p2->setReleaseStability('beta');
$p2->setAPIStability('stable');
$p2->setNotes('Probably the last release before final version planned for August 1st.
Please give it a good testing !

* bugs
- #14187 : cli -r switch does not work in 1.8.0.b4
- getFileList() obey now to "recurse_dir" option (when set to false)
- parse functions obey now to "ignore_files" option

* changes
- Result values are now sorted in alphabetic order for a better human reading
Thats include 6 following entries:
"ignore_functions", "ignore_extensions", "ignore_constants"
"functions" "extensions" "constants"
- Event "AuditFinised" give now the result of parsing

Do not forget to have a look on all examples pci180_parse* that demonstrates
the new API.

* QA
- Test Suites (Standard, Bugs, Cli) included in this first release candidate
are full compatible with API 1.8.0
');
//$p2->setPearinstallerDep('1.5.4');
//$p2->setPhpDep('4.3.10');
//$p2->addPackageDepWithChannel('required', 'Event_Dispatcher', 'pear.php.net', '1.0.0');
//$p2->addPackageDepWithChannel('optional', 'PHPUnit', 'pear.phpunit.de', '3.2.0');
//$p2->addPackageDepWithChannel('optional', 'XML_Util', 'pear.php.net', '1.1.4');
//$p2->addPackageDepWithChannel('required', 'File_Find', 'pear.php.net', '1.3.0');
//$p2->addPackageDepWithChannel('optional', 'XML_Beautifier', 'pear.php.net', '1.1');
//$p2->addPackageDepWithChannel('optional', 'Console_ProgressBar', 'pear.php.net', '0.5.2beta');
$p2->addPackageDepWithChannel('optional', 'Var_Dump', 'pear.php.net', '1.0.3');

if (isset($_GET['make']) ||
    (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $p2->writePackageFile();
} else {
    $p2->debugPackageFile();
}
?>