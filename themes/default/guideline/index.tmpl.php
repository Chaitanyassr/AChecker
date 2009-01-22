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

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<h2 align="center"><?php echo $this->title ;?></h2>

<table class="data" summary="" rules="rows">

<thead>
	<tr>
		<th scope="col">&nbsp;</th>
		<th scope="col"><?php echo _AC('title');?></th>
		<th scope="col"><?php echo _AC('description');?></th>
		<?php if ($this->showStatus) {?>
		<th scope="col"><?php echo _AC('status');?></th>
		<?}?>
	</tr>
</thead>

<tfoot>
	<tr>
		<td colspan="4">
			<?php foreach ($this->buttons as $button_text) {?>
			<input type="submit" name="<?php echo $button_text; ?>" value="<?php echo _AC($button_text); ?>" />
			<?}?>
		</td>
	</tr>
</tfoot>

<tbody>
<?php foreach ($this->rows as $row) {?>
	<tr <?php echo 'onmousedown="document.form[\'m'. $row["guideline_id"].'\'].checked = true; rowselect(this);" id="r_'. $row["guideline_id"] .'"'; ?>>
		<td><input type="radio" name="id" value="<?php echo $row["guideline_id"]; ?>"<?php echo 'id="m'. $row["guideline_id"].'"'; ?> /></td>
		<td><label for="m<?php echo $row["guideline_id"]; ?>"><?php echo $row["title"]; ?></label></td>
		<td><?php echo _AC($row['long_name']); ?></td>
		<?php if ($this->showStatus) {?>
		<td><?php if ($row['status']) echo _AC('enabled'); else echo _AC('disabled'); ?></td>
		<?}?>
	</tr>
<?}?>
</tbody>

</table>
</form>

