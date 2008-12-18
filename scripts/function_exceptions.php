<?php
/**
 * Predefined Functions official version known for PHP_CompatInfo 1.9.0a1 or better
 *
 * Sources come from :
 * - http://cvs.php.net/viewvc.cgi/phpdoc/phpbook/phpbook-xsl/version.xml
 *   revision 1.12
 * - http://cvs.php.net/viewvc.cgi/phpdoc/funclist.txt
 *   revision 1.39
 *
 * PHP versions 5
 *
 * @category PHP
 * @package  PHP_CompatInfo
 * @author   Davey Shafik <davey@php.net>
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/PHP_CompatInfo
 */

$function_exceptions = array();

$xml = simplexml_load_file(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'version.xml');

$version_pattern = '\d+(?:\.\d+)*(?:[a-zA-Z]+\d*)?';

foreach ($xml->function as $function) {
    $name = (string) $function['name'];
    if (strpos($name, '::') !== false) {
        // Do not keep extension methods class
        continue;
    }
    $from = (string) $function['from'];

    /**
     * Match strings like :
     *  "PHP 3 &gt;= 3.0.7, PHP 4, PHP 5"          for _
     *  "PHP 5 &gt;= 5.1.0RC1"                     for date_modify
     *  "PHP 3 &gt;= 3.0.7, PHP 4 &lt;= 4.2.3"     for aspell_check
     *  "PHP 5 &gt;= 5.2.0, PECL zip:1.1.0-1.9.0"  for addfile
     */
    if (preg_match('/>= ('.$version_pattern.')/', $from, $matches)) {
        $init = $matches[1];
        if (preg_match('/<= ('.$version_pattern.')/', $from, $matches)) {
            $end = $matches[1];
        } else {
            $end = false;
        }
        if (preg_match('/(\.*)PECL ([a-zA-Z_]+):('.$version_pattern.')-('.
            $version_pattern.')/', $from, $matches)) {

            $ext  = $matches[2];
            $function_exceptions[$ext][$name]['init'] = $matches[3];
            $function_exceptions[$ext][$name]['end']  = $matches[4];
            $function_exceptions[$ext][$name]['ext']  = $ext;
            $function_exceptions[$ext][$name]['pecl'] = true;

        } elseif (preg_match('/(\.*)PECL ([a-zA-Z_]+):('.$version_pattern.')/',
            $from, $matches)) {

            $ext  = $matches[2];
            $function_exceptions[$ext][$name]['init'] = $matches[3];
            $function_exceptions[$ext][$name]['end']  = $matches[3];
            $function_exceptions[$ext][$name]['ext']  = $ext;
            $function_exceptions[$ext][$name]['pecl'] = true;
        } else {
            $ext = 'standard';
            $function_exceptions[$ext][$name]['init'] = $init;
            if ($end !== false) {
                $function_exceptions[$ext][$name]['end'] = $end;
            }
            $function_exceptions[$ext][$name]['ext']  = $ext;
            $function_exceptions[$ext][$name]['pecl'] = false;
        }
        continue;

        /**
         * Match string like :
         *  "PHP 5 &lt;= 5.0.4"                       for php_check_syntax
         *  "PHP 4 &lt;= 4.2.3, PECL cybercash:1.18"  for cybercash_base64_decode
         */
    } elseif (preg_match('/<= ('.$version_pattern.')/', $from, $matches)) {
        $end = $matches[1];

        if (preg_match('/(\.*)PECL ([a-zA-Z_]+):('.$version_pattern.')-('.
            $version_pattern.')/', $from, $matches)) {

            $ext  = $matches[2];
            $function_exceptions[$ext][$name]['init'] = $matches[3];
            $function_exceptions[$ext][$name]['end']  = $matches[4];
            $function_exceptions[$ext][$name]['ext']  = $ext;
            $function_exceptions[$ext][$name]['pecl'] = true;
            continue;

        } elseif (preg_match('/(\.*)PECL ([a-zA-Z_]+):('.$version_pattern.')/',
            $from, $matches)) {

            $ext  = $matches[2];
            $function_exceptions[$ext][$name]['init'] = $matches[3];
            $function_exceptions[$ext][$name]['end']  = $matches[3];
            $function_exceptions[$ext][$name]['ext']  = $ext;
            $function_exceptions[$ext][$name]['pecl'] = true;
            continue;
        }
        $ext = 'standard';
        $function_exceptions[$ext][$name]['end']  = $end;
        $function_exceptions[$ext][$name]['ext']  = $ext;
        $function_exceptions[$ext][$name]['pecl'] = false;

        /**
         * Match string like :
         *  "4.0.2 - 4.0.6 only"    for accept_connect
         */
    } elseif (preg_match('/('.$version_pattern.') - ('.$version_pattern.') only/',
        $from, $matches)) {

        $ext = 'standard';
        $function_exceptions[$ext][$name]['init'] = $matches[1];
        $function_exceptions[$ext][$name]['end']  = $matches[2];
        $function_exceptions[$ext][$name]['ext']  = $ext;
        $function_exceptions[$ext][$name]['pecl'] = false;
        continue;

        /**
         * Match string like :
         *  "PHP 3.0.5 only"    for PHP3_UODBC_FIELD_NUM
         *  "PHP 4.0.6 only"    for ocifreecoll
         */
    } elseif (preg_match('/PHP (\d)('.$version_pattern.') only/',
        $from, $matches)) {

        $ext = 'standard';
        $function_exceptions[$ext][$name]['init'] = $matches[1] .'.0.0';
        $function_exceptions[$ext][$name]['end']  = $matches[2];
        $function_exceptions[$ext][$name]['ext']  = $ext;
        $function_exceptions[$ext][$name]['pecl'] = false;
        continue;

        /**
         * Match string like :
         *  "PHP 5, PECL oci8:1.1-1.2.4"   for oci_error
         */
    } elseif (preg_match('/(\.*)PECL ([a-zA-Z_]+):('.$version_pattern.')-('.
        $version_pattern.')/', $from, $matches)) {

        $ext = $matches[2];
        $function_exceptions[$ext][$name]['init'] = $matches[3];
        $function_exceptions[$ext][$name]['end']  = $matches[4];
        $function_exceptions[$ext][$name]['ext']  = $ext;
        $function_exceptions[$ext][$name]['pecl'] = true;
        continue;

        /**
         * Match string like :
         *  "PHP 4, PHP 5, PECL mysql:1.0"  for mysql_connect
         */
    } elseif (preg_match('/(\.*)PECL ([a-zA-Z_]+):('.$version_pattern.')/',
        $from, $matches)) {

        $ext  = $matches[2];
        $function_exceptions[$ext][$name]['init'] = $matches[3];
        $function_exceptions[$ext][$name]['end']  = $matches[3];
        $function_exceptions[$ext][$name]['ext']  = $ext;
        $function_exceptions[$ext][$name]['pecl'] = true;
        continue;
    }

    $ext = 'standard';
    if (strpos($function['from'], 'PHP 3') !== false) {
        $function_exceptions[$ext][$name]['init'] = '3.0.0';
        $function_exceptions[$ext][$name]['ext']  = $ext;
        $function_exceptions[$ext][$name]['pecl'] = false;
        continue;
    }
    if (strpos($function['from'], 'PHP 4') !== false) {
        $function_exceptions[$ext][$name]['init'] = '4.0.0';
        $function_exceptions[$ext][$name]['ext']  = $ext;
        $function_exceptions[$ext][$name]['pecl'] = false;
        continue;
    }
    if (strpos($function['from'], 'PHP 5') !== false) {
        $function_exceptions[$ext][$name]['init'] = '5.0.0';
        $function_exceptions[$ext][$name]['ext']  = $ext;
        $function_exceptions[$ext][$name]['pecl'] = false;
        continue;
    }
}

