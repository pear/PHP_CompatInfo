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

require_once 'File/Find.php';

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
     *  - 'debug'                   Contains a boolean to control whether
     *                              extra ouput is shown.
     *  - 'ignore_functions'        Contains an array of functions to ignore
     *                              when calculating the version needed.
     *  - 'ignore_constants'        Contains an array of constants to ignore
     *                              when calculating the version needed.
     *  - 'ignore_extensions'       Contains an array of php extensions to ignore
     *                              when calculating the version needed.
     *  - 'ignore_versions'         Contains an array of php versions to ignore
     *                              when calculating the version needed.
     *  - 'ignore_functions_match'  Contains an array of function patterns to ignore
     *                              when calculating the version needed.
     *  - 'ignore_extensions_match' Contains an array of extension patterns to ignore
     *                              when calculating the version needed.
     *  - 'ignore_constants_match'  Contains an array of constant patterns to ignore
     *                              when calculating the version needed.
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
     *  - 'debug'                   Contains a boolean to control whether
     *                              extra ouput is shown.
     *  - 'ignore_functions'        Contains an array of functions to ignore
     *                              when calculating the version needed.
     *  - 'ignore_constants'        Contains an array of constants to ignore
     *                              when calculating the version needed.
     *  - 'ignore_extensions'       Contains an array of php extensions to ignore
     *                              when calculating the version needed.
     *  - 'ignore_versions'         Contains an array of php versions to ignore
     *                              when calculating the version needed.
     *  - 'ignore_functions_match'  Contains an array of function patterns to ignore
     *                              when calculating the version needed.
     *  - 'ignore_extensions_match' Contains an array of extension patterns to ignore
     *                              when calculating the version needed.
     *  - 'ignore_constants_match'  Contains an array of constant patterns to ignore
     *                              when calculating the version needed.
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
     *  - 'file_ext'                Contains an array of file extensions to parse
     *                              for PHP code. Default: php, php4, inc, phtml
     *  - 'recurse_dir'             Boolean on whether to recursively find files
     *  - 'debug'                   Contains a boolean to control whether
     *                              extra ouput is shown.
     *  - 'ignore_functions'        Contains an array of functions to ignore
     *                              when calculating the version needed.
     *  - 'ignore_constants'        Contains an array of constants to ignore
     *                              when calculating the version needed.
     *  - 'ignore_files'            Contains an array of files to ignore.
     *                              File names are case insensitive.
     *  - 'ignore_dirs'             Contains an array of directories to ignore.
     *                              Directory names are case insensitive.
     *  - 'ignore_extensions'       Contains an array of php extensions to ignore
     *                              when calculating the version needed.
     *  - 'ignore_versions'         Contains an array of php versions to ignore
     *                              when calculating the version needed.
     *  - 'ignore_functions_match'  Contains an array of function patterns to ignore
     *                              when calculating the version needed.
     *  - 'ignore_extensions_match' Contains an array of extension patterns to ignore
     *                              when calculating the version needed.
     *  - 'ignore_constants_match'  Contains an array of constant patterns to ignore
     *                              when calculating the version needed.
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

        $files_parsed       = array();
        $latest_version     = $this->latest_version;
        $earliest_version   = $this->earliest_version;
        $extensions         = array();
        $constants          = array();
        $tokens             = array();
        $ignored            = array();
        $ignored_functions  = array();
        $ignored_extensions = array();
        $ignored_constants  = array();
        $default_options    = array(
            'file_ext' => array('php', 'php4', 'inc', 'phtml'),
            'recurse_dir' => true,
            'debug' => false,
            'ignore_files' => array(),
            'ignore_dirs' => array()
            );

        $options = array_merge($default_options, $options);

        if ($dir{strlen($dir)-1} == '/' || $dir{strlen($dir)-1} == '\\') {
            $path = $dir;
            $dir  = substr($dir, 0, -1);
        } else {
            $path = $dir . '/';
        }
        $path = str_replace("\\", '/', $dir);

        $options['file_ext']
            = array_map('strtolower', $options['file_ext']);

        // get directory list that should be ignored from scope
        $ignore_dirs = array();
        if (count($options['ignore_dirs']) > 0) {
            foreach ($options['ignore_dirs'] as $cond) {
                $dirs = File_Find::search('`'.$cond.'`', $dir, 'perl',
                                          true, 'directories');
                foreach ($dirs as $i => $d) {
                    $dirs[$i] = str_replace("\\", '/', $d);
                }
                $ignore_dirs = array_merge($ignore_dirs, $dirs);
            }
        }

        // get file list that should be ignored from scope
        $ignore_files = array();
        if (count($options['ignore_files']) > 0) {
            foreach ($options['ignore_files'] as $cond) {
                $files = File_Find::search('`'.$cond.'`', $dir, 'perl',
                                           true, 'files');
                foreach ($files as $i => $f) {
                    $files[$i] = str_replace("\\", '/', $f);
                }
                $ignore_files = array_merge($ignore_files, $files);
            }
        }

        $files_filter = array();

        if ($options['recurse_dir'] === false) {
            $files = File_Find::glob('`.*`', $dir, 'perl');

            foreach ($files as $f) {
                $file_info = pathinfo($f);
                $entry = $path . $file_info['basename'];
                if (is_dir($entry)) {
                    continue;
                } else {
                    if (in_array($entry, $ignore_files)) {
                        $ignored[] = $entry;
                        continue;
                    }
                }
                $files_filter[] = $entry;
            }
        } else {
            list($directories, $files) = File_Find::maptree($dir);

            foreach ($files as $f) {
                $entry     = str_replace("\\", '/', $f);
                $file_info = pathinfo($f);
                if (in_array($file_info['dirname'], $ignore_dirs)) {
                    $ignored[] = $entry;
                    continue;
                }
                if (in_array($entry, $ignore_files)) {
                    $ignored[] = $entry;
                    continue;
                }
                $files_filter[] = $entry;
            }
        }

        foreach ($files_filter as $file) {
            $file_info = pathinfo($file);
            if (isset($file_info['extension'])
                && in_array(strtolower($file_info['extension']),
                    $options['file_ext'])) {
                $tokens_list         = $this->_tokenize($file);
                $files_parsed[$file] = $this->_parseTokens($tokens_list, $options);
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
            foreach ($file['tokens'] as $token) {
                if (!in_array($token, $tokens)) {
                    $tokens[] = $token;
                }
            }
            foreach ($file['ignored_functions'] as $if) {
                if (!in_array($if, $ignored_functions)) {
                    $ignored_functions[] = $if;
                }
            }
            foreach ($file['ignored_extensions'] as $ie) {
                if (!in_array($ie, $ignored_extensions)) {
                    $ignored_extensions[] = $ie;
                }
            }
            foreach ($file['ignored_constants'] as $ic) {
                if (!in_array($ic, $ignored_constants)) {
                    $ignored_constants[] = $ic;
                }
            }
        }

        if (count($files_parsed) == 0) {
            return false;
        }

        $main_info = array('ignored_files'      => $ignored,
                           'ignored_functions'  => $ignored_functions,
                           'ignored_extensions' => $ignored_extensions,
                           'ignored_constants'  => $ignored_constants,
                           'max_version'   => $earliest_version,
                           'version'       => $latest_version,
                           'extensions'    => $extensions,
                           'constants'     => $constants,
                           'tokens'        => $tokens);

        $files_parsed = array_merge($main_info, $files_parsed);
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
     *  - 'file_ext'                Contains an array of file extensions to parse
     *                              for PHP code. Default: php, php4, inc, phtml
     *  - 'debug'                   Contains a boolean to control whether
     *                              extra ouput is shown.
     *  - 'ignore_functions'        Contains an array of functions to ignore
     *                              when calculating the version needed.
     *  - 'ignore_constants'        Contains an array of constants to ignore
     *                              when calculating the version needed.
     *  - 'ignore_files'            Contains an array of files to ignore.
     *                              File names are case insensitive.
     *  - 'is_string'               Contains a boolean which says if the array values
     *                              are strings or file names.
     *  - 'ignore_extensions'       Contains an array of php extensions to ignore
     *                              when calculating the version needed.
     *  - 'ignore_versions'         Contains an array of php versions to ignore
     *                              when calculating the version needed.
     *  - 'ignore_functions_match'  Contains an array of function patterns to ignore
     *                              when calculating the version needed.
     *  - 'ignore_extensions_match' Contains an array of extension patterns to ignore
     *                              when calculating the version needed.
     *  - 'ignore_constants_match'  Contains an array of constant patterns to ignore
     *                              when calculating the version needed.
     *
     * @access public
     * @return array|false
     * @since  version 0.7.0 (2004-03-09)
     */
    function parseArray($files, $options = array())
    {
        $files_parsed       = array();
        $latest_version     = $this->latest_version;
        $earliest_version   = $this->earliest_version;
        $extensions         = array();
        $constants          = array();
        $tokens             = array();
        $ignored            = array();
        $ignored_functions  = array();
        $ignored_extensions = array();
        $ignored_constants  = array();

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
                    $tokens_list = $this->_tokenize($file, $options['is_string']);
                    $files_parsed[$file]
                            = $this->_parseTokens($tokens_list, $options);
                } else {
                    $ignored[] = $file;
                }
            } else {
                $token_list     = $this->_tokenize($file, $options['is_string']);
                $files_parsed[] = $this->_parseTokens($token_list, $options);
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
            foreach ($file['tokens'] as $token) {
                if (!in_array($token, $tokens)) {
                    $tokens[] = $token;
                }
            }
            foreach ($file['ignored_functions'] as $if) {
                if (!in_array($if, $ignored_functions)) {
                    $ignored_functions[] = $if;
                }
            }
            foreach ($file['ignored_extensions'] as $ie) {
                if (!in_array($ie, $ignored_extensions)) {
                    $ignored_extensions[] = $ie;
                }
            }
            foreach ($file['ignored_constants'] as $ic) {
                if (!in_array($ic, $ignored_constants)) {
                    $ignored_constants[] = $ic;
                }
            }
        }

        if (count($files_parsed) == 0) {
            return false;
        }

        $main_info = array('ignored_files'      => $ignored,
                           'ignored_functions'  => $ignored_functions,
                           'ignored_extensions' => $ignored_extensions,
                           'ignored_constants'  => $ignored_constants,
                           'max_version'   => $earliest_version,
                           'version'       => $latest_version,
                           'extensions'    => $extensions,
                           'constants'     => $constants,
                           'tokens'        => $tokens);

        $files_parsed = array_merge($main_info, $files_parsed);
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

        $functions          = array();
        $functions_version  = array();
        $latest_version     = $this->latest_version;
        $earliest_version   = $this->earliest_version;
        $extensions         = array();
        $constants          = array();
        $constant_names     = array();
        $token_names        = array();
        $udf                = array();
        $ignore_functions   = array();
        $ignored_functions  = array();
        $ignore_extensions  = array();
        $ignored_extensions = array();
        $ignore_constants   = array();
        $ignored_constants  = array();
        $function_exists    = array();

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

        if (isset($options['ignore_functions_match'])) {
            list($ifm_compare, $ifm_patterns) = $options['ignore_functions_match'];
        } else {
            $ifm_compare = false;
        }
        if (isset($options['ignore_extensions_match'])) {
            list($iem_compare, $iem_patterns) = $options['ignore_extensions_match'];
        } else {
            $iem_compare = false;
        }
        if (isset($options['ignore_constants_match'])) {
            list($icm_compare, $icm_patterns) = $options['ignore_constants_match'];
        } else {
            $icm_compare = false;
        }

        $token_count = sizeof($tokens);
        $i           = 0;
        $found_class = false;
        while ($i < $token_count) {
            if ($this->_isToken($tokens[$i], 'T_FUNCTION')) {
                $found_func = false;
            } else {
                $found_func = true;
            }
            while ($found_func == false) {
                $i += 1;
                if ($this->_isToken($tokens[$i], 'T_STRING')) {
                    $found_func = true;
                    $func       = $tokens[$i][1];
                    if ($found_class === false
                        || in_array($func, $function_exists)) {
                        $udf[] = $func;
                    }
                }
            }

            // Try to detect PHP method chaining implementation
            if ($this->_isToken($tokens[$i], 'T_VARIABLE')
                && $this->_isToken($tokens[$i+1], 'T_OBJECT_OPERATOR')
                && $this->_isToken($tokens[$i+2], 'T_STRING')
                && $this->_isToken($tokens[$i+3], '(')) {

                $i                   += 3;
                $php5_method_chaining = false;
                while ((!is_array($tokens[$i]) && $tokens[$i] == ';') === false) {
                    $i += 1;
                    if ((($this->_isToken($tokens[$i], ')'))
                        || ($this->_isToken($tokens[$i], 'T_WHITESPACE')))
                        && $this->_isToken($tokens[$i+1], 'T_OBJECT_OPERATOR')) {

                        $php5_method_chaining = true;
                    }
                }
            }

            // Compare "ignore_functions_match" pre-condition
            if (is_string($ifm_compare)) {
                if (strcasecmp('preg_match', $ifm_compare) != 0) {
                    // Try to catch function_exists() condition
                    if ($this->_isToken($tokens[$i], 'T_STRING')
                        && (strcasecmp($tokens[$i][1], $ifm_compare) == 0)) {

                        while ((!$this->_isToken($tokens[$i],
                                                 'T_CONSTANT_ENCAPSED_STRING'))) {
                            $i += 1;
                        }
                        $func = trim($tokens[$i][1], "'");

                        /**
                         * try if function_exists()
                         * match one or more pattern condition
                         */
                        foreach ($ifm_patterns as $pattern) {
                            if (preg_match($pattern, $func) === 1) {
                                $ignore_functions[] = $func;
                            }
                        }
                    }
                }
            }

            // Compare "ignore_extensions_match" pre-condition
            if (is_string($iem_compare)) {
                if (strcasecmp('preg_match', $iem_compare) != 0) {
                    // Try to catch extension_loaded() condition
                    if ($this->_isToken($tokens[$i], 'T_STRING')
                        && (strcasecmp($tokens[$i][1], $iem_compare) == 0)) {

                        while ((!$this->_isToken($tokens[$i],
                                                 'T_CONSTANT_ENCAPSED_STRING'))) {
                            $i += 1;
                        }
                        $ext = trim($tokens[$i][1], "'");

                        /**
                         * try if extension_loaded()
                         * match one or more pattern condition
                         */
                        foreach ($iem_patterns as $pattern) {
                            if (preg_match($pattern, $ext) === 1) {
                                $ignore_extensions[] = $ext;
                            }
                        }
                    }
                }
            }

            // Compare "ignore_constants_match" pre-condition
            if (is_string($icm_compare)) {
                if (strcasecmp('preg_match', $icm_compare) != 0) {
                    // Try to catch defined() condition
                    if ($this->_isToken($tokens[$i], 'T_STRING')
                        && (strcasecmp($tokens[$i][1], $icm_compare) == 0)) {

                        while ((!$this->_isToken($tokens[$i],
                                                 'T_CONSTANT_ENCAPSED_STRING'))) {
                            $i += 1;
                        }
                        $cst = trim($tokens[$i][1], "'");

                        /**
                         * try if defined()
                         * match one or more pattern condition
                         */
                        foreach ($icm_patterns as $pattern) {
                            if (preg_match($pattern, $cst) === 1) {
                                $ignore_constants[] = $cst;
                            }
                        }
                    }
                }
            }

            if ($this->_isToken($tokens[$i], 'T_STRING')
                && (isset($tokens[$i+1]))
                && $this->_isToken($tokens[$i+1], '(')) {

                $is_function = false;

                if (isset($tokens[$i-1])
                    && !$this->_isToken($tokens[$i-1], 'T_DOUBLE_COLON')
                    && !$this->_isToken($tokens[$i-1], 'T_OBJECT_OPERATOR')) {

                    if (isset($tokens[$i-2])
                        && !$this->_isToken($tokens[$i-2], 'T_FUNCTION')) {
                        $is_function = true;
                    }
                }
                if ($is_function == true || !is_array($tokens[$i-1])) {
                    $functions[] = strtolower($tokens[$i][1]);
                }
            }

            // try to detect condition function_exists()
            if ($this->_isToken($tokens[$i], 'T_STRING')
                && (strcasecmp($tokens[$i][1], 'function_exists') == 0)) {

                $j = $i;
                while ((!$this->_isToken($tokens[$j],
                                         'T_CONSTANT_ENCAPSED_STRING'))) {
                    $j++;
                }
                $function_exists[] = trim($tokens[$j][1], "'");
            }

            // try to detect beginning of a class
            if ($this->_isToken($tokens[$i], 'T_CLASS')) {
                $found_class = true;
            }

            if (is_array($tokens[$i])) {
                if (!isset($akeys)) {
                    // build contents one time only (static variable)
                    $akeys = array_keys($GLOBALS['_PHP_COMPATINFO_CONST']);
                }
                $const = strtoupper($tokens[$i][1]);
                $found = array_search($const, $akeys);
                if ($found !== false) {
                    if ($this->_isToken($tokens[$i], 'T_ENCAPSED_AND_WHITESPACE')) {
                        // PHP 5 constant tokens found into a string
                    } else {
                        // Compare "ignore_constants_match" free condition
                        $icm_preg_match = false;
                        if (is_string($icm_compare)) {
                            if (strcasecmp('preg_match', $icm_compare) == 0) {
                                /**
                                 * try if preg_match()
                                 * match one or more pattern condition
                                 */
                                foreach ($icm_patterns as $pattern) {
                                    if (preg_match($pattern, $const) === 1) {
                                        $icm_preg_match = true;
                                        break;
                                    }
                                }
                            }
                        }

                        $init = $GLOBALS['_PHP_COMPATINFO_CONST'][$const]['init'];
                        if (!PHP_CompatInfo::_ignore($init, $min_ver, $max_ver)) {
                            $constants[] = $const;
                            if (in_array($const, $ignore_constants)
                                || in_array($const, $options['ignore_constants'])
                                || $icm_preg_match) {
                                $ignored_constants[] = $const;
                            } else {
                                $latest_version = $init;
                            }
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
        if (count($ignore_functions) > 0) {
            $ignore_functions = array_map('strtolower', $ignore_functions);
            $options['ignore_functions']
                = array_merge($options['ignore_functions'], $ignore_functions);
            $options['ignore_functions']
                = array_unique($options['ignore_functions']);
        }
        if (count($ignore_extensions) > 0) {
            $ignore_extensions = array_map('strtolower', $ignore_extensions);
            $options['ignore_extensions']
                = array_merge($options['ignore_extensions'], $ignore_extensions);
            $options['ignore_extensions']
                = array_unique($options['ignore_extensions']);
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

            // Compare "ignore_functions_match" free condition
            $ifm_preg_match = false;
            if (is_string($ifm_compare)) {
                if (strcasecmp('preg_match', $ifm_compare) == 0) {
                    /**
                     * try if preg_match()
                     * match one or more pattern condition
                     */
                    foreach ($ifm_patterns as $pattern) {
                        if (preg_match($pattern, $name) === 1) {
                            $ifm_preg_match = true;
                            break;
                        }
                    }
                }
            }

            if ((!in_array($name, $udf))
                && (!in_array($name, $options['ignore_functions']))
                && ($ifm_preg_match === false)) {

                if ($extension && !in_array($extension, $extensions)) {
                    $extensions[] = substr($func['ext'], 0, 4) == 'ext_'
                        ? $extension : $func['ext'];
                }

                // Compare "ignore_extensions_match" free condition
                $iem_preg_match = false;
                if (is_string($iem_compare)) {
                    if (strcasecmp('preg_match', $iem_compare) == 0) {
                        /**
                         * try if preg_match()
                         * match one or more pattern condition
                         */
                        foreach ($iem_patterns as $pattern) {
                            if (preg_match($pattern, $extension) === 1) {
                                $iem_preg_match = true;
                                break;
                            }
                        }
                    }
                }

                if ($extension
                    && (in_array($extension, $options['ignore_extensions'])
                        || $iem_preg_match)) {
                    if (!in_array($extension, $ignored_extensions)) {
                        // extension is ignored (only once)
                        $ignored_extensions[] = $extension;
                    }
                    // all extension functions are also ignored
                    $ignored_functions[] = $name;
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

            } else {
                // function is ignored
                $ignored_functions[] = $name;
            }
        }

        $ignored_constants = array_unique($ignored_constants);
        $constants         = array_unique($constants);
        foreach ($constants as $constant) {
            $const = $GLOBALS['_PHP_COMPATINFO_CONST'][$constant];
            if (PHP_CompatInfo::_ignore($const['init'], $min_ver, $max_ver)) {
                continue;  // skip this constant version
            }
            if (!in_array($constant, $ignored_constants)) {
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
            }
            if (!in_array($const['name'], $constant_names)) {
                // split PHP5 tokens and pure PHP constants
                if ($const['name'] == strtolower($const['name'])) {
                    $token_names[] = $const['name'];
                } else {
                    $constant_names[] = $const['name'];
                }
            }
        }

        if (isset($php5_method_chaining)
            && $php5_method_chaining === true
            && version_compare($latest_version, '5.0.0') < 0) {
            // when PHP Method chaining is detected, only available for PHP 5
            $latest_version = '5.0.0';
        }

        ksort($functions_version);

        $main_info = array('ignored_functions'  => $ignored_functions,
                           'ignored_extensions' => $ignored_extensions,
                           'ignored_constants'  => $ignored_constants,
                           'max_version' => $earliest_version,
                           'version'     => $latest_version,
                           'extensions'  => $extensions,
                           'constants'   => $constant_names,
                           'tokens'      => $token_names);

        $functions_version = array_merge($main_info, $functions_version);
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
     * Checks if the given token is of this symbolic name
     *
     * @param mixed  $token    Single PHP token to test
     * @param string $symbolic Symbolic name of the given token
     *
     * @access private
     * @return bool
     * @since  version 1.7.0b4 (2008-04-03)
     */
    function _isToken($token, $symbolic)
    {
        if (is_array($token)) {
            $t = token_name($token[0]);
        } else {
            $t = $token;
        }
        return ($t == $symbolic);
    }
}
?>