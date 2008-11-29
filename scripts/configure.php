<?php
/**
 * The Predefined Classes/Constants array script generator.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @since    File available since Release 1.9.0b1
 */

require_once 'Console/Getargs.php';

$opts = array('enable' =>
                  array('short'   => 'e',
                        'desc'    => 'A comma separated list of extensions '
                                   . 'you want only',
                        'default' => '',
                        'min'     => 0 , 'max' => 1),
              'disable' =>
                  array('short'   => 'd',
                        'desc'    => 'A comma separated list of extensions '
                                   . 'you want to disable',
                        'default' => '',
                        'min'     => 0 , 'max' => 1),
              'exceptions' =>
                  array('short'   => 'x',
                        'desc'    => 'File that provides exceptions results',
                        'default' => 'exceptions.conf.php',
                        'min'     => 0 , 'max' => 1),
              'output' =>
                  array('short'   => 'o',
                        'desc'    => 'Target directory where to write results',
                        'default' => './',
                        'min'     => 0 , 'max' => 1),
              'verbose' =>
                  array('short'   => 'v',
                        'desc'    => 'Set the verbose level',
                        'default' => 1,
                        'min'     => 0 , 'max' => 1),
              'version' =>
                  array('short' => 'V',
                        'desc'  => 'Print version information',
                        'max'   => 0),
              'help' =>
                  array('short' => 'h',
                        'desc'  => 'Show this help',
                        'max'   => 0)
              );

$args = Console_Getargs::factory($opts);

if (PEAR::isError($args)) {
    $header = "PHP_CompatInfo build system \n".
              'Usage: '.basename($_SERVER['SCRIPT_NAME'])." [options]\n\n";
    if ($args->getCode() === CONSOLE_GETARGS_ERROR_USER) {
        echo Console_Getargs::getHelp($opts, $header, $args->getMessage())."\n";
    } else if ($args->getCode() === CONSOLE_GETARGS_HELP) {
        echo Console_Getargs::getHelp($opts, $header)."\n";
    }
    exit(1);
}

// version
if ($args->isDefined('V')) {
    echo 'PHP_CompatInfo build system version 1.9.0b1';
    exit(0);
}

// verbose
if ($args->isDefined('v')) {
    $verbose = $args->getValue('v');
} else {
    $verbose = 1;
}

// output
if ($args->isDefined('o')) {
    $o = $args->getValue('o');
    if (is_dir($o) && (is_writable($o))) {
        /* Directory where to write
           all "*_const_array.php" and "*_class_array.php" files
           Must ended with a trailing directory separator */
        if (substr($o, -1, 1) !== DIRECTORY_SEPARATOR) {
            $o .= DIRECTORY_SEPARATOR;
        }
        $target_directory = $o;
    } else {
        echo 'Invalid (or not writable) target directory';
        exit(1);
    }
} else {
    $target_directory = dirname(__FILE__) . DIRECTORY_SEPARATOR;
}

// enable
if ($args->isDefined('e')) {
    $extensions = explode(',', $args->getValue('e'));
} else {
    $extensions = get_loaded_extensions();
}

// disable
if ($args->isDefined('d')) {
    $d          = explode(',', $args->getValue('d'));
    $extensions = array_diff($extensions, $d);
}

// exceptions
if ($args->isDefined('x')) {
    $x = $args->getValue('x');
    if (file_exists($x)) {
        include_once $x;
        if (!function_exists('getExceptions')) {
            echo 'getExceptions() function does not exists';
            exit(1);
        }
    } else {
        echo 'Exceptions file does not exists';
        exit(1);
    }
} else {
    include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'exceptions.conf.php';
}

$const_glob_list = array();
$class_glob_list = array();

