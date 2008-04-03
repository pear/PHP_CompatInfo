<?php
/**
 * CLI Script to Check Compatibility of chunk of PHP code
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
 * @since    File available since Release 0.8.0
 */

require_once 'PHP/CompatInfo.php';
require_once 'Console/Getargs.php';
require_once 'Console/Table.php';

/**
 * CLI Script to Check Compatibility of chunk of PHP code
 *
 * <code>
 * <?php
 *     require_once 'PHP/CompatInfo/Cli.php';
 *     $cli = new PHP_CompatInfo_Cli;
 *     $cli->run();
 * ?>
 * </code>
 *
 * @example docs/examples/cliCustom.php Example of using PHP_CompatInfo_Cli
 *
 * @category  PHP
 * @package   PHP_CompatInfo
 * @author    Davey Shafik <davey@php.net>
 * @author    Laurent Laville <pear@laurent-laville.org>
 * @copyright 2003 Davey Shafik and Synaptic Media. All Rights Reserved.
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CompatInfo
 * @since     Class available since Release 0.8.0
 */

class PHP_CompatInfo_Cli extends PHP_CompatInfo
{
    /**
     * @var    array    Current CLI Flags
     * @since  0.8.0
     */
    var $opts = array();

    /**
     * @var    string   error message
     * @since  0.8.0
     */
    var $error;

    /**
     * @var    string   String to be Processed
     * @since  1.6.0
     */
    var $string;

    /**
     * @var    string   File to be Processed
     * @since  0.8.0
     */
    var $file;

    /**
     * @var    string   Directory to be Processed
     * @since  0.8.0
     */
    var $dir;

    /**
     * @var    object   Console_Getargs instance
     * @since  1.4.0
     */
    var $args;

    /**
     * @var    array    Current parser options
     * @since  1.4.0
     */
    var $options = array();

    /**
     * @var    integer  Output level detail
     * @since  1.7.0b3
     */
    var $_output_level;


