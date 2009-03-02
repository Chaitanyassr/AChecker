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

/**
* AccessibilityRpt
* Base Class for outputing accessibility validation report
* This class returns accessibility validation report
* @access	public
* @author	Cindy Qi Li
* @package checker
*/
if (!defined("AC_INCLUDE_PATH")) die("Error: AC_INCLUDE_PATH is not defined.");

define(IS_ERROR, 1);
define(IS_WARNING, 2);
define(IS_INFO, 3);

class AccessibilityRpt {

	// all private
	var $errors;                         // an array, output of AccessibilityValidator -> getValidationErrorRpt
	var $user_link_id;                   // user_links.user_link_id; default to ''
	var $format;                         // display format. default to "html". Could be "html", "rest"
	var $show_decision;                  // 'true' or 'false'. default to 'false'. show decision choices or not.
	
	var $num_of_errors;                  // Number of known errors. (db: checks.confidence = "Known")
	var $num_of_likely_problems;         // Number of likely errors. (db: checks.confidence = "Likely")
	var $num_of_potential_problems;      // Number of potential errors. (db: checks.confidence = "Potential")
	
	var $num_of_no_decisions;            // Number of likely/potential errors that decisions have not been made
	var $num_of_made_decisions;            // Number of likely/potential errors that decisions have been made
	
	var $rpt_errors;                     // <DIV> section of errors
	var $rpt_likely_problems;            // <DIV> section of likely problems
	var $rpt_potential_problems;         // <DIV> section of potential problems
	
	/**
	* public
	* $errors: an array, output of AccessibilityValidator -> getValidationErrorRpt
	* $type: html
	*/
	function AccessibilityRpt($errors, $user_link_id = '')
	{
		$this->errors = $errors;
		$this->user_link_id = $user_link_id;
		$this->show_decision = 'false';           // set default "show decision choices" to false
		
		$this->num_of_errors = 0;
		$this->num_of_likely_problems = 0;
		$this->num_of_potential_problems = 0;
		
		$this->rpt_errors = "";
		$this->rpt_likely_problems = "";
		$this->rpt_potential_problems = "";
	}
	
	/**
	* public 
	* set show decision choices
	*/
	public function setShowDecisions($showDecisions)
	{
		$this->show_decision = $showDecisions;
	}

	/**
	* public 
	* set display format
	*/
	public function getShowDecisions()
	{
		return $this->show_decision;
	}

	/**
	* public 
	* return validation error report in html
	*/
	public function getErrorRpt()
	{
		return $this->rpt_errors;
	}

	/**
	* public 
	* return validation likely problem report in html
	*/
	public function getLikelyProblemRpt()
	{
		return $this->rpt_likely_problems;
	}

	/**
	* public 
	* return validation error report in html
	*/
	public function getPotentialProblemRpt()
	{
		return $this->rpt_potential_problems;
	}

	/**
	* public 
	* return number of known errors
	*/
	public function getNumOfErrors()
	{
		return $this->num_of_errors;
	}
	
	/**
	* public 
	* return number of known errors
	*/
	public function getNumOfLikelyProblems()
	{
		return $this->num_of_likely_problems;
	}
	
	/**
	* public 
	* return number of known errors
	*/
	public function getNumOfPotentialProblems()
	{
		return $this->num_of_potential_problems;
	}

}
?>  
