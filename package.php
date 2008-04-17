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
$p2->setReleaseVersion('1.7.0');
$p2->setAPIVersion('1.7.0');
$p2->setReleaseStability('stable');
$p2->setAPIStability('stable');
$p2->setNotes('After 1 alpha and 4 beta releases, I am pleased to announce the
release of final stable version. This new API 1.7.0 introduce many new features.
Here are a summary of all changes since API 1.6.0.

All SAPI:
=========
* news
- add 3 options:
  ignore_functions_match, ignore_extensions_match, ignore_constants_match.
  ==> Request #12857
- separate constants and tokens in results
  ==> Request #13138

* changes
- full support of regular expressions for "ignore_dirs" and "ignore_files" options.
- all reports display now the max version information when needed.

* bugs
- #13568 : User functions are not ignored
- #13417 : Parser ignore class-method that are named as standard php functions
- #13137 : Standard test suite failed under *nix

CLI only:
=========
* news
- Add new --output-level switch to customize (all) reports output.
  ==> Request #13493
- Add new --summarize switch to limit output to first line (summary)
  when parsing directory.
- Add new --version switch to print version number of PHP_CompatInfo package.
- Add --filter-ext switch to filter file extension when parsing directory
  ==> Request #13147
- Add new column C to indicate a level of conditional code used (0: none,
  1: function_exists() used, 2: extension_loaded() used, 4: defined() used)

* changes
- XML report structure change.
  Render is improve if PEAR::XML_Beautifier is available.
- pci.php file was rename to pci (without extension) to match unix command syntax
- Text report is now really limited to 80 columns in all situation
- All switchs that accept option file parameter allow now
  to put line in comment with ; character (as in php.ini)

* QA
- dependencies Console_Table, Console_GetArgs, XML_Util
  are defined as required and no more optional. CLI is a standard feature and should
  not be considered as optional.

---------------------------------------------------------------------------
Changes since version 1.7.0b4 (2008-04-03)

- parseString(), parseFile() and parseArray() handle now correctly the new
  key-info "cond_code".
- Add new column C to indicate a level of conditional code used.
- CLI text report support version max information in main table results.
- Regression Tests Suite was fixed.
');
//$p2->setPearinstallerDep('1.5.4');
//$p2->setPhpDep('4.3.10');
//$p2->addPackageDepWithChannel('optional', 'PHPUnit', 'pear.phpunit.de', '3.2.0');
//$p2->addPackageDepWithChannel('optional', 'XML_Util', 'pear.php.net', '1.1.4');
//$p2->addPackageDepWithChannel('required', 'File_Find', 'pear.php.net', '1.3.0');
//$p2->addPackageDepWithChannel('optional', 'XML_Beautifier', 'pear.php.net', '1.1');

if (isset($_GET['make']) ||
    (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $p2->writePackageFile();
} else {
    $p2->debugPackageFile();
}
?>