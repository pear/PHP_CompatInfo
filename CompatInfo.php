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

require_once 'PHP/CompatInfo/Parser.php';

/**
 * Check Compatibility of chunk of PHP code
 *
 * This class is the controller in the MVC design pattern of API 1.8.0 (since beta 2)
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
     * Instance of the parser (model in MVC desing pattern)
     *
     * @var    object
     * @since  1.8.0b2
     * @access protected
     */
    var $parser;

    /**
     * Class constructor (ZE1) for PHP4
     *
     * @param string $render (optional) Type of renderer to show results
     * @param array  $conf   (optional) A hash containing any additional
     *                       configuration a renderer may use
     *
     * @access public
     * @since  version 1.8.0b2 (2008-06-03)
     */
    function PHP_CompatInfo($render = 'array', $conf = array())
    {
        $this->__construct($render, $conf);
    }

    /**
     * Class constructor (ZE2) for PHP5+
     *
     * @param string $render (optional) Type of renderer to show results
     * @param array  $conf   (optional) A hash containing any additional
     *                       configuration a renderer may use
     *
     * @access public
     * @since  version 1.8.0b2 (2008-06-03)
     */
    function __construct($render = 'array', $conf = array())
    {
        $this->parser = new PHP_CompatInfo_Parser();
        $this->parser->setOutputDriver($render, $conf);
    }

    /**
     * Registers a new listener
     *
     * Registers a new listener with the given criteria.
     *
     * @param mixed  $callback A PHP callback
     * @param string $nName    (optional) Expected notification name
     *
     * @access public
     * @return void
     * @since  version 1.8.0b3 (2008-06-07)
     */
    function addListener($callback, $nName = EVENT_DISPATCHER_GLOBAL)
    {
        $this->parser->addListener($callback, $nName);
    }

    /**
     * Removes a registered listener
     *
     * Removes a registered listener that correspond to the given criteria.
     *
     * @param mixed  $callback A PHP callback
     * @param string $nName    (optional) Expected notification name
     *
     * @access public
     * @return bool  True if listener was removed, false otherwise.
     * @since  version 1.8.0b3 (2008-06-07)
     */
    function removeListener($callback, $nName = EVENT_DISPATCHER_GLOBAL)
    {
        return $this->parser->removeListener($callback, $nName);
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
     * @param boolean        $groupby_vers  (optional) give initial php version
     *                                                 of function or constant
     *
     * @return array         An array of php function/constant names to ignore
     * @access public
     * @static
     * @since  version 1.2.0 (2006-08-23)
     */
    function loadVersion($min, $max = false,
                         $include_const = false, $groupby_vers = false)
    {
        return $this->parser->loadVersion($min, $max, $include_const, $groupby_vers);
    }

    /**
     * Parse a data source
     *
     * Parse a data source with auto detect ability. This data source, may be
     * one of these follows: a directory, a file, a string (chunk of code),
     * an array of multiple origin.
     *
     * Each of five parsing functions support common and specifics options.
     *
     *  * Common options :
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
     *  * parseArray, parseDir|parseFolder, specific options :
     *  - 'file_ext'                Contains an array of file extensions to parse
     *                              for PHP code. Default: php, php4, inc, phtml
     *  - 'ignore_files'            Contains an array of files to ignore.
     *                              File names are case insensitive.
     *
     *  * parseArray specific options :
     *  - 'is_string'               Contains a boolean which says if the array values
     *                              are strings or file names.
     *
     *  * parseDir|parseFolder specific options :
     *  - 'recurse_dir'             Boolean on whether to recursively find files
     *  - 'ignore_dirs'             Contains an array of directories to ignore.
     *                              Directory names are case insensitive.
     *
     * @param mixed $data    Data source (may be file, dir, string, or array)
     * @param array $options An array of options. See above.
     *
     * @access public
     * @return array or false on error
     * @since  version 1.8.0b2 (2008-06-03)
     * @see    PHP_CompatInfo_Parser::parseData()
     */
    function parseData($data, $options = array())
    {
        return $this->parser->parseData($data, $options);
    }

    /**
     * Parse an Array of Files or Strings
     *
     * You can parse an array of Files or Strings, to parse
     * strings, $options['is_string'] must be set to true.
     *
     * This recommandation is no more valid since version 1.8.0b2
     * Array my contains multiple and mixed origin (file, dir, string).
     *
     * @param array $array   Array of data sources
     * @param array $options Parser options (see parseData() method for details)
     *
     * @access public
     * @return array or false on error
     * @since  version 0.7.0 (2004-03-09)
     * @see    parseData()
     */
    function parseArray($array, $options = array())
    {
        return $this->parser->parseData($array, $options);
    }

    /**
     * Parse a string
     *
     * Parse a string for its compatibility info
     *
     * @param string $string  PHP Code to parse
     * @param array  $options Parser options (see parseData() method for details)
     *
     * @access public
     * @return array or false on error
     * @since  version 0.7.0 (2004-03-09)
     * @see    parseData()
     */
    function parseString($string, $options = array())
    {
        return $this->parser->parseData($string, $options);
    }

    /**
     * Parse a single file
     *
     * Parse a file for its compatibility info
     *
     * @param string $file    Path of File to parse
     * @param array  $options Parser options (see parseData() method for details)
     *
     * @access public
     * @return array or false on error
     * @since  version 0.7.0 (2004-03-09)
     * @see    parseData()
     */
    function parseFile($file, $options = array())
    {
        return $this->parser->parseData($file, $options);
    }

    /**
     * Parse a directory
     *
     * Parse a directory recursively for its compatibility info
     *
     * @param string $dir     Path of folder to parse
     * @param array  $options Parser options (see parseData() method for details)
     *
     * @access public
     * @return array or false on error
     * @since  version 0.8.0 (2004-04-22)
     * @see    parseData()
     */
    function parseDir($dir, $options = array())
    {
        return $this->parser->parseData($dir, $options);
    }

    /**
     * Alias of parseDir
     *
     * Alias of parseDir function
     *
     * @param string $folder  Path of folder to parse
     * @param array  $options Parser options (see parseData() method for details)
     *
     * @access public
     * @return array or false on error
     * @since  version 0.7.0 (2004-03-09)
     * @see    parseDir(), parseData()
     */
    function parseFolder($folder, $options = array())
    {
        return $this->parser->parseData($folder, $options);
    }
}
?>