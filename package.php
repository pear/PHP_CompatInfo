<?php
require_once 'PEAR/PackageFileManager.php';

$version = '1.0.0RC1';
$notes = <<<EOT
Now doesn't detect foo::bar() or \$foo->bar() as a function call.
State moved to beta, first RC.
EOT;

$description =<<<EOT
PHP_CompatInfo will parse a file/folder/script/array to find out the minimum
version and extensions required for it to run. Features advanced debug output
which shows which functions require which version and CLI output script
EOT;

$package = new PEAR_PackageFileManager();

$result = $package->setOptions(array(
    'package'           => 'PHP_CompatInfo',
    'summary'           => 'Find out the minimum version and the extensions required for a piece of code to run',
    'description'       => $description,
    'version'           => $version,
    'state'             => 'beta',
    'license'           => 'PHP License',
    'ignore'            => array('*entries*','*Template*','*Root*','*Repository*','package.php', 'package.xml', '*.bak', '*src*', '*.tgz', '*pear_media*', 'index.htm', '*tests*'),
	'filelistgenerator' => 'cvs', // other option is 'file'
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

/*$package->addDependency('php', '4.3.0', 'ge', 'php', false);
$package->addDependency('tokenizer', '', 'has', 'ext', false);*/
$package->addDependency('auto');
$package->addDependency('Console_Table','1.0.1','ge','pkg',true);
$package->addDependency('Console_Getopt','1.2','ge','pkg',true);


if (isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] == 'commit') {
    $result = $package->writePackageFile();
} else {
    $result = $package->debugPackageFile();
}

if (PEAR::isError($result)) {
    echo $result->getMessage();
    die();
}
?>