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

/**
 * CLI Script to Check Compatibility of chunk of PHP code
 *
 * <code>
 * <?php
 *     require_once 'PHP/CompatInfo/Cli.php';
 *     $cli = new PHP_CompatInfo_Cli();
 *     $cli->run();
 * ?>
 * </code>
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

class PHP_CompatInfo_Cli
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
     * Unified data source reference
     *
     * @var    string   Directory, File or String to be processed
     * @since  1.8.0b3
     */
    var $dataSource;

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
            'file-ext' =>
                array('short'   => 'fe',
                      'desc'    => 'A comma separated list of file extensions '
                                 . 'to parse (only valid if parsing a directory)',
                      'default' => 'php, php4, inc, phtml',
                      'min'     => 0 , 'max' => 1),
            'report' =>
                array('short' => 'r',
                      'desc' => 'Print either "xml" or "csv" report',
                      'default' => 'text',
                      'min'   => 0 , 'max' => 1),
            'output-level' =>
                array('short' => 'o',
                      'desc' => 'Print Path/File + Version with additional data',
                      'default' => 31,
                      'min'   => 0 , 'max' => 1),
            'progress' =>
                array('short' => 'p',
                      'desc' => 'Show a wait message [text] or a progress bar [bar]',
                      'default' => 'bar',
                      'min'   => 0 , 'max' => 1),
            'summarize' =>
                array('short' => 'S',
                      'desc' => 'Print only summary when parsing directory',
                      'max'   => 0),
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

        // default parser options
        $this->options = array(
            'file_ext' => array('php', 'php4', 'inc', 'phtml'),
            'recurse_dir' => true,
            'debug' => false,
            'is_string' => false,
            'ignore_files' => array(),
            'ignore_dirs' => array()
            );

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
                $this->dataSource = str_replace('\\', '/', realpath($d));
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
                $this->dataSource = $f;
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
                $this->dataSource           = sprintf("<?php %s ?>", $s);
                $this->options['is_string'] = true;
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
            $args = $this->args->getValues();

            // output-level
            if (!$this->args->isDefined('o')) {
                $args['output-level'] = 31; // default = full detail
            }

            if ($this->args->isDefined('r')) {
                $report = $args['report'];
            } else {
                $report = 'text';
            }

            $compatInfo = new PHP_CompatInfo($report, array('args' => $args));
            $compatInfo->parseData($this->dataSource, $this->options);
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
}
?>