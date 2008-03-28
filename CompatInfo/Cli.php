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
            'report' =>
                array('short' => 'r',
                      'desc' => 'Print either "xml" or "cli" report',
                      'default' => 'cli',
                      'min'   => 0 , 'max' => 1),
            'file-ext' =>
                array('short'   => 'fe',
                      'desc'    => 'A comma separated list of file extensions '
                                 . 'to parse (only valid if parsing a directory)',
                      'default' => 'php, php4, inc, phtml',
                      'min'     => 0 , 'max' => 1),
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
                $options                       = file($if);
                $this->options['ignore_files'] = array_map('rtrim', $options);
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
                $options                      = file($id);
                $this->options['ignore_dirs'] = array_map('rtrim', $options);
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
                $lines   = file($in);
                $options = array();
                foreach ($lines as $line) {
                    $line = rtrim($line);  // remove line ending
                    if (strlen($line) == 0) {
                        continue;  // skip empty lines
                    }
                    if ($line{0} == ';') {
                        continue;  // skip this entry: consider as comment
                    }
                    $options[] = $line;
                }
                $this->options['ignore_functions'] = $options;
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
                $lines   = file($ic);
                $options = array();
                foreach ($lines as $line) {
                    $line = rtrim($line);  // remove line ending
                    if (strlen($line) == 0) {
                        continue;  // skip empty lines
                    }
                    if ($line{0} == ';') {
                        continue;  // skip this entry: consider as comment
                    }
                    $options[] = $line;
                }
                $this->options['ignore_constants'] = $options;
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
                $lines   = file($ie);
                $options = array();
                foreach ($lines as $line) {
                    $line = rtrim($line);  // remove line ending
                    if (strlen($line) == 0) {
                        continue;  // skip empty lines
                    }
                    if ($line{0} == ';') {
                        continue;  // skip this entry: consider as comment
                    }
                    $options[] = $line;
                }
                $this->options['ignore_extensions'] = $options;
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
                $lines    = file($inm);
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
                        $patterns['std'][] = '/'.$line.'/';
                    }
                }
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
                $lines    = file($iem);
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
                        $patterns['std'][] = '/'.$line.'/';
                    }
                }
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
                $lines    = file($icm);
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
                        $patterns['std'][] = '/'.$line.'/';
                    }
                }
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
        if ($this->args->isDefined('r')) {
            $r = $this->args->getValue('r');
            if ($r == 'xml') {
                $this->_printXMLReport($info);
                return;
            }
        }

        $table = new Console_Table();
        $table->setHeaders(array(
            'Path', 'Version', 'Extensions', 'Constants/Tokens'));
        $filter0 = array(&$this, '_splitFilename');
        $table->addFilter(0, $filter0);
        $filter2 = array(&$this, '_splitExtname');
        $table->addFilter(2, $filter2);
        $filter3 = array(&$this, '_splitConstant');
        $table->addFilter(3, $filter3);

        $ext   = implode("\r\n", $info['extensions']);
        $const = implode("\r\n", array_merge($info['constants'], $info['tokens']));

        $dir = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $this->dir);
        $table->addRow(array($dir . DIRECTORY_SEPARATOR . '*',
            $info['version'], $ext, $const));

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

            $file = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $file);
            $table->addSeparator();
            $table->addRow(array($file, $info['version'], $ext, $const));
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
        if ($this->args->isDefined('r')) {
            $r = $this->args->getValue('r');
            if ($r == 'xml') {
                $this->_printXMLReport($info);
                return;
            }
        }

        $table = new Console_Table();
        $table->setHeaders(array(
            'File', 'Version', 'Extensions', 'Constants/Tokens'));
        $filter0 = array(&$this, '_splitFilename');
        $table->addFilter(0, $filter0);
        $filter2 = array(&$this, '_splitExtname');
        $table->addFilter(2, $filter2);
        $filter3 = array(&$this, '_splitConstant');
        $table->addFilter(3, $filter3);

        $ext   = implode("\r\n", $info['extensions']);
        $const = implode("\r\n", array_merge($info['constants'], $info['tokens']));

        $table->addRow(array($this->file, $info['version'], $ext, $const));

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
        $extArr = explode("\r\n", $data);
        $str    = '';
        foreach ($extArr as $ext) {
            if (strlen($ext) <= 11) {
                $str .= str_pad($ext, 11);
            } else {
                $str .= '...' . substr($ext, (strlen($ext) - 8));
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
        $cstArr = explode("\r\n", $data);
        $str    = '';
        foreach ($cstArr as $cst) {
            if (strlen($cst) <= 21) {
                $str .= str_pad($cst, 21);
            } else {
                $str .= '...' . substr($cst, (strlen($cst) - 18));
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

        echo XML_Util::getXMLDeclaration("1.0", "UTF-8");
        echo PHP_EOL;

        if (isset($this->dir)) {
            unset($info['ignored_files']);
            unset($info['ignored_functions']);
            unset($info['ignored_extensions']);
            unset($info['ignored_constants']);
            unset($info['max_version']);
            unset($info['version']);
            unset($info['extensions']);
            unset($info['constants']);
            unset($info['tokens']);
        } else {
            $info = array($this->file => $info);
        }

        echo XML_Util::createStartElement('pci',
                           array('version' => '@package_version@'));
        echo PHP_EOL;

        foreach ($info as $file => $info) {

            echo XML_Util::createStartElement('file', array('name' => $file));
            echo PHP_EOL;

            $tag = array('qname' => 'version',
                         'content' => $info['version']);
            echo XML_Util::createTagFromArray($tag);
            echo PHP_EOL;

            $c = count($info['extensions']);
            if ($c > 0) {
                $tag = array('qname' => 'extension',
                             'attributes' => array('count' => $c),
                             'content' => implode(', ', $info['extensions']));
                echo XML_Util::createTagFromArray($tag);
                echo PHP_EOL;
            }
            $c = count($info['constants']);
            if ($c > 0) {
                $tag = array('qname' => 'constant',
                             'attributes' => array('count' => $c),
                             'content' => implode(', ', $info['constants']));
                echo XML_Util::createTagFromArray($tag);
                echo PHP_EOL;
            }
            $c = count($info['tokens']);
            if ($c > 0) {
                $tag = array('qname' => 'token',
                             'attributes' => array('count' => $c),
                             'content' => implode(', ', $info['tokens']));
                echo XML_Util::createTagFromArray($tag);
                echo PHP_EOL;
            }

            // verbose level
            $v = $this->args->getValue('v');

            // extra information
            if ($v & 4) {
                unset($info['max_version']);
                unset($info['version']);
                unset($info['constants']);
                unset($info['tokens']);
                unset($info['extensions']);

                foreach ($info as $version => $functions) {
                    foreach ($functions as $func) {
                        $attr = array('version' => $version);
                        if (!empty($func['extension'])) {
                            $attr['extension'] = $func['extension'];
                            $attr['pecl']      = $func['pecl'] === true ?
                                                    'true' : 'false';
                        }
                        $tag = array('qname' => 'function',
                                     'attributes' => $attr,
                                     'content' => $func['function']);
                        echo XML_Util::createTagFromArray($tag);
                        echo PHP_EOL;
                    }
                }
            }
            echo XML_Util::createEndElement('file');
            echo PHP_EOL;
        }
        echo XML_Util::createEndElement('pci');
        echo PHP_EOL;
    }
}
?>