<?php
/**
 * CLI Script to Check Compatibility of chunk of PHP code
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   PHP
 * @package    PHP_CompatInfo
 * @author     Davey Shafik <davey@php.net>
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.0
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/PHP_CompatInfo
 * @since      File available since Release 0.8.0
 */

require_once 'PHP/CompatInfo.php';
require_once 'Console/Getopt.php';
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
 * @example docs/examples/Cli.php Example of using PHP_CompatInfo_Cli
 *
 * @category   PHP
 * @package    PHP_CompatInfo
 * @author     Davey Shafik <davey@php.net>
 * @copyright  Copyright 2003 Davey Shafik and Synaptic Media. All Rights Reserved.
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/PHP_CompatInfo
 * @since      Class available since Release 0.8.0
 */

class PHP_CompatInfo_Cli extends PHP_CompatInfo
{
    /**
     * @var array Current CLI Flags
     */
    var $opts = array();

    /**
     * @var boolean Whether an error has occured
     */
    var $error = false;

    /**
     * @var string File to be Processed
     */
    var $file;

    /**
     * @var string Directory to be Processed
     */
    var $dir;

    /**
     * @var boolean Whether to show debug output
     */
    var $debug;

    /**
     * @var boolean Whether to recurse directories when using --dir or -d
     */
    var $recurse = true;

    /**
     * Constructor
     */
     function __construct()
     {
        $opts = Console_Getopt::readPHPArgv();
        $short_opts = 'd:f:hn';
        $long_opts = array('dir=','file=','help','debug','no-recurse');
        $this->opts = Console_Getopt::getopt($opts, $short_opts, $long_opts);
        if (PEAR::isError($this->opts)) {
            $this->error = true;
            return;
        }
        foreach ($this->opts[0] as $option) {
            switch ($option[0]) {
                case '--no-recurse':
                case 'n':
                    $this->recurse = false;
                    break;
                case '--debug':
                    $this->debug = true;
                    break;
                case '--dir':
                    $this->dir = $option[1];
                    if ($this->dir{strlen($this->dir)-1} == '/' ||
                        $this->dir{strlen($this->dir)-1} == '\\') {
                        $this->dir = substr($this->dir, 0, -1);
                    }
                    $this->dir = str_replace('\\', '/', realpath($this->dir));
                    break;
                case 'd':
                    if ($option[1]{0} == '=') {
                        $this->dir = substr($option[1], 1);
                    } else {
                        $this->dir = $option[1];
                    }

                    if ($this->dir{strlen($this->dir)-1} == '/' ||
                        $this->dir{strlen($this->dir)-1} == '\\') {
                        $this->dir = substr($this->dir, 0, -1);
                    }
                    $this->dir = str_replace('\\', '/', realpath($this->dir));
                    break;
                case '--file':
                    $this->file = $option[1];
                    break;
                case 'f':
                    if ($option[1]{0} == '=') {
                        $this->file = substr($option[1], 1);
                    } else {
                        $this->file = $option[1];
                    }
                    break;
                case 'h':
                case '--help':
                    $this->_printHelp();
                    break;
            }
        }
     }

     /**
      * PHP4 Compatible Constructor
      */
     function PHP_CompatInfo_Cli()
     {
        $this->__construct();
     }

     /**
      * Run the CLI Script
      *
      * @access public
      * @return void
      */
     function run()
     {
        if ($this->error == true) {
            echo $this->opts->message;
            $this->_printUsage();
        } else {
            if (isset($this->dir)) {
                $output = $this->_parseDir();
                echo $output;
            } elseif (isset($this->file)) {
                $output = $this->_parseFile();
                echo $output;
            } else {
                $this->_printHelp();
            }
        }
     }

     /**
      * Parse Directory Input
      *
      * @access private
      * @return boolean|string Returns Boolean False on fail
      */
      function _parseDir()
      {
        $info = $this->parseDir($this->dir,
            array('debug' => $this->debug, 'recurse_dir' => $this->recurse)
            );
        if ($info == false) {
            echo 'Failed opening directory ("' .$this->dir. '"). Please check your spelling and try again.';
            $this->_printUsage();
            return;
        }
        $table = new Console_Table();
        $table->setHeaders(array('File','Version','Extensions','Constants/Tokens'));
        if (!isset($info['extensions'][0])) {
            $ext = '';
        } else {
            $ext = array_shift($info['extensions']);
        }

        if (!isset($info['constants'][0])) {
            $const = '';
        } else {
            $const = array_shift($info['constants']);
        }
        $dir = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $this->dir);
        $table->addRow(array($dir . DIRECTORY_SEPARATOR . '*', $info['version'], $ext, $const));
        if (sizeof($info['extensions']) >= sizeof($info['constants'])) {
            foreach ($info['extensions'] as $i => $ext) {
                if (isset($info['constants'][$i])) {
                    $const = $info['constants'][$i];
                } else {
                    $const = '';
                }
                $table->addRow(array('', '', $ext, $const));
            }
        } else {
            foreach ($info['constants'] as $i => $const) {
                if (isset($info['extensions'][$i])) {
                    $ext = $info['extensions'][$i];
                } else {
                    $ext = '';
                }
                $table->addRow(array('', '', $ext, $const));
            }
        }
        unset($info['version']);
        unset($info['extensions']);
        unset($info['constants']);

