<?php
require 'c:\web\php-cvs\pear\PHP_CompatInfo\data\func_array.php';

$xml = simplexml_load_file('c:\web\php-cvs\phpdoc\xsl\version.xml');

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

$txt = file('c:\web\php-cvs\phpdoc\funclist.txt');

for ($i = 0; $i < sizeof($txt); $i++) {
    if (strpos($txt[$i], '#') !== FALSE) {
        if (strpos(strtolower($txt[$i]), 'zend') !== FALSE) {
            $module = 'zend';
        } else {
            preg_match('@# php-src/(ext|sapi)/(.*?)/.*@', $txt[$i], $matches);
            $module = $matches[1] .'_'. $matches[2];
        }
        $i += 1;
        while (strpos($txt[$i], '#') === FALSE && ($i < sizeof($txt))) {
            if ($txt[$i] != '') {
                $name = trim($txt[$i]);
                if (!isset($funcs[$name]['ext'])) {
                    $funcs[$name]['ext'] = $module;
                }
            }
            $i += 1;
        }
        $i -= 1;
    }
}

foreach ($funcs as $key => $function) {
    if (!isset($function['init']) || ($function['init'] == '')) {
        $funcs[$key]['init'] = '5-dev';
    }
    if ($function['init']{0} == 3) {
        unset($funcs[$key]);
    }
}

unset($funcs[""]);

file_put_contents('c:\web\php-cvs\pear\PHP_CompatInfo\data\func_array.php', 
"<?php
# This file is generated!
\$funcs = " .var_export($funcs, true). "
?>");
?>