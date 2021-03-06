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

require '../include/selity-lib.php';

check_login(__FILE__);

$tpl = new pTemplate();
$tpl->define_dynamic('page', Config::get('ADMIN_TEMPLATE_PATH') . '/settings_server_traffic.tpl');
$tpl->define_dynamic('page_message', 'page');
$tpl->define_dynamic('hosting_plans', 'page');

$theme_color = Config::get('USER_INITIAL_THEME');

$tpl->assign(
	array('TR_ADMIN_CHANGE_SERVER_TRAFFIC_SETTINGS_TITLE' => tr('Selity - Admin/Server Traffic Settings'),
		'THEME_COLOR_PATH' => "../themes/$theme_color",
		'THEME_CHARSET' => tr('encoding'),
		'ISP_LOGO' => get_logo($_SESSION['user_id'])
		)
	);

function update_server_settings(&$sql) {
	if (!isset($_POST['uaction']) && !isset($_POST['uaction'])) {
		return;
	}
	/*global $data;
	$match = array();
	preg_match("/^(-1|0|[1-9][0-9]*)$/D", $data, $match);*/

	$max_traffic = clean_input($_POST['max_traffic']);

	$traffic_warning = $_POST['traffic_warning'];

	if (!is_numeric($max_traffic) || !is_numeric($traffic_warning)) {
		set_page_message(tr('Wrong data input!'));
	}

	if ($traffic_warning > $max_traffic) {
		set_page_message(tr('Warning traffic is bigger than max traffic!'));

		return;
	}

	if ($max_traffic < 0) {
		$max_traffic = 0;
	}
	if ($traffic_warning < 0) {
		$traffic_warning = 0;
	}

	$query = '
				update
					straff_settings
				set
					straff_max = ?,
					straff_warn  = ?
';
	$rs = exec_query($sql, $query, array($max_traffic, $traffic_warning));

	set_page_message(tr('Server traffic settings updated successfully!'));
}

function generate_server_data(&$tpl, &$sql) {
	$query = '
		select
		   straff_max,
		   straff_warn
		from
			straff_settings
';

	$rs = exec_query($sql, $query, array());

	$tpl->assign(
		array('MAX_TRAFFIC' => $rs->fields['straff_max'],
			'TRAFFIC_WARNING' => $rs->fields['straff_warn'],

			)
		);
}

/*
 *
 * static page messages.
 *
 */
gen_admin_mainmenu($tpl, Config::get('ADMIN_TEMPLATE_PATH') . '/main_menu_settings.tpl');
gen_admin_menu($tpl, Config::get('ADMIN_TEMPLATE_PATH') . '/menu_settings.tpl');

$tpl->assign(
	array('TR_MODIFY' => tr('Modify'),
		'TR_SERVER_TRAFFIC_SETTINGS' => tr('Server traffic settings'),
		'TR_SET_SERVER_TRAFFIC_SETTINGS' => tr('Set server traffic settings'),
		'TR_MAX_TRAFFIC' => tr('Max traffic [MB]'),
		'TR_WARNING' => tr('Warning traffic [MB]'),
		)
	);

update_server_settings($sql);

generate_server_data($tpl, $sql);

gen_page_message($tpl);

$tpl->parse('PAGE', 'page');
$tpl->prnt();

if (Config::get('DUMP_GUI_DEBUG'))
	dump_gui_debug();

unset_messages();

