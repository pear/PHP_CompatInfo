<?php
/**
 * Html renderer for PHP_CompatInfo component.
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
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @since    File available since Release 1.8.0b4
 */

require_once 'HTML/Table.php';

/**
 * The PHP_CompatInfo_Renderer_Html class is a concrete implementation
 * of PHP_CompatInfo_Renderer abstract class. It simply display results
 * as web/html content with help of PEAR::Html_Table
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @since    Class available since Release 1.8.0b4
 */
class PHP_CompatInfo_Renderer_Html extends PHP_CompatInfo_Renderer
{
    /**
     * All console arguments that have been parsed and recognized
     *
     * @var   array
     * @since 1.8.0b4
     */
    var $args;

    /**
     * Text Renderer Class constructor (ZE1) for PHP4
     *
     * @param object &$parser Instance of the parser (model of MVC pattern)
     * @param array  $conf    A hash containing any additional configuration
     *
     * @access public
     * @since  version 1.8.0b4 (2008-06-18)
     */
    function PHP_CompatInfo_Renderer_Html(&$parser, $conf)
    {
        $this->__construct($parser, $conf);
    }

    /**
     * Text Renderer Class constructor (ZE2) for PHP5+
     *
     * @param object &$parser Instance of the parser (model of MVC pattern)
     * @param array  $conf    A hash containing any additional configuration
     *
     * @access public
     * @since  version 1.8.0b4 (2008-06-18)
     */
    function __construct(&$parser, $conf)
    {
        parent::PHP_CompatInfo_Renderer($parser, $conf);

        $args = array('summarize' => false, 'output-level' => 31);
        if (isset($conf['args']) && is_array($conf['args'])) {
            $this->args = array_merge($args, $conf['args']);
        } else {
            $this->args = $args;
        }
    }

    /**
     * Display final results
     *
     * Display final results, when data source parsing is over.
     *
     * @access public
     * @return void
     * @since  version 1.8.0b4 (2008-06-18)
     */
    function display()
    {
        $o    = $this->args['output-level'];
        $info = $this->parseData;

        $src = $this->_parser->dataSource;
        if ($src['dataType'] == 'directory') {
            $dir      = $src['dataSource'];
            $hdr_col1 = 'Directory';
        } elseif ($src['dataType'] == 'file') {
            $file     = $src['dataSource'];
            $hdr_col1 = 'File';
        } else {
            $string   = $src['dataSource'];
            $hdr_col1 = 'Source code';
        }

        $dataTable = new HTML_Table();
        $thead     =& $dataTable->getHeader();
        $tbody     =& $dataTable->getBody();
        $tfoot     =& $dataTable->getFooter();

        $hdr = array($hdr_col1);
        $atr = array('scope="col"');
        if ($o & 16) {
            $hdr[] = 'Version';
            $atr[] = 'scope="col"';
        }
        if ($o & 1) {
            $hdr[] = 'C';
            $atr[] = 'scope="col"';
        }
        if ($o & 2) {
            $hdr[] = 'Extensions';
            $atr[] = 'scope="col"';
        }
        if ($o & 4) {
            if ($o & 8) {
                $hdr[] = 'Constants/Tokens';
                $atr[] = 'scope="col"';
            } else {
                $hdr[] = 'Constants';
                $atr[] = 'scope="col"';
            }
        } else {
            if ($o & 8) {
                $hdr[] = 'Tokens';
                $atr[] = 'scope="col"';
            }
        }

        $thead->addRow($hdr, $atr);

        $ext   = implode("<br/>", $info['extensions']);
        $const = implode("<br/>", array_merge($info['constants'], $info['tokens']));
        if (isset($dir)) {
            $ds    = DIRECTORY_SEPARATOR;
            $dir   = str_replace(array('\\', '/'), $ds, $dir);
            $title = $src['dataCount'] . ' file';
            if ($src['dataCount'] > 1) {
                $title .= 's'; // plural
            }
        } elseif (isset($file)) {
            $title = '1 file';
        } else {
            $title = '1 chunk of code';
        }
        $data = array('Summary: '. $title . ' parsed');

        if ($o & 16) {
            if (empty($info['max_version'])) {
                $data[] = $info['version'];
            } else {
                $data[] = implode("<br/>", array($info['version'],
                                                $info['max_version']));
            }
        }
        if ($o & 1) {
            $data[] = $info['cond_code'][0];
        }
        if ($o & 2) {
            $data[] = $ext;
        }
        if ($o & 4) {
            if ($o & 8) {
                $data[] = $const;
            } else {
                $data[] = implode("<br/>", $info['constants']);
            }
        } else {
            if ($o & 8) {
                $data[] = implode("<br/>", $info['tokens']);
            }
        }

        // summary informations
        $tfoot->addRow($data);

        // summarize : print only summary for directory without files details
        if ($this->args['summarize'] === false && isset($dir)) {
            // display result of parsing multiple files

            unset($info['max_version']);
            unset($info['version']);
            unset($info['functions']);
            unset($info['extensions']);
            unset($info['constants']);
            unset($info['tokens']);
            unset($info['cond_code']);

            if (isset($dir)) {
                $ignored = $info['ignored_files'];
                unset($info['ignored_files']);
            }
            unset($info['ignored_functions']);
            unset($info['ignored_extensions']);
            unset($info['ignored_constants']);

            foreach ($info as $file => $info) {
                if ($info === false) {
                    continue;  // skip this (invalid) file
                }
                $ext   = implode("<br/>", $info['extensions']);
                $const = implode("<br/>", array_merge($info['constants'],
                                                      $info['tokens']));

                $file = str_replace(array('\\', '/'), $ds, $file);

                $path = dirname($file);
                $tbody->addRow(array($path), array('class' => 'dirname',
                                                   'colspan' => count($hdr)));

                $data = array(basename($file));
                if (empty($info['max_version'])) {
                    $data[] = $info['version'];
                } else {
                    $data[] = implode("<br/>",
                                      array($info['version'], $info['max_version']));
                }

                if ($o & 1) {
                    $data[] = $info['cond_code'][0];
                }
                if ($o & 2) {
                    $data[] = $ext;
                }
                if ($o & 4) {
                    if ($o & 8) {
                        $data[] = $const;
                    } else {
                        $data[] = implode("<br/>", $info['constants']);
                    }
                } else {
                    if ($o & 8) {
                        $data[] = implode("<br/>", $info['tokens']);
                    }
                }

                $tbody->addRow($data);
            }
        } elseif ($this->args['summarize'] === false && !isset($dir)) {
            // display result of parsing a single file, or a chunk of code
            if (isset($file)) {
                $path = dirname($file);
            } else {
                $path = '.';
            }
            $tbody->addRow(array($path), array('class' => 'dirname',
                                               'colspan' => count($hdr)));
            if (isset($file)) {
                $data[0] = basename($file);
            } else {
                $data[0] = htmlspecialchars('<?php ... ?>');
            }
            $tbody->addRow($data);
        } else {
            // display only result summary of parsing a data source
            if (isset($dir)) {
                $path = dirname($dir[0]);
            } elseif (isset($file)) {
                $path = dirname($file);
            } else {
                $path = '.';
            }
            $tbody->addRow(array($path), array('class' => 'dirname',
                                               'colspan' => count($hdr)));
        }

        $evenRow = array('class' => 'even');
        $oddRow  = null;
        $tbody->altRowAttributes(1, $evenRow, $oddRow, true);

        echo $this->toHtml($dataTable);
    }

