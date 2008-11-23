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
 * @since    version 1.9.0a1
 * @ignore
 */

/* Directory where to write all "*_const_array.php" and "*_class_array.php" files
   Must ended with a trailing directory separator */
$target_directory = dirname(__FILE__) . DIRECTORY_SEPARATOR;

/* default version for each extension
   if not defined, then suppose its 5.0.0 */
$exceptions = array('standard' => '4.0.0');
/* if default version is not 4.0.0, then we can fix the right
   constant initial version here */
require_once 'constant_exceptions.php';
/* if default version is not 4.0.0, then we can fix the right
   predefined class initial version here */
require_once 'class_exceptions.php';

$const_glob_list = array();
$class_glob_list = array();

//$extensions = get_loaded_extensions();
$extensions = array('standard','date');

foreach($extensions as $extension) {

    if (!extension_loaded($extension)) {
        continue;  // skip this extension if not loaded : prevent error
    }

    $ext = new ReflectionExtension($extension);

    // name of the current Extension
    $extName = $ext->getName();

    // version of the current Extension
    $extVers = $ext->getVersion();

    // default version to apply to each constant and class predefined
    $ver = isset($exceptions[$extName]) ? $exceptions[$extName] : '5.0.0';

    // constants described by the Extension interface
    $extConstants = $ext->getConstants();
    if (count($extConstants) > 0) {
        $const_glob_list[] = $extName;

        $constants = array();
        foreach($extConstants as $cst => $val) {
            $constants[$cst]['init'] = $ver;
            $constants[$cst]['name'] = $cst;
        }
        if (isset($constant_exceptions[$extName])) {
            // apply exceptions to give final constant results
            $constants = array_merge($constants, $constant_exceptions[$extName]);
        }
        ksort($constants);

        file_put_contents($target_directory . $extName . '_const_array.php',

"<?php
/**
 * $extName extension Constant dictionnary for PHP_CompatInfo 1.9.0a1 or better
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

\$GLOBALS['_PHP_COMPATINFO_CONST_" .strtoupper($extName). "'] = " .var_export($constants, true). ";
?>");

    }

    // classes described by the Extension interface
    $extClasses = $ext->getClassNames();
    if (count($extClasses) > 0) {
        $class_glob_list[] = $extName;

        $classes = array();
        foreach($extClasses as $i => $cls) {
            $classes[$cls]['init'] = $ver;
            $classes[$cls]['name'] = $cls;
            $classes[$cls]['ext']  = $extName;
            $classes[$cls]['pecl'] = false;
        }
        if (isset($class_exceptions[$extName])) {
            // apply exceptions to give final class results
            $classes = array_merge($classes, $class_exceptions[$extName]);
        }
        ksort($classes);

        file_put_contents($target_directory . $extName . '_class_array.php',

"<?php
/**
 * $extName extension Class dictionnary for PHP_CompatInfo 1.9.0a1 or better
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

\$GLOBALS['_PHP_COMPATINFO_CLASS_" .strtoupper($extName). "'] = " .var_export($classes, true). ";
?>");

    }
}

$const_glob_list = array_unique($const_glob_list);
sort($const_glob_list);

$requires  = '';
$globals   = '';
foreach($const_glob_list as $cstExt) {
    $requires .= "require_once 'PHP/CompatInfo/".$cstExt."_const_array.php';".PHP_EOL;
    $globals  .= "    \$GLOBALS['_PHP_COMPATINFO_CONST_".strtoupper($cstExt)."'], ".PHP_EOL;
}
$globals  = rtrim($globals, ", ".PHP_EOL);
$globals .= PHP_EOL;

file_put_contents($target_directory . 'const_array.php',

"<?php
/**
 * Constant dictionnary for PHP_CompatInfo 1.1.1 or better
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

$requires  = '';
$globals   = '';
foreach($class_glob_list as $clsExt) {
    $requires .= "require_once 'PHP/CompatInfo/".$clsExt."_class_array.php';".PHP_EOL;
    $globals  .= "    \$GLOBALS['_PHP_COMPATINFO_CLASS_".strtoupper($clsExt)."'], ".PHP_EOL;
}
$globals  = rtrim($globals, ", ".PHP_EOL);
$globals .= PHP_EOL;

file_put_contents($target_directory . 'class_array.php',

"<?php
/**
 * Class dictionnary for PHP_CompatInfo 1.9.0a1 or better
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