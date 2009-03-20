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

/*
 * Called by "register.php" and "user/user_create_edit.php
 * 
 * Accept parameters:
 * 
 * check_row: only need when edit existing user.
 * all_html_tags: display selections in dropdown list box "HTML Tag"
 */

global $onload;
$onload = "initial();";

require(AC_INCLUDE_PATH.'header.inc.php'); 
?>

<form method="post" action="<?php $id_str = ''; if (isset($_GET['id'])) $id_str='?id='.$_GET['id']; echo $_SERVER['PHP_SELF'].$id_str; ?>" name="input_form">

<div class="input-form">
<fieldset class="group_form"><legend class="group_form"><?php echo _AC('create_edit_check'); ?></legend>

	<table class="form-data">
		<tr>
			<td colspan="2" align="left"><p><?php echo _AC('required_field_text') ;?><br /><br/></p></td>
		</tr>

		<tr>
			<th align="left"><div class="required" title="<?php echo _AC('required_field'); ?>">*</div><label for="html_tag"><?php echo _AC('html_tag'); ?></label>:</th>
			<td align="left">
				<input name="html_tag" id="html_tag" value="<?php if (isset($_POST['html_tag'])) echo stripslashes(htmlspecialchars($_POST['html_tag'])); else echo stripslashes(htmlspecialchars($this->check_row['html_tag'])); ?>" />
				<a href="<?php echo AC_BASE_HREF; ?>check/html_tag_list.php" onclick="popup('<?php echo AC_BASE_HREF; ?>check/html_tag_list.php'); return false;" title="<?php echo _AC('select_from_tag_list'); ?>"><?php echo _AC('select_from_tag_list'); ?></a>
			</td>
		</tr>

		<tr>
			<td align="left" colspan="2">
				<small>&middot; <?php echo _AC('html_tag_text'); ?></small>
			</td>
		</tr>
		
		<tr>
			<th align="left"><div class="required" title="<?php echo _AC('required_field'); ?>">*</div><label for="confidence"><?php echo _AC('error_type'); ?></label>:</th>
			<td align="left">
			<select name="confidence" id="confidence">
				<option value="-1">- <?php echo _AC('select'); ?> -</option>
				<option value="<?php echo KNOWN; ?>" <?php if ((isset($_POST['confidence']) && $_POST['confidence']==KNOWN) || (!isset($_POST['confidence']) && isset($this->check_row['confidence']) && $this->check_row['confidence'] == KNOWN)) echo 'selected="selected"'; ?>><?php echo _AC('known_problem'); ?></option>
				<option value="<?php echo LIKELY; ?>" <?php if ((isset($_POST['confidence']) && $_POST['confidence']==LIKELY) || (!isset($_POST['confidence']) && isset($this->check_row['confidence']) && $this->check_row['confidence'] == LIKELY)) echo 'selected="selected"'; ?>><?php echo _AC('likely_problem'); ?></option>
				<option value="<?php echo POTENTIAL; ?>" <?php if ((isset($_POST['confidence']) && $_POST['confidence']==POTENTIAL) || (!isset($_POST['confidence']) && isset($this->check_row['confidence']) && $this->check_row['confidence'] == POTENTIAL)) echo 'selected="selected"'; ?>><?php echo _AC('potential_problem'); ?></option>
			</select>
			</td>
		</tr>

		<tr>
			<th align="left"><div class="required" title="<?php echo _AC('required_field'); ?>">*</div><? echo _AC("open_to_public"); ?>:</th>
			<td align="left">
				<input type="radio" name="open_to_public" id="open_to_publicN" value="0" <?php if ((isset($_POST['open_to_public']) && $_POST['open_to_public']==0) || (!isset($_POST['open_to_public']) && $this->check_row['open_to_public']==0)) echo 'checked="checked"'; ?> /><label for="open_to_publicN"><?php echo _AC('no'); ?></label> 
				<input type="radio" name="open_to_public" id="open_to_publicY" value="1" <?php if ((isset($_POST['open_to_public']) && $_POST['open_to_public']==1) || (!isset($_POST['open_to_public']) && $this->check_row['open_to_public']==1)) echo 'checked="checked"'; ?> /><label for="open_to_publicY"><?php echo _AC('yes'); ?></label>
			</td>
		</tr>

		<tr>
			<th align="left"><div class="required" title="<?php echo _AC('required_field'); ?>">*</div><label for="name"><?php echo _AC('name'); ?></label>:</th>
			<td align="left"><input id="name" name="name" type="text" size="100" value="<?php if (isset($_POST['name'])) echo $_POST['name']; else echo stripslashes(htmlspecialchars(_AC($this->check_row['name']))); ?>" /></td>
		</tr>

		<tr>
			<th align="left"><div class="required" title="<?php echo _AC('required_field'); ?>">*</div><label for="err"><?php echo _AC('error'); ?></label>:</th>
			<td align="left"><input id="err" name="err" type="text" size="100" value="<?php if (isset($_POST['err'])) echo $_POST['err']; else echo stripslashes(htmlspecialchars(_AC($this->check_row['err']))); ?>" /></td>
		</tr>

		<tr>
			<th align="left"><label for="short_desc"><?php echo _AC('short_desc'); ?></label>:</th>
			<td align="left"><textarea rows="3" cols="60" name="description" id="short_desc"><?php if (isset($_POST['description'])) echo $_POST['description']; else echo _AC($this->check_row["description"]); ?></textarea></td>
		</tr>

		<tr>
			<th align="left"><label for="long_desc"><?php echo _AC('long_desc'); ?></label>:</th>
			<td align="left"><textarea rows="3" cols="60" name="long_description" id="long_desc"><?php if (isset($_POST['long_description'])) echo $_POST['long_description']; else echo _AC($this->check_row["long_description"]); ?></textarea></td>
		</tr>

		<tr>
			<th align="left"><label for="rationale"><?php echo _AC('rationale'); ?></label>:</th>
			<td align="left"><textarea rows="3" cols="60" name="rationale" id="rationale"><?php if (isset($_POST['rationale'])) echo $_POST['rationale']; else echo _AC($this->check_row["rationale"]); ?></textarea></td>
		</tr>

		<tr>
			<th align="left"><label for="how_to_repair"><?php echo _AC('how_to_repair'); ?></label>:</th>
			<td align="left"><textarea rows="3" cols="60" name="how_to_repair" id="how_to_repair"><?php if (isset($_POST['how_to_repair'])) echo $_POST['how_to_repair']; else echo _AC($this->check_row["how_to_repair"]); ?></textarea></td>
		</tr>

		<tr>
			<th align="left"><label for="repair_example"><?php echo _AC('repair_example'); ?></label>:</th>
			<td align="left"><textarea rows="3" cols="60" name="repair_example" id="repair_example"><?php if (isset($_POST['repair_example'])) echo $_POST['repair_example']; else echo _AC($this->check_row["repair_example"]); ?></textarea></td>
		</tr>
		
		<tr>
			<th align="left"><label for="note"><?php echo _AC('note'); ?></label>:</th>
			<td align="left"><textarea rows="3" cols="60" name="note" id="note"><?php if (isset($_POST['note'])) echo $_POST['note']; else echo _AC($this->check_row["note"]); ?></textarea></td>
		</tr>
		
		<tr>
			<td align="left" colspan="2"><h3><?php echo _AC('how_to_determine');?></h3></td>
		</tr>

		<tr>
			<th align="left"><label for="question"><?php echo _AC('question'); ?></label>:</th>
			<td align="left">
				<input name="question" id="question" size="100" value="<?php if (isset($_POST['question'])) echo $_POST['question']; else echo _AC($this->check_row['question']); ?>" />
			</td>
		</tr>

		<tr>
			<th align="left"><label for="decision_pass"><?php echo _AC('decision_pass'); ?></label>:</th>
			<td align="left">
				<input name="decision_pass" id="decision_pass" size="100" value="<?php if (isset($_POST['decision_pass'])) echo $_POST['decision_pass']; else echo _AC($this->check_row['decision_pass']); ?>" />
			</td>
		</tr>

		<tr>
			<th align="left"><label for="decision_fail"><?php echo _AC('decision_fail'); ?></label>:</th>
			<td align="left">
				<input name="decision_fail" id="decision_fail" size="100" value="<?php if (isset($_POST['decision_fail'])) echo $_POST['decision_fail']; else echo _AC($this->check_row['decision_fail']); ?>" />
			</td>
		</tr>

		<tr>
			<td align="left" colspan="2"><h3><?php echo _AC('steps_to_check');?></h3></td>
		</tr>

		<tr>
			<th align="left"><label for="test_procedure"><?php echo _AC('procedure'); ?></label>:</th>
			<td align="left"><textarea rows="3" cols="60" name="test_procedure" id="test_procedure"><?php if (isset($_POST['test_procedure'])) echo $_POST['test_procedure']; else echo _AC($this->check_row["test_procedure"]); ?></textarea></td>
		</tr>
		
		<tr>
			<th align="left"><label for="test_expected_result"><?php echo _AC('expected_result'); ?></label>:</th>
			<td align="left"><textarea rows="3" cols="60" name="test_expected_result" id="test_expected_result"><?php if (isset($_POST['test_expected_result'])) echo $_POST['test_expected_result']; else echo _AC($this->check_row["test_expected_result"]); ?></textarea></td>
		</tr>
		
		<tr>
			<th align="left"><label for="test_failed_result"><?php echo _AC('failed_result'); ?></label>:</th>
			<td align="left"><textarea rows="3" cols="60" name="test_failed_result" id="test_failed_result"><?php if (isset($_POST['test_failed_result'])) echo $_POST['test_failed_result']; else echo _AC($this->check_row["test_failed_result"]); ?></textarea></td>
		</tr>
		
		<?php if (isset($this->check_row)) {?>
		<tr>
			<th align="left"><? echo _AC("author"); ?>:</th>
			<td align="left"><?php echo $this->author; ?></td>
		</tr>
		<tr>
			<th align="left"><? echo _AC("date_created"); ?>:</th>
			<td align="left"><?php echo $this->check_row['create_date']; ?></td>
		</tr>
		<?php } ?>
	</table>

	<!-- section of displaying existing prerequisite checks -->
	<br/>
	<h2 id="pre_checks"><?php echo _AC('pre_checks');?></h2>
	<a href="<?php echo AC_BASE_HREF; ?>check/index.php?list=pre&cid=<?php echo $this->check_row['check_id']; ?>" onclick="popup('<?php echo AC_BASE_HREF; ?>check/index.php?list=pre&cid=<?php echo $this->check_row['check_id']; ?>'); return false;" title="<?php echo _AC('add_pre_checks'); ?>"><?php echo _AC('add_pre_checks'); ?></a>
	<table class="data" summary="" rules="rows" >
		<thead>
		<tr>
			<th align="left"><input type="checkbox" value="<?php echo _AC('select_all'); ?>" id="all" title="<?php echo _AC('select_all'); ?>" name="selectall_delchecks" onclick="CheckAll('del_checks_id[]','selectall_delchecks');" /></th>
			<th align="center"><?php echo _AC('html_tag'); ?></th>
			<th align="center"><?php echo _AC('error_type'); ?></th>
			<th align="center"><?php echo _AC('description'); ?></th>
		</tr>
		</thead>
	<?php if (is_array($this->pre_rows)) { ?>
			
		<tfoot>
			<tr>
				<td colspan="4">
					<input type="submit" name="remove" value="<?php echo _AC('remove'); ?>" />
				</td>
			</tr>
		</tfoot>

		<tbody>
	<?php foreach ($this->pre_rows as $pre_row) { ?>
		<tr onmousedown="document.input_form['del_checks_<?php echo $pre_row['check_id']; ?>'].checked = !document.input_form['del_checks_<?php echo $pre_row['check_id']; ?>'].checked; togglerowhighlight(this, 'del_checks_<?php echo $pre_row['check_id']; ?>');" id="rdel_checks_<?php echo $pre_row['check_id']; ?>">
			<td><input type="checkbox" name="del_checks_id[]" value="<?php echo $pre_row['check_id']; ?>" id="del_checks_<?php echo $pre_row['check_id']; ?>" onmouseup="this.checked=!this.checked" <?php if (is_array($_POST['del_checks_id']) && in_array($pre_row['check_id'], $_POST['del_checks_id'])) echo 'checked="checked"';?> /></td>
			<td><?php echo $pre_row['html_tag']; ?></td>
			<td><?php echo get_confidence_by_code($pre_row['confidence']); ?></td>
			<td><span class="msg"><a target="_new" href="<?php echo AC_BASE_HREF; ?>checker/suggestion.php?id=<?php echo $pre_row["check_id"]; ?>" onclick="popup('<?php echo AC_BASE_HREF; ?>checker/suggestion.php?id=<?php echo $pre_row["check_id"]; ?>'); return false;"><?php echo _AC($pre_row['name']); ?></a></span></td>
		</tr>
	<?php } // end of foreach?>
	<?php } else {// end of if?>
		<tr><td colspan="4"><?php echo _AC('none_found'); ?></td></tr>
	<?php }?>
		</tbody>
	</table>

	<!-- section of displaying checks to add 
	<div class="row">
		<h2>
			<img src="images/arrow-closed.png" alt="<?php echo _AC("expand_add_pre_checks"); ?>" title="<?php echo _AC("expand_add_pre_checks"); ?>" id="toggle_image" border="0" />
			<a href="javascript:toggleToc('div_add_checks')"><?php echo _AC("add_pre_checks"); ?></a>
		</h2>
	</div>
	
	<div id="div_add_checks">
	<?php 
	if (!is_array($this->pre_to_add_rows)){ 
		echo _AC('none_found');
	} 
	else {?>
		<table class="data" summary="" rules="rows" >
			<thead>
			<tr>
				<th align="left"><input type="checkbox" value="<?php echo _AC('select_all'); ?>" id="all" title="<?php echo _AC('select_all'); ?>" name="selectall_addchecks" onclick="CheckAll('add_checks_id[]','selectall_addchecks');" /></th>
				<th align="center"><?php echo _AC('html_tag'); ?></th>
				<th align="center"><?php echo _AC('error_type'); ?></th>
				<th align="center"><?php echo _AC('description'); ?></th>
			</tr>
			</thead>
			
			<tbody>
	<?php foreach ($this->pre_to_add_rows as $pre_to_add_row) { ?>
			<tr onmousedown="document.input_form['add_checks_<?php echo $pre_to_add_row['check_id']; ?>'].checked = !document.input_form['add_checks_<?php echo $pre_to_add_row['check_id']; ?>'].checked; togglerowhighlight(this, 'add_checks_<?php echo $pre_to_add_row['check_id']; ?>');" id="radd_checks_<?php echo $pre_to_add_row['check_id']; ?>">
				<td><input type="checkbox" name="add_checks_id[]" value="<?php echo $pre_to_add_row['check_id']; ?>" id="add_checks_<?php echo $pre_to_add_row['check_id']; ?>" onmouseup="this.checked=!this.checked" <?php if (is_array($_POST['add_checks_id']) && in_array($pre_to_add_row['check_id'], $_POST['add_checks_id'])) echo 'checked="checked"';?> /></td>
				<td><?php echo $pre_to_add_row['html_tag']; ?></td>
				<td><?php echo get_confidence_by_code($pre_to_add_row['confidence']); ?></td>
				<td><span class="msg"><a target="_blank" href="<?php echo AC_BASE_HREF; ?>checker/suggestion.php?id=<?php echo $pre_to_add_row["check_id"]; ?>"><?php echo _AC($pre_to_add_row['name']); ?></a></span></td>
			</tr>
	<?php } // end of foreach?>
			</tbody>
		</table>
	<?php } // end of if?>
	</div>
	-->
	<div class="row">
		<input type="submit" name="submit" value="<?php echo _AC('submit'); ?>" class="submit" /> 
		<input type="submit" name="cancel" value=" <?php echo _AC('cancel'); ?> "  class="submit" />
	</div>
</fieldset>

</div>
</form>

<script type="text/JavaScript">
//<!--

function initial()
{
	// hide guideline div
//	document.getElementById("div_add_checks").style.display = 'none';

	// set cursor focus
	document.input_form.html_tag.focus();
}

function CheckAll(element_name, selectall_checkbox_name) {
	for (var i=0;i<document.input_form.elements.length;i++)	{
		var e = document.input_form.elements[i];
		if ((e.name == element_name) && (e.type=='checkbox')) {
			e.checked = document.input_form[selectall_checkbox_name].checked;
			togglerowhighlight(document.getElementById("r" + e.id), e.id);
		}
	}
}

function togglerowhighlight(obj, boxid) {
	if (document.getElementById(boxid).checked) {
		obj.className = 'selected';
	} else {
		obj.className = '';
	}
}
//  End -->
//-->
</script>

<?php require(AC_INCLUDE_PATH.'footer.inc.php'); ?>