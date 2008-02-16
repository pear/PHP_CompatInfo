<?php
/**
 * Check Compatibility of chunk of PHP code
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
 * @author   Davey Shafik <davey@php.net>
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @since    File available since Release 0.7.0
 */

/**
 * An array of function init versions and extension
 */
require_once 'PHP/CompatInfo/func_array.php';

/**
 * An array of constants and their init versions
 */
require_once 'PHP/CompatInfo/const_array.php';

/**
 * Check Compatibility of chunk of PHP code
 *
 * @example docs/examples/checkConstants.php
 *          Example that shows minimum version with Constants
 * @example docs/examples/parseFile.php
 *          Example on how to parse a file
 * @example docs/examples/parseDir.php
 *          Example on how to parse a directory
 * @example docs/examples/parseArray.php
 *          Example on using using parseArray() to parse a script
 * @example docs/examples/parseString.php
 *          Example on how to parse a string
 * @example docs/examples/cliCustom.php
 *          Example of using PHP_CompatInfo_Cli
 *
 * @category  PHP
 * @package   PHP_CompatInfo
 * @author    Davey Shafik <davey@php.net>
 * @author    Laurent Laville <pear@laurent-laville.org>
 * @copyright 2003 Davey Shafik and Synaptic Media. All Rights Reserved.
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CompatInfo
 * @since     Class available since Release 0.7.0
 */

class PHP_CompatInfo
{
    /**
     * @var string Earliest version of PHP to use
     * @since  0.7.0
     */
    var $latest_version = '3.0.0';

    /**
     * @var string Last version of PHP to use
     */
    var $earliest_version = '';

    /**
     * @var boolean Toggle parseDir recursion
     * @since  0.7.0
     */
    var $recurse_dir = true;

    /**
     * Parse a single file
     *
     * Parse a file for its compatibility info
     *
     * @param string $file    Path of File to parse
     * @param array  $options An array of options where:
     *  - 'debug'            Contains a boolean to control whether
     *                       extra ouput is shown.
     *  - 'ignore_functions' Contains an array of functions to ignore
     *                       when calculating the version needed.
     *  - 'ignore_constants' Contains an array of constants to ignore
     *                       when calculating the version needed.
     *  - 'ignore_extensions' Contains an array of php extensions to ignore
     *                       when calculating the version needed.
     *  - 'ignore_versions'  Contains an array of php versions to ignore
     *                       when calculating the version needed.
     *
     * @access public
     * @return array
     * @since  version 0.7.0 (2004-03-09)
     */
    function parseFile($file, $options = array())
    {
        if (is_string($file) && !file_exists($file)) {
            // filter invalid input
            return false;
        }
        $options = array_merge(array('debug' => false), $options);
        $tokens  = $this->_tokenize($file);
        return $this->_parseTokens($tokens, $options);
    }

    /**
     * Parse a string
     *
     * Parse a string for its compatibility info
     *
     * @param string $string  PHP Code to parses
     * @param array  $options An array of options where:
     *  - 'debug'            Contains a boolean to control whether
     *                       extra ouput is shown.
     *  - 'ignore_functions' Contains an array of functions to ignore
     *                       when calculating the version needed.
     *  - 'ignore_constants' Contains an array of constants to ignore
     *                       when calculating the version needed.
     *  - 'ignore_extensions' Contains an array of php extensions to ignore
     *                       when calculating the version needed.
     *  - 'ignore_versions'  Contains an array of php versions to ignore
     *                       when calculating the version needed.
     *
     * @access public
     * @return array|false
     * @since  version 0.7.0 (2004-03-09)
     */
    function parseString($string, $options = array())
    {
        if (!is_string($string)) {
            // filter invalid input
            return false;
        }
        $options = array_merge(array('debug' => false), $options);
        $tokens  = $this->_tokenize($string, true);
        return $this->_parseTokens($tokens, $options);
    }

