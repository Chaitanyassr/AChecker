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

global $addslashes;

include_once(AC_INCLUDE_PATH.'classes/Utility.class.php');
include_once(AC_INCLUDE_PATH.'classes/DAO/UserLinksDAO.class.php');
?>

<div id="output_div" class="validator-output-form">

<?php 
if (isset($this->aValidator) && $this->a_rpt->getShowDecisions() == 'true')
{
	$sessionID = Utility::getSessionID();
	
	$userLinksDAO = new UserLinksDAO();
	$userLinksDAO->setLastSessionID($this->a_rpt->getUserLinkID(), $sessionID);
	
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">'."\n\r";
	echo '<input type="hidden" name="jsessionid" value="'.$sessionID.'" />'."\n\r";
	echo '<input type="hidden" name="uri" value="'.$addslashes($_POST["uri"]).'" />'."\n\r";
	echo '<input type="hidden" name="output" value="html" />'."\n\r";
	echo '<input type="hidden" name="validate_uri" value="1" />'."\n\r";

	foreach ($_POST['gid'] as $gid)
		echo '<input type="hidden" name="gid[]" value="'.$gid.'" />'."\n\r";
}
?>
	<fieldset class="group_form"><legend class="group_form"><?php echo _AC("accessibility_review"); ?></legend>
	<h3><?php echo _AC("accessibility_review") . ' ('. _AC("guidelines"). ': '.$this->guidelines. ')'; ?></h3>

	<div class="topnavlistcontainer">
		<ul class="topnavlist">
			<li><a href="checker/index.php#output_div" accesskey="1" title="<?php echo _AC("known_problems"); ?> Alt+1" id="menu_errors" onclick="showDiv('errors');"><?php echo _AC("known_problems"); ?> <span class="small_font">(<?php echo $this->num_of_errors; ?>)</span></a></li>
			<li><a href="checker/index.php#output_div" accesskey="2" title="<?php echo _AC("likely_problems"); ?> Alt+2" id="menu_likely_problems" onclick="showDiv('likely_problems');"><?php echo _AC("likely_problems"); ?> <span class="small_font">(<?php echo $this->num_of_likely_problems_no_decision; ?>)</span></a></li>
			<li><a href="checker/index.php#output_div" accesskey="3" title="<?php echo _AC("potential_problems"); ?> Alt+3" id="menu_potential_problems" onclick="showDiv('potential_problems');"><?php echo _AC("potential_problems"); ?> <span class="small_font">(<?php echo $this->num_of_potential_problems_no_decision; ?>)</span></a></li>
			<li><a href="checker/index.php#output_div" accesskey="4" title="<?php echo _AC("html_validation_result"); ?> Alt+4" id="menu_html_validation_result" onclick="showDiv('html_validation_result');"><?php echo _AC("html_validation_result"); ?> <span class="small_font"><?php if (isset($_POST["enable_html_validation"])) echo "(".$this->num_of_html_errors.")"; ?></span></a></li>
		</ul>
	</div>

	<div id="errors" style="margin-top:1em">
<?php

if (isset($this->aValidator))
{
	if ($this->num_of_errors > 0)
		echo $this->a_rpt->getErrorRpt();
	else
		echo "<span class='congrats_msg'><img src='".AC_BASE_HREF."images/feedback.gif' alt='"._AC("feedback")."' />  ". _AC("congrats_no_known") ."</span>";
}

?>
	</div>

	<div id="likely_problems" style="margin-top:1em">
<?php

if (isset($this->aValidator))
{
	if ($this->num_of_likely_problems > 0)
		echo $this->a_rpt->getLikelyProblemRpt();
	else
		echo "<span class='congrats_msg'><img src='".AC_BASE_HREF."images/feedback.gif' alt='"._AC("feedback")."' />  ". _AC("congrats_no_likely") ."</span>";
}

?>
	</div>

	<div id="potential_problems" style="margin-top:1em">
<?php

if (isset($this->aValidator))
{
	if ($this->num_of_potential_problems > 0)
		echo $this->a_rpt->getPotentialProblemRpt();
	else
		echo "<span class='congrats_msg'><img src='".AC_BASE_HREF."images/feedback.gif' alt='"._AC("feedback")."' />  ". _AC("congrats_no_potential") ."</span>";
}

?>
	</div>

	<div id="html_validation_result" style="margin-top:1em">
<?php
if (isset($this->htmlValidator))
{
	echo '		<ol><li class="msg_err">'. _AC("html_validator_provided_by") .'</li></ol>'. "\n";
	
	if ($this->htmlValidator->containErrors())
		echo $this->htmlValidator->getErrorMsg();
	else
	{
		if ($this->num_of_html_errors > 0)
			echo $this->htmlValidator->getValidationRpt();
		else
			echo "<span class='congrats_msg'><img src='".AC_BASE_HREF."images/feedback.gif' alt='"._AC("feedback")."' />  ". _AC("congrats_html_validation") ."</span>";
	}
}
else
	echo '<span class="info_msg"><img src="'.AC_BASE_HREF.'images/info.png" width="15" height="15" alt="'._AC("info").'"/>  '._AC("html_validator_disabled").'</span>';
?>
	</div>
	</fieldset>

<?php 
if (isset($this->aValidator) && $this->a_rpt->getShowDecisions() == 'true')
{
	if ($this->a_rpt->getNumOfNoDecisions() > 0)
	{
		echo '<div align="center"><input type="submit" name="make_decision" value="'._AC('make_decision').'" style="align:center" /></div>';
	}
	echo '</form>';
}
?>
</div>

<?php if (isset($_POST['show_source']) && isset($this->aValidator)) {?>
<div id="source" class="validator-output-form">
<h3><?php echo _AC('source');?></h3>
<p><?php echo _AC('source_note');?></p>

<?php echo $this->a_rpt->getSourceRpt();?>
</div>
<?php }?>

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
