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

        $suite = new PHPUnit_Framework_TestSuite('PHP_CompatInfo Bugs Tests');
        PHPUnit_TextUI_TestRunner::run($suite);
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
        $exp = array('ignored_functions' => array(),
                     'ignored_extensions' => array(),
                     'ignored_constants' => array(),
                     'max_version' => '',
                     'version' => '4.0.0',
                     'extensions' => array(),
                     'constants' => array(),
                     'tokens' => array(),
                     'cond_code' => array(0, array()));
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
        $exp = array('ignored_functions' => array(),
                     'ignored_extensions' => array(),
                     'ignored_constants' => array(),
                     'max_version' => '',
                     'version' => '4.3.0',
                     'extensions' => array('sapi_apache'),
                     'constants' => array(),
                     'tokens' => array(),
                     'cond_code' => array(0, array()));
        $this->assertSame($exp, $r);
    }

    /**
     * Regression test for bug #7813
     *
     * Parse source file of PEAR_PackageUpdate 0.5.0
     *
     * @return void
     * @link   http://pear.php.net/bugs/bug.php?id=7813
     *         wrong PHP minimum version detection
     */
    public function testBug7813()
    {
        $ds  = DIRECTORY_SEPARATOR;
        $fn  = dirname(__FILE__) . $ds . 'parseFile' . $ds . 'PackageUpdate.php';
        $opt = array('debug' => true,
                     'ignore_functions' => array('debug_backtrace'));
        $r   = $this->pci->parseFile($fn, $opt);
        $exp = array('ignored_functions' => array('debug_backtrace'),
                     'ignored_extensions' => array(),
                     'ignored_constants' => array(),
                     'max_version' => '',
                     'version' => '4.3.0',
                     'extensions' => array(),
                     'constants' => array('PATH_SEPARATOR', 'DIRECTORY_SEPARATOR'),
                     'tokens' => array(),
                     'cond_code' => array(1, array(array('debug_backtrace'),
                                                   array(),
                                                   array())),
                     '4.0.0' =>
                     array (
                       0 =>
                       array (
                         'function' => 'define',
                         'extension' => false,
                         'pecl' => false
                       ),
                       1 =>
                       array (
                         'function' => 'get_class',
                         'extension' => false,
                         'pecl' => false
                       ),
                       2 =>
                       array (
                         'function' => 'function_exists',
                         'extension' => false,
                         'pecl' => false
                       ),
                       3 =>
                       array (
                         'function' => 'count',
                         'extension' => false,
                         'pecl' => false
                       ),
                       4 =>
                       array (
                         'function' => 'class_exists',
                         'extension' => false,
                         'pecl' => false
                       ),
                       5 =>
                       array (
                         'function' => 'explode',
                         'extension' => false,
                         'pecl' => false
                       ),
                       6 =>
                       array (
                         'function' => 'file_exists',
                         'extension' => false,
                         'pecl' => false
                       ),
                       7 =>
                       array (
                         'function' => 'is_readable',
                         'extension' => false,
                         'pecl' => false
                       ),
                       8 =>
                       array (
                         'function' => 'unserialize',
                         'extension' => false,
                         'pecl' => false
                       ),
                       9 =>
                       array (
                         'function' => 'strlen',
                         'extension' => false,
                         'pecl' => false
                       ),
                       10 =>
                       array (
                         'function' => 'getenv',
                         'extension' => false,
                         'pecl' => false
                       ),
                       11 =>
                       array (
                         'function' => 'reset',
                         'extension' => false,
                         'pecl' => false
                       ),
                       12 =>
                       array (
                         'function' => 'array_keys',
                         'extension' => false,
                         'pecl' => false
                       ),
                       13 =>
                       array (
                         'function' => 'fopen',
                         'extension' => false,
                         'pecl' => false
                       ),
                       14 =>
                       array (
                         'function' => 'serialize',
                         'extension' => false,
                         'pecl' => false
                       ),
                       15 =>
                       array (
                         'function' => 'fwrite',
                         'extension' => false,
                         'pecl' => false
                       ),
                       16 =>
                       array (
                         'function' => 'fclose',
                         'extension' => false,
                         'pecl' => false
                       ),
                       17 =>
                       array (
                         'function' => 'settype',
                         'extension' => false,
                         'pecl' => false
                       ),
                       18 =>
                       array (
                         'function' => 'is_int',
                         'extension' => false,
                         'pecl' => false
                       ),
                       19 =>
                       array (
                         'function' => 'is_array',
                         'extension' => false,
                         'pecl' => false,
                       ),
                       20 =>
                       array (
                         'function' => 'array_shift',
                         'extension' => false,
                         'pecl' => false
                       )
                     ),
                     '4.0.7' =>
                     array (
                       0 =>
                       array (
                         'function' => 'version_compare',
                         'extension' => false,
                         'pecl' => false
                       )
                     ),
                     '4.3.0' =>
                     array (
                       0 =>
                       array (
                         'function' => 'get_include_path',
                         'extension' => false,
                         'pecl' => false,
                       ),
                       1 =>
                       array (
                         'function' => 'file_get_contents',
                         'extension' => false,
                         'pecl' => false
                       )
                     ));

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
        $exp = array('ignored_functions' => array(),
                     'ignored_extensions' => array(),
                     'ignored_constants' => array(),
                     'max_version' => '',
                     'version' => '4.0.0',
                     'extensions' => array(),
                     'constants' => array(),
                     'tokens' => array(),
                     'cond_code' => array(0, array()));
        $this->assertSame($exp, $r);
    }

    /**
     * Regression test for bug #13873
     *
     * @return void
     * @link   http://pear.php.net/bugs/bug.php?id=13873
     *         PHP_CompatInfo fails to scan conditional code
     *         if it finds other than encapsed string
     */
    public function testBug13873()
    {
        $ds  = DIRECTORY_SEPARATOR;
        $dir = dirname(__FILE__) . $ds . 'beehiveforum082' . $ds . 'forum';
        $r   = $this->pci->parseFolder($dir);
        $exp = array (
          'ignored_files' =>
          array (
          ),
          'ignored_functions' =>
          array (
          ),
          'ignored_extensions' =>
          array (
          ),
          'ignored_constants' =>
          array (
          ),
          'max_version' => '',
          'version' => '4.0.6',
          'extensions' =>
          array (
            0 => 'pcre',
            1 => 'date',
            2 => 'hash',
          ),
          'constants' =>
          array (
            0 => '__FILE__',
          ),
          'tokens' =>
          array (
          ),
          'cond_code' =>
          array (
            0 => 4,
            1 =>
            array (
              0 =>
              array (
              ),
              1 =>
              array (
              ),
              2 =>
              array (
              ),
            ),
          ),
        $dir . $ds . 'include' . $ds . 'forum.inc.php' =>
        array (
          'ignored_functions' =>
          array (
          ),
          'ignored_extensions' =>
          array (
          ),
          'ignored_constants' =>
          array (
          ),
          'max_version' => '',
          'version' => '4.0.6',
          'extensions' =>
          array (
            0 => 'pcre',
            1 => 'date',
            2 => 'hash',
          ),
          'constants' =>
          array (
            0 => '__FILE__',
          ),
          'tokens' =>
          array (
          ),
          'cond_code' =>
          array (
            0 => 4,
            1 =>
            array (
              0 =>
              array (
              ),
              1 =>
              array (
              ),
              2 =>
              array (
              ),
            ),
          ),
        ),

        );
        $this->assertSame($exp, $r);
    }
}

// Call PHP_CompatInfo_TestSuite_Bugs::main() if file is executed directly.
if (PHPUnit_MAIN_METHOD == "PHP_CompatInfo_TestSuite_Bugs::main") {
    PHP_CompatInfo_TestSuite_Bugs::main();
}
?>