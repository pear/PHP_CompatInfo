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

@include_once 'D:\php\pear\PHP_CompatInfo\CompatInfo\func_array.php';

$xml = simplexml_load_file('D:\php\pear\PHP_CompatInfo\scripts\version.xml');

foreach ($xml->function as $function) {
    $name = (string) $function['name'];
    $version = false;
    if (preg_match('/= (\d\.\d\.\d)/', (string) $function['from'], $matches)) {
        $funcs[$name]['init'] = $matches[1];
        continue;
    } elseif (preg_match('/(\d\.\d\.\d) - (\d\.\d\.\d) only/', (string) $function['from'], $matches)) {
        $funcs[$name]['init'] = $matches[1];
        continue;
    } else {
        if (strpos($function['from'], '3') !== FALSE) {
            $funcs[$name]['init'] = "3.0.0";
            continue;
        }
        if (strpos($function['from'], '4') !== FALSE) {
            $funcs[$name]['init'] = "4.0.0";
            continue;
        }
        if (strpos($function['from'], '5') !== FALSE) {
            $funcs[$name]['init'] = "5.0.0";
            continue;
        }
    }
}

$txt = file('D:\php\pear\PHP_CompatInfo\scripts\funclist.txt');

$i = 0;
$limit = count($txt);

while ($i < $limit) {
    if (strpos($txt[$i], '#') !== FALSE) {
        if (strpos(strtolower($txt[$i]), 'zend') !== FALSE) {
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
            if (strpos($txt[$i], '#') === FALSE) {
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

file_put_contents('D:\php\pear\PHP_CompatInfo\CompatInfo\func_array.php',
"<?php
# This file is generated!
\$GLOBALS['_PHP_COMPATINFO_FUNCS'] = " .var_export($funcs, true). "
?>");
?>