    /**
     * ZE2 Constructor
     *
     * @since  0.8.0
     */
    function __construct()
    {
        $this->opts = array(
            'dir' =>
                array('short' => 'd',
                      'desc'  => 'Parse DIR to get its compatibility info',
                      'default' => '',
                      'min'   => 0 , 'max' => 1),
            'file' =>
                array('short' => 'f',
                      'desc' => 'Parse FILE to get its compatibility info',
                      'default' => '',
                      'min'   => 0 , 'max' => 1),
            'string' =>
                array('short' => 's',
                      'desc' => 'Parse STRING to get its compatibility info',
                      'default' => '',
                      'min'   => 0 , 'max' => 1),
            'verbose' =>
                array('short'   => 'v',
                      'desc'    => 'Set the verbose level',
                      'default' => 1,
                      'min'     => 0 , 'max' => 1),
            'no-recurse' =>
                array('short' => 'n',
                      'desc'  => 'Do not recursively parse files when using --dir',
                      'max'   => 0),
            'ignore-files' =>
                array('short'   => 'if',
                      'desc'    => 'Data file name which contains a list of '
                                 . 'file to ignore',
                      'default' => 'files.txt',
                      'min'     => 0 , 'max' => 1),
            'ignore-dirs' =>
                array('short'   => 'id',
                      'desc'    => 'Data file name which contains a list of '
                                 . 'directory to ignore',
                      'default' => 'dirs.txt',
                      'min'     => 0 , 'max' => 1),
            'ignore-functions' =>
                array('short'   => 'in',
                      'desc'    => 'Data file name which contains a list of '
                                 . 'php function to ignore',
                      'default' => 'functions.txt',
                      'min'     => 0 , 'max' => 1),
            'ignore-constants' =>
                array('short'   => 'ic',
                      'desc'    => 'Data file name which contains a list of '
                                 . 'php constant to ignore',
                      'default' => 'constants.txt',
                      'min'     => 0 , 'max' => 1),
            'ignore-extensions' =>
                array('short'   => 'ie',
                      'desc'    => 'Data file name which contains a list of '
                                 . 'php extension to ignore',
                      'default' => 'extensions.txt',
                      'min'     => 0 , 'max' => 1),
            'ignore-versions' =>
                array('short'   => 'iv',
                      'desc'    => 'PHP versions - functions to exclude '
                                 . 'when parsing source code',
                      'default' => '5.0.0',
                      'min'     => 0 , 'max' => 2),
            'ignore-functions-match' =>
                array('short'   => 'inm',
                      'desc'    => 'Data file name which contains a list of '
                                 . 'php function pattern to ignore',
                      'default' => 'functions-match.txt',
                      'min'     => 0 , 'max' => 1),
            'ignore-extensions-match' =>
                array('short'   => 'iem',
                      'desc'    => 'Data file name which contains a list of '
                                 . 'php extension pattern to ignore',
                      'default' => 'extensions-match.txt',
                      'min'     => 0 , 'max' => 1),
            'ignore-constants-match' =>
                array('short'   => 'icm',
                      'desc'    => 'Data file name which contains a list of '
                                 . 'php constant pattern to ignore',
                      'default' => 'constants-match.txt',
                      'min'     => 0 , 'max' => 1),
            'file-ext' =>
                array('short'   => 'fe',
                      'desc'    => 'A comma separated list of file extensions '
                                 . 'to parse (only valid if parsing a directory)',
                      'default' => 'php, php4, inc, phtml',
                      'min'     => 0 , 'max' => 1),
            'report' =>
                array('short' => 'r',
                      'desc' => 'Print either "xml" or "cli" report',
                      'default' => 'cli',
                      'min'   => 0 , 'max' => 1),
            'output-level' =>
                array('short' => 'o',
                      'desc' => 'Print Path/File + Version with additional data',
                      'default' => 15,
                      'min'   => 0 , 'max' => 1),
            'version' =>
                array('short' => 'V',
                      'desc'  => 'Print version information',
                      'max'   => 0),
            'help' =>
                array('short' => 'h',
                      'desc'  => 'Show this help',
                      'max'   => 0),
        );
        $this->args = & Console_Getargs::factory($this->opts);
        if (PEAR::isError($this->args)) {
            if ($this->args->getCode() === CONSOLE_GETARGS_HELP) {
                $this->error = '';
            } else {
                $this->error = $this->args->getMessage();
            }
            return;
        }

        // version
        $V = $this->args->getValue('V');
        if (isset($V)) {
            $this->error = 'PHP_CompatInfo (cli) version @package_version@'
                         . ' (http://pear.php.net/package/PHP_CompatInfo)';
            return;
        }

        // debug
        if ($this->args->isDefined('v')) {
            $v = $this->args->getValue('v');
            if ($v > 3) {
                $this->options['debug'] = true;
            }
        }

        // no-recurse
        if ($this->args->isDefined('n')) {
            $this->options['recurse_dir'] = false;
        }

        // dir
        if ($this->args->isDefined('d')) {
            $d = $this->args->getValue('d');
            if (file_exists($d)) {
                if ($d{strlen($d)-1} == '/' || $d{strlen($d)-1} == '\\') {
                    $d = substr($d, 0, -1);
                }
                $this->dir = str_replace('\\', '/', realpath($d));
            } else {
                $this->error = 'Failed opening directory "' . $d
                     . '". Please check your spelling and try again.';
                return;
            }
        }

        // file
        if ($this->args->isDefined('f')) {
            $f = $this->args->getValue('f');
            if (file_exists($f)) {
                $this->file = $f;
            } else {
                $this->error = 'Failed opening file "' . $f
                     . '". Please check your spelling and try again.';
                return;
            }
        }

        // string
        if ($this->args->isDefined('s')) {
            $s = $this->args->getValue('s');
            if (!empty($s)) {
                $this->string = sprintf("<?php %s ?>", $s);
            } else {
                $this->error = 'Failed opening string "' . $s
                     . '". Please check your spelling and try again.';
                return;
            }
        }

        // ignore-files
        $if = $this->args->getValue('if');
        if (isset($if)) {
            if (file_exists($if)) {
                $options                       = $this->_parseParamFile($if);
                $this->options['ignore_files'] = $options['std'];
            } else {
                $this->error = 'Failed opening file "' . $if
                     . '" (ignore-files option). '
                     . 'Please check your spelling and try again.';
                return;
            }
        }

        // ignore-dirs
        $id = $this->args->getValue('id');
        if (isset($id)) {
            if (file_exists($id)) {
                $options                      = $this->_parseParamFile($id);
                $this->options['ignore_dirs'] = $options['std'];
            } else {
                $this->error = 'Failed opening file "' . $id
                     . '" (ignore-dirs option). '
                     . 'Please check your spelling and try again.';
                return;
            }
        }

        // ignore-functions
        $in = $this->args->getValue('in');
        if (isset($in)) {
            if (file_exists($in)) {
                $options                           = $this->_parseParamFile($in);
                $this->options['ignore_functions'] = $options['std'];
            } else {
                $this->error = 'Failed opening file "' . $in
                     . '" (ignore-functions option). '
                     . 'Please check your spelling and try again.';
                return;
            }
        }

        // ignore-constants
        $ic = $this->args->getValue('ic');
        if (isset($ic)) {
            if (file_exists($ic)) {
                $options                           = $this->_parseParamFile($ic);
                $this->options['ignore_constants'] = $options['std'];
            } else {
                $this->error = 'Failed opening file "' . $ic
                     . '" (ignore-constants option). '
                     . 'Please check your spelling and try again.';
                return;
            }
        }

        // ignore-extensions
        $ie = $this->args->getValue('ie');
        if (isset($ie)) {
            if (file_exists($ie)) {
                $options                            = $this->_parseParamFile($ie);
                $this->options['ignore_extensions'] = $options['std'];
            } else {
                $this->error = 'Failed opening file "' . $ie
                     . '" (ignore-extensions option). '
                     . 'Please check your spelling and try again.';
                return;
            }
        }

        // ignore-versions
        $iv = $this->args->getValue('iv');
        if (isset($iv)) {
            if (!is_array($iv)) {
                $iv = array($iv);
            }
            $this->options['ignore_versions'] = $iv;
        }

        // ignore-functions-match
        $inm = $this->args->getValue('inm');
        if (isset($inm)) {
            if (file_exists($inm)) {
                $patterns = $this->_parseParamFile($inm, true);
                if (count($patterns['std']) > 0
                    && count($patterns['reg']) > 0) {
                    $this->error = 'Mixed "function_exists" and '
                         . '"preg_match" conditions are not allowed. '
                         . 'Please check your spelling and try again.';
                    return;

                } elseif (count($patterns['std']) > 0) {
                    $this->options['ignore_functions_match']
                        = array('function_exists', $patterns['std']);
                } elseif (count($patterns['reg']) > 0) {
                    $this->options['ignore_functions_match']
                        = array('preg_match', $patterns['reg']);
                }
            } else {
                $this->error = 'Failed opening file "' . $inm
                     . '" (ignore-functions-match option). '
                     . 'Please check your spelling and try again.';
                return;
            }
        }

        // ignore-extensions-match
        $iem = $this->args->getValue('iem');
        if (isset($iem)) {
            if (file_exists($iem)) {
                $patterns = $this->_parseParamFile($iem, true);
                if (count($patterns['std']) > 0
                    && count($patterns['reg']) > 0) {
                    $this->error = 'Mixed "extension_loaded" and '
                         . '"preg_match" conditions are not allowed. '
                         . 'Please check your spelling and try again.';
                    return;

                } elseif (count($patterns['std']) > 0) {
                    $this->options['ignore_extensions_match']
                        = array('extension_loaded', $patterns['std']);
                } elseif (count($patterns['reg']) > 0) {
                    $this->options['ignore_extensions_match']
                        = array('preg_match', $patterns['reg']);
                }
            } else {
                $this->error = 'Failed opening file "' . $iem
                     . '" (ignore-extensions-match option). '
                     . 'Please check your spelling and try again.';
                return;
            }
        }

        // ignore-constants-match
        $icm = $this->args->getValue('icm');
        if (isset($icm)) {
            if (file_exists($icm)) {
                $patterns = $this->_parseParamFile($icm, true);
                if (count($patterns['std']) > 0
                    && count($patterns['reg']) > 0) {
                    $this->error = 'Mixed "defined" and '
                         . '"preg_match" conditions are not allowed. '
                         . 'Please check your spelling and try again.';
                    return;

                } elseif (count($patterns['std']) > 0) {
                    $this->options['ignore_constants_match']
                        = array('defined', $patterns['std']);
                } elseif (count($patterns['reg']) > 0) {
                    $this->options['ignore_constants_match']
                        = array('preg_match', $patterns['reg']);
                }
            } else {
                $this->error = 'Failed opening file "' . $icm
                     . '" (ignore-constants-match option). '
                     . 'Please check your spelling and try again.';
                return;
            }
        }

        // file-ext
        if ($this->args->isDefined('d') && $this->args->isDefined('fe')) {
            $fe = $this->args->getValue('fe');
            if (is_string($fe)) {
                $this->options['file_ext'] = explode(',', $fe);
            } else {
                $this->error = 'No valid file extensions provided "'
                     . '". Please check your spelling and try again.';
                return;
            }
        }

        // output-level
        if ($this->args->isDefined('o')) {
            $this->_output_level = $this->args->getValue('o');
        } else {
            $this->_output_level = 15; // default = full detail
        }

        // file or directory options are minimum required to work
        if (!$this->args->isDefined('f')
            && !$this->args->isDefined('d')
            && !$this->args->isDefined('s')) {
            $this->error = 'ERROR: You must supply at least '
                . 'one string, file or directory to process';
        }
    }

