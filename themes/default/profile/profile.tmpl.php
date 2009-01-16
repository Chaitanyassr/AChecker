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

require(AC_INCLUDE_PATH.'header.inc.php'); 
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<input name="password_error" type="hidden" />
<input type="hidden" name="form_password_hidden" value="" />

<div class="center-input-form">
<fieldset class="group_form"><legend class="group_form"><?php echo _AC('edit_profile'); ?></legend>

	<table align="center">
		<tr>
			<td colspan="2" align="left"><p><?php echo _AC('required_field_text') ;?><br /><br /><br /></p></td>
		</tr>

		<tr>
			<td align="left">&nbsp;&nbsp;&nbsp;&nbsp;<label for="login"><?php echo _AC('login_name'); ?></label>:</td>
			<td align="left"><?php echo stripslashes(htmlspecialchars($_POST['login'])); ?></td>
		</tr>

		<tr><td><br /></td></tr>

		<tr>
			<td align="left"><div class="required" title="<?php echo _AC('required_field'); ?>">*</div><label for="first_name"><?php echo _AC('first_name'); ?></label>:</td>
			<td align="left"><input id="first_name" name="first_name" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['first_name'])); ?>" /></td>
		</tr>

		<tr>
			<td align="left"><div class="required" title="<?php echo _AC('required_field'); ?>">*</div><label for="last_name"><?php echo _AC('last_name'); ?></label>:</td>
			<td align="left"><input id="last_name" name="last_name" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['last_name'])); ?>" /></td>
		</tr>

		<tr>
			<td colspan="2"><p class="submit_button">
				<input type="submit" name="submit" value="<?php echo _AC('save'); ?>" class="submit" /> 
				<input type="submit" name="cancel" value=" <?php echo _AC('cancel'); ?> "  class="submit" />
			</td>
		</tr>
	</table>
</fieldset>

</div>
</form>

<?php require(AC_INCLUDE_PATH.'footer.inc.php'); ?>