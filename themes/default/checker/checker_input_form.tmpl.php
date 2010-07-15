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

global $onload;
$onload="initial()";

include(AC_INCLUDE_PATH.'header.inc.php');

if (isset($this->error)) echo $this->error;
?>
<table style="width:100%">
<tr>
<td>
<div class="center-input-form">
<form name="input_form" enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >

	<div class="left-col" style="float:left;clear:left;"><br />
	<fieldset class="group_form"><legend class="group_form"><?php echo _AC("input"); ?></legend>

			<div  style="text-align:center;"><h2><label for="checkuri"><?php echo _AC("check_by_uri"); ?></label></h2></div>
			<div style="margin-left:auto;margin-right:auto;text-align:center;">
			<input type="text" name="uri" id="checkuri" value="<?php if (isset($_POST['uri'])) echo $_POST['uri']; else echo $this->default_uri_value; ?>" size="50"   />
			<p class="submit_button"  style="text-align:center;">
				<input type="submit" name="validate_uri" size="100" value="<?php echo _AC("check_it"); ?>" onclick="return validate_this_uri();" class="submit"/>
			</p>
			</div>

			<div style="text-align:center;"><h2><label for="checkfile"><?php echo _AC("check_by_upload"); ?></label></h2></div>

			<div  style="text-align:center;">
			<input type="hidden" name="MAX_FILE_SIZE" value="52428800" />
			<input type="file" id="checkfile" name="uploadfile" size="47" />
		
			<p class="submit_button">
				<input type="submit" name="validate_file" value="<?php echo _AC("check_it"); ?>" onclick="return validate_filename();" class="submit" />
			</p>
			</div>

		<div>
			<h2 align="left">
				<img src="images/arrow-closed.png" alt="<?php echo _AC("expand_guidelines"); ?>" title="<?php echo _AC("expand_guidelines"); ?>" id="toggle_image" border="0" />
				<a href="javascript:toggleToc('div_options')"><?php echo _AC("options"); ?></a>
			</h2>
		</div>

		<div id="div_options" style="display:none">

		<table class="data static" style="background-colour:#eeeeee;">
			<tr>
				<td>
				<input type="checkbox" name="enable_html_validation" id="enable_html_validation" value="1" <?php if (isset($_POST["enable_html_validation"])) echo 'checked="checked"'; ?> />
				<label for='enable_html_validation'><?php echo _AC("enable_html_validator"); ?></label>
				</td>
			<td>
				<input type="checkbox" name="enable_css_validation" id="enable_css_validation" value="1" <?php if (isset($_POST["enable_css_validation"])) echo 'checked="checked"'; ?> />
				<label for='enable_css_validation'><?php echo _AC("enable_css_validation"); ?></label>
				</td>
				
				<td colspan="2">
				<input type="checkbox" name="show_source" id="show_source" value="1" <?php if (isset($_POST["show_source"])) echo 'checked="checked"'; ?> />
				<label for='show_source'><?php echo _AC("show_source"); ?></label>
				</td>
				
				
			</tr>
			
			<tr>
				<td colspan="3"><h3><?php echo _AC("guidelins_to_check"); ?></h3></td>
			</tr>
<?php
$count_guidelines_in_current_row = 0;

if (is_array($this->rows))
{
	foreach ($this->rows as $id => $row)
	{
		if ($count_guidelines_in_current_row == 0 || $count_guidelines_in_current_row == $this->num_of_guidelines_per_row)
		{
			$count_guidelines_in_current_row = 0;
			echo "			<tr>\n";
		}
?>
				<td>
					<input type="checkbox" name="gid[]" id='gid_<?php echo $row["guideline_id"]; ?>' value='<?php echo $row["guideline_id"]; ?>' <?php foreach($_POST["gid"] as $gid) {if ($gid == $row["guideline_id"]) echo 'checked="checked"';} ?> />
					<label for='gid_<?php echo $row["guideline_id"]; ?>'><?php echo $row["title"]; ?></label>
				</td>
<?php
		$count_guidelines_in_current_row++;
	
		if ($count_guidelines_in_current_row == $this->num_of_guidelines_per_row)
			echo "			</tr>\n";
	
	}
}
?>
		</table>
		</div>
	</fieldset>
	</div>
</form>
<div style="float:right;margin-right:2em;clear:right;width:250px;"><br />
<a href="checker/index.php#skipads"><img src="images/clr.gif" alt="<?php echo _AC("skip_over_ads"); ?>" border="0"/></a>	
	<script type="text/javascript">
	<!--
	google_ad_client = "pub-8538177464726172";
	/* 250x250, created 3/13/09 */
	google_ad_slot = "0783349774";
	google_ad_width = 250;
	google_ad_height = 250;
	//-->
	</script>
	<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	</script>
<a name="skipads" title="passed ads"></a>
</div>

</div>
</td>
</tr>
</table>

<script type="text/JavaScript">
<!--

function initial()
{
	var div_errors = document.getElementById("errors");
	var div_error = document.getElementById("error");
	
	if (div_errors != null)
	{
		// show tab "errors", hide other tabs
		div_errors.style.display = 'block';
		document.getElementById("likely_problems").style.display = 'none';
		document.getElementById("potential_problems").style.display = 'none';
		document.getElementById("html_validation_result").style.display = 'none';
		document.getElementById("css_validation_result").style.display = 'none';
		//document.getElementById("errors_10").style.display = 'none';
		//document.getElementById("errors_11").style.display = 'none';
	//	document.getElementById("errors_12").style.display = 'none';
	//	document.getElementById("errors_13").style.display = 'none';
		
		
		
		
		// highlight tab "errors"
		document.getElementById("menu_errors").className = 'active';

		// hide button "make decision" as tab "errors" are selected
		eButtonMakeDecision = document.getElementById('make_decision');
		if (eButtonMakeDecision != null) eButtonMakeDecision.style.display = 'none';
	}
	else if (div_error == null)
		document.input_form.uri.focus();
}

String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}

// This function validates if and only if a zip file is given
function validate_filename() {
  // check file type
  var upload_file = document.input_form.uploadfile.value;
  if (!upload_file || upload_file.trim()=='') {
    alert('Please provide a html file!');
    return false;
  }
  
  file_extension = upload_file.slice(upload_file.lastIndexOf(".")).toLowerCase();
  if(file_extension != '.html' && file_extension != '.htm') {
    alert('Please upload html (or htm) file only!');
    return false;
  }
}

// This function validates if and only if a uri is given
function validate_this_uri() {
  // check uri
  var uri = document.input_form.uri.value;
  if (!uri || uri=="<?php echo $default_uri_value; ?>" ) {
    alert('Please provide a uri!');
    return false;
  }
}  
//  End -->
//-->
</script>