    /**
     * Parse a directory
     *
     * Parse a directory recursively for its compatibility info
     *
     * @param string $dir     Path of folder to parse
     * @param array  $options An array of options where:
     *  - 'file_ext'         Contains an array of file extensions to parse
     *                       for PHP code. Default: php, php4, inc, phtml
     *  - 'recurse_dir'      Boolean on whether to recursively find files
     *  - 'debug'            Contains a boolean to control whether
     *                       extra ouput is shown.
     *  - 'ignore_functions' Contains an array of functions to ignore
     *                       when calculating the version needed.
     *  - 'ignore_constants' Contains an array of constants to ignore
     *                       when calculating the version needed.
     *  - 'ignore_files'     Contains an array of files to ignore.
     *                       File names are case insensitive.
     *  - 'ignore_dirs'      Contains an array of directories to ignore.
     *                       Directory names are case insensitive.
     *  - 'ignore_extensions' Contains an array of php extensions to ignore
     *                       when calculating the version needed.
     *  - 'ignore_versions'  Contains an array of php versions to ignore
     *                       when calculating the version needed.
     *
     * @access public
     * @return array|false
     * @since  version 0.8.0 (2004-04-22)
     * @see PHP_CompatInfo::_fileList()
     */
    function parseDir($dir, $options = array())
    {
        if (!is_dir($dir) || !is_readable($dir)) {
            // filter invalid input
            return false;
        }

        $files_parsed     = array();
        $latest_version   = $this->latest_version;
        $earliest_version = $this->earliest_version;
        $extensions       = array();
        $constants        = array();
        $ignored          = array();
        $default_options  = array(
            'file_ext' => array('php', 'php4', 'inc', 'phtml'),
            'recurse_dir' => true,
            'debug' => false,
            'ignore_files' => array(),
            'ignore_dirs' => array()
            );

        $options = array_merge($default_options, $options);

        if ($dir{strlen($dir)-1} == '/' || $dir{strlen($dir)-1} == '\\') {
            $dir = substr($dir, 0, -1);
        }
        $options['file_ext']
            = array_map('strtolower', $options['file_ext']);
        $options['ignore_files']
            = array_map('strtolower', $options['ignore_files']);
        $options['ignore_dirs']
            = array_map('strtolower', $options['ignore_dirs']);
        $files_raw = $this->_fileList($dir, $options);
        foreach ($files_raw as $file) {
            if (in_array(strtolower($file), $options['ignore_files'])) {
                $ignored[] = $file;
                continue;
            }
            $file_info = pathinfo($file);
            if (isset($file_info['extension'])
                && in_array(strtolower($file_info['extension']),
                    $options['file_ext'])) {
                $tokens              = $this->_tokenize($file);
                $files_parsed[$file] = $this->_parseTokens($tokens, $options);
            }
        }
        foreach ($files_parsed as $file) {
            $cmp = version_compare($latest_version, $file['version']);
            if ($cmp === -1) {
                $latest_version = $file['version'];
            }
            if ($file['max_version'] != '') {
                $cmp = version_compare($earliest_version, $file['max_version']);
                if ($earliest_version == '' || $cmp === 1) {
                    $earliest_version = $file['max_version'];
                }
            }
            foreach ($file['extensions'] as $ext) {
                if (!in_array($ext, $extensions)) {
                    $extensions[] = $ext;
                }
            }
            foreach ($file['constants'] as $const) {
                if (!in_array($const, $constants)) {
                    $constants[] = $const;
                }
            }
        }

        if (count($files_parsed) == 0) {
            return false;
        }

        $files_parsed['constants']     = $constants;
        $files_parsed['extensions']    = $extensions;
        $files_parsed['version']       = $latest_version;
        $files_parsed['max_version']   = $earliest_version;
        $files_parsed['ignored_files'] = $ignored;

        $files_parsed = array_reverse($files_parsed);
        return $files_parsed;
    }

    /**
     * Alias of parseDir
     *
     * Alias of parseDir function
     *
     * @param string $folder  Path of folder to parse
     * @param array  $options An array of options
     *
     * @uses   PHP_CompatInfo::parseDir()
     * @access public
     * @return array
     * @since  version 0.7.0 (2004-03-09)
     */
    function parseFolder($folder, $options = array())
    {
        return $this->parseDir($folder, $options);
    }

