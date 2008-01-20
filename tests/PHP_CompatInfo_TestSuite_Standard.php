<?php
/**
 * Test suite for the PHP_CompatInfo class
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
    define("PHPUnit_MAIN_METHOD", "PHP_CompatInfo_TestSuite_Standard::main");
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
class PHP_CompatInfo_TestSuite_Standard extends PHPUnit_Framework_TestCase
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
     * Tests tokenizer with a single file and empty contents
     *
     * @return void
     */
    public function testTokenizerWithEmptyFile()
    {
        $ds  = DIRECTORY_SEPARATOR;
        $fn  = dirname(__FILE__) . $ds . 'parseFile' . $ds . 'empty.php';

        $r   = $this->pci->_tokenize($fn, false);
        $empty = array(0 =>
                   array (
                   0 => 311,
                   1 => '
',
                   2 => 1));
        $this->assertSame($empty, $r);

        $r   = $this->pci->_tokenize($fn, false, true);
        $empty = array(0 =>
                   array (
                   0 => 311,
                   1 => '
',
                   2 => 1,
                   3 => 'T_INLINE_HTML'));
        $this->assertSame($empty, $r);
    }

    /**
     * Tests parsing a single file that does not exists
     *
     * @return void
     */
    public function testParseInvalidFile()
    {
        $ds  = DIRECTORY_SEPARATOR;
        $fn  = dirname(__FILE__) . $ds . 'parseFile' . $ds . 'nothere.php';
        $r   = $this->pci->parseFile($fn);
        $this->assertFalse($r);
    }

    /**
     * Tests parsing a single file with empty contents
     *
     * @return void
     */
    public function testParseEmptyFile()
    {
        $ds  = DIRECTORY_SEPARATOR;
        $fn  = dirname(__FILE__) . $ds . 'parseFile' . $ds . 'empty.php';
        $r   = $this->pci->parseFile($fn);

        $exp = array('max_version' => '',
                     'version' => '3.0.0',
                     'extensions' => array(),
                     'constants' => array());
        $this->assertSame($exp, $r);
    }

    /**
     * Tests parsing a single file
     *
     * @return void
     */
    public function testParseNotEmptyFile()
    {
        $ds  = DIRECTORY_SEPARATOR;
        $fn  = dirname(__FILE__) . $ds . 'parseFile' . $ds . 'math.php';
        $r   = $this->pci->parseFile($fn);
        $this->assertType('array', $r);

        $exp = array('max_version' => '',
                     'version' => '4.0.0',
                     'extensions' => array('bcmath', 'pcre'),
                     'constants' => array());
        $this->assertSame($exp, $r);
    }

    /**
     * Tests parsing an invalid input
     *
     * @return void
     */
    public function testParseInvalidString()
    {
        $in  = array();
        $r   = $this->pci->parseString($in);
        $this->assertFalse($r);
    }

    /**
     * Tests parsing a string
     *
     * @return void
     */
    public function testParseNotEmptyString()
    {
        $ds  = DIRECTORY_SEPARATOR;
        $fn  = dirname(__FILE__) . $ds . 'sample_req6056.php';
        $str = file_get_contents($fn);
        $r   = $this->pci->parseString($str);
        $this->assertType('array', $r);

        $exp = array('max_version' => '5.0.4',
                     'version' => '5.1.0',
                     'extensions' => array(),
                     'constants' => array());
        $this->assertSame($exp, $r);
    }
}

// Call PHP_CompatInfo_TestSuite_Standard::main() if file is executed directly.
if (PHPUnit_MAIN_METHOD == "PHP_CompatInfo_TestSuite_Standard::main") {
    PHP_CompatInfo_TestSuite_Standard::main();
}
?>