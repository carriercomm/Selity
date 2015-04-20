<?php
/**
 * Selity - A server control panel
 *
 * @copyright 	2001-2006 by moleSoftware GmbH
 * @copyright 	2006-2008 by ispCP | http://isp-control.net
 * @copyright	2012-2015 by Selity
 * @link 		http://selity.org
 * @author 		ispCP Team
 *
 * @license
 *   This program is free software; you can redistribute it and/or modify it under
 *   the terms of the MPL General Public License as published by the Free Software
 *   Foundation; either version 1.1 of the License, or (at your option) any later
 *   version.
 *   You should have received a copy of the MPL Mozilla Public License along with
 *   this program; if not, write to the Open Source Initiative (OSI)
 *   http://opensource.org | osi@opensource.org
 */

require '../include/selity-lib.php';

check_login(__FILE__);

$tpl = new pTemplate();
$tpl->define_dynamic('page', Config::get('RESELLER_TEMPLATE_PATH') . '/alias_edit.tpl');
$tpl->define_dynamic('page_message', 'page');
$tpl->define_dynamic('logged_from', 'page');

$theme_color = Config::get('USER_INITIAL_THEME');

$tpl->assign(array(
	'TR_PAGE_TITLE'		=> tr('Selity - Manage Domain Alias/Edit Alias'),
	'THEME_COLOR_PATH'	=> '../themes/'.$theme_color,
	'THEME_CHARSET'		=> tr('encoding'),
	'ISP_LOGO'			=> get_logo($_SESSION['user_id'])
));

/*
 *
 * static page messages.
 *
 */
$tpl->assign(array(
	'TR_MANAGE_DOMAIN_ALIAS'	=> tr('Manage domain alias'),
	'TR_EDIT_ALIAS'				=> tr('Edit domain alias'),
	'TR_ALIAS_NAME'				=> tr('Alias name'),
	'TR_DOMAIN_IP'				=> tr('Domain IP'),
	'TR_FORWARD'				=> tr('Forward to URL'),
	'TR_MODIFY'					=> tr('Modify'),
	'TR_CANCEL'					=> tr('Cancel'),
	'TR_ENABLE_FWD'				=> tr('Enable Forward'),
	'TR_ENABLE'					=> tr('Enable'),
	'TR_DISABLE'				=> tr('Disable'),
	'TR_FWD_HELP'				=> tr('A forward URL has to start with "http://"')
));

gen_reseller_mainmenu($tpl, Config::get('RESELLER_TEMPLATE_PATH') . '/main_menu_users_manage.tpl');
gen_reseller_menu($tpl, Config::get('RESELLER_TEMPLATE_PATH') . '/menu_users_manage.tpl');

gen_logged_from($tpl);

// "Modify" button has ben pressed
if (isset($_POST['uaction']) && ('modify' === $_POST['uaction'])) {
	if (isset($_SESSION['edit_ID'])) {
		$editid = $_SESSION['edit_ID'];
	} else if (isset($_GET['edit_id'])) {
		$editid = (int) $_GET['edit_id'];
	} else {
		unset($_SESSION['edit_ID']);
		set_page_message(tr('Domain not modified!'));
		header('Location: alias.php');
		die();
	}
	// Save data to db
	if (check_fwd_data($tpl, $editid)) {
		set_page_message(tr('Domain modified!'));
		header('Location: alias.php');
		die();
	}
} else {
	// Get user id that come for edit
	if (isset($_GET['edit_id'])) {
		$editid = (int) $_GET['edit_id'];
	}

	$_SESSION['edit_ID'] = $editid;
	$tpl->assign('PAGE_MESSAGE', '');
}

gen_editalias_page($tpl, $editid);

$tpl->parse('PAGE', 'page');
$tpl->prnt();

if (configs::getInstance()->GUI_DEBUG)
	dump_gui_debug();

unset_messages();

// Begin function block