    /**
     * Parse an Array of Files
     *
     * You can parse an array of Files or Strings, to parse
     * strings, $options['is_string'] must be set to true
     *
     * @param array $files   Array of file names or code strings
     * @param array $options An array of options where:
     *  - 'file_ext'         Contains an array of file extensions to parse
     *                       for PHP code. Default: php, php4, inc, phtml
     *  - 'debug'            Contains a boolean to control whether
     *                       extra ouput is shown.
     *  - 'ignore_functions' Contains an array of functions to ignore
     *                       when calculating the version needed.
     *  - 'ignore_constants' Contains an array of constants to ignore
     *                       when calculating the version needed.
     *  - 'ignore_files'     Contains an array of files to ignore.
     *                       File names are case insensitive.
     *  - 'is_string'        Contains a boolean which says if the array values
     *                       are strings or file names.
     *  - 'ignore_extensions' Contains an array of php extensions to ignore
     *                       when calculating the version needed.
     *  - 'ignore_versions'  Contains an array of php versions to ignore
     *                       when calculating the version needed.
     *
     * @access public
     * @return array|false
     * @since  version 0.7.0 (2004-03-09)
     */
    function parseArray($files, $options = array())
    {
        $files_parsed     = array();
        $latest_version   = $this->latest_version;
        $earliest_version = $this->earliest_version;
        $extensions       = array();
        $constants        = array();

        $options = array_merge(array(
            'file_ext' => array('php', 'php4', 'inc', 'phtml'),
            'is_string' => false,
            'debug' => false,
            'ignore_files' => array()
            ), $options);

        $options['ignore_files'] = array_map('strtolower', $options['ignore_files']);
        foreach ($files as $file) {
            if ($options['is_string'] === false) {
                $pathinfo = pathinfo($file);
                if (!in_array(strtolower($file), $options['ignore_files'])
                    && in_array($pathinfo['extension'], $options['file_ext'])) {
                    $tokens = $this->_tokenize($file, $options['is_string']);
                    $files_parsed[$file]
                            = $this->_parseTokens($tokens, $options);
                } else {
                    $ignored[] = $file;
                }
            } else {
                $tokens         = $this->_tokenize($file, $options['is_string']);
                $files_parsed[] = $this->_parseTokens($tokens, $options);
            }
        }

        foreach ($files_parsed as $file) {
            $cmp = version_compare($latest_version, $file['version']);
            if ($cmp === -1) {
                $latest_version = $file['version'];
            }
            if ($file['max_version'] != '') {
                $cmp = version_compare($earliest_version, $file['max_version']);
                if ($earliest_version == '' || $cmp === 1) {
                    $earliest_version = $file['max_version'];
                }
            }
            foreach ($file['extensions'] as $ext) {
                if (!in_array($ext, $extensions)) {
                    $extensions[] = $ext;
                }
            }
            foreach ($file['constants'] as $const) {
                if (!in_array($const, $constants)) {
                    $constants[] = $const;
                }
            }
        }

        if (count($files_parsed) == 0) {
            return false;
        }

        $files_parsed['constants']     = $constants;
        $files_parsed['extensions']    = $extensions;
        $files_parsed['version']       = $latest_version;
        $files_parsed['max_version']   = $earliest_version;
        $files_parsed['ignored_files'] =  isset($ignored) ? $ignored : array();

        $files_parsed = array_reverse($files_parsed);
        return $files_parsed;
    }

