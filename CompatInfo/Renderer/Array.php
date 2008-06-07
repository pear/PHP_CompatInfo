<?php
/**
 * Array renderer for PHP_CompatInfo component.
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
 * @since    File available since Release 1.8.0b2
 */

/**
 * The PHP_CompatInfo_Renderer_Array class is a concrete implementation
 * of PHP_CompatInfo_Renderer abstract class. It simply display results as
 * a PHP array.
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @since    Class available since Release 1.8.0b2
 */
class PHP_CompatInfo_Renderer_Array extends PHP_CompatInfo_Renderer
{
    /**
     * Driver to display results array.
     *
     * Default is PHP::var_export, but you can use PEAR::Var_Dump if available
     *
     * @var    string
     * @access public
     */
    var $driver;

    /**
     * Array Renderer Class constructor (ZE1) for PHP4
     *
     * @param object &$parser Instance of the parser (model of MVC pattern)
     * @param array  $conf    A hash containing any additional configuration
     *
     * @access public
     * @since  version 1.8.0b2 (2008-06-03)
     */
    function PHP_CompatInfo_Renderer_Array(&$parser, $conf)
    {
        $this->__construct($parser, $conf);
    }

    /**
     * Array Renderer Class constructor (ZE2) for PHP5+
     *
     * @param object &$parser Instance of the parser (model of MVC pattern)
     * @param array  $conf    A hash containing any additional configuration
     *
     * @access public
     * @since  version 1.8.0b2 (2008-06-03)
     */
    function __construct(&$parser, $conf)
    {
        parent::PHP_CompatInfo_Renderer($parser, $conf);

        $driver = 'PEAR::Var_Dump';

        if (isset($conf[$driver])) {
            $var_dump = 'Var_Dump.php';
            if (PHP_CompatInfo_Renderer::isIncludable($var_dump)) {
                include_once $var_dump;

                $class_options = $conf['PEAR::Var_Dump'];
                if (isset($class_options['options'])) {
                    $options = $class_options['options'];
                } else {
                    $options = array();
                }
                if (isset($class_options['rendererOptions'])) {
                    $rendererOptions = $class_options['rendererOptions'];
                } else {
                    $rendererOptions = array();
                }
                if (php_sapi_name() == 'cli') {
                    // prevent wrong display on command line interface
                    $options['display_mode'] = 'Text';
                }

                Var_Dump::displayInit($options, $rendererOptions);
                $this->driver = $driver;
            }
        }

        if (!isset($this->driver)) {
            // if optional driver not defined, then use default PHP::var_export
            $this->driver = 'PHP';
        }
    }

    /**
     * Display final results
     *
     * Display final results, when data source parsing is over.
     *
     * @access public
     * @return void
     * @since  version 1.8.0b2 (2008-06-03)
     */
    function display()
    {
        if ($this->driver == 'PHP') {
            var_export($this->parseData);
        } else {
            Var_Dump::display($this->parseData);
        }
    }
}
?>