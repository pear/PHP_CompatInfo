<?php
/**
 * Test suite for bugs declared in the PHP_CompatInfo class
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
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "PHP_CompatInfo_TestSuite_Bugs::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'PHP/CompatInfo.php';

/**
 * Test suite class to test standard PHP_CompatInfo API.
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @since    File available since Release 1.6.0
 */
class PHP_CompatInfo_TestSuite_Bugs extends PHPUnit_Framework_TestCase
{
    /**
     * A PCI object
     * @var  object
     */
    protected $pci;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        include_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite('PHP_CompatInfo Standard Tests');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->pci = new PHP_CompatInfo();
    }

    /**
     * Tears down the fixture.
     * This method is called after a test is executed.
     *
     * @return void
     */
    protected function tearDown()
    {
        unset($this->pci);
    }

    /**
     * Regression test for bug #1626
     *
     * @return void
     * @link   http://pear.php.net/bugs/bug.php?id=1626
     *         Class calls are seen wrong
     */
    public function testBug1626()
    {
        $str = '<?php
include("File.php");
File::write("test", "test");
?>';
        $r   = $this->pci->parseString($str);
        $exp = array('max_version' => '',
                     'version' => '3.0.0',
                     'extensions' => array(),
                     'constants' => array());
        $this->assertSame($exp, $r);
    }

    /**
     * Regression test for bug #2771
     *
     * @return void
     * @link   http://pear.php.net/bugs/bug.php?id=2771
     *         Substr($var,4) not working for SAPI_ extensions
     */
    public function testBug2771()
    {
        $str = '<?php
apache_request_headers();
apache_response_headers();
?>';
        $r   = $this->pci->parseString($str);
        $exp = array('max_version' => '',
                     'version' => '4.3.0',
                     'extensions' => array('sapi_apache'),
                     'constants' => array());
        $this->assertSame($exp, $r);
    }

    /**
     * Regression test for bug #8559
     *
     * @return void
     * @link   http://pear.php.net/bugs/bug.php?id=8559
     *         PHP_CompatInfo fails to scan if it finds empty file in path
     */
    public function testBug8559()
    {
        $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'emptyDir';
        $r   = $this->pci->parseDir($dir);
        $this->assertFalse($r);
    }

    /**
     * Regression test for bug #10100
     *
     * @return void
     * @link   http://pear.php.net/bugs/bug.php?id=10100
     *         Wrong parsing of possible attributes in strings
     */
    public function testBug10100()
    {
        $str = '<?php
$test = "public$link";
?>';
        $r   = $this->pci->parseString($str);
        $exp = array('max_version' => '',
                     'version' => '3.0.0',
                     'extensions' => array(),
                     'constants' => array());
        $this->assertSame($exp, $r);
    }
}

// Call PHP_CompatInfo_TestSuite_Bugs::main() if file is executed directly.
if (PHPUnit_MAIN_METHOD == "PHP_CompatInfo_TestSuite_Bugs::main") {
    PHP_CompatInfo_TestSuite_Bugs::main();
}
?>