        $ignored = $info['ignored_files'];

        unset($info['ignored_files']);

        foreach ($info as $file => $info) {
            if (!isset($info['extensions'][0])) {
                $ext = '';
            } else {
                $ext = array_shift($info['extensions']);
            }

            if (!isset($info['constants'][0])) {
                $const = '';
            } else {
                $const = array_shift($info['constants']);
            }
            $key = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $file);
            $table->addRow(array($file, $info['version'], $ext, $const));
            if (sizeof($info['extensions']) >= sizeof($info['constants'])) {
                foreach ($info['extensions'] as $i => $ext) {
                    if (isset($info['constants'][$i])) {
                        $const = $info['constants'][$i];
                    } else {
                        $const = '';
                    }
                    $table->addRow(array('', '', $ext, $const));
                }
            } else {
                foreach ($info['constants'] as $i => $const) {
                    if (isset($info['extensions'][$i])) {
                        $ext = $info['extensions'][$i];
                    } else {
                        $ext = '';
                    }
                    $table->addRow(array('', '', $ext, $const));
                }
            }
        }

        return $table->getTable();
    }

    /**
     * Parse File Input
     *
     * @access private
     * @return boolean|string Returns Boolean False on fail
     */
    function _parseFile()
    {
        $info = $this->parseFile($this->file, array('debug' => $this->debug));
        if ($info == false) {
            echo 'Failed opening file. Please check your spelling and try again.';
            $this->_printUsage();
            return false;
        }
        $table = new Console_Table();
        if ($this->debug == false) {
            $table->setHeaders(array('File','Version','Extensions','Constants/Tokens'));
            if (!isset($info['extensions'][0])) {
                $ext = '';
            } else {
                $ext = array_shift($info['extensions']);
            }

            if (!isset($info['constants'][0])) {
                $const = '';
            } else {
                $const = array_shift($info['constants']);
            }

            $table->addRow(array($this->file, $info['version'], $ext, $const));
            if (sizeof($info['extensions']) >= sizeof($info['constants'])) {
                foreach ($info['extensions'] as $i => $ext) {
                    if (isset($info['constants'][$i])) {
                        $const = $info['constants'][$i];
                    } else {
                        $const = '';
                    }
                    $table->addRow(array('', '', $ext, $const));
                }
            } else {
                foreach ($info['constants'] as $i => $const) {
                    if (isset($info['extensions'][$i])) {
                        $ext = $info['extensions'][$i];
                    } else {
                        $ext = '';
                    }
                    $table->addRow(array('', '', $ext, $const));
                }
            }
        } else {
            $table->setHeaders(array('File','Version','Extensions','Constants/Tokens'));

            if (!isset($info['extensions'][0])) {
                $ext = '';
            } else {
                $ext = array_shift($info['extensions']);
            }

            if (!isset($info['constants'][0])) {
                $const = '';
            } else {
                $const = array_shift($info['constants']);
            }

            $table->addRow(array($this->file, $info['version'], $ext, $const));

            if (sizeof($info['extensions']) >= sizeof($info['constants'])) {
                foreach ($info['extensions'] as $i => $ext) {
                    if (isset($info['constants'][$i])) {
                        $const = $info['constants'][$i];
                    } else {
                        $const = '';
                    }
                    $table->addRow(array('', '', $ext, $const));
                }
            } else {
                foreach ($info['constants'] as $i => $const) {
                    if (isset($info['extensions'][$i])) {
                        $ext = $info['extensions'][$i];
                    } else {
                        $ext = '';
                    }
                    $table->addRow(array('', '', $ext, $const));
                }
            }
        }

        $output = $table->getTable();

        if ($this->debug == true) {
            $output .= "\nDebug:\n\n";

            $table = new Console_Table();

            $table->setHeaders(array('Version','Function','Extension'));

            unset($info['version']);
            unset($info['constants']);
            unset($info['extensions']);

            foreach ($info as $version => $functions) {
                foreach ($functions as $func) {
                    $table->addRow(array($version, $func['function'], $func['extension']));
                }
            }

            $output .= $table->getTable();
        }

        return $output;
    }

    /**
     * Show basic Usage
     *
     * @access private
     * @return void
     */
    function _printUsage()
    {
        echo "\n";
        echo 'Usage:' . "\n";
        echo "  " .basename(__FILE__). ' --dir=DIR [--no-recurse] | --file=FILE [--debug] | [--help]';
        echo "\n";
    }

    /**
     * Show full help information
     *
     * @access private
     * @return void
     */
    function _printHelp()
    {
        $this->_printUsage();
        echo "Commands:\n";
        echo "  --file=FILE (-f) \tParse FILE to get its Compatibility Info";
        echo "\n";
        echo "  --dir=DIR (-d) \tParse DIR to get its Compatibility Info";
        echo "\n";
        echo "  --no-recurse (-n) \tDo not Recursively parse files when using --dir";
        echo "\n";
        echo "  --debug\t\tDisplay Extra (debug) Information when using --file";
        echo "\n";
        echo "  --help (-h) \t\tShow this help";
        echo "\n";
    }
}
?>