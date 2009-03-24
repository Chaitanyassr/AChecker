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

define('AC_INCLUDE_PATH', '../include/');
include(AC_INCLUDE_PATH.'vitals.inc.php');
include(AC_INCLUDE_PATH.'classes/DAO/ChecksDAO.class.php');
include(AC_INCLUDE_PATH.'classes/DAO/CheckPrerequisitesDAO.class.php');
include(AC_INCLUDE_PATH.'classes/DAO/TestPassDAO.class.php');

// initialize constants
$results_per_page = 50;

$dao = new DAO();
$checkPrerequisitesDAO = new CheckPrerequisitesDAO();
$testPassDAO = new TestPassDAO();

// handle submit
if ((isset($_GET['edit']) || isset($_GET['edit_function'])|| isset($_GET['delete']) || isset($_GET['add'])) && !isset($_GET['id']) ) 
{
	$msg->addError('SELECT_ONE_ITEM');
} else if (isset($_GET['edit'], $_GET['id'])) {
	header('Location: check_create_edit.php?id='.$_GET['id']);
	exit;
} else if (isset($_GET['edit_function'], $_GET['id'])) {
	header('Location: check_function_edit.php?id='.$_GET['id']);
	exit;
} else if ( isset($_GET['delete'], $_GET['id'])) {
	header('Location: check_delete.php?id='.$_GET['id']);
	exit;
} else if (isset($_GET['add'], $_GET['id']) ) {
	if (is_array($_GET['id']))
	{
		if ($_GET['list'] == 'pre')
		{
			foreach ($_GET['id'] as $pre_check_id)
			{
				$checkPrerequisitesDAO->Create($_GET['cid'], $pre_check_id);
			}
		}

		if ($_GET['list'] == 'next')
		{
			foreach ($_GET['id'] as $next_check_id)
			{
				$testPassDAO->Create($_GET['cid'], $next_check_id);
			}
		}
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		
		// force refresh parent window
		$javascript_run_now = '<script language="JavaScript">
<!--
window.opener.location.href = window.opener.location.href;
//-->
</script>';
	}
} else if (isset($_GET['edit']) || isset($_GET['delete']) || isset($_GET['edit_function']) || isset($_GET['add'])) {
	$msg->addError('NO_ITEM_SELECTED');
} 


// page initialize
if ($_GET['reset_filter']) {
	unset($_GET);
}

$page_string = '';
$orders = array('asc' => 'desc', 'desc' => 'asc');
$cols   = array('html_tag' => 1, 'public_field' => 1, 'confidence' => 1, 'description' => 1, 'open_to_public' => 1);

if (isset($_GET['asc'])) {
	$order = 'asc';
	$col   = isset($cols[$_GET['asc']]) ? $_GET['asc'] : 'html_tag';
} else if (isset($_GET['desc'])) {
	$order = 'desc';
	$col   = isset($cols[$_GET['desc']]) ? $_GET['desc'] : 'html_tag';
} else {
	// no order set
	$order = 'asc';
	$col   = 'html_tag';
}

// initialize default search values
if (!isset($_GET['html_tag']) || $_GET['html_tag'] == '') $_GET['html_tag'] = '-1';
if (!isset($_GET['confidence']) || $_GET['confidence'] == '') $_GET['confidence'] = '-1';
if (!isset($_GET['open_to_public']) || $_GET['open_to_public'] == '') $_GET['open_to_public'] = '-1';

if ($_GET['html_tag'] && $_GET['html_tag'] <> -1) {
	$condition = " html_tag = '".$_GET['html_tag']."'";
	$page_string .= SEP.'html_tag='.urlencode($_GET['html_tag']);
}

if (isset($_GET['confidence']) && $_GET['confidence'] <> -1) {
	$_GET['confidence'] = intval($_GET['confidence']);
	$page_string .= SEP.'confidence='.intval($_GET['confidence']);

	if ($_GET['confidence'] <> -1) 
	{
		if ($condition <> '') $condition .= ' AND';
		$condition .= ' confidence = ' . intval($_GET['confidence']);
	}
}

if (isset($_GET['open_to_public']) && $_GET['open_to_public'] <> -1) {
	$_GET['open_to_public'] = intval($_GET['open_to_public']);
	$page_string .= SEP.'open_to_public='.intval($_GET['open_to_public']);

	if ($_GET['open_to_public'] <> -1)
	{
		if ($condition <> '') $condition .= ' AND';
		$condition .= ' open_to_public = ' . intval($_GET['open_to_public']);
	}
}

// when only list the checks that are
if (isset($_GET['list']))
{
	if ($_GET['list'] == 'pre')
	{
		$existing_rows = $checkPrerequisitesDAO->getPreChecksByCheckID($_GET['cid']);
	}
	if ($_GET['list'] == 'next')
	{
		$existing_rows = $testPassDAO->getNextChecksByCheckID($_GET['cid']);
	}
	// get checks that are open to public and not in guideline
	unset($str_existing_checks);
	if (is_array($existing_rows))
	{
		foreach($existing_rows as $existing_row)
			$str_existing_checks .= $existing_row['check_id'] .',';
		$str_existing_checks = substr($str_existing_checks, 0, -1);
	}
	
	if ($condition <> '') $condition .= ' AND';
	$condition .= " open_to_public=1";
	if ($str_existing_checks <> '') $condition .= " AND check_id NOT IN (".$str_existing_checks.")";
}

if ($condition == '') $condition = '1';

$sql = "SELECT COUNT(check_id) AS cnt FROM ".TABLE_PREFIX."checks WHERE $condition";

$rows = $dao->execute($sql);
$num_results = $rows[0]['cnt'];

$num_pages = max(ceil($num_results / $results_per_page), 1);
$page = intval($_GET['p']);
if (!$page) {
	$page = 1;
}	
$count  = (($page-1) * $results_per_page) + 1;
$offset = ($page-1)*$results_per_page;

if ( isset($_GET['apply_all']) && $_GET['change_status'] >= -1) {
	$offset = 0;
	$results_per_page = 999999;
}

$checksDAO = new ChecksDAO();

$sql = "SELECT * 
          FROM ".TABLE_PREFIX."checks
          WHERE $condition ORDER BY $col $order LIMIT $offset, $results_per_page";
$check_rows = $dao->execute($sql);


// if prerequisite or next checks are inserted into db successfully, 
// $javascript_run_now is set to refresh parent window 
if (isset($javascript_run_now)) $savant->assign('javascript_run_now', $javascript_run_now);

$savant->assign('check_rows', $check_rows);
$savant->assign('all_html_tags', $checksDAO->getAllHtmlTags());
$savant->assign('results_per_page', $results_per_page);
$savant->assign('num_results', $num_results);
$savant->assign('col_counts', $col_counts);
$savant->assign('page',$page);
$savant->assign('page_string', $page_string);
$savant->assign('orders', $orders);
$savant->assign('order', $order);
$savant->assign('col', $col);

if (isset($_GET['list']))
{
	$savant->assign('row_button_type', 'checkbox');
	$savant->assign('buttons', array('add'));
}
else
{
	$savant->assign('row_button_type', 'radio');
	$savant->assign('buttons', array('edit', 'edit_function','delete'));
}

$savant->display('check/index.tmpl.php');

?>