    /**
     * Load components list
     *
     * Load components list for a PHP version or subset
     *
     * @param string         $min           PHP minimal version
     * @param string|boolean $max           (optional) PHP maximal version
     * @param boolean        $include_const (optional) include constants list
     *                                                 in final result
     *
     * @return array         An array of php function/constant names to ignore
     * @access public
     * @static
     * @since  version 1.2.0 (2006-08-23)
     */
    function loadVersion($min, $max = false, $include_const = false)
    {
        $keys = array();
        foreach ($GLOBALS['_PHP_COMPATINFO_FUNCS'] as $func => $arr) {
            if (isset($arr['pecl']) && $arr['pecl'] === true) {
                continue;
            }
            if (version_compare($arr['init'], $min) < 0) {
                continue;
            }
            if ($max) {
                $end = (isset($arr['end'])) ? $arr['end'] : $arr['init'];

                if (version_compare($end, $max) < 1) {
                    $keys[] = $func;
                }
            } else {
                $keys[] = $func;
            }
        }

        if ($include_const === true) {
            $keys = array('functions' => $keys, 'constants' => array());
            foreach ($GLOBALS['_PHP_COMPATINFO_CONST'] as $const => $arr) {
                if (version_compare($arr['init'], $min) < 0) {
                    continue;
                }
                if ($max) {
                    $end = (isset($arr['end'])) ? $arr['end'] : $arr['init'];

                    if (version_compare($end, $max) < 1) {
                        $keys['constants'][] = $arr['name'];
                    }
                } else {
                    $keys['constants'][] = $arr['name'];
                }
            }
        }
        return $keys;
    }