    /**
     * ZE1 PHP4 Compatible Constructor
     *
     * @since  0.8.0
     */
    function PHP_CompatInfo_Cli()
    {
        $this->__construct();
    }

    /**
     * Run the CLI Script
     *
     * @return void
     * @access public
     * @since  0.8.0
     */
    function run()
    {
        if (isset($this->error)) {
            if (strpos($this->error, 'PHP_CompatInfo') === false) {
                $this->_printUsage($this->error);
            } else {
                // when Version asked, do not print help usage
                echo $this->error;
            }
        } else {
            if (isset($this->dir)) {
                $this->_parseDir();
            } elseif (isset($this->file)) {
                $this->_parseFile();
            } elseif (isset($this->string)) {
                $this->_parseString();
            }
        }
    }

    /**
     * Parse content of parameter files
     *
     * Parse content of parameter files used by switches
     * <ul>
     * <li>ignore-files
     * <li>ignore-dirs
     * <li>ignore-functions
     * <li>ignore-constants
     * <li>ignore-extensions
     * <li>ignore-functions-match
     * <li>ignore-extensions-match
     * <li>ignore-constants-match
     * </ul>
     *
     * @param string $fn          Parameter file name
     * @param bool   $withPattern TRUE if the file may contain regular expression
     *
     * @return array
     * @access private
     * @since  version 1.7.0b4 (2008-04-03)
     */
    function _parseParamFile($fn, $withPattern = false)
    {
        $lines    = file($fn);
        $patterns = array('std' => array(), 'reg' => array());
        foreach ($lines as $line) {
            $line = rtrim($line);  // remove line ending
            if (strlen($line) == 0) {
                continue;  // skip empty lines
            }
            if ($line{0} == ';') {
                continue;  // skip this pattern: consider as comment line
            }
            if ($line{0} == '=') {
                list($p, $s)       = explode('=', $line);
                $patterns['reg'][] = '/'.$s.'/';
            } else {
                if ($withPattern === true) {
                    $patterns['std'][] = '/'.$line.'/';
                } else {
                    $patterns['std'][] = $line;
                }
            }
        }
        return $patterns;
    }

