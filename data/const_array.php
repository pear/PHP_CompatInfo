<?php
$const['tokens'][] = T_FILE;
$const['tokens'][] = T_LINE;
$const['tokens'][] = T_FUNC_C;
$const['tokens'][] = T_CLASS_C;
$const['tokens'][] = T_METHOD_C;
$const[T_FILE]['init'] = '4.0.0';
$const[T_FILE]['name'] = '__FILE__';
$const[T_LINE]['init'] = '4.0.0';
$const[T_LINE]['name'] = '__LINE__';
$const[T_FUNC_C]['init'] = '4.3.0';
$const[T_FUNC_C]['name'] = '__FUNCTION__';
$const[T_CLASS_C]['init'] = '4.3.0';
$const[T_CLASS_C]['name'] = '__CLASS__';
@$const[T_METHOD_C]['name'] = '__METHOD__';
@$const[T_METHOD_C]['init'] = '5.0.0';
?>