$txt = file(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'funclist.txt');

$i     = 0;
$limit = count($txt);

while ($i < $limit) {
    if (strpos($txt[$i], '#') !== false) {

        $found = preg_match('@# php-src/(ext|sapi)/(.*?)/.*@',
            $txt[$i], $matches);
        if ($found) {
            $module = $matches[2];
            if ($matches[1] == 'ext') {
                $sapi   = false;
            } else {
                $sapi   = true;
            }
            $skip = false;

        } else {
            $skip = true;
        }
        $i   += 1;
        while ($i < $limit) {
            if (strpos($txt[$i], '#') === false) {
                $name = trim($txt[$i]);
                if (empty($name)) {
                    break;
                }
                if ($skip === false) {
                    $ext = 'standard';
                    if ((isset($function_exceptions[$ext][$name])
                        && $ext != $module) || ($sapi == true)) {
                        /* re-affect the function
                           from 'standard' module to the right extension */

                        $function_exceptions[$module][$name] = $function_exceptions[$ext][$name];
                        if ($sapi == true) {
                            $function_exceptions[$module][$name]['sapi'] = $module;
                        } else {
                            $function_exceptions[$module][$name]['ext'] = $module;
                        }
                        if ($sapi == false) {
                            unset($function_exceptions[$ext][$name]);
                        }
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
?>