    /**
     * Parse Directory Input
     *
     * @return void
     * @access private
     * @since  0.8.0
     */
    function _parseDir()
    {
        $info = $this->parseDir($this->dir, $this->options);
        if ($info === false) {
            $err = 'No valid files into directory "' . $this->dir
               . '". Please check your spelling and try again.';
            $this->_printUsage($err);
            return;
        }
        $o = $this->_output_level;
        if ($this->args->isDefined('r')) {
            $r = $this->args->getValue('r');
            if ($r == 'xml') {
                $this->_printXMLReport($info);
                return;
            }
        }

        $table = new Console_Table();
        $hdr   = array('Path', 'Version');
        $f     = 1;
        if ($o & 2) {
            $hdr[]   = 'Extensions';
            $filter2 = array(&$this, '_splitExtname');
            $table->addFilter(2, $filter2);
            $f++;
        }
        if ($o & 4) {
            if ($o & 8) {
                $hdr[] = 'Constants/Tokens';
            } else {
                $hdr[] = 'Constants';
            }
            $f++;
        } else {
            if ($o & 8) {
                $hdr[] = 'Tokens';
                $f++;
            }
        }
        $table->setHeaders($hdr);
        $filter0 = array(&$this, '_splitFilename');
        $table->addFilter(0, $filter0);
        if ($o > 3) {
            $filter3 = array(&$this, '_splitConstant');
            $table->addFilter($f, $filter3);
        }

        $ext   = implode("\r\n", $info['extensions']);
        $const = implode("\r\n", array_merge($info['constants'], $info['tokens']));
        $ds    = DIRECTORY_SEPARATOR;
        $dir   = str_replace(array('\\', '/'), $ds, $this->dir);

        $data = array($dir . $ds . '*' , $info['version']);
        if ($o & 2) {
            $data[] = $ext;
        }
        if ($o & 4) {
            if ($o & 8) {
                $data[] = $const;
            } else {
                $data[] = implode("\r\n", $info['constants']);
            }
        } else {
            if ($o & 8) {
                $data[] = implode("\r\n", $info['tokens']);
            }
        }

        $table->addRow($data);

        unset($info['max_version']);
        unset($info['version']);
        unset($info['extensions']);
        unset($info['constants']);
        unset($info['tokens']);

        $ignored = $info['ignored_files'];

        unset($info['ignored_files']);
        unset($info['ignored_functions']);
        unset($info['ignored_extensions']);
        unset($info['ignored_constants']);

        foreach ($info as $file => $info) {
            if ($info === false) {
                continue;  // skip this (invalid) file
            }
            $ext   = implode("\r\n", $info['extensions']);
            $const = implode("\r\n", array_merge($info['constants'],
                                                 $info['tokens']));

            $file = str_replace(array('\\', '/'), $ds, $file);
            $table->addSeparator();

            $data = array($file, $info['version']);
            if ($o & 2) {
                $data[] = $ext;
            }
            if ($o & 4) {
                if ($o & 8) {
                    $data[] = $const;
                } else {
                    $data[] = implode("\r\n", $info['constants']);
                }
            } else {
                if ($o & 8) {
                    $data[] = implode("\r\n", $info['tokens']);
                }
            }

            $table->addRow($data);
        }

        $output = $table->getTable();

        // verbose level
        $v = $this->args->getValue('v');

        // command line resume
        if ($v & 1) {
            $output .= "\nCommand Line resume :\n\n";

            $table = new Console_Table();
            $table->setHeaders(array('Option', 'Value'));

            $filter0 = array(&$this, '_splitOption');
            $table->addFilter(0, $filter0);
            $filter1 = array(&$this, '_splitValue');
            $table->addFilter(1, $filter1);

            $opts = $this->args->getValues();
            if (is_array($opts)) {
                foreach ($opts as $key => $raw) {
                    if (is_array($raw)) {
                        $raw = implode(', ', $raw);
                    }
                    $contents = array($key, $raw);
                    $table->addRow($contents);
                }
            }

            $output .= $table->getTable();
        }

        // parser options resume
        if ($v & 2) {
            $output .= "\nParser options :\n\n";

            $table = new Console_Table();
            $table->setHeaders(array('Option', 'Value'));

            $filter0 = array(&$this, '_splitOption');
            $table->addFilter(0, $filter0);
            $filter1 = array(&$this, '_splitValue');
            $table->addFilter(1, $filter1);

            $opts = $this->options;
            if (is_array($opts)) {
                foreach ($opts as $key => $raw) {
                    if ($key == 'debug' || $key == 'recurse_dir') {
                        $raw = ($raw === true) ? 'TRUE' : 'FALSE';
                    }
                    if (substr($key, -6) == '_match') {
                        $val = array_values($raw[1]);
                        array_unshift($val, $raw[0]);
                        $raw = implode("\r\n", $val);
                    } else {
                        if (is_array($raw)) {
                            $raw = implode("\r\n", $raw);
                        }
                    }
                    $contents = array($key, $raw);
                    $table->addRow($contents);
                }
            }

            $output .= $table->getTable();
        }

        echo $output;
    }

