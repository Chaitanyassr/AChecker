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

if (AC_INCLUDE_PATH !== 'NULL') {
	$db = @mysql_connect(DB_HOST . ':' . DB_PORT, DB_USER, DB_PASSWORD);
	if (!$db) {
		/* AC_ERROR_NO_DB_CONNECT */
		require_once(AC_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
		$err =& new ErrorHandler();
		trigger_error('VITAL#Unable to connect to db.', E_USER_ERROR);
		exit;
	}
	if (!@mysql_select_db(DB_NAME, $db)) {
		require_once(AC_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
		$err =& new ErrorHandler();
		trigger_error('VITAL#DB connection established, but database "'.DB_NAME.'" cannot be selected.',
						E_USER_ERROR);
		exit;
	}
}
?>