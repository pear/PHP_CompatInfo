<?php
/**
 * Null renderer for PHP_CompatInfo component.
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
 * The PHP_CompatInfo_Renderer_Null class is a concrete implementation
 * of PHP_CompatInfo_Renderer abstract class. It simply consumes output events.
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @since    Class available since Release 1.8.0b2
 */
class PHP_CompatInfo_Renderer_Null extends PHP_CompatInfo_Renderer
{
    /**
     * Null Renderer Class constructor (ZE1) for PHP4
     *
     * @param object &$parser Instance of the parser (model of MVC pattern)
     * @param array  $conf    A hash containing any additional configuration
     *
     * @access public
     * @since  version 1.8.0b2 (2008-06-03)
     */
    function PHP_CompatInfo_Renderer_Null(&$parser, $conf)
    {
        $this->__construct($parser, $conf);
    }

    /**
     * Null Renderer Class constructor (ZE2) for PHP5+
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
    }

    /**
     * Consumes output events
     *
     * @access public
     * @return void
     * @since  version 1.8.0RC1 (2008-07-01)
     */
    function display()
    {
    }
}
?>