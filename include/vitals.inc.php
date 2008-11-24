<?php
/************************************************************************/
/* AChecker                                                             */
/************************************************************************/
/* Copyright (c) 2008 by Greg Gay, Cindy Li                             */
/* Adaptive Technology Resource Centre / University of Toronto			    */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_DEVEL', 1);
define('AT_ERROR_REPORTING', E_ALL ^ E_NOTICE); // default is E_ALL ^ E_NOTICE, use E_ALL or E_ALL + E_STRICT for developing

// Emulate register_globals off. src: http://php.net/manual/en/faq.misc.php#faq.misc.registerglobals
function unregister_GLOBALS() {
   if (!ini_get('register_globals')) { return; }

   // Might want to change this perhaps to a nicer error
   if (isset($_REQUEST['GLOBALS'])) { die('GLOBALS overwrite attempt detected'); }

   // Variables that shouldn't be unset
   $noUnset = array('GLOBALS','_GET','_POST','_COOKIE','_REQUEST','_SERVER','_ENV', '_FILES');
   $input = array_merge($_GET,$_POST,$_COOKIE,$_SERVER,$_ENV,$_FILES,isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
  
   foreach ($input as $k => $v) {
       if (!in_array($k, $noUnset) && isset($GLOBALS[$k])) { unset($GLOBALS[$k]); }
   }
}

/*
 * structure of this document (in order):
 *
 * 0. load config.inc.php
 * 1. load constants
 * 2. initilize session
 * 3. initilize db connection
 ***/

/**** 0. start system configuration options block ****/
	error_reporting(0);
	if (!defined('AT_REDIRECT_LOADED')){
		include_once(AT_INCLUDE_PATH.'config.inc.php');
	}
	error_reporting(AT_ERROR_REPORTING);

	if (!defined('AT_INSTALL') || !AT_INSTALL) {
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Pragma: no-cache');

		$relative_path = substr(AT_INCLUDE_PATH, 0, -strlen('include/'));
		header('Location: ' . $relative_path . 'install/not_installed.php');
		exit;
	}
/*** end system config block ****/

/*** 1. constants ***/
	require_once(AT_INCLUDE_PATH.'constants.inc.php');

/*** 2. initilize session ***/
	@set_time_limit(0);
	@ini_set('session.gc_maxlifetime', '36000'); /* 10 hours */
	@session_cache_limiter('private, must-revalidate');

	session_name('CheckerID');
	error_reporting(AT_ERROR_REPORTING);

	ob_start();
	session_set_cookie_params(0, $_base_path);
	session_start();
	$str = ob_get_contents();
	ob_end_clean();
	unregister_GLOBALS();

/***** end session initilization block ****/

/* 3. database connection */
if (!defined('AT_REDIRECT_LOADED')){
	require_once(AT_INCLUDE_PATH.'lib/mysql_connect.inc.php');
}
/***** end database connection ****/

/***** 4. start language block *****/
	// set current language
	require(AT_INCLUDE_PATH . 'classes/Language/LanguageManager.class.php');
	$languageManager =& new LanguageManager();

	$myLang =& $languageManager->getMyLanguage();

	if ($myLang === FALSE) {
		echo 'There are no languages installed!';
		exit;
	}
	$myLang->saveToSession();
//	if (isset($_GET['lang']) && $_SESSION['valid_user']) {
//		if ($_SESSION['course_id'] == -1) {
//			$myLang->saveToPreferences($_SESSION['login'], 1);	//1 for admin			
//		} else {
//			$myLang->saveToPreferences($_SESSION['member_id'], 0);	//0 for non-admin
//		}
//	}
//	$myLang->sendContentTypeHeader();

	/* set right-to-left language */
	$rtl = '';
	if ($myLang->isRTL()) {
		$rtl = 'rtl_'; /* basically the prefix to a rtl variant directory/filename. eg. rtl_tree */
	}
/***** end language block ****/

/* 5. load common libraries */
	require(AT_INCLUDE_PATH.'lib/output.inc.php');           /* output functions */
/***** end load common libraries ****/

 /**
 * This function is used for printing variables for debugging.
 * @access  public
 * @param   mixed $var	The variable to output
 * @param   string $title	The name of the variable, or some mark-up identifier.
 * @author  Joel Kronenberg
 */
function debug($var, $title='') {
	if (!defined('AT_DEVEL') || !AT_DEVEL) {
		return;
	}
	
	echo '<pre style="border: 1px black solid; padding: 0px; margin: 10px;" title="debugging box">';
	if ($title) {
		echo '<h4>'.$title.'</h4>';
	}
	
	ob_start();
	print_r($var);
	$str = ob_get_contents();
	ob_end_clean();

	$str = str_replace('<', '&lt;', $str);

	$str = str_replace('[', '<span style="color: red; font-weight: bold;">[', $str);
	$str = str_replace(']', ']</span>', $str);
	$str = str_replace('=>', '<span style="color: blue; font-weight: bold;">=></span>', $str);
	$str = str_replace('Array', '<span style="color: purple; font-weight: bold;">Array</span>', $str);
	echo $str;
	echo '</pre>';
}

 /**
 * This function is used for checking if the given $uri is valid.
 * @access  public
 * @param   string $uri  The uri address
 * @author  Cindy Qi Li
 */
function is_uri_valid($uri)
{
	$connection = @file_get_contents($uri);
	
	if (!$connection) 
		return false;
	else
		return true;
}


if ( get_magic_quotes_gpc() == 1 ) {
	$addslashes   = 'my_add_null_slashes';
	$stripslashes = 'stripslashes';
} else {
	$addslashes   = 'mysql_real_escape_string';
	$stripslashes = 'my_null_slashes';
}

?>