    /**
     * Parse File Input
     *
     * @return void
     * @access private
     * @since  0.8.0
     */
    function _parseFile()
    {
        $info = $this->parseFile($this->file, $this->options);
        if ($info === false) {
            $err = 'Failed opening file "' . $this->file
               . '". Please check your spelling and try again.';
            $this->_printUsage($err);
            return;
        }
        $o = $this->_output_level;
        if ($this->args->isDefined('r')) {
            $r = $this->args->getValue('r');
            if ($r == 'xml') {
                $this->_printXMLReport($info);
                return;
            }
        }

        $table = new Console_Table();
        $hdr   = array('File', 'Version');
        $f     = 1;
        if ($o & 2) {
            $hdr[]   = 'Extensions';
            $filter2 = array(&$this, '_splitExtname');
            $table->addFilter(2, $filter2);
            $f++;
        }
        if ($o & 4) {
            if ($o & 8) {
                $hdr[] = 'Constants/Tokens';
            } else {
                $hdr[] = 'Constants';
            }
            $f++;
        } else {
            if ($o & 8) {
                $hdr[] = 'Tokens';
                $f++;
            }
        }
        $table->setHeaders($hdr);
        $filter0 = array(&$this, '_splitFilename');
        $table->addFilter(0, $filter0);
        if ($o > 3) {
            $filter3 = array(&$this, '_splitConstant');
            $table->addFilter($f, $filter3);
        }

        $ext   = implode("\r\n", $info['extensions']);
        $const = implode("\r\n", array_merge($info['constants'], $info['tokens']));

        $data = array($this->file, $info['version']);
        if ($o & 2) {
            $data[] = $ext;
        }
        if ($o & 4) {
            if ($o & 8) {
                $data[] = $const;
            } else {
                $data[] = implode("\r\n", $info['constants']);
            }
        } else {
            if ($o & 8) {
                $data[] = implode("\r\n", $info['tokens']);
            }
        }

        $table->addRow($data);

        $output = $table->getTable();

        // verbose level
        $v = $this->args->getValue('v');

        // command line resume
        if ($v & 1) {
            $output .= "\nCommand Line resume :\n\n";

            $table = new Console_Table();
            $table->setHeaders(array('Option', 'Value'));

            $filter0 = array(&$this, '_splitOption');
            $table->addFilter(0, $filter0);
            $filter1 = array(&$this, '_splitValue');
            $table->addFilter(1, $filter1);

            $opts = $this->args->getValues();
            if (is_array($opts)) {
                foreach ($opts as $key => $raw) {
                    if (is_array($raw)) {
                        $raw = implode(', ', $raw);
                    }
                    $contents = array($key, $raw);
                    $table->addRow($contents);
                }
            }

            $output .= $table->getTable();
        }

        // parser options resume
        if ($v & 2) {
            $output .= "\nParser options :\n\n";

            $table = new Console_Table();
            $table->setHeaders(array('Option', 'Value'));

            $filter0 = array(&$this, '_splitOption');
            $table->addFilter(0, $filter0);
            $filter1 = array(&$this, '_splitValue');
            $table->addFilter(1, $filter1);

            $opts = $this->options;
            if (is_array($opts)) {
                foreach ($opts as $key => $raw) {
                    if ($key == 'debug' || $key == 'recurse_dir') {
                        $raw = ($raw === true) ? 'TRUE' : 'FALSE';
                    }
                    if (substr($key, -6) == '_match') {
                        $val = array_values($raw[1]);
                        array_unshift($val, $raw[0]);
                        $raw = implode("\r\n", $val);
                    } else {
                        if (is_array($raw)) {
                            $raw = implode("\r\n", $raw);
                        }
                    }
                    $contents = array($key, $raw);
                    $table->addRow($contents);
                }
            }

            $output .= $table->getTable();
        }

        // extra information
        if ($v & 4) {
            $output .= "\nDebug:\n\n";

            $table = new Console_Table();
            $table->setHeaders(array('Version', 'Function', 'Extension', 'PECL'));

            unset($info['max_version']);
            unset($info['version']);
            unset($info['constants']);
            unset($info['tokens']);
            unset($info['extensions']);
            unset($info['ignored_functions']);
            unset($info['ignored_extensions']);
            unset($info['ignored_constants']);

            foreach ($info as $version => $functions) {
                foreach ($functions as $func) {
                    $table->addRow(array($version,
                        $func['function'], $func['extension'],
                        (isset($func['pecl']) ?
                        (($func['pecl'] === true) ? 'yes' : 'no') : '')));
                }
            }

            $output .= $table->getTable();
        }

        echo $output;
    }

