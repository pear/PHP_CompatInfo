<?php
/**
 * The functions array script generator.
 *
 * Sources come from :
 * - http://cvs.php.net/viewcvs.cgi/phpdoc/phpbook/phpbook-xsl/version.xml
 *   revision 1.9
 * - http://cvs.php.net/viewcvs.cgi/phpdoc/funclist.txt
 *   revision 1.39
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Davey Shafik <davey@php.net>
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @ignore
 */

$GLOBALS['_PHP_COMPATINFO_FUNCS'] = array();

$funcs =& $GLOBALS['_PHP_COMPATINFO_FUNCS'];

$funcArray = 'C:\php\pear\PHP_CompatInfo\CompatInfo\func_array.php';
if (file_exists($funcArray)) {
    include_once $funcArray;
}

$xml = simplexml_load_file('C:\php\pear\PHP_CompatInfo\scripts\version.xml');

$version_pattern = '\d+(?:\.\d+)*(?:[a-zA-Z]+\d*)?';

foreach ($xml->function as $function) {
    $name = (string) $function['name'];
    $from = (string) $function['from'];

    /**
     * Match strings like :
     *  "PHP 3 &gt;= 3.0.7, PHP 4, PHP 5"          for _
     *  "PHP 5 &gt;= 5.1.0RC1"                     for date_modify
     *  "PHP 3 &gt;= 3.0.7, PHP 4 &lt;= 4.2.3"     for aspell_check
     *  "PHP 5 &gt;= 5.2.0, PECL zip:1.1.0-1.9.0"  for addfile
     */
    if (preg_match('/>= ('.$version_pattern.')/', $from, $matches)) {
        $funcs[$name]['init'] = $matches[1];
        if (preg_match('/<= ('.$version_pattern.')/', $from, $matches)) {
            $funcs[$name]['end'] = $matches[1];
        }
        if (preg_match('/(\.*)PECL ([a-zA-Z_]+):('.$version_pattern.')-('.
            $version_pattern.')/', $from, $matches)) {

            $funcs[$name]['ext']  = $matches[2];
            $funcs[$name]['pecl'] = true;
            $funcs[$name]['init'] = $matches[3];
            $funcs[$name]['end']  = $matches[4];

        } elseif (preg_match('/(\.*)PECL ([a-zA-Z_]+):('.$version_pattern.')/',
            $from, $matches)) {

            $funcs[$name]['ext']  = $matches[2];
            $funcs[$name]['pecl'] = true;
            $funcs[$name]['init'] = $funcs[$name]['end'] = $matches[3];
        }
        continue;

        /**
         * Match string like :
         *  "PHP 5 &lt;= 5.0.4"                       for php_check_syntax
         *  "PHP 4 &lt;= 4.2.3, PECL cybercash:1.18"  for cybercash_base64_decode
         */
    } elseif (preg_match('/<= ('.$version_pattern.')/', $from, $matches)) {
        $funcs[$name]['end'] = $matches[1];

        if (preg_match('/(\.*)PECL ([a-zA-Z_]+):('.$version_pattern.')-('.
            $version_pattern.')/', $from, $matches)) {

            $funcs[$name]['ext']  = $matches[2];
            $funcs[$name]['pecl'] = true;
            $funcs[$name]['init'] = $matches[3];
            $funcs[$name]['end']  = $matches[4];
            continue;

        } elseif (preg_match('/(\.*)PECL ([a-zA-Z_]+):('.$version_pattern.')/',
            $from, $matches)) {

            $funcs[$name]['ext']  = $matches[2];
            $funcs[$name]['pecl'] = true;
            $funcs[$name]['init'] = $funcs[$name]['end'] = $matches[3];
            continue;
        }

        /**
         * Match string like :
         *  "4.0.2 - 4.0.6 only"    for accept_connect
         */
    } elseif (preg_match('/('.$version_pattern.') - ('.$version_pattern.') only/',
        $from, $matches)) {
        $funcs[$name]['init'] = $matches[1];
        $funcs[$name]['end']  = $matches[2];
        continue;

        /**
         * Match string like :
         *  "PHP 3.0.5 only"    for PHP3_UODBC_FIELD_NUM
         *  "PHP 4.0.6 only"    for ocifreecoll
         */
    } elseif (preg_match('/PHP (\d)('.$version_pattern.') only/',
        $from, $matches)) {

        $funcs[$name]['init'] = $matches[1] .'.0.0';
        $funcs[$name]['end']  = $matches[2];
        continue;

        /**
         * Match string like :
         *  "PHP 5, PECL oci8:1.1-1.2.4"   for oci_error
         */
    } elseif (preg_match('/(\.*)PECL ([a-zA-Z_]+):('.$version_pattern.')-('.
        $version_pattern.')/', $from, $matches)) {

        $funcs[$name]['ext']  = $matches[2];
        $funcs[$name]['pecl'] = true;
        $funcs[$name]['init'] = $matches[3];
        $funcs[$name]['end']  = $matches[4];
        continue;

        /**
         * Match string like :
         *  "PHP 4, PHP 5, PECL mysql:1.0"  for mysql_connect
         */
    } elseif (preg_match('/(\.*)PECL ([a-zA-Z_]+):('.$version_pattern.')/',
        $from, $matches)) {

        $funcs[$name]['ext']  = $matches[2];
        $funcs[$name]['pecl'] = true;
        $funcs[$name]['init'] = $funcs[$name]['end'] = $matches[3];
        continue;
    }

    if (strpos($function['from'], 'PHP 3') !== false) {
        $funcs[$name]['init'] = '3.0.0';
        continue;
    }
    if (strpos($function['from'], 'PHP 4') !== false) {
        $funcs[$name]['init'] = '4.0.0';
        continue;
    }
    if (strpos($function['from'], 'PHP 5') !== false) {
        $funcs[$name]['init'] = '5.0.0';
        continue;
    }
}

