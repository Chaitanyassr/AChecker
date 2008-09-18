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

$lang_charset = "UTF-8";

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 TRANSITIONAL//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 

<head>
	<title>ATRC Web Accessibility Checker</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $lang_charset; ?>" />
	<meta name="Generator" content="Checker - Copyright 2008 by http://checker.atrc.utoronto.ca" />
	<base href="<?php echo AT_BASE_HREF; ?>" />
	<link rel="shortcut icon" href="<?php echo $_base_href; ?>images/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="forms.css" type="text/css" />
	<link rel="stylesheet" href="styles.css" type="text/css" />

	<script type="text/javascript">
	//<!--
	var newwindow;
	function popup(url) 
	{
		newwindow=window.open(url,'popup','height=600,width=800,scrollbars=yes,resizable=yes');
		if (window.focus) {newwindow.focus()}
	}
	
	function initial()
	{
		document.input_form.uri.focus();
		
		// hide guideline div
		document.getElementById("div_guidelines").style.display = 'none';
		
		var div_error = document.getElementById("errors");
		
		if (div_error != null)
		{
			// show tab "errors", hide other tabs
			div_error.style.display = 'block';
			document.getElementById("likely_problems").style.display = 'none';
			document.getElementById("potential_problems").style.display = 'none';
			document.getElementById("html_validation_result").style.display = 'none';

			// highlight tab "errors"
			document.getElementById("menu_errors").className = 'active';
		}
	}
	
	function toggleToc(objId) {
		var toc = document.getElementById(objId);
		if (toc == null) return;

		if (toc.style.display == 'none')
		{
			toc.style.display = '';
			document.getElementById("toggle_image").src = "images/arrow-open.png";
			document.getElementById("toggle_image").alt = "Collapse";
			document.getElementById("toggle_image").title = "Collapse Getting Started";
		}
		else
		{
			toc.style.display = 'none';
			document.getElementById("toggle_image").src = "images/arrow-closed.png";
			document.getElementById("toggle_image").alt = "Expand";
			document.getElementById("toggle_image").title = "Expand Getting Started";
		}
	}
	//-->
	</script>

</head>

<body onload="initial();">

	<div id="banner" style="padding-top:4px;padding-left:4px; vertical-align:middle;">
		<a href="http://www.atutor.ca/achecker/"><img width="145" src="images/header_logo_checker.gif" height="43" alt="AChecker" style="border:none;" /></a>
		<h1 style="vertical-align:super; ">Web Accessibility Checker<span id="versioninfo">Version 0.1 Beta</span>
		</h1>
	</div>