foreach ($extensions as $extension) {

    if (!extension_loaded($extension)) {
        continue;  // skip this extension if not loaded : prevent error
    }

    $ext = new ReflectionExtension($extension);

    // name of the current Extension
    $extName = $ext->getName();

    // version of the current Extension
    $extVers = $ext->getVersion();

    if ($verbose > 0) {
        print 'Found '. $extName;
        if ($extVers) {
            print ' version '. $extVers;
        }
        print PHP_EOL;
    }

    // default version to apply to each constant and class predefined
    $ver = getExceptions($extName, 'version');
    if ($ver === false) {
        $ver = '5.0.0';
    }

    // constants described by the Extension interface
    $extConstants = $ext->getConstants();
    if (count($extConstants) > 0) {
        $const_glob_list[] = $extName;

        $constants = array();
        foreach ($extConstants as $cst => $val) {
            $constants[$cst]['init'] = $ver;
            $constants[$cst]['name'] = $cst;
        }

        $exceptions = getExceptions($extName, 'constant');
        if ($exceptions === false) {
            // no constant exceptions for this extension
        } else {
            // apply exceptions to give final constant results
            $constants = array_merge($constants, $exceptions);
        }
        ksort($constants);

        file_put_contents($target_directory . $extName . '_const_array.php',
                          "<?php
/**
 * $extName extension Constant dictionary for PHP_CompatInfo 1.9.0a1 or better
 *
 * PHP versions 4 and 5
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Davey Shafik <davey@php.net>
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD
 * @version  CVS: \$Id$
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @since    version 1.9.0a1 (2008-11-23)
 * @ignore
 */

\$GLOBALS['_PHP_COMPATINFO_CONST_" . strtoupper($extName) . "'] = " .
        var_export($constants, true) . ";
?>");

    }

    // classes described by the Extension interface
    $extClasses = $ext->getClassNames();
    if (count($extClasses) > 0) {
        $class_glob_list[] = $extName;

        $classes = array();
        foreach ($extClasses as $i => $cls) {
            $classes[$cls]['init'] = $ver;
            $classes[$cls]['name'] = $cls;
            $classes[$cls]['ext']  = $extName;
            $classes[$cls]['pecl'] = false;
        }

        $exceptions = getExceptions($extName, 'class');
        if ($exceptions === false) {
            // no class exceptions for this extension
        } else {
            // apply exceptions to give final class results
            $classes = array_merge($classes, $exceptions);
        }
        ksort($classes);

        file_put_contents($target_directory . $extName . '_class_array.php',
                          "<?php
/**
 * $extName extension Class dictionary for PHP_CompatInfo 1.9.0a1 or better
 *
 * PHP versions 4 and 5
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Davey Shafik <davey@php.net>
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD
 * @version  CVS: \$Id$
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @since    version 1.9.0a1 (2008-11-23)
 * @ignore
 */

\$GLOBALS['_PHP_COMPATINFO_CLASS_" . strtoupper($extName) . "'] = " .
        var_export($classes, true) . ";
?>");

    }
}

$const_glob_list = array_unique($const_glob_list);
sort($const_glob_list);

$requires = '';
$globals  = '';
foreach ($const_glob_list as $cstExt) {
    $requires .= "require_once 'PHP/CompatInfo/" . $cstExt . "_const_array.php';"
              . PHP_EOL;
    $globals  .= "    \$GLOBALS['_PHP_COMPATINFO_CONST_" . strtoupper($cstExt)
              . "'], " . PHP_EOL;
}
$globals  = rtrim($globals, ", ".PHP_EOL);
$globals .= PHP_EOL;

file_put_contents($target_directory . 'const_array.php',

"<?php
/**
 * Constant dictionary for PHP_CompatInfo 1.1.1 or better
 *
 * PHP versions 4 and 5
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Davey Shafik <davey@php.net>
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD
 * @version  CVS: \$Id$
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @since    version 1.1.1 (2006-07-27)
 * @ignore
 */

". $requires .
"
/**
 * Predefined Constants
 *
 * @link http://www.php.net/manual/en/reserved.constants.php
 * @global array \$GLOBALS['_PHP_COMPATINFO_CONST']
 */

\$GLOBALS['_PHP_COMPATINFO_CONST'] = array_merge(
". $globals .
"    );
?>");


$class_glob_list = array_unique($class_glob_list);
sort($class_glob_list);

$requires = '';
$globals  = '';
foreach ($class_glob_list as $clsExt) {
    $requires .= "require_once 'PHP/CompatInfo/" . $clsExt . "_class_array.php';"
              . PHP_EOL;
    $globals  .= "    \$GLOBALS['_PHP_COMPATINFO_CLASS_" . strtoupper($clsExt)
              . "'], " . PHP_EOL;
}
$globals  = rtrim($globals, ", ".PHP_EOL);
$globals .= PHP_EOL;

file_put_contents($target_directory . 'class_array.php',

"<?php
/**
 * Class dictionary for PHP_CompatInfo 1.9.0a1 or better
 *
 * PHP versions 4 and 5
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Davey Shafik <davey@php.net>
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD
 * @version  CVS: \$Id$
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @since    version 1.9.0a1 (2008-11-23)
 * @ignore
 */

". $requires .
"
/**
 * Predefined Classes
 *
 * > Standard Defined Classes
 *   These classes are defined in the standard set of functions included in
 *   the PHP build.
 * - Directory
 * - stdClass
 * -  __PHP_Incomplete_Class
 *
 * > Predefined classes as of PHP 5
 *   These additional predefined classes were introduced in PHP 5.0.0
 * - Exception
 * - php_user_filter
 *
 * > Miscellaneous extensions
 *   define other classes which are described in their reference.
 *
 * @link http://www.php.net/manual/en/function.get-declared-classes.php
 * @link http://www.php.net/manual/en/reserved.classes.php
 * @global array \$GLOBALS['_PHP_COMPATINFO_CLASS']
 */

\$GLOBALS['_PHP_COMPATINFO_CLASS'] = array_merge(
". $globals .
"    );
?>");
?>