$txt = file('C:\php\pear\PHP_CompatInfo\scripts\funclist.txt');

$i     = 0;
$limit = count($txt);

while ($i < $limit) {
    if (strpos($txt[$i], '#') !== false) {
        // bypass the start comment tag that identify PECL extensions
        // and avoid to lose the Net_Gopher ext/func
        if (strpos(strtolower($txt[$i]), 'pecl stuff') !== false) {
            $i += 1;
        }

        if (strpos(strtolower($txt[$i]), 'zend') !== false) {
            $module = 'zend';
        } else {
            $found = preg_match('@# php-src/(ext|sapi)/(.*?)/.*@',
                $txt[$i], $matches);
            if ($found) {
                $module = $matches[1] .'_'. $matches[2];
            } else {
                $found = preg_match('@# (pecl)/(.*?)/.*@', $txt[$i], $matches);
                if ($found) {
                    $module = $matches[1] .'_'. $matches[2];
                } else {
                    $module = ' ';
                }
            }
        }
        $i   += 1;
        $skip = false;
        while ($i < $limit) {
            if (strpos($txt[$i], '#') === false) {
                $name = trim($txt[$i]);
                if (empty($name)) {
                    break;
                }
                if ($skip === false) {
                    if (!isset($funcs[$name]['ext'])) {
                        $funcs[$name]['ext']  = $module;
                        $funcs[$name]['pecl'] = false;
                    }
                } else {
                    /**
                     * removed only extension methods
                     * and not basic functions of standard extension
                     * (like 'reset' on array)
                     */
                    if (version_compare($funcs[$name]['init'], '5.0.0', 'ge')) {
                        unset($funcs[$name]);
                    }
                }
            } else {
                // DO NOT KEEP extension methods
                if (strpos($txt[$i], '_methods[]') !== false) {
                    $skip = true;
                }
            }
            $i += 1;
        }
    }
    $i += 1;
}

foreach ($funcs as $key => $function) {
    if (!isset($function['init']) || ($function['init'] == '')) {
        if (substr($funcs[$key]['ext'], 0, 4) == 'pecl') {
            $funcs[$key]['init'] = '4-dev';
        } else {
            $funcs[$key]['init'] = '5-dev';
        }
    }

    if (array_key_exists('ext', $function) === false) {
        $funcs[$key]['ext'] = 'ext_standard';
    }
    if (array_key_exists('pecl', $function) === false) {
        $funcs[$key]['pecl'] = false;
    }
}

unset($funcs[""]);

file_put_contents('C:\php\pear\PHP_CompatInfo\CompatInfo\func_array.php',
"<?php
/**
 * This file was generated for PHP_CompatInfo 1.1.1 or better
 *
 * PHP versions 4 and 5
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Davey Shafik <davey@php.net>
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD
 * @version  CVS: \$Id$
 * @link     http://pear.php.net/package/PHP_CompatInfo
 * @ignore
 */

\$GLOBALS['_PHP_COMPATINFO_FUNCS'] = " .var_export($funcs, true). "
?>");
?>