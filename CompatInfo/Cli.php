<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Davey Shafik <davey@php.net>                                |
// +----------------------------------------------------------------------+
//
// $Id$

/**
 * CLI Script to Check Compatibility of chunk of PHP code
 * @package PHP_CompatInfo
 * @category PHP
 */
 
/**
 * CLI Script to Check Compatibility of chunk of PHP code
 *
 * @package PHP_CompatInfo
 * @author Davey Shafik <davey@php.net>
 * @copyright Copyright 2003 Davey Shafik and Synaptic Media. All Rights Reserved.
 */
 
class PHP_CompatInfo_Cli {
	
	var $opts = array();
	
	var $error = false;
	
	var $compat;
	
	var $file;
	
	var $dir;
	
	var $debug;
	
	/**
	 * Constructor
	 */
	 
	 function __construct() {
	 	require_once 'Console/GetOpt.php';
	 	$stdin = new Console_Getopt();
	 	$opts = Console_Getopt::readPHPArgv();
	 	$short_opts = 'd:f:hi';
	 	$long_opts = array('dir=','file=','help','debug');
	 	$this->opts = Console_Getopt::getopt($opts,$short_opts,$long_opts);
 		require_once 'PEAR.php';
// 		var_dump($this->opts);
	 	if (PEAR::isError($this->opts)) {
	 		$this->error = true;
	 		return;
	 	}
	 	require_once 'PHP/CompatInfo.php';
	 	$this->compat = new PHP_CompatInfo();
		foreach ($this->opts[0] as $option) {
			switch ($option[0]) {
				case '--debug':
					$this->debug = true;
					break;
				case '--dir':
					$this->dir = $option[1];
					$this->_parseFolder();
					break;
				case 'd':
					$this->dir = substr($option[1],1);
					$this->_parseFolder();
					break;
				case '--file':
				case 'f':
					$this->file = $option[1];
					break;
			}
		}
	 }
	 
	 /**
	  * PHP4 Compatible Constructor
	  */
	 
	 function PHP_CompatInfo_Cli() {
	 	$this->__construct();
	 }
	 
	 /**
	  * Run the CLI Script
	  */
	  
	 function run() {
	 	if ($this->error == true) {
	 		echo $this->opts->message;
	 		$this->_printUsage();
	 	}
	 }
	 
	 /**
	  * Parse Directory Input
	  *
	  * @access private
	  * @return boolean|string Returns Boolean False on fail
	  */
	  
	  function _parseFolder() {
	  	require_once 'Console/Table.php';
	  	$info = $this->compat->parseFolder($this->dir,array('debug' => $this->debug));
	  	if ($info == false) {
	  		echo 'Failed opening directory. Please check your spelling and try again.';
	  		$this->_printUsage();
	  		return;
	  	}
	    $table = new Console_Table();
	    if ($this->debug == true) {
	    	$table->setHeaders(array('File','Function','Version','Extension'));
	    } else {
	    	$table->setHeaders(array('File','Version','Extension'));
	    }
	  	foreach ($info as $key => $i) {
	  		if (($key != 'version') && ($key != 'extensions')) {
	  			foreach ($i as $file_name => $file) {
	  				if ($this->debug == true) {
	  					foreach ($file as $version => $function) {
	  						if (($key != 'version') && ($key != 'extensions')) {
	  							$table->addRow(array($file_name,$function,$version,''));
	  						}
	  					}
	  					$table->addRow($file_name,'',$file['version'],'');
	  					foreach ($file['extensions'] as $ext) {
	  						$table->addRow($file_name,'','',$ext);
	  					}
	  				}
	  			}
	  		}
	  	}
	}
	
	function _printUsage() {
		echo "\n";
		echo 'Usage:' . "\n";
	 	echo "  " .basename(__FILE__). ' --dir=DIR|--file=FILE [--debug]';
	}
}

?>