    /**
     * Parse the given Tokens
     *
     * The tokens are those returned by
     * token_get_all() which is nicely
     * wrapped in PHP_CompatInfo::_tokenize
     *
     * @param array   $tokens  Array of PHP Tokens
     * @param boolean $options Show Extra Output
     *
     * @access private
     * @return array
     * @since  version 0.7.0 (2004-03-09)
     */
    function _parseTokens($tokens, $options)
    {
        static $akeys;

        $functions         = array();
        $functions_version = array();
        $latest_version    = $this->latest_version;
        $earliest_version  = $this->earliest_version;
        $extensions        = array();
        $constants         = array();
        $constant_names    = array();
        $udf               = array();

        if (isset($options['ignore_constants'])) {
            $options['ignore_constants']
                = array_map('strtoupper', $options['ignore_constants']);
        } else {
            $options['ignore_constants'] = array();
        }
        if (isset($options['ignore_extensions'])) {
            $options['ignore_extensions']
                = array_map('strtolower', $options['ignore_extensions']);
        } else {
            $options['ignore_extensions'] = array();
        }
        if (isset($options['ignore_versions'][0])) {
            $min_ver = $options['ignore_versions'][0];
        } else {
            $min_ver = false;
        }
        if (isset($options['ignore_versions'][1])) {
            $max_ver = $options['ignore_versions'][1];
        } else {
            $max_ver = false;
        }

        $token_count = sizeof($tokens);
        $i           = 0;
        while ($i < $token_count) {
            $found_func = true;
            if (is_array($tokens[$i])
                && (token_name($tokens[$i][0]) == 'T_FUNCTION')) {
                $found_func = false;
            }
            while ($found_func == false) {
                $i += 1;
                if (is_array($tokens[$i])
                    && (token_name($tokens[$i][0]) == 'T_STRING')) {
                    $found_func = true;
                    $udf[]      = $tokens[$i][1];
                }
            }
            // Try to detect PHP method chaining implementation
            if (is_array($tokens[$i])
                && (token_name($tokens[$i][0]) == 'T_VARIABLE')
                && (is_array($tokens[$i+1]))
                && (token_name($tokens[$i+1][0]) == 'T_OBJECT_OPERATOR')
                && (is_array($tokens[$i+2]))
                && (token_name($tokens[$i+2][0]) == 'T_STRING')
                && (is_array($tokens[$i+3]) === false)
                && ($tokens[$i+3] == '(')) {

                $i                   += 3;
                $php5_method_chaining = false;
                while ((!is_array($tokens[$i]) && $tokens[$i] == ';') === false) {
                    $i += 1;
                    if (((is_array($tokens[$i]) === false && $tokens[$i] == ')')
                        || (is_array($tokens[$i])
                        && token_name($tokens[$i][0]) == 'T_WHITESPACE'))
                        && is_array($tokens[$i+1])
                        && token_name($tokens[$i+1][0]) == 'T_OBJECT_OPERATOR') {
                        $php5_method_chaining = true;
                    }
                }
            }
            if (is_array($tokens[$i])
                && (token_name($tokens[$i][0]) == 'T_STRING')
                && (isset($tokens[$i + 1]))
                && ($tokens[$i + 1][0] == '(')) {
                if ((is_array($tokens[$i - 1]))
                    && (token_name($tokens[$i - 1][0]) != 'T_DOUBLE_COLON')
                    && (token_name($tokens[$i - 1][0]) != 'T_OBJECT_OPERATOR')) {
                    $functions[] = strtolower($tokens[$i][1]);
                } elseif (!is_array($tokens[$i - 1])) {
                    $functions[] = strtolower($tokens[$i][1]);
                }
            }
            if (is_array($tokens[$i])) {
                if (!isset($akeys)) {
                    // build contents one time only (static variable)
                    $akeys = array_keys($GLOBALS['_PHP_COMPATINFO_CONST']);
                }
                $const = strtoupper($tokens[$i][1]);
                $found = array_search($const, $akeys);
                if ($found !== false) {
                    if (token_name($tokens[$i][0])
                        == 'T_ENCAPSED_AND_WHITESPACE') {
                        // PHP 5 constant tokens found into a string
                    } else {
                        $init = $GLOBALS['_PHP_COMPATINFO_CONST'][$const]['init'];
                        if (!in_array($const, $options['ignore_constants'])
                            && (!PHP_CompatInfo::_ignore($init,
                                $min_ver, $max_ver))) {
                            $constants[]    = $const;
                            $latest_version = $init;
                        }
                    }
                }
            }
            $i += 1;
        }

        $functions = array_unique($functions);
        if (isset($options['ignore_functions'])) {
            $options['ignore_functions']
                = array_map('strtolower', $options['ignore_functions']);
        } else {
            $options['ignore_functions'] = array();
        }
        foreach ($functions as $name) {
            if (!isset($GLOBALS['_PHP_COMPATINFO_FUNCS'][$name])) {
                continue;  // skip this unknown function
            }
            $func = $GLOBALS['_PHP_COMPATINFO_FUNCS'][$name];

            // retrieve if available the extension name
            if ((isset($func['ext']))
                && ($func['ext'] != 'ext_standard')
                && ($func['ext'] != 'zend')) {
                if ($func['pecl'] === false) {
                    $extension = substr($func['ext'], 4);
                    if ($extension{0} == '_') {
                        $extension = $func['ext'];
                    }
                } else {
                    $extension = $func['ext'];
                }
            } else {
                $extension = false;
            }

            if ((!in_array($name, $udf))
                && (!in_array($name, $options['ignore_functions']))) {

                if ($extension
                    && in_array($extension, $options['ignore_extensions'])) {
                    continue;  // skip this extension function
                }

                if (PHP_CompatInfo::_ignore($func['init'], $min_ver, $max_ver)) {
                    continue;  // skip this function version
                }

                if ($options['debug'] == true) {
                    $functions_version[$func['init']][] = array(
                        'function' => $name,
                        'extension' => substr($func['ext'], 0, 4) == 'ext_'
                            ? $extension : $func['ext'],
                        'pecl' => $func['pecl']
                        );
                }
                if ($extension === false
                    || (isset($func['pecl']) && $func['pecl'] === false) ) {
                    $cmp = version_compare($latest_version, $func['init']);
                    if ($cmp === -1) {
                        $latest_version = $func['init'];
                    }
                    if (array_key_exists('end', $func)) {
                        $cmp = version_compare($earliest_version, $func['end']);
                        if ($earliest_version == '' || $cmp === 1) {
                            $earliest_version = $func['end'];
                        }
                    }
                }

                if ($extension && !in_array($extension, $extensions)) {
                    $extensions[] = substr($func['ext'], 0, 4) == 'ext_'
                        ? $extension : $func['ext'];
                }
            }
        }

        $constants = array_unique($constants);
        foreach ($constants as $constant) {
            $const = $GLOBALS['_PHP_COMPATINFO_CONST'][$constant];
            if (PHP_CompatInfo::_ignore($const['init'], $min_ver, $max_ver)) {
                continue;  // skip this constant version
            }

            $cmp = version_compare($latest_version, $const['init']);
            if ($cmp === -1) {
                $latest_version = $const['init'];
            }
            if (array_key_exists('end', $const)) {
                $cmp = version_compare($earliest_version, $const['end']);
                if ($earliest_version == '' || $cmp === 1) {
                    $earliest_version = $const['end'];
                }
            }
            if (!in_array($const['name'], $constant_names)) {
                $constant_names[] = $const['name'];
            }
        }

        if (isset($php5_method_chaining)
            && $php5_method_chaining === true
            && version_compare($latest_version, '5.0.0') < 0) {
            // when PHP Method chaining is detected, only available for PHP 5
            $latest_version = '5.0.0';
        }

        ksort($functions_version);

        $functions_version['constants']   = $constant_names;
        $functions_version['extensions']  = $extensions;
        $functions_version['version']     = $latest_version;
        $functions_version['max_version'] = $earliest_version;

        $functions_version = array_reverse($functions_version);
        return $functions_version;
    }

