<?php
/**
 * Selity - A server control panel
 *
 * @copyright 	2001-2006 by moleSoftware GmbH
 * @copyright 	2006-2008 by ispCP | http://isp-control.net
 * @copyright	2012-2014 by Selity
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

// Begin page line
require '../include/selity-lib.php';

check_login(__FILE__);

$tpl = new pTemplate();
$tpl->define_dynamic('page', Config::get('RESELLER_TEMPLATE_PATH') . '/hosting_plan.tpl');
$tpl->define_dynamic('page_message', 'page');
$tpl->define_dynamic('logged_from', 'page');
// Table with hosting plans
$tpl->define_dynamic('hp_table', 'page');
$tpl->define_dynamic('hp_entry', 'hp_table');
$tpl->define_dynamic('hp_delete', 'page');
$tpl->define_dynamic('hp_menu_add', 'page');

$theme_color = Config::get('USER_INITIAL_THEME');

$tpl->assign(
		array(
			'TR_RESELLER_MAIN_INDEX_PAGE_TITLE' => tr('Selity - Reseller/Main Index'),
			'THEME_COLOR_PATH' => "../themes/$theme_color",
			'THEME_CHARSET' => tr('encoding'),
			'ISP_LOGO' => get_logo($_SESSION['user_id'])
			)
		);

/*
 *
 * static page messages.
 *
 */

gen_reseller_mainmenu($tpl, Config::get('RESELLER_TEMPLATE_PATH') . '/main_menu_hosting_plan.tpl');
gen_reseller_menu($tpl, Config::get('RESELLER_TEMPLATE_PATH') . '/menu_hosting_plan.tpl');

gen_logged_from($tpl);

gen_hp_table($tpl, $_SESSION['user_id']);

$tpl->assign(
		array(
			'TR_HOSTING_PLANS' => tr('Hosting plans'),
			'TR_PAGE_MENU' => tr('Manage hosting plans'),
			'TR_PURCHASING' => tr('Purchasing'),
			'TR_ADD_HOSTING_PLAN' => tr('Add hosting plan'),
			'TR_TITLE_ADD_HOSTING_PLAN' => tr('Add new user hosting plan'),
			'TR_BACK' => tr('Back'),
			'TR_TITLE_BACK' => tr('Return to previous menu'),
			'TR_MESSAGE_DELETE' => tr('Are you sure you want to delete %s?', true, '%s')
			)
		);

gen_hp_message($tpl);
gen_page_message($tpl);

$tpl->parse('PAGE', 'page');
$tpl->prnt();

// BEGIN FUNCTION DECLARE PATH

function gen_hp_message (&$tpl) {
	// global $externel_event, $hp_added, $hp_deleted, $hp_updated;
	global $external_event;

	if (isset($_SESSION["hp_added"]) && $_SESSION["hp_added"] == '_yes_') {
		$external_event = '_on_';
		set_page_message(tr('Hosting plan added!'));
		unset($_SESSION["hp_added"]);
		if (isset($GLOBALS['hp_added']))
			unset($GLOBALS['hp_added']);
	} else if (isset($_SESSION["hp_deleted"]) && $_SESSION["hp_deleted"] == '_yes_') {
		$external_event = '_on_';
		set_page_message(tr('Hosting plan deleted!'));
		unset($_SESSION["hp_deleted"]);
		if (isset($GLOBALS['hp_deleted']))
			unset($GLOBALS['hp_deleted']);
	} else if (isset($_SESSION["hp_updated"]) && $_SESSION["hp_updated"] == '_yes_') {
		$external_event = '_on_';
		set_page_message(tr('Hosting plan updated!'));
		unset($_SESSION["hp_updated"]);
		if (isset($GLOBALS['hp_updated']))
			unset($GLOBALS['hp_updated']);
	}
	else if (isset($_SESSION["hp_deleted_ordererror"]) && $_SESSION["hp_deleted_ordererror"] == '_yes_') {
		//$external_event = '_on_';
		set_page_message(tr('Hosting plan can\'t be deleted, there are orders!'));
		unset($_SESSION["hp_deleted_ordererror"]);
	}
} // End of gen_hp_message()

// Extract and show data for hosting plants
function gen_hp_table(&$tpl, $reseller_id) {
	$sql = Database::getInstance();
	global $external_event;

	if (Config::exists('HOSTING_PLANS_LEVEL') && Config::get('HOSTING_PLANS_LEVEL') === 'admin') {
		$query = '
			SELECT
				t1.id, t1.reseller_id, t1.name, t1.props, t1.status,
				t2.admin_id, t2.admin_type
			FROM
				hosting_plans AS t1,
				admin AS t2
			WHERE
				t2.admin_type=?
			  AND
				t1.reseller_id = t2.admin_id
			  AND
				t1.status=1
			ORDER BY
				t1.name
';

		$rs = exec_query($sql, $query, array('admin'));
		$tr_edit = tr('View details');
		$tpl->assign('HP_MENU_ADD', '');
	} else {
		$query = '
			SELECT
				id, name, props, status
			FROM
				hosting_plans
			WHERE
				reseller_id = ?
			ORDER BY
				name
';
		$rs = exec_query($sql, $query, array($reseller_id));
		$tr_edit = tr('Edit');
	}

	if ($rs->RowCount() == 0) {
		// if ($external_event == '_off_') {
		set_page_message(tr('Hosting plans not found!'));
		// }
		$tpl->assign('HP_TABLE', '');
	} else { // There are data for hosting plants :-)
		if ($external_event == '_off_') {
			$tpl->assign('HP_MESSAGE', '');
		}

		$tpl->assign(array('TR_HOSTING_PLANS' => tr('Hosting plans'),
				'TR_NOM' => tr('No.'),
				'TR_EDIT' => $tr_edit,
				'TR_PLAN_NAME' => tr('Name'),
				'TR_ACTION' => tr('Action')));

		$i = 1;

		while ($data = $rs->FetchRow()) {
			if ($i % 2 == 0) {
				$tpl->assign(array('CLASS_TYPE_ROW' => 'content'));
			} else {
				$tpl->assign(array('CLASS_TYPE_ROW' => 'content2'));
			}
			$status = $data['status'];
			if ($status == 1) {
				$status = tr('Enabled');
			} else {
				$status = tr('Disabled');
			}

			$tpl->assign(array('PLAN_NOM' => $i++,
					'PLAN_NAME' => stripslashes($data['name']),
					'PLAN_ACTION' => tr('Delete'),
					'PLAN_SHOW' => tr('Show hosting plan'),
					'PURCHASING' => $status,
					'HP_ID' => $data['id'],
					'RESELLER_ID' => $_SESSION['user_id']));
			$tpl->parse('HP_ENTRY', '.hp_entry');
		} // End  loop
		$tpl->parse('HP_TABLE', 'hp_table');
	}
} // End of gen_hp_table()

// ******************************
// END OF FUNCTION DECLARE PATH
// *****************************
if (Config::get('DUMP_GUI_DEBUG'))
	dump_gui_debug();

unset_messages();

