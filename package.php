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
    'ignore' => array(__FILE__, 'tests/', 'Cli.php',
        'funclist.txt', 'updateVersionInfo.php', 'version.xml')
    );

$p2 = &PEAR_PackageFileManager2::importOptions($packagefile, $options);
$p2->setPackageType('php');
$p2->generateContents();
$p2->addRelease();
/*
$p2->setOSInstallCondition('windows');
$p2->addInstallAs('scripts/compatinfo.bat', 'pci.bat');
$p2->addInstallAs('scripts/pci.php', 'pci');
$p2->addRelease();
$p2->addIgnoreToRelease('scripts/compatinfo.bat');
$p2->addInstallAs('scripts/pci.php', 'pci');
*/
//$p2->addReplacement('scripts/pci.php', 'pear-config', '@php_bin@', 'php_bin');
$p2->setReleaseVersion('1.8.0b2');
$p2->setAPIVersion('1.8.0');
$p2->setReleaseStability('beta');
$p2->setAPIStability('stable');
$p2->setNotes('CREDITS
Thanks to John Parise and Ken Guest, for their agreement to reuse code and idea
from their packages. Idea about connecting a driver (see PEAR_Size) and
how to build/check an instance of a custom renderer (see Log) if class
is available loaded somewhere.

* news
API 1.8.0 since beta 2 was almost full rewrite following the MVC design pattern.
This version, that DO NOT break Backward Compatibility, introduces at least two big
features :
1. Event-Driven capability (see PEAR::Event_Dispatcher),
2. Customizable and extendable Renderers

Do not forget to have a look on 3 news examples pci180_parse* that demonstrates
the new API.

Beta 2 include 3 renderers :
- "Null" that consumes all output. Usefull for batch mode
- "Array" the default. That dump results as a PHP array (like previous versions)
  but allow also to print improved array with help of PEAR::Var_Dump if available.
- "Xml" that produces XML results with PEAR::XML_Util and improve its render
  with PEAR::XML_Beautifier if available.

  WARNING: if you use benefit of XML_Beautifier with the Xml renderer and see
            the XML prolog/declaration be removed, DO NOT add a new bug report
            it is the old Bug #5450 !

Beta 3 that will come shortly will include 2 new more renderers :
- Html, for web page content
- Text, for command line interface (that will come back again live)

* QA
- Do no search Unit tests in this release, it is not yet ready, so I have removed
  them until beta3
');
//$p2->setPearinstallerDep('1.5.4');
//$p2->setPhpDep('4.3.10');
$p2->addPackageDepWithChannel('required', 'Event_Dispatcher', 'pear.php.net', '1.0.0');
//$p2->addPackageDepWithChannel('optional', 'PHPUnit', 'pear.phpunit.de', '3.2.0');
//$p2->addPackageDepWithChannel('optional', 'XML_Util', 'pear.php.net', '1.1.4');
//$p2->addPackageDepWithChannel('required', 'File_Find', 'pear.php.net', '1.3.0');
//$p2->addPackageDepWithChannel('optional', 'XML_Beautifier', 'pear.php.net', '1.1');
//$p2->addPackageDepWithChannel('optional', 'Console_ProgressBar', 'pear.php.net', '0.5.2beta');

if (isset($_GET['make']) ||
    (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $p2->writePackageFile();
} else {
    $p2->debugPackageFile();
}
?>