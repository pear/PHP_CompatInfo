<?php
require_once 'PEAR/PackageFileManager.php';
require_once 'Console/Getopt.php';

$version = '0.7.0';
$notes = <<<EOT
* First release on PEAR-web.
* Added the ability to ignore files 
in parseFolder() and parseArray(), and the ability to ignore folders
in parseFolder(). Added the ability to ignore functions in all public methods.
EOT;

$description =<<<EOT
PHP_CompatInfo will parse a file/folder/script/array to find out the minimum
version and extensions required for it to run. Features advanced debug output
which shows which functions require which version
EOT;

$package = new PEAR_PackageFileManager();

$result = $package->setOptions(array(
    'package'           => 'PHP_CompatInfo',
    'summary'           => 'Find out the minimum version and the extensions required for a piece of code to run',
    'description'       => $description,
    'version'           => $version,
    'state'             => 'alpha',
    'license'           => 'PHP License',
    'ignore'            => array('package.php', 'package.xml', '*.bak', '*src*', '*.tgz', '*pear_media*', 'index.htm', '*tests*'),
	'filelistgenerator' => 'cvs',
    'notes'             => $notes,
    'changelogoldtonew' => false,
    'baseinstalldir'    => 'PHP',
    'packagedirectory'  => '',
    'simpleoutput'      => true
    ));

if (PEAR::isError($result)) {
    echo $result->getMessage();
    die();
}

$package->addMaintainer('davey','lead','Davey Shafik','davey@php.net');

$package->addDependency('php', '4.3.0', 'ge', 'php', false);
$package->addDependency('tokenizer', '', 'has', 'ext', false);
$package->addDependency('sockets', '', 'has', 'ext', false);

if ($_SERVER['argv'][1] == 'commit') {
    $result = $package->writePackageFile();
} else {
    $result = $package->debugPackageFile();
}

if (PEAR::isError($result)) {
    echo $result->getMessage();
    die();
}
?>