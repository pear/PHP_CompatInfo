<?php
/**
 * Test suite for the PHP_CompatInfo_Cli class
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
    define("PHPUnit_MAIN_METHOD", "PHP_CompatInfo_TestSuite_Cli::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'PHP/CompatInfo/Cli.php';

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
class PHP_CompatInfo_TestSuite_Cli extends PHPUnit_Framework_TestCase
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

        $suite  = new PHPUnit_Framework_TestSuite('PHP_CompatInfo CLI Tests');
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
        $this->pci = new PHP_CompatInfo_Cli();
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
     * Assert results of php exec returns
     *
     * @param string $args Arguments list that will be pass to the 'pci' command
     * @param array  $exp  PCI output results expected
     *
     * @return void
     */
    private function assertPhpExec($args, $exp)
    {
        $ps      = PATH_SEPARATOR;
        $command = '@php_bin@ '
                 . '-d include_path=.' . $ps . '@php_dir@ '
                 . '-f @bin_dir@/pci -- ';

        $output = array();
        $return = 0;
        exec("$command $args", $output, $return);
        if ($return == 0) {
            $this->assertEquals($exp, $output);
        } else {
            $this->assertTrue($return);
        }
    }

    /**
     * Regression test for bug #3657
     *
     * @return void
     * @link   http://pear.php.net/bugs/bug.php?id=3657
     *         php5 clone constant/token in all sources
     */
    public function testBug3657()
    {
        $ds  = DIRECTORY_SEPARATOR;
        $exp = array('+-----------------------------+---------+-------------+-----------------------+',
                     '| File                        | Version | Extensions  | Constants/Tokens      |',
                     '+-----------------------------+---------+-------------+-----------------------+',
                     '| ...File'.$ds.'phpweb-entities.php | 4.0.0   |             | __FILE__              |',
                     '+-----------------------------+---------+-------------+-----------------------+');

        $fn = dirname(__FILE__) . $ds . 'parseFile' . $ds . 'phpweb-entities.php';

        $args   = '-f ' . $fn;
        $this->assertPhpExec($args, $exp);
    }

    /**
     * Regression test for bug #6581
     *
     * @return void
     * @link   http://pear.php.net/bugs/bug.php?id=6581
     *         Functions missing in func_array.php
     */
    public function testBug6581()
    {
        $ds  = DIRECTORY_SEPARATOR;
        $exp = array('+-----------------------------+---------+-------------+-----------------------+',
                     '| File                        | Version | Extensions  | Constants/Tokens      |',
                     '+-----------------------------+---------+-------------+-----------------------+',
                     '| ...tests'.$ds.'parseFile'.$ds.'math.php | 4.0.0   | bcmath      |                       |',
                     '|                             |         | pcre        |                       |',
                     '+-----------------------------+---------+-------------+-----------------------+');

        $fn = dirname(__FILE__) . $ds . 'parseFile' . $ds . 'math.php';

        $args   = '-f ' . $fn;
        $this->assertPhpExec($args, $exp);
    }

    /**
     * Tests parsing an empty directory
     *
     * Regression test for bug #8559
     *
     * @return void
     * @link   http://pear.php.net/bugs/bug.php?id=8559
     *         PHP_CompatInfo fails to scan if it finds empty file in path
     * @see    PHP_CompatInfo_TestSuite_Bugs::testBug8559()
     */
    public function testBug8559()
    {
        $ds = DIRECTORY_SEPARATOR;
        $dn = dirname(__FILE__) . $ds . 'emptyDir';

        $exp = array('Usage: pci [options]',
                     '',
                     '  -d   --dir (optional)value                      Parse DIR to get its',
                     '                                                  compatibility info ()',
                     '  -f   --file (optional)value                     Parse FILE to get its',
                     '                                                  compatibility info ()',
                     '  -s   --string (optional)value                   Parse STRING to get its',
                     '                                                  compatibility info ()',
                     '  -v   --verbose (optional)value                  Set the verbose level (1)',
                     '  -n   --no-recurse                               Do not recursively parse files',
                     '                                                  when using --dir',
                     '  -if  --ignore-files (optional)value             Data file name which contains',
                     '                                                  a list of file to ignore',
                     '                                                  (files.txt)',
                     '  -id  --ignore-dirs (optional)value              Data file name which contains',
                     '                                                  a list of directory to ignore',
                     '                                                  (dirs.txt)',
                     '  -in  --ignore-functions (optional)value         Data file name which contains',
                     '                                                  a list of php function to',
                     '                                                  ignore (functions.txt)',
                     '  -ic  --ignore-constants (optional)value         Data file name which contains',
                     '                                                  a list of php constant to',
                     '                                                  ignore (constants.txt)',
                     '  -ie  --ignore-extensions (optional)value        Data file name which contains',
                     '                                                  a list of php extension to',
                     '                                                  ignore (extensions.txt)',
                     '  -iv  --ignore-versions values(optional)         PHP versions - functions to',
                     '                                                  exclude when parsing source',
                     '                                                  code (5.0.0)',
                     '  -inm --ignore-functions-match (optional)value   Data file name which contains',
                     '                                                  a list of php function pattern',
                     '                                                  to ignore',
                     '                                                  (functions-match.txt)',
                     '  -iem --ignore-extensions-match (optional)value  Data file name which contains',
                     '                                                  a list of php extension',
                     '                                                  pattern to ignore',
                     '                                                  (extensions-match.txt)',
                     '  -icm --ignore-constants-match (optional)value   Data file name which contains',
                     '                                                  a list of php constant pattern',
                     '                                                  to ignore',
                     '                                                  (constants-match.txt)',
                     '  -fe  --file-ext (optional)value                 A comma separated list of file',
                     '                                                  extensions to parse (only',
                     '                                                  valid if parsing a directory)',
                     '                                                  (php, php4, inc, phtml)',
                     '  -r   --report (optional)value                   Print either "xml" or "cli"',
                     '                                                  report (cli)',
                     '  -o   --output-level (optional)value             Print Path/File + Version with',
                     '                                                  additional data (6)',
                     '  -V   --version                                  Print version information',
                     '  -h   --help                                     Show this help',
                     '',
                     'No valid files into directory "'. str_replace($ds, '/', $dn) .
                     '". Please check your spelling and try again.',
                     '');

        $args   = '-d ' . $dn;
        $this->assertPhpExec($args, $exp);
    }

    /**
     * Regression test for bug #12350
     *
     * Be sure ( chdir() ) to check
     * if file (checkMax.php) is in current directory ( dirname(__FILE__) )
     *
     * @return void
     * @link   http://pear.php.net/bugs/bug.php?id=12350
     *         file in current directory is not found
     */
    public function testBug12350()
    {
        $exp = array('+-----------------------------+---------+-------------+-----------------------+',
                     '| File                        | Version | Extensions  | Constants/Tokens      |',
                     '+-----------------------------+---------+-------------+-----------------------+',
                     '| checkMax.php                | 4.0.7   |             | __FILE__              |',
                     '|                             |         |             | DIRECTORY_SEPARATOR   |',
                     '+-----------------------------+---------+-------------+-----------------------+');

        chdir(dirname(__FILE__));
        $args   = '-f checkMax.php';
        $this->assertPhpExec($args, $exp);
    }

    /**
     * Parsing string with -s | --string parameter
     *
     * @return void
     */
    public function testParseString()
    {
        $exp = array('+---------+------------+------------------+',
                     '| Version | Extensions | Constants/Tokens |',
                     '+---------+------------+------------------+',
                     '| 5.1.1   |            | DATE_RSS         |',
                     '+---------+------------+------------------+');

        $str = "\"echo DATE_RSS;\"";

        $args = '-s ' . $str;
        $this->assertPhpExec($args, $exp);
    }

    /**
     * Regression test for request#13147
     *
     * @return void
     * @link   http://pear.php.net/bugs/bug.php?id=13147
     *         CLI: add filter file extension option on parsing directory
     */
    public function testRequest13147()
    {
        $ds  = DIRECTORY_SEPARATOR;
        $exp = array('+-----------------------------+---------+-------------+-----------------------+',
                     '| Path                        | Version | Extensions  | Constants/Tokens      |',
                     '+-----------------------------+---------+-------------+-----------------------+',
                     '| ...patInfo'.$ds.'tests'.$ds.'parseDir'.$ds.'* | 5.2.0   | xdebug      | UPLOAD_ERR_INI_SIZE   |',
                     '|                             |         | gd          | UPLOAD_ERR_FORM_SIZE  |',
                     '|                             |         | sapi_apache | UPLOAD_ERR_PARTIAL    |',
                     '|                             |         | sapi_cgi    | UPLOAD_ERR_NO_FILE    |',
                     '|                             |         | sqlite      | UPLOAD_ERR_NO_TMP_DIR |',
                     '|                             |         |             | UPLOAD_ERR_CANT_WRITE |',
                     '|                             |         |             | UPLOAD_ERR_EXTENSION  |',
                     '|                             |         |             | UPLOAD_ERR_OK         |',
                     '|                             |         |             | abstract              |',
                     '|                             |         |             | protected             |',
                     '|                             |         |             | interface             |',
                     '|                             |         |             | public                |',
                     '|                             |         |             | implements            |',
                     '|                             |         |             | private               |',
                     '|                             |         |             | clone                 |',
                     '|                             |         |             | instanceof            |',
                     '|                             |         |             | try                   |',
                     '|                             |         |             | throw                 |',
                     '|                             |         |             | catch                 |',
                     '|                             |         |             | final                 |',
                     '+-----------------------------+---------+-------------+-----------------------+',
                     '| ...'.$ds.'parseDir'.$ds.'extensions.php | 4.3.2   | xdebug      |                       |',
                     '|                             |         | gd          |                       |',
                     '|                             |         | sapi_apache |                       |',
                     '|                             |         | sapi_cgi    |                       |',
                     '|                             |         | sqlite      |                       |',
                     '+-----------------------------+---------+-------------+-----------------------+',
                     '| ...arseDir'.$ds.'PHP5'.$ds.'tokens.php5 | 5.0.0   |             | abstract              |',
                     '|                             |         |             | protected             |',
                     '|                             |         |             | interface             |',
                     '|                             |         |             | public                |',
                     '|                             |         |             | implements            |',
                     '|                             |         |             | private               |',
                     '|                             |         |             | clone                 |',
                     '|                             |         |             | instanceof            |',
                     '|                             |         |             | try                   |',
                     '|                             |         |             | throw                 |',
                     '|                             |         |             | catch                 |',
                     '|                             |         |             | final                 |',
                     '+-----------------------------+---------+-------------+-----------------------+',
                     '| ...ir'.$ds.'PHP5'.$ds.'upload_error.php | 5.2.0   |             | UPLOAD_ERR_INI_SIZE   |',
                     '|                             |         |             | UPLOAD_ERR_FORM_SIZE  |',
                     '|                             |         |             | UPLOAD_ERR_PARTIAL    |',
                     '|                             |         |             | UPLOAD_ERR_NO_FILE    |',
                     '|                             |         |             | UPLOAD_ERR_NO_TMP_DIR |',
                     '|                             |         |             | UPLOAD_ERR_CANT_WRITE |',
                     '|                             |         |             | UPLOAD_ERR_EXTENSION  |',
                     '|                             |         |             | UPLOAD_ERR_OK         |',
                     '|                             |         |             | throw                 |',
                     '+-----------------------------+---------+-------------+-----------------------+',
                     '| ...sts'.$ds.'parseDir'.$ds.'phpinfo.php | 4.0.0   |             |                       |',
                     '+-----------------------------+---------+-------------+-----------------------+');

        $dn = dirname(__FILE__) . $ds . 'parseDir';
        $fe = 'php,php5';

        $args   = '-fe '. $fe . ' -d '. $dn;
        $this->assertPhpExec($args, $exp);
    }
}

// Call PHP_CompatInfo_TestSuite_Cli::main() if file is executed directly.
if (PHPUnit_MAIN_METHOD == "PHP_CompatInfo_TestSuite_Cli::main") {
    PHP_CompatInfo_TestSuite_Cli::main();
}
?>