// Show user data
function gen_editalias_page(&$tpl, $edit_id) {
	$sql = Database::getInstance();

	$reseller_id = $_SESSION['user_id'];

	$query = '
		select
			t1.*
		from
			domain_aliasses as t1
		LEFT JOIN
			admin as t2
		ON
			t1.admin_id = t2.admin_id
		where
			t1.alias_id = ?
		and
			t2.created_by = ?
	';

	$res = exec_query($sql, $query, array($edit_id, $reseller_id));

	if ($res->RecordCount() == 0) {
		set_page_message(tr('User does not exist or you do not have permission to access this interface!'));
		header('Location: alias.php');
		die();
	}

	$data = $res->FetchRow();
	// Get ip-data
	$ipres = exec_query($sql, 'select * from server_ips where ip_id = ?', array($data['alias_ips']));
	$ipdat = $ipres->FetchRow();
	$ip_data = $ipdat['ip_number'] . ' (' . $ipdat['ip_alias'] . ')';

	if (isset($_POST['uaction']) && ($_POST['uaction'] == 'modify'))
		$url_forward = decode_idna($_POST['forward']);
	else
		$url_forward = decode_idna($data['url_forward']);

	if ($data['url_forward'] == 'no') {
		$check_en = '';
		$check_dis = 'checked';
		$url_forward = '';
	} else {
		$check_en = 'checked';
		$check_dis = '';
	}
	// Fill in the fileds
	$tpl->assign(array(
		'ALIAS_NAME'	=> decode_idna($data['alias_name']),
		'DOMAIN_IP'		=> $ip_data,
		'FORWARD'		=> $url_forward,
		'CHECK_EN'		=> $check_en,
		'CHECK_DIS'		=> $check_dis,
		'ID'			=> $edit_id
	));
} // End of gen_editalias_page()

// Check input data
function check_fwd_data(&$tpl, $alias_id) {
	$sql = Database::getInstance();

	$reseller_id = $_SESSION['user_id'];

	$query = '
		select
			t1.*
		from
			domain_aliasses as t1
		LEFT JOIN
			admin as t2
		ON
			t1.admin_id = t2.admin_id
		where
			t1.alias_id = ?
		and
			t2.created_by = ?
	';

	$res = exec_query($sql, $query, array($alias_id, $reseller_id));

	if ($res->RecordCount() == 0) {
		set_page_message(tr('User does not exist or you do not have permission to access this interface!'));
		header('Location: alias.php');
		die();
	}

	$forward_url = encode_idna($_POST['forward']);
	$status = $_POST['status'];
	// unset errors
	$ed_error = '_off_';
	$admin_login = '';

	if ($status != '0') {
		if (!chk_forward_url($forward_url)) {
			$ed_error = tr('Incorrect forward syntax');
		}
		if (!preg_match('/\/$/', $forward_url)) {
			$forward_url .= '/';
		}
	}

	if ($ed_error === '_off_') {
		if ($_POST['status'] == 0) {
			$forward_url = 'no';
		}

		$query = '
			UPDATE
				domain_aliasses
			SET
				url_forward = ?,
				alias_status = ?
			WHERE
				alias_id = ?
		';
		exec_query($sql, $query, array($forward_url, Config::get('ITEM_CHANGE_STATUS'), $alias_id));

		$query = '
			UPDATE
				subdomain_alias
			SET
				subdomain_alias_status = ?
			WHERE
				alias_id = ?
		';
		exec_query($sql, $query, array(Config::get('ITEM_CHANGE_STATUS'), $alias_id));

		send_request();

		write_log($_SESSION['user_logged'].': changes domain alias forward: ' . $res->fields['alias_name']);
		unset($_SESSION['edit_ID']);
		$tpl->assign('MESSAGE', '');
		return true;
	} else {
		$tpl->assign('MESSAGE', $ed_error);
		$tpl->parse('PAGE_MESSAGE', 'page_message');
		return false;
	}
}