    /**
     * Parse String Input
     *
     * @return void
     * @access private
     * @since  1.6.0
     */
    function _parseString()
    {
        $info = $this->parseString($this->string, $this->options);
        if ($info === false) {
            $err = 'Failed opening string "' . $this->string
               . '". Please check your spelling and try again.';
            $this->_printUsage($err);
            return;
        }
        $table = new Console_Table();
        $table->setHeaders(array('Version', 'Extensions', 'Constants/Tokens'));

        $ext   = implode("\r\n", $info['extensions']);
        $const = implode("\r\n", $info['constants']);

        $table->addRow(array($info['version'], $ext, $const));

        $output = $table->getTable();

        // verbose level
        $v = $this->args->getValue('v');

        // command line resume
        if ($v & 1) {
            $output .= "\nCommand Line resume :\n\n";

            $table = new Console_Table();
            $table->setHeaders(array('Option', 'Value'));

            $filter0 = array(&$this, '_splitOption');
            $table->addFilter(0, $filter0);
            $filter1 = array(&$this, '_splitValue');
            $table->addFilter(1, $filter1);

            $opts = $this->args->getValues();
            if (is_array($opts)) {
                foreach ($opts as $key => $raw) {
                    if (is_array($raw)) {
                        $raw = implode(', ', $raw);
                    }
                    $contents = array($key, $raw);
                    $table->addRow($contents);
                }
            }

            $output .= $table->getTable();
        }

        // parser options resume
        if ($v & 2) {
            $output .= "\nParser options :\n\n";

            $table = new Console_Table();
            $table->setHeaders(array('Option', 'Value'));

            $filter0 = array(&$this, '_splitOption');
            $table->addFilter(0, $filter0);
            $filter1 = array(&$this, '_splitValue');
            $table->addFilter(1, $filter1);

            $opts = $this->options;
            if (is_array($opts)) {
                foreach ($opts as $key => $raw) {
                    if ($key == 'debug') {
                        $raw = ($raw === true) ? 'TRUE' : 'FALSE';
                    }
                    if (substr($key, -6) == '_match') {
                        $val = array_values($raw[1]);
                        array_unshift($val, $raw[0]);
                        $raw = implode("\r\n", $val);
                    } else {
                        if (is_array($raw)) {
                            $raw = implode("\r\n", $raw);
                        }
                    }
                    $contents = array($key, $raw);
                    $table->addRow($contents);
                }
            }

            $output .= $table->getTable();
        }

        // extra information
        if ($v & 4) {
            $output .= "\nDebug:\n\n";

            $table = new Console_Table();
            $table->setHeaders(array('Version', 'Function', 'Extension', 'PECL'));

            unset($info['max_version']);
            unset($info['version']);
            unset($info['constants']);
            unset($info['extensions']);
            unset($info['ignored_functions']);
            unset($info['ignored_extensions']);
            unset($info['ignored_constants']);

            foreach ($info as $version => $functions) {
                foreach ($functions as $func) {
                    $table->addRow(array($version,
                        $func['function'], $func['extension'],
                        (isset($func['pecl']) ?
                        (($func['pecl'] === true) ? 'yes' : 'no') : '')));
                }
            }

            $output .= $table->getTable();
        }

        echo $output;
    }

    /**
     * The Console_Table filter callback limits table output to 80 columns,
     * and Path column to 29 characters
     * (27 + 1 blank margin left + 1 blank margin right).
     *
     * @param string $data Content of filename column (0)
     *
     * @return string
     * @access private
     * @since  1.3.0
     */
    function _splitFilename($data)
    {
        if (strlen($data) <= 27) {
            $str = str_pad($data, 27);
        } else {
            $str = '...' . substr($data, (strlen($data) - 24));
        }
        return $str;
    }

    /**
     * The Console_Table filter callback limits table output to 80 columns,
     * and Extensions column to 13 characters
     * (11 + 1 blank margin left + 1 blank margin right).
     *
     * @param string $data Content of extensions column (2)
     *
     * @return string
     * @access private
     * @since  1.7.0
     */
    function _splitExtname($data)
    {
        $szlim  = ($this->_output_level & 12) ? 11 : 35;
        $extArr = explode("\r\n", $data);
        $str    = '';
        foreach ($extArr as $ext) {
            if (strlen($ext) <= $szlim) {
                $str .= str_pad($ext, $szlim);
            } else {
                $str .= '...' . substr($ext, (strlen($ext) - ($szlim - 3)));
            }
            $str .= "\r\n";
        }
        $str = rtrim($str, "\r\n");
        return $str;
    }

