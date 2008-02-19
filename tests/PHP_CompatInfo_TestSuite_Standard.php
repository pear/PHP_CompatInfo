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

require_once 'PEAR.php';
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
                   1 => "\n",
                   2 => 1));
        $this->assertSame($empty, $r);

        $r     = $this->pci->_tokenize($fn, false, true);
        $empty = array(0 =>
                   array (
                   0 => 311,
                   1 => "\n",
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
                     'constants' => array('PHP_EOL',
                                          'DIRECTORY_SEPARATOR',
                                          '__FILE__'));
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
                     'constants' => array('DIRECTORY_SEPARATOR',
                                          '__FILE__'));
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
                     'constants' => array('DIRECTORY_SEPARATOR',
                                          '__FILE__'));
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

    /**
     * Tests parsing string UPLOAD_ERR constants
     *
     * @return void
     * @link   http://www.php.net/features.file-upload.errors
     *         File Upload Error specific Constants
     */
    public function testParseUploadErrString()
    {
        $str = '<?php
$uploadErrors = array(
    UPLOAD_ERR_INI_SIZE   => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
    UPLOAD_ERR_FORM_SIZE  => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
    UPLOAD_ERR_PARTIAL    => "The uploaded file was only partially uploaded.",
    UPLOAD_ERR_NO_FILE    => "No file was uploaded.",
    UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
    UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
    UPLOAD_ERR_EXTENSION  => "File upload stopped by extension.",
);

$errorCode = $_FILES["myUpload"]["error"];

if ($errorCode !== UPLOAD_ERR_OK) {
    if (isset($uploadErrors[$errorCode])) {
        throw new Exception($uploadErrors[$errorCode]);
    } else {
        throw new Exception("Unknown error uploading file.");
    }
}
?>';
        $r   = $this->pci->parseString($str);
        $exp = array('max_version' => '',
                     'version' => '5.2.0',
                     'extensions' => array(),
                     'constants' => array('UPLOAD_ERR_INI_SIZE',
                                          'UPLOAD_ERR_FORM_SIZE',
                                          'UPLOAD_ERR_PARTIAL',
                                          'UPLOAD_ERR_NO_FILE',
                                          'UPLOAD_ERR_NO_TMP_DIR',
                                          'UPLOAD_ERR_CANT_WRITE',
                                          'UPLOAD_ERR_EXTENSION',
                                          'UPLOAD_ERR_OK',
                                          'throw'));
        $this->assertSame($exp, $r);
    }

    /**
     * Tests parsing a directory that does not exists
     *
     * @return void
     */
    public function testParseInvalidDirectory()
    {
        $ds  = DIRECTORY_SEPARATOR;
        $dir = dirname(__FILE__) . $ds . 'parseDir' . $ds . 'nothere';

        $r = $this->pci->parseFolder($dir);
        $this->assertFalse($r);
    }

    /**
     * Tests parsing a directory without recursive 'recurse_dir' option
     *
     * @return void
     */
    public function testParseNoRecursiveDirectory()
    {
        $ds  = DIRECTORY_SEPARATOR;
        $dir = dirname(__FILE__) . $ds . 'parseDir';
        $opt = array('recurse_dir' => false);

        $r   = $this->pci->parseDir($dir, $opt);
        $exp = array('ignored_files' => array(),
                     'max_version' => '',
                     'version' => '4.3.2',
                     'extensions' => array('xdebug', 'gd',
                                           'sapi_apache', 'sapi_cgi'),
                     'constants' => array(),
                     $dir . $ds . 'extensions.php' =>
                         array('max_version' => '',
                               'version' => '4.3.2',
                               'extensions' => array('xdebug', 'gd',
                                                     'sapi_apache', 'sapi_cgi'),
                               'constants' => array()),
                     $dir . $ds . 'phpinfo.php' =>
                         array('max_version' => '',
                               'version' => '4.0.0',
                               'extensions' => array(),
                               'constants' => array()));
        $this->assertSame($exp, $r);
    }

    /**
     * Tests parsing a directory with 'recurse_dir' option active
     * and filter files by extension with 'file_ext' option
     *
     * @return void
     */
    public function testParseRecursiveDirectory()
    {
        $ds  = DIRECTORY_SEPARATOR;
        $dir = dirname(__FILE__) . $ds . 'parseDir' . $ds;
        $opt = array('recurse_dir' => true,
                     'file_ext' => array('php', 'php5'));

        $r   = $this->pci->parseDir($dir, $opt);
        $exp = array('ignored_files' => array(),
                     'max_version' => '',
                     'version' => '5.0.0',
                     'extensions' => array('xdebug', 'gd',
                                           'sapi_apache', 'sapi_cgi'),
                     'constants' => array(0 => 'abstract',
                                          1 => 'protected',
                                          2 => 'interface',
                                          3 => 'public',
                                          4 => 'implements',
                                          5 => 'private',
                                          6 => 'clone',
                                          7 => 'instanceof',
                                          8 => 'try',
                                          9 => 'throw',
                                          10 => 'catch',
                                          11 => 'final'),
                     $dir . 'extensions.php' =>
                         array('max_version' => '',
                               'version' => '4.3.2',
                               'extensions' => array('xdebug', 'gd',
                                                     'sapi_apache', 'sapi_cgi'),
                               'constants' => array()),
                     $dir . 'PHP5' . $ds . 'tokens.php5' =>
                         array('max_version' => '',
                               'version' => '5.0.0',
                               'extensions' => array(),
                               'constants' => array(0 => 'abstract',
                                                    1 => 'protected',
                                                    2 => 'interface',
                                                    3 => 'public',
                                                    4 => 'implements',
                                                    5 => 'private',
                                                    6 => 'clone',
                                                    7 => 'instanceof',
                                                    8 => 'try',
                                                    9 => 'throw',
                                                    10 => 'catch',
                                                    11 => 'final')),
                     $dir . 'phpinfo.php' =>
                         array('max_version' => '',
                               'version' => '4.0.0',
                               'extensions' => array(),
                               'constants' => array()));
        $this->assertSame($exp, $r);
    }

    /**
     * Tests parsing a directory with 'recurse_dir' option active
     * with 'ignore_files' options
     *
     * @return void
     */
    public function testParseRecursiveDirectoryWithIgnoreFiles()
    {
        $ds  = DIRECTORY_SEPARATOR;
        $dir = dirname(__FILE__) . $ds . 'parseDir' . $ds;
        $opt = array('recurse_dir' => true,
                     'ignore_files' => array($dir . 'phpinfo.php'),
                     'file_ext' => array('php'));

        $r   = $this->pci->parseDir($dir, $opt);
        $exp = array('ignored_files' => array($dir . 'phpinfo.php'),
                     'max_version' => '',
                     'version' => '4.3.2',
                     'extensions' => array('xdebug', 'gd',
                                           'sapi_apache', 'sapi_cgi'),
                     'constants' => array(),
                     $dir . 'extensions.php' =>
                         array('max_version' => '',
                               'version' => '4.3.2',
                               'extensions' => array('xdebug', 'gd',
                                                     'sapi_apache', 'sapi_cgi'),
                               'constants' => array()));
        $this->assertSame($exp, $r);
    }

    /**
     * Tests parsing multiple file data sources reference
     *
     * @return void
     */
    public function testParseArrayFile()
    {
        $ds    = DIRECTORY_SEPARATOR;
        $files = get_included_files();
        $rsrc  = array();
        $base  = array();
        foreach ($files as $file) {
            if (basename($file) == 'PEAR.php') {
                $rsrc[] = $file;
                $base[] = dirname($file);
                continue;
            }
            if (basename($file) == 'CompatInfo.php') {
                $rsrc[] = $file;
                $base[] = dirname($file);
                continue;
            }
        }

        $r   = $this->pci->parseArray($rsrc);
        $exp = array('ignored_files' => array(),
                     'max_version' => '',
                     'version' => '4.3.0',
                     'extensions' => array('sapi_cgi', 'tokenizer'),
                     'constants' => array('DIRECTORY_SEPARATOR'),
                     $base[1] . $ds . 'CompatInfo.php' =>
                         array('max_version' => '',
                               'version' => '4.3.0',
                               'extensions' => array('tokenizer'),
                               'constants' => array('DIRECTORY_SEPARATOR')),
                     $base[0] . $ds . 'PEAR.php' =>
                         array('max_version' => '',
                               'version' => '4.3.0',
                               'extensions' => array('sapi_cgi'),
                               'constants' => array()));
        $this->assertSame($exp, $r);
    }

    /**
     * Tests parsing multiple file data sources reference
     * with option 'ignore_files'
     *
     * @return void
     */
    public function testParseArrayFileWithIgnoreFiles()
    {
        $ds    = DIRECTORY_SEPARATOR;
        $files = get_included_files();
        $incl  = array();
        $excl  = array();

        foreach ($files as $file) {
            if (basename($file) == 'PEAR.php') {
                $incl[] = $file;
                $base   = dirname($file);
            } else {
                $excl[] = $file;
            }
        }
        $opt = array('ignore_files' => $excl);

        $r   = $this->pci->parseArray($files, $opt);
        $exp = array('ignored_files' => $excl,
                     'max_version' => '',
                     'version' => '4.3.0',
                     'extensions' => array('sapi_cgi'),
                     'constants' => array(),
                     $base . $ds . 'PEAR.php' =>
                         array('max_version' => '',
                               'version' => '4.3.0',
                               'extensions' => array('sapi_cgi'),
                               'constants' => array()));
        $this->assertSame($exp, $r);
    }

    /**
     * Tests parsing multiple strings (chunk of code)
     *
     * @return void
     */
    public function testParseArrayString()
    {
        $code1 = "<?php
php_check_syntax('somefile.php');
?>";
        $code2 = "<?php
\$array1 = array('blue'  => 1, 'red'  => 2, 'green'  => 3, 'purple' => 4);
\$array2 = array('green' => 5, 'blue' => 6, 'yellow' => 7, 'cyan'   => 8);

\$diff = array_diff_key(\$array1, \$array2);
?>";
        $data  = array($code1, $code2);
        $opt   = array('is_string' => true);

        $r   = $this->pci->parseArray($data, $opt);
        $exp = array('ignored_files' => array(),
                     'max_version' => '5.0.4',
                     'version' => '5.1.0',
                     'extensions' => array(),
                     'constants' => array(),
                     0 => array(
                          'max_version' => '',
                          'version' => '5.1.0',
                          'extensions' => array(),
                          'constants' => array()),
                     1 => array(
                          'max_version' => '5.0.4',
                          'version' => '5.0.0',
                          'extensions' => array(),
                          'constants' => array())
                     );
        $this->assertSame($exp, $r);
    }

    /**
     * Tests parsing nothing (all files are excluded from scope)
     *
     * @return void
     */
    public function testParseArrayWithNoFiles()
    {
        $files = get_included_files();
        $opt   = array('ignore_files' => $files);

        $r = $this->pci->parseArray($files, $opt);
        $this->assertFalse($r);
    }

    /**
     * Tests loading functions list for a PHP version
     *
     * @return void
     */
    public function testLoadVersion()
    {
        $r   = $this->pci->loadVersion('5.2.0');
        $exp = array('appenditerator::getarrayiterator',
                     'appenditerator::getiteratorindex',
                     'array_fill_keys',
                     'arrayobject::asort',
                     'arrayobject::ksort',
                     'arrayobject::natcasesort',
                     'arrayobject::natsort',
                     'arrayobject::uasort',
                     'arrayobject::uksort',
                     'cachingiterator::count',
                     'cachingiterator::getcache',
                     'cachingiterator::getflags',
                     'cachingiterator::offsetexists',
                     'cachingiterator::offsetget',
                     'cachingiterator::offsetset',
                     'cachingiterator::offsetunset',
                     'cachingiterator::setflags',
                     'directoryiterator::getbasename',
                     'domdocument::registernodeclass',
                     'domnode::c14n',
                     'domnode::c14nfile',
                     'domnode::getnodepath',
                     'error_get_last',
                     'gmp_nextprime',
                     'imagegrabscreen',
                     'imagegrabwindow',
                     'iterator_apply',
                     'mb_list_encodings_alias_names',
                     'mb_list_mime_names',
                     'mb_stripos',
                     'mb_stristr',
                     'mb_strrchr',
                     'mb_strrichr',
                     'mb_strripos',
                     'mb_strstr',
                     'memory_get_peak_usage',
                     'ming_setswfcompression',
                     'mysql_set_charset',
                     'openssl_csr_get_public_key',
                     'openssl_csr_get_subject',
                     'openssl_pkcs12_export',
                     'openssl_pkcs12_export_to_file',
                     'openssl_pkcs12_read',
                     'openssl_pkey_get_details',
                     'pg_field_table',
                     'php_ini_loaded_file',
                     'posix_initgroups',
                     'preg_last_error',
                     'recursiveregexiterator::__construct',
                     'recursiveregexiterator::getchildren',
                     'reflectionclass::getinterfacenames',
                     'reflectionextension::info',
                     'reflectionfunction::isdisabled',
                     'regexiterator::__construct',
                     'regexiterator::accept',
                     'regexiterator::getflags',
                     'regexiterator::getmode',
                     'regexiterator::setflags',
                     'regexiterator::setmode',
                     'regexiterator::setpregflags',
                     'simplexmlelement::registerxpathnamespace',
                     'simplexmlelement::xpath',
                     'snmp_set_oid_output_format',
                     'soapserver::setobject',
                     'spl_object_hash',
                     'splfileinfo::getbasename',
                     'splfileinfo::getlinktarget',
                     'splfileinfo::getrealpath',
                     'splfileobject::getcsvcontrol',
                     'splfileobject::setcsvcontrol',
                     'splobjectstorage::serialize',
                     'splobjectstorage::unserialize',
                     'spltempfileobject::__construct',
                     'stream_is_local',
                     'stream_socket_shutdown',
                     'swfmovie::namedanchor',
                     'swfmovie::protect',
                     'swfmovie::remove',
                     'sys_get_temp_dir',
                     'tidynode::getparent',
                     'timezone_transitions_get',
                     'xmlreader::readinnerxml',
                     'xmlreader::readouterxml',
                     'xmlreader::readstring',
                     'xmlreader::setrelaxngschema',
                     'xmlreader::setschema');
        $this->assertSame($exp, $r);
    }

    /**
     * Tests loading functions list for a PHP version range
     *
     * @return void
     */
    public function testLoadVersionRange()
    {
        $r   = $this->pci->loadVersion('5.2.0', '5.2.2');
        $exp = array('appenditerator::getarrayiterator',
                     'appenditerator::getiteratorindex',
                     'array_fill_keys',
                     'arrayobject::asort',
                     'arrayobject::ksort',
                     'arrayobject::natcasesort',
                     'arrayobject::natsort',
                     'arrayobject::uasort',
                     'arrayobject::uksort',
                     'cachingiterator::count',
                     'cachingiterator::getcache',
                     'cachingiterator::getflags',
                     'cachingiterator::offsetexists',
                     'cachingiterator::offsetget',
                     'cachingiterator::offsetset',
                     'cachingiterator::offsetunset',
                     'cachingiterator::setflags',
                     'directoryiterator::getbasename',
                     'domdocument::registernodeclass',
                     'domnode::c14n',
                     'domnode::c14nfile',
                     'domnode::getnodepath',
                     'error_get_last',
                     'gmp_nextprime',
                     'imagegrabscreen',
                     'imagegrabwindow',
                     'iterator_apply',
                     'mb_list_encodings_alias_names',
                     'mb_list_mime_names',
                     'mb_stripos',
                     'mb_stristr',
                     'mb_strrchr',
                     'mb_strrichr',
                     'mb_strripos',
                     'mb_strstr',
                     'memory_get_peak_usage',
                     'ming_setswfcompression',
                     'openssl_csr_get_public_key',
                     'openssl_csr_get_subject',
                     'openssl_pkcs12_export',
                     'openssl_pkcs12_export_to_file',
                     'openssl_pkcs12_read',
                     'openssl_pkey_get_details',
                     'pg_field_table',
                     'posix_initgroups',
                     'preg_last_error',
                     'recursiveregexiterator::__construct',
                     'recursiveregexiterator::getchildren',
                     'reflectionclass::getinterfacenames',
                     'reflectionfunction::isdisabled',
                     'regexiterator::__construct',
                     'regexiterator::accept',
                     'regexiterator::getflags',
                     'regexiterator::getmode',
                     'regexiterator::setflags',
                     'regexiterator::setmode',
                     'regexiterator::setpregflags',
                     'simplexmlelement::registerxpathnamespace',
                     'simplexmlelement::xpath',
                     'snmp_set_oid_output_format',
                     'soapserver::setobject',
                     'spl_object_hash',
                     'splfileinfo::getbasename',
                     'splfileinfo::getlinktarget',
                     'splfileinfo::getrealpath',
                     'splfileobject::getcsvcontrol',
                     'splfileobject::setcsvcontrol',
                     'splobjectstorage::serialize',
                     'splobjectstorage::unserialize',
                     'spltempfileobject::__construct',
                     'stream_socket_shutdown',
                     'swfmovie::namedanchor',
                     'swfmovie::protect',
                     'swfmovie::remove',
                     'sys_get_temp_dir',
                     'tidynode::getparent',
                     'timezone_transitions_get',
                     'xmlreader::readinnerxml',
                     'xmlreader::readouterxml',
                     'xmlreader::readstring',
                     'xmlreader::setrelaxngschema',
                     'xmlreader::setschema');
        $this->assertSame($exp, $r);
    }

    /**
     * Tests loading function+constant list for a single PHP version
     *
     * What's new with version 4.3.10 : 0 functions and 2 new constants
     *
     * @return void
     */
    public function testLoadVersionRangeWithConstant()
    {
        $r   = $this->pci->loadVersion('4.3.10', '4.3.10', true);
        $exp = array('functions' => array(),
                     'constants' => array('PHP_EOL',
                                          'UPLOAD_ERR_NO_TMP_DIR'));
        $this->assertSame($exp, $r);
    }

    /**
     * Tests loading function+constant list for a PHP version range
     *
     * What's new with since version 5.2.1 : 24 new functions and 0 constant
     *
     * @return void
     */
    public function testLoadVersionWithConstant()
    {
        $r   = $this->pci->loadVersion('5.2.1', false, true);
        $exp = array('functions' => array('cachingiterator::count',
                                          'directoryiterator::getbasename',
                                          'imagegrabscreen',
                                          'imagegrabwindow',
                                          'ming_setswfcompression',
                                          'mysql_set_charset',
                                          'openssl_pkcs12_export',
                                          'openssl_pkcs12_export_to_file',
                                          'openssl_pkcs12_read',
                                          'php_ini_loaded_file',
                                          'reflectionextension::info',
                                          'regexiterator::setpregflags',
                                          'splfileinfo::getbasename',
                                          'splfileinfo::getlinktarget',
                                          'splfileinfo::getrealpath',
                                          'splobjectstorage::serialize',
                                          'splobjectstorage::unserialize',
                                          'stream_is_local',
                                          'stream_socket_shutdown',
                                          'swfmovie::namedanchor',
                                          'swfmovie::protect',
                                          'swfmovie::remove',
                                          'sys_get_temp_dir',
                                          'tidynode::getparent'),
                     'constants' => array());
        $this->assertSame($exp, $r);
    }

    /**
     * Tests the PHP Method chaining feature introduced with PHP5
     * Sample #1
     *
     * @link http://cowburn.info/php/php5-method-chaining/
     * @return void
     */
    public function testPHP5MethodChainingSamp1()
    {
        $ds  = DIRECTORY_SEPARATOR;
        $fn  = dirname(__FILE__) . $ds . 'parseFile' .
               $ds . 'php5_method_chaining.php';

        $r   = $this->pci->parseFile($fn);
        $exp = array('max_version' => '',
                     'version' => '5.0.0',
                     'extensions' => array(),
                     'constants' => array());
        $this->assertSame($exp, $r);
    }

    /**
     * Tests the PHP Method chaining feature introduced with PHP5.
     * Sample #2
     *
     * @return void
     */
    public function testPHP5MethodChainingSamp2()
    {
        $ds  = DIRECTORY_SEPARATOR;
        $fn  = dirname(__FILE__) . $ds . 'parseFile' .
               $ds . 'php5_method_chaining_samp2.php';

        $r   = $this->pci->parseFile($fn);
        $exp = array('max_version' => '',
                     'version' => '5.0.0',
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