    /**
     * Returns HTML code of parsing result
     *
     * @param object $obj instance of HTML_Table
     *
     * @access public
     * @return string
     * @since  version 1.8.0b4 (2008-06-18)
     */
    function toHtml($obj)
    {
        $styles = '
.outer {
  position:relative;
  padding:4em 0 7em 0;
  width:750px;
  background:#eee;
  margin:0 auto 3em auto;
  border: 1px solid #666;
}
.inner {
  overflow:auto;
  width:750px;
  height:20em;
  background:#eee;
}
.outer thead td, .outer thead th {
  background:#666;
  color: #fff;
}
.outer thead tr {
  position:absolute;
  left:0;
  top:1.5em;
  height:1.5em;
}
.outer thead td {
  text-align:center;
  font-weight:bold;
}
.outer tfoot tr {
  position:absolute;
  width:730px;
  border:0;
  bottom:0;
  left:0;
}
.outer tfoot td {
  text-align:left;
  background:#666;
  color:#fff;
}
.outer th, .outer td {
  text-align:left;
  font-family:Arial,Verdana;
}
.outer .even {
  background-color:#fff;
}
.outer td.dirname { width: 44em; color:#666; }
.outer th, .outer td { width: 18em; }
.outer th+th, .outer td+td { width: 4em; }
.outer th+th+th, .outer td+td+td { width: 2em; }
.outer th+th+th+th, .outer td+td+td+td { width: 7em; }
.outer th+th+th+th+th, .outer td+td+td+td+td { width: 13em; }
';

        $body = $obj->toHtml();

        $html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3c.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<title>PHP_CompatInfo</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<style type="text/css">
<!--
$styles
 -->
</style>
</head>
<body>
<div class="outer">
<div class="inner">
$body
</div>
</div>
</body>
</html>
HTML;
        return $html;
    }
}
?>