    /**
     * Checks if function which has $init version should be keep
     * or ignore (version is between $min_ver and $max_ver).
     *
     * @param string $init    version of current function
     * @param string $min_ver minimum version of function to ignore
     * @param string $max_ver maximum version of function to ignore
     *
     * @access private
     * @return boolean True to ignore function/constant, false otherwise
     * @since  version 1.4.0 (2006-09-27)
     * @static
     */
    function _ignore($init, $min_ver, $max_ver)
    {
        if ($min_ver) {
            $cmp = version_compare($init, $min_ver);
            if ($max_ver && $cmp >= 0) {
                $cmp = version_compare($init, $max_ver);
                if ($cmp < 1) {
                    return true;
                }
            } elseif ($cmp === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Token a file or string
     *
     * @param string  $input     Filename or PHP code
     * @param boolean $is_string Whether or note the input is a string
     * @param boolean $debug     add token names for human read
     *
     * @access private
     * @return array
     * @since  version 0.7.0 (2004-03-09)
     */
    function _tokenize($input, $is_string = false, $debug = false)
    {
        if ($is_string === false) {
            $input = file_get_contents($input, true);
        }
        $tokens = token_get_all($input);

        if ($debug === true) {
            $r = array();
            foreach ($tokens as $token) {
                if (is_array($token)) {
                    $token[] = token_name($token[0]);
                } else {
                    $token = $token[0];
                }
                $r[] = $token;
            }
        } else {
            $r = $tokens;
        }
        return $r;
    }

    /**
     * Retrieve a listing of every file in $directory and
     * all subdirectories. Taken from PEAR_PackageFileManager_File
     *
     * @param string $directory full path to the directory you want the list of
     * @param array  $options   array of public config. options
     *
     * @access private
     * @return array list of files in a directory
     * @since  version 0.7.0 (2004-03-09)
     */
    function _fileList($directory, $options)
    {
        $ret = false;
        if (@is_dir($directory)
            && (!in_array(strtolower($directory), $options['ignore_dirs']))) {
            $ret = array();
            $d   = @dir($directory);
            while ($d && $entry = $d->read()) {
                if ($entry{0} != '.') {
                    if (is_file($directory . DIRECTORY_SEPARATOR . $entry)) {
                        $ret[] = $directory . DIRECTORY_SEPARATOR . $entry;
                    }
                    if (is_dir($directory . DIRECTORY_SEPARATOR . $entry)
                        && ($options['recurse_dir'] != false)) {
                        $tmp = $this->_fileList($directory
                            . DIRECTORY_SEPARATOR . $entry, $options);
                        if (is_array($tmp)) {
                            foreach ($tmp as $ent) {
                                $ret[] = $ent;
                            }
                        }
                    }
                }
            }
            if ($d) {
                $d->close();
            }
        }

        return $ret;
    }
}
?>