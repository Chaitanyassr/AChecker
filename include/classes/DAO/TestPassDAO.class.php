<?php
/************************************************************************/
/* AChecker                                                             */
/************************************************************************/
/* Copyright (c) 2008 by Greg Gay, Cindy Li                             */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

/**
* DAO for "test_pass" table
* @access	public
* @author	Cindy Qi Li
* @package	DAO
*/

if (!defined('AC_INCLUDE_PATH')) exit;

require_once(AC_INCLUDE_PATH. 'classes/DAO/DAO.class.php');

class TestPassDAO extends DAO {

	/**
	* Create a new entry
	* @access  public
	* @param   $checkID
	*          $nextCheckID
	* @return  created row : if successful
	*          false : if not successful
	* @author  Cindy Qi Li
	*/
	public function Create($checkID, $nextCheckID)
	{
		$sql = "INSERT INTO ".TABLE_PREFIX."test_pass (check_id, next_check_id) 
		        VALUES (".$checkID.", ".$nextCheckID.")";
		return $this->execute($sql);
	}
	
	/**
	* Delete by primary key
	* @access  public
	* @param   $checkID
	*          $nextCheckID
	* @return  true : if successful
	*          false : if unsuccessful
	* @author  Cindy Qi Li
	*/
	public function Delete($checkID, $nextCheckID)
	{
		$sql = "DELETE FROM ".TABLE_PREFIX."test_pass 
		         WHERE check_id=".$checkID." AND next_check_id=".$nextCheckID;
		return $this->execute($sql);
	}
	
	/**
	* Delete next checks by given check ID
	* @access  public
	* @param   $checkID
	* @return  true : if successful
	*          false : if unsuccessful
	* @author  Cindy Qi Li
	*/
	public function DeleteByCheckID($checkID)
	{
		$sql = "DELETE FROM ".TABLE_PREFIX."test_pass WHERE check_id=".$checkID;
		return $this->execute($sql);
	}
	
	/**
	* Return next check IDs by given check ID
	* @access  public
	* @param   $checkID
	* @return  table rows : if successful
	*          false : if unsuccessful
	* @author  Cindy Qi Li
	*/
	public function getNextChecksByCheckID($checkID)
	{
		$sql = "SELECT * FROM ".TABLE_PREFIX."checks 
		         WHERE check_id in (SELECT next_check_id 
		                              FROM ".TABLE_PREFIX."test_pass 
		                             WHERE check_id=".$checkID.")";
		return $this->execute($sql);
	}
	
}
?>