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
$p2->setReleaseVersion('1.7.0b3');
$p2->setAPIVersion('1.7.0');
$p2->setReleaseStability('beta');
$p2->setAPIStability('stable');
$p2->setNotes('Changes since version 1.7.0b2 (2008-03-24)

* bugs
- notice errors on XML report output when parsing a directory with CLI
- add summary in XML report when parsing a directory with CLI

* news
- implement request #13493: customize CLI data output

Changes since version 1.7.0b1 (2008-03-17)

- pci.php file was rename to pci (without extension) to match unix command syntax
- CLI may print (-V | --version) version number of PHP_CompatInfo package used

* bugs
- bug #13417 : Parser ignore class-method that are named as standard php functions

* QA
- dependencies related to CLI (Console_Table, Console_GetArgs, XML_Util)
  are defined as required and no more optional. CLI is a standard feature and should
  not be considered as optional.

Changes since version 1.7.0a1 (2008-02-21)

- fix CLI output render to 80 columns, on main table :
  29 characters for File/Path column (1)
   9 characters for Version column (2)
  13 characters for Extensions column (3)
  23 characters for Constants/Tokens column (4)
- fix CLI output render to 80 columns, on additionnal tables :
  25 characters for Option column (1)
  51 characters for Value column (2)
- On CLI, the XML report generation is now xml compliant with a root tag (pci)
- On CLI, implement options:
  ignore_functions_match  with -inm switch,
  ignore_extensions_match with -iem switch,
  ignore_constants_match  with -icm switch
- On CLI options files (see -in, -ie, -ic, -inm, -iem, -icm)
  allow to put line in comment with ; character (as in php.ini)

Changes since stable version 1.6.1 (2008-02-16)

* bugs
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