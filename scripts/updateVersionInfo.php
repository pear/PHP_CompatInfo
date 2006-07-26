<?php
/**
 * The functions array script generator.
 *
 * Sources come from :
 * - http://cvs.php.net/viewcvs.cgi/phpdoc/xsl/version.xml?revision=1.19&view=markup
 * - http://cvs.php.net/viewcvs.cgi/phpdoc/funclist.txt?revision=1.35&view=markup
 *
 * @version    $Id$
 * @author     Davey Shafik <davey@php.net>
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @package    PHP_CompatInfo
 * @ignore
 */

@require_once 'C:\php\pear\PHP_CompatInfo\CompatInfo\func_array.php';

$xml = simplexml_load_file('C:\php\pear\PHP_CompatInfo\scripts\version.xml');

$version_pattern = '\d+(?:\.\d+)*(?:[a-zA-Z]+\d*)?';

foreach ($xml->function as $function) {
    $name = (string) $function['name'];
    $from = (string) $function['from'];

    /**
     * Match strings like :
     *  "PHP 3 &gt;= 3.0.7, PHP 4, PHP 5"       for _
     *  "PHP 5 &gt;= 5.1.0RC1"                  for date_modify
     *  "PHP 3 &gt;= 3.0.7, PHP 4 &lt;= 4.2.3"  for aspell_check
     */
    if (preg_match('/>= ('.$version_pattern.')/', $from, $matches)) {
        $funcs[$name]['init'] = $matches[1];
        if (preg_match('/<= ('.$version_pattern.')/', $from, $matches)) {
            $funcs[$name]['end'] = $matches[1];
        }
        continue;

        /**
         * Match string like :
         *  "PHP 5 &lt;= 5.0.4"    for php_check_syntax
         */
    } elseif (preg_match('/<= ('.$version_pattern.')/', $from, $matches)) {
        $funcs[$name]['end'] = $matches[1];

        /**
         * Match string like :
         *  "4.0.2 - 4.0.6 only"    for accept_connect
         */
    } elseif (preg_match('/('.$version_pattern.') - ('.$version_pattern.') only/', $from, $matches)) {
        $funcs[$name]['init'] = $matches[1];
        $funcs[$name]['end']  = $matches[2];
        continue;

        /**
         * Match string like :
         *  "PHP 33.0.5 only"    for PHP3_UODBC_FIELD_NUM
         *  "PHP 44.0.6 only"    for ocifreecoll
         */
    } elseif (preg_match('/PHP (\d)('.$version_pattern.') only/', $from, $matches)) {
        $funcs[$name]['init'] = $matches[1] .'.0.0';
        $funcs[$name]['end']  = $matches[2];
        continue;
    }

    if (strpos($function['from'], 'PHP 3') !== false) {
        $funcs[$name]['init'] = "3.0.0";
        continue;
    }
    if (strpos($function['from'], 'PHP 4') !== false) {
        $funcs[$name]['init'] = "4.0.0";
        continue;
    }
    if (strpos($function['from'], 'PHP 5') !== false) {
        $funcs[$name]['init'] = "5.0.0";
        continue;
    }
}

$txt = file('C:\php\pear\PHP_CompatInfo\scripts\funclist.txt');

$i     = 0;
$limit = count($txt);

while ($i < $limit) {
    if (strpos($txt[$i], '#') !== false) {
        if (strpos(strtolower($txt[$i]), 'zend') !== false) {
            $module = 'zend';
        } else {
            $found = preg_match('@# php-src/(ext|sapi)/(.*?)/.*@', $txt[$i], $matches);
            if ($found) {
                $module = $matches[1] .'_'. $matches[2];
            } else {
                $module = ' ';
            }
        }
        $i += 1;
        while ($i < $limit) {
            if (strpos($txt[$i], '#') === false) {
                $name = trim($txt[$i]);
                if (empty($name)) {
                    break;
                }
                if (!isset($funcs[$name]['ext'])) {
                    $funcs[$name]['ext'] = $module;
                }
            }
            $i += 1;
        }
    }
    $i += 1;
}

foreach ($funcs as $key => $function) {
    if (!isset($function['init']) || ($function['init'] == '')) {
        $funcs[$key]['init'] = '5-dev';
    }
}

unset($funcs[""]);

file_put_contents('C:\php\pear\PHP_CompatInfo\CompatInfo\func_array.php',
"<?php
# This file is generated!
\$GLOBALS['_PHP_COMPATINFO_FUNCS'] = " .var_export($funcs, true). "
?>");
?>

