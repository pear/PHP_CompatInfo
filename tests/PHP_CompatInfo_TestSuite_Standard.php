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
        $ds = DIRECTORY_SEPARATOR;
        $fn = dirname(__FILE__) . $ds . 'parseFile' . $ds . 'empty.php';

        $r     = $this->pci->_tokenize($fn, false);
        $empty = array(0 =>
                   array (
                   0 => 311,
                   1 => '
',
                   2 => 1));
        $this->assertSame($empty, $r);

        $r     = $this->pci->_tokenize($fn, false, true);
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
        $ds = DIRECTORY_SEPARATOR;
        $fn = dirname(__FILE__) . $ds . 'parseFile' . $ds . 'nothere.php';

        $r = $this->pci->parseFile($fn);
        $this->assertFalse($r);
    }

    /**
     * Tests parsing a single file with empty contents
     *
     * @return void
     */
    public function testParseEmptyFile()
    {
        $ds = DIRECTORY_SEPARATOR;
        $fn = dirname(__FILE__) . $ds . 'parseFile' . $ds . 'empty.php';

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
        $ds = DIRECTORY_SEPARATOR;
        $fn = dirname(__FILE__) . $ds . 'parseFile' . $ds . 'math.php';

        $r = $this->pci->parseFile($fn);
        $this->assertType('array', $r);

        $exp = array('max_version' => '',
                     'version' => '4.0.0',
                     'extensions' => array('bcmath', 'pcre'),
                     'constants' => array());
        $this->assertSame($exp, $r);
    }

    /**
     * Tests parsing a single file with 'ignore_functions' option
     *
     * @return void
     */
    public function testParseFileWithIgnoreFunctions()
    {
        $ds  = DIRECTORY_SEPARATOR;
        $fn  = dirname(__FILE__) . $ds . 'parseFile' . $ds . 'conditional.php';
        $opt = array('ignore_functions' =>
                   array('simplexml_load_file'));

        $r = $this->pci->parseFile($fn, $opt);
        $this->assertType('array', $r);

        $exp = array('max_version' => '',
                     'version' => '4.3.10',
                     'extensions' => array(),
                     'constants' => array('PHP_EOL'));
        $this->assertSame($exp, $r);
    }

    /**
     * Tests parsing a single file with 'ignore_constants' option
     *
     * @return void
     */
    public function testParseFileWithIgnoreConstants()
    {
        $ds  = DIRECTORY_SEPARATOR;
        $fn  = dirname(__FILE__) . $ds . 'parseFile' . $ds . 'conditional.php';
        $opt = array('ignore_constants' =>
                   array('PHP_EOL'));

        $r = $this->pci->parseFile($fn, $opt);
        $this->assertType('array', $r);

        $exp = array('max_version' => '',
                     'version' => '5.0.0',
                     'extensions' => array('simplexml'),
                     'constants' => array());
        $this->assertSame($exp, $r);
    }

    /**
     * Tests parsing a single file with 'ignore_extensions' option
     *
     * @return void
     * @link   http://www.php.net/zip
     */
    public function testParseFileWithIgnoreExtensions()
    {
        $ds  = DIRECTORY_SEPARATOR;
        $fn  = dirname(__FILE__) . $ds . 'parseFile' . $ds . 'zip.php';
        $opt = array('ignore_extensions' =>
                   array('zip'));

        $r = $this->pci->parseFile($fn, $opt);
        $this->assertType('array', $r);

        $exp = array('max_version' => '',
                     'version' => '3.0.0',
                     'extensions' => array(),
                     'constants' => array());
        $this->assertSame($exp, $r);
    }

    /**
     * Tests parsing a single file with 'ignore_versions' option
     * Ignored all PHP functions between 4.3.10 and 4.4.8
     *
     * @return void
     */
    public function testParseFileWithIgnoreVersions()
    {
        $ds  = DIRECTORY_SEPARATOR;
        $fn  = dirname(__FILE__) . $ds . 'parseFile' . $ds . 'conditional.php';
        $opt = array('ignore_versions' =>
                   array('4.3.10', '4.4.8'));

        $r = $this->pci->parseFile($fn, $opt);
        $this->assertType('array', $r);

        $exp = array('max_version' => '',
                     'version' => '5.0.0',
                     'extensions' => array('simplexml'),
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
        $in = array();
        $r  = $this->pci->parseString($in);
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

        $r = $this->pci->parseString($str);
        $this->assertType('array', $r);

        $exp = array('max_version' => '5.0.4',
                     'version' => '5.1.0',
                     'extensions' => array(),
                     'constants' => array());
        $this->assertSame($exp, $r);
    }

    /**
     * Tests parsing string DATE constants
     *
     * @return void
     * @link   http://php.net/manual/en/ref.datetime.php Predefined Date Constants
     */
    public function testParseDate511String()
    {
        $str = '<?php
$nl = "\n";
echo "$nl Atom    = " . DATE_ATOM;
echo "$nl Cookie  = " . DATE_COOKIE;
echo "$nl Iso8601 = " . DATE_ISO8601;
echo "$nl Rfc822  = " . DATE_RFC822;
echo "$nl Rfc850  = " . DATE_RFC850;
echo "$nl Rfc1036 = " . DATE_RFC1036;
echo "$nl Rfc1123 = " . DATE_RFC1123;
echo "$nl Rfc2822 = " . DATE_RFC2822;
echo "$nl RSS     = " . DATE_RSS;
echo "$nl W3C     = " . DATE_W3C;
?>';

        $r = $this->pci->parseString($str);
        $this->assertType('array', $r);

        $exp = array('max_version' => '',
                     'version' => '5.1.1',
                     'extensions' => array(),
                     'constants' => array('DATE_ATOM', 'DATE_COOKIE',
                         'DATE_ISO8601', 'DATE_RFC822', 'DATE_RFC850',
                         'DATE_RFC1036', 'DATE_RFC1123', 'DATE_RFC2822',
                         'DATE_RSS', 'DATE_W3C'
                         ));
        $this->assertSame($exp, $r);
    }

    /**
     * Tests parsing string DATE constants
     *
     * @return void
     * @link   http://php.net/manual/en/ref.datetime.php Predefined Date Constants
     */
    public function testParseDate513String()
    {
        $str = '<?php
$nl = "\n";
echo "$nl Rfc3339 = " . DATE_RFC3339;
echo "$nl RSS     = " . DATE_RSS;
?>';

        $r = $this->pci->parseString($str);
        $this->assertType('array', $r);

        $exp = array('max_version' => '',
                     'version' => '5.1.3',
                     'extensions' => array(),
                     'constants' => array('DATE_RFC3339', 'DATE_RSS'));
        $this->assertSame($exp, $r);
    }
}

// Call PHP_CompatInfo_TestSuite_Standard::main() if file is executed directly.
if (PHPUnit_MAIN_METHOD == "PHP_CompatInfo_TestSuite_Standard::main") {
    PHP_CompatInfo_TestSuite_Standard::main();
}
?>