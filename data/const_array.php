<?php
$GLOBALS['const'] = array (
  'tokens' => 
    array (
    0 => 363,
    1 => 362,
    2 => 361,
    3 => 359,
    4 => 360,
    5 => 345,
    6 => 337,
    7 => 344,
    8 => 288,
    9 => 343,
    10 => 342,
    11 => 341,
    12 => 338,
    13 => 336,
    14 => 298,
    15 => 352,
    16 => 354,
  ),
  363 => 
  array (
    'init' => '4.0.0',
    'name' => '__FILE__',
  ),
  362 => 
  array (
    'init' => '4.0.0',
    'name' => '__LINE__',
  ),
  361 => 
  array (
    'init' => '4.3.0',
    'name' => '__FUNCTION__',
  ),
  359 => 
  array (
    'init' => '4.3.0',
    'name' => '__CLASS__',
  ),
  360 => 
  array (
    'name' => '__METHOD__',
    'init' => '5.0.0',
  ),
  345 => 
  array (
    'init' => '5.0.0',
    'name' => 'abstract',
  ),
  337 => 
  array (
    'init' => '5.0.0',
    'name' => 'catch',
  ),
  344 => 
  array (
    'init' => '5.0.0',
    'name' => 'final',
  ),
  288 => 
  array (
    'init' => '5.0.0',
    'name' => 'instanceof',
  ),
  343 => 
  array (
    'init' => '5.0.0',
    'name' => 'private',
  ),
  342 => 
  array (
    'init' => '5.0.0',
    'name' => 'protected',
  ),
  341 => 
  array (
    'init' => '5.0.0',
    'name' => 'public',
  ),
  338 => 
  array (
    'init' => '5.0.0',
    'name' => 'throw',
  ),
  336 => 
  array (
    'init' => '5.0.0',
    'name' => 'try',
  ),
  298 => 
  array (
    'init' => '5.0.0',
    'name' => 'clone',
  ),
  352 => 
  array (
    'init' => '5.0.0',
    'name' => 'interface',
  ),
  354 => 
  array (
    'init' => '5.0.0',
    'name' => 'implements',
  ),
);

$php5_only = array(
                298 => 'T_CLONE',
                360 => '__METHOD__',
                343 => 'T_PRIVATE',
                342 => 'T_PUBLIC',
                341 => 'T_PROTECTED',
                338 => 'T_THROW',
                337 => 'T_CATCH',
                336 => 'T_TRY',
                352 => 'T_INTERFACE',
                345 => 'T_ABSTRACT',
                288 => 'T_INSTANCEOF',
                344 => 'T_FINAL',
                 );


if (!defined('T_CLONE')) {
    foreach ($php5_only as $i => $token) {
        unset($GLOBALS['const']['tokens'][$i]);
    }
}
?>