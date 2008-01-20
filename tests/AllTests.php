<?php
/**
 * Test suite for PHP_CompatInfo
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @since    File available since Release 1.6.0
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'PHP_CompatInfo_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

chdir(dirname(__FILE__));

require_once 'PHP_CompatInfo_TestSuite_Standard.php';

/**
 * Class for running all test suites for PHP_CompatInfo package.
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @since    File available since Release 1.6.0
 */

class PHP_CompatInfo_AllTests
{
    /**
     * Runs the test suite.
     *
     * @return void
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    /**
     * Adds the PHP_CompatInfo test suite.
     *
     * @return object the PHPUnit_Framework_TestSuite object
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHP_CompatInfo Test Suite');
        $suite->addTestSuite('PHP_CompatInfo_TestSuite_Standard');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'PHP_CompatInfo_AllTests::main') {
    PHP_CompatInfo_AllTests::main();
}
?>