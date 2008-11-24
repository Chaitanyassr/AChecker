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

if (!defined("AT_INCLUDE_PATH")) die("Error: AT_INCLUDE_PATH is not defined in checker_input_form.php.");

if (!isset($aValidator) && !isset($htmlValidator)) die(_AC("no_instance"));

if (isset($aValidator))
{
	// find out selected guidelines
	foreach ($_POST["gid"] as $gid)
		$gids .= $gid . ",";
	
	$sql = "select title
					from ". TABLE_PREFIX ."guidelines
					where guideline_id in (" . substr($gids, 0, -1) . ")
					order by title";
	$result	= mysql_query($sql, $db) or die(mysql_error());
	
	while ($row = mysql_fetch_assoc($result))
	{
		$guidelines .= $row["title"]. ", ";
	}
	$guidelines = substr($guidelines, 0, -2); // remove ending space and ,
	
	$num_of_total_a_errors = $aValidator->getNumOfValidateError();

	if ($num_of_total_a_errors > 0)
	{
		include(AT_INCLUDE_PATH. "classes/AccessibilityRpt.class.php");

		$a_rpt = new AccessibilityRpt($aValidator->getValidationErrorRpt());
		
		$num_of_errors = $a_rpt->getNumOfErrors();
		$num_of_likely_problems = $a_rpt->getNumOfLikelyProblems();
		$num_of_potential_problems = $a_rpt->getNumOfPotentialProblems();
	}

}

if (isset($htmlValidator))
{
	$num_of_html_errors = $htmlValidator->getNumOfValidateError();
}
?>

<div id="output_div" class="output-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AC("accessibility_review"); ?></legend>
	<h3 class="indent"><?php echo _AC("accessibility_review") . ' ('. _AC("guidelines"). ': '.$guidelines. ')'; ?></h3>

	<div id="topnavlistcontainer">
		<ul id="topnavlist">
				<li><a href="index.php#output_div" accesskey="1" title="<?php echo _AC("known_problems"); ?> Alt+1" id="menu_errors" onclick="showDiv('errors');"><?php echo _AC("known_problems"); ?> <span class="small_font">(<?php echo $num_of_errors; ?>)</span></a></li>
				<li><a href="index.php#output_div" accesskey="2" title="<?php echo _AC("likely_problems"); ?> Alt+2" id="menu_likely_problems" onclick="showDiv('likely_problems');"><?php echo _AC("likely_problems"); ?> <span class="small_font">(<?php echo $num_of_likely_problems; ?>)</span></a></li>
				<li><a href="index.php#output_div" accesskey="3" title="<?php echo _AC("potential_problems"); ?> Alt+3" id="menu_potential_problems" onclick="showDiv('potential_problems');"><?php echo _AC("potential_problems"); ?> <span class="small_font">(<?php echo $num_of_potential_problems; ?>)</span></a></li>
				<li><a href="index.php#output_div" accesskey="4" title="<?php echo _AC("html_validation_result"); ?> Alt+4" id="menu_html_validation_result" onclick="showDiv('html_validation_result');"><?php echo _AC("html_validation_result"); ?> <span class="small_font"><?php if (isset($_POST["enable_html_validation"])) echo "(".$num_of_html_errors.")"; ?></span></a></li>
		</ul>
	</div>

	<div id="errors">
<?php

if (isset($aValidator))
{
	if ($num_of_errors > 0)
		echo $a_rpt->getErrorRpt();
	else
		echo "<span class='congrats_msg'><img src='images/feedback.gif' />  ". _AC("congrats_no_known") ."</span>";
}

?>
	</div>

	<div id="likely_problems">
<?php

if (isset($aValidator))
{
	if ($num_of_likely_problems > 0)
		echo $a_rpt->getLikelyProblemRpt();
	else
		echo "<span class='congrats_msg'><img src='images/feedback.gif' />  ". _AC("congrats_no_likely") ."</span>";
}

?>
	</div>

	<div id="potential_problems">
<?php

if (isset($aValidator))
{
	if ($num_of_potential_problems > 0)
		echo $a_rpt->getPotentialProblemRpt();
	else
		echo "<span class='congrats_msg'><img src='images/feedback.gif' />  ". _AC("congrats_no_potential") ."</span>";
}

?>
	</div>

	<div id="html_validation_result">
<?php
if (isset($htmlValidator))
{
	echo '		<ol><li class="msg_err">'. _AC("html_validator_provided_by") .'</li></ol>'. "\n";
	
	if ($htmlValidator->containErrors())
		echo $htmlValidator->getErrorMsg();
	else
	{
		if ($num_of_html_errors > 0)
			echo $htmlValidator->getValidationRpt();
		else
			echo "<span class='congrats_msg'><img src='images/feedback.gif' />  ". _AC("congrats_html_validation") ."</span>";
	}
}
else
	echo '<span class="info_msg"><img src="images/info.png" width="15" height="15"/>  '._AC("html_validator_disabled").'</span>';
?>
	</div>

</div>

<script language="JavaScript" type="text/javascript">
<!--

// show and highlight the given div, hide other output divs. 
function showDiv(divName)
{
	// all ids of dives to hide/show
	var allDivIDs = new Array("errors", "likely_problems", "potential_problems", "html_validation_result");
	var i;
	
	for (i in allDivIDs)
	{
		if (allDivIDs[i] == divName)
		{
			document.getElementById(allDivIDs[i]).style.display = 'block';
			eval('document.getElementById("menu_'+ allDivIDs[i] +'").className = "active"');
		}
		else
		{
			document.getElementById(allDivIDs[i]).style.display = 'none';
			eval('document.getElementById("menu_'+ allDivIDs[i] +'").className = ""');
		}
	}
}
//-->
</script>