    /**
     * The Console_Table filter callback limits table output to 80 columns,
     * and Constants/Tokens column to 23 characters
     * (21 + 1 blank margin left + 1 blank margin right)
     *
     * @param string $data Content of constants/tokens column (3)
     *
     * @return string
     * @access private
     * @since  1.7.0
     */
    function _splitConstant($data)
    {
        $szlim  = ($this->_output_level & 2) ? 21 : 35;
        $cstArr = explode("\r\n", $data);
        $str    = '';
        foreach ($cstArr as $cst) {
            if (strlen($cst) <= $szlim) {
                $str .= str_pad($cst, $szlim);
            } else {
                $str .= '...' . substr($cst, (strlen($cst) - ($szlim - 3)));
            }
            $str .= "\r\n";
        }
        $str = rtrim($str, "\r\n");
        return $str;
    }

    /**
     * The Console_Table filter callback limits table output to 80 columns,
     * and Command line Option column to 25 characters
     * (23 + 1 blank margin left + 1 blank margin right).
     *
     * @param string $data Content of option column (0)
     *
     * @return string
     * @access private
     * @since  1.7.0
     */
    function _splitOption($data)
    {
        if (strlen($data) <= 23) {
            $str = str_pad($data, 23);
        } else {
            $str = '...' . substr($data, (strlen($data) - 20));
        }
        return $str;
    }

    /**
     * The Console_Table filter callback limits table output to 80 columns,
     * and Command line Value column to 51 characters
     * (49 + 1 blank margin left + 1 blank margin right)
     *
     * @param string $data Content of value column (1)
     *
     * @return string
     * @access private
     * @since  1.7.0
     */
    function _splitValue($data)
    {
        $cstArr = explode("\r\n", $data);
        $str    = '';
        foreach ($cstArr as $cst) {
            if (strlen($cst) <= 49) {
                $str .= str_pad($cst, 49);
            } else {
                $str .= '...' . substr($cst, (strlen($cst) - 46));
            }
            $str .= "\r\n";
        }
        $str = rtrim($str, "\r\n");
        return $str;
    }

    /**
     * Show full help information
     *
     * @param string $footer (optional) page footer content
     *
     * @return void
     * @access private
     * @since  0.8.0
     */
    function _printUsage($footer = '')
    {
        $header = 'Usage: '
            . basename($_SERVER['SCRIPT_NAME']) . " [options]\n\n";
        echo Console_Getargs::getHelp($this->opts, $header,
            "\n$footer\n", 78, 2)."\n";
    }

    /**
     * Print XML report
     *
     * @param array $info File or directory parsing data
     *
     * @return void
     * @access private
     * @since  1.6.0
     */
    function _printXMLReport($info)
    {
        include_once 'XML/Util.php';

        ob_start();

        echo XML_Util::getXMLDeclaration("1.0", "UTF-8");
        echo PHP_EOL;
        echo XML_Util::createStartElement('pci',
                                          array('version' => '@package_version@'));
        echo PHP_EOL;

        if (isset($this->dir)) {
            // parsing a directory

            // print <dir> tag
            $tag = array('qname' => 'dir',
                         'content' => $this->dir);
            echo XML_Util::createTagFromArray($tag);
            echo PHP_EOL;

            // print global <version> tag
            if (empty($info['max_version'])) {
                $attr = array();
            } else {
                $attr = array('max' => $info['max_version']);
            }
            $tag = array('qname' => 'version',
                         'attributes' => $attr,
                         'content' => $info['version']);
            echo XML_Util::createTagFromArray($tag);
            echo PHP_EOL;

            // print global <extensions> tag group
            $this->_printTagList($info['extensions'], 'extension');
            // print global <constants> tag group
            $this->_printTagList($info['constants'], 'constant');
            // print global <tokens> tag group
            $this->_printTagList($info['tokens'], 'token');

            // print global <ignored> tag group
            echo XML_Util::createStartElement('ignored');
            echo PHP_EOL;
            // with children groups <files>, <functions>, <extensions>, <constants>
            $ignored = array('file' => $info['ignored_files'],
                             'function' => $info['ignored_functions'],
                             'extension' => $info['ignored_extensions'],
                             'constant' => $info['ignored_constants']);
            foreach ($ignored as $tag => $data) {
                $this->_printTagList($data, $tag);
            }
            echo XML_Util::createEndElement('ignored');
            echo PHP_EOL;

            // remove summary data
            unset($info['ignored_files']);
            unset($info['ignored_functions']);
            unset($info['ignored_extensions']);
            unset($info['ignored_constants']);
            unset($info['max_version']);
            unset($info['version']);
            unset($info['extensions']);
            unset($info['constants']);
            unset($info['tokens']);

            $files = $info;
        } else {
            // parsing a single file
            $files = array($this->file => $info);
        }

        // print <files> tag group
        echo XML_Util::createStartElement('files', array('count' => count($files)));
        echo PHP_EOL;

        foreach ($files as $file => $info) {
            // print local <file> tag
            echo XML_Util::createStartElement('file', array('name' => $file));
            echo PHP_EOL;

            // print local <version> tag
            if (empty($info['max_version'])) {
                $attr = array();
            } else {
                $attr = array('max' => $info['max_version']);
            }
            $tag = array('qname' => 'version',
                         'attributes' => $attr,
                         'content' => $info['version']);
            echo XML_Util::createTagFromArray($tag);
            echo PHP_EOL;

            // print local <extensions> tag group
            $this->_printTagList($info['extensions'], 'extension');
            // print local <constants> tag group
            $this->_printTagList($info['constants'], 'constant');
            // print local <tokens> tag group
            $this->_printTagList($info['tokens'], 'token');

            // print local <ignored> tag group
            echo XML_Util::createStartElement('ignored');
            echo PHP_EOL;
            // with children groups <functions>, <extensions>, <constants>
            $ignored = array('function' => $info['ignored_functions'],
                             'extension' => $info['ignored_extensions'],
                             'constant' => $info['ignored_constants']);
            foreach ($ignored as $tag => $data) {
                $this->_printTagList($data, $tag);
            }
            echo XML_Util::createEndElement('ignored');
            echo PHP_EOL;

            // verbose level
            $v = $this->args->getValue('v');

            // extra information only if verbose mode >= 4
            if ($v & 4) {
                unset($info['ignored_files']);
                unset($info['ignored_functions']);
                unset($info['ignored_extensions']);
                unset($info['ignored_constants']);
                unset($info['max_version']);
                unset($info['version']);
                unset($info['constants']);
                unset($info['tokens']);
                unset($info['extensions']);

                // print local <functions> tag group
                $this->_printTagList($info, 'function');
            }

            echo XML_Util::createEndElement('file');
            echo PHP_EOL;
        }
        echo XML_Util::createEndElement('files');
        echo PHP_EOL;
        echo XML_Util::createEndElement('pci');
        echo PHP_EOL;

        $result = ob_get_clean();

        // try to see if we can improve XML render
        $beautifier = 'XML/Beautifier.php';
        if (PHP_CompatInfo_Cli::isIncludable($beautifier)) {
            include_once $beautifier;
            $options = array('indent' => ' ');
            $fmt     = new XML_Beautifier($options);
            $result  = $fmt->formatString($result);
        }
        echo $result;
    }

    /**
     * Print a group of same tag in the XML report.
     *
     * Groups list are : extension(s), constant(s), token(s)
     *
     * @param array  $dataSrc Data source
     * @param string $tagName Name of the XML tag
     *
     * @return void
     * @access private
     * @since  version 1.7.0b4 (2008-04-03)
     */
    function _printTagList($dataSrc, $tagName)
    {
        if ($tagName == 'function') {
            $c = 0;
            foreach ($dataSrc as $version => $functions) {
                $c += count($functions);
            }
        } else {
            $c = count($dataSrc);
        }

        echo XML_Util::createStartElement($tagName.'s', array('count' => $c));
        echo PHP_EOL;

        if ($tagName == 'function') {
            foreach ($dataSrc as $version => $functions) {
                foreach ($functions as $data) {
                    $attr = array('version' => $version);
                    if (!empty($data['extension'])) {
                        $attr['extension'] = $data['extension'];
                        $attr['pecl']      = $data['pecl'] === true ?
                                                'true' : 'false';
                    }
                    $tag = array('qname' => $tagName,
                                 'attributes' => $attr,
                                 'content' => $data['function']);
                    echo XML_Util::createTagFromArray($tag);
                    echo PHP_EOL;
                }
            }
        } else {
            foreach ($dataSrc as $data) {
                $tag = array('qname' => $tagName,
                             'attributes' => array(),
                             'content' => $data);
                echo XML_Util::createTagFromArray($tag);
                echo PHP_EOL;
            }
        }

        echo XML_Util::createEndElement($tagName.'s');
        echo PHP_EOL;
    }

    /**
     * Returns whether or not a file is in the include path.
     *
     * @param string $file Path to filename to check if includable
     *
     * @static
     * @access public
     * @return boolean TRUE if the file is in the include path, FALSE otherwise
     * @since  version 1.7.0b4 (2008-04-03)
     */
    function isIncludable($file)
    {
        foreach (explode(PATH_SEPARATOR, get_include_path()) as $ip) {
            if (file_exists($ip . DIRECTORY_SEPARATOR . $file)
                && is_readable($ip . DIRECTORY_SEPARATOR . $file)
                ) {
                return true;
            }
        }
        return false;
    }
}
?>