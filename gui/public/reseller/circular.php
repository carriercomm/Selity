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

function gen_page_data(&$tpl, &$sql) {
	if (isset($_POST['uaction']) && $_POST['uaction'] === 'send_circular') {
		$tpl->assign(
			array(
				'MESSAGE_SUBJECT' => clean_input($_POST['msg_subject'], false),
				'MESSAGE_TEXT' => clean_input($_POST['msg_text'], false),
				'SENDER_EMAIL' => clean_input($_POST['sender_email'], false),
				'SENDER_NAME' => clean_input($_POST['sender_name'], false)
				)
			);
	} else {
		$user_id = $_SESSION['user_id'];

		$query = '
			select
				fname, lname, email
			from
				admin
			where
				admin_id = ?
			group by
				email
';

		$rs = exec_query($sql, $query, array($user_id));

		if (isset($rs->fields['fname']) && isset($rs->fields['lname'])) {
			$sender_name = $rs->fields['fname'] . " " . $rs->fields['lname'];
		} elseif(isset($rs->fields['fname']) && !isset($rs->fields['lname'])) {
			$sender_name = $rs->fields['fname'];
		} elseif(!isset($rs->fields['fname']) && isset($rs->fields['lname'])) {
			$sender_name = $rs->fields['lname'];
		} else {
			$sender_name = "";
		}

		$tpl->assign(
			array(
				'MESSAGE_SUBJECT' => '',
				'MESSAGE_TEXT' => '',
				'SENDER_EMAIL' => $rs->fields['email'],
				'SENDER_NAME' => $sender_name
				)
			);
	}
}

function check_user_data (&$tpl) {
	global $msg_subject, $msg_text, $sender_email, $sender_name;

	$err_message = '';

	$msg_subject = clean_input($_POST['msg_subject'], false);
	$msg_text = clean_input($_POST['msg_text'], false);
	$sender_email = clean_input($_POST['sender_email'], false);
	$sender_name = clean_input($_POST['sender_name'], false);

	if (empty($msg_subject)) {
		$err_message .= tr('Please specify a message subject!');
	}
	if (empty($msg_text)) {
		$err_message .= tr('Please specify a message content!');
	}
	if (empty($sender_name)) {
		$err_message .= tr('Please specify a sender name!');
	}
	if (empty($sender_email)) {
		$err_message .= tr('Please specify a sender email!');
	}
	else if (!chk_email($sender_email)) {
		$err_message .= tr("Incorrect email length or syntax!");
	}

	if (!empty($err_message)) {
		set_page_message($err_message);
		return false;
	} else {
		return true;
	}
}

function send_circular(&$tpl, &$sql) {
	if (isset($_POST['uaction']) && $_POST['uaction'] === 'send_circular') {
		if (check_user_data($tpl)) {
			send_reseller_users_message ($sql, $_SESSION['user_id']);
			unset($_POST['uaction']);
			gen_page_data($tpl, $sql);
		}
	}
}

function send_reseller_users_message (&$sql, $admin_id) {

	$msg_subject = clean_input($_POST['msg_subject'], false);
	$msg_text = clean_input($_POST['msg_text'], false);
	$sender_email = clean_input($_POST['sender_email'], false);
	$sender_name = clean_input($_POST['sender_name'], false);

	$query = '
		select
			fname, lname, email
		from
			admin
		where
			admin_type = \'user\' and created_by = ?
		group by
			email
';

	$rs = exec_query($sql, $query, array($admin_id));

	while (!$rs->EOF) {
		$to = "\"" . encode($rs->fields['fname'] . " " . $rs->fields['lname']) . "\" <" . $rs->fields['email'] . ">";

		send_circular_email($to, "\"" . encode($sender_name) . "\" <" . $sender_email . ">", stripslashes($msg_subject), stripslashes($msg_text));

		$rs->MoveNext();
	}

	set_page_message(tr('You send email to your users successfully!'));
	write_log("Mass email was sended from Reseller " . $sender_name . " <" . $sender_email . ">");
}

function send_circular_email ($to, $from, $subject, $message) {
	$subject = encode($subject);

	$headers  = "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 8bit\n";
	$headers .= "From: " . $from . "\n";
	$headers .= "X-Mailer: Selity marketing mailer";

	mail($to, $subject, $message, $headers);
}

$tpl = new pTemplate();
$tpl->define_dynamic('page', Config::get('RESELLER_TEMPLATE_PATH') . '/circular.tpl');
$tpl->define_dynamic('page_message', 'page');
$tpl->define_dynamic('logged_from', 'page');

$theme_color = Config::get('USER_INITIAL_THEME');

$tpl->assign(
		array(
			'TR_RESELLER_CIRCULAR_PAGE_TITLE' => tr('Selity - Circular'),
			'THEME_COLOR_PATH' => "../themes/$theme_color",
			'THEME_CHARSET' => tr('encoding'),

			'ISP_LOGO' => get_logo($_SESSION['user_id']),
			)
		);

/*
 *
 * static page messages.
 *
 */

gen_reseller_mainmenu($tpl, Config::get('RESELLER_TEMPLATE_PATH') . '/main_menu_users_manage.tpl');
gen_reseller_menu($tpl, Config::get('RESELLER_TEMPLATE_PATH') . '/menu_users_manage.tpl');

gen_logged_from($tpl);

$tpl->assign(
		array(
			'TR_CIRCULAR' => tr('Circular'),
			'TR_CORE_DATA' => tr('Core data'),
			'TR_SEND_TO' => tr('Send message to'),
			'TR_ALL_USERS' => tr('All users'),
			'TR_ALL_RESELLERS' => tr('All resellers'),
			'TR_ALL_USERS_AND_RESELLERS' => tr('All users & resellers'),
			'TR_MESSAGE_SUBJECT' => tr('Message subject'),
			'TR_MESSAGE_TEXT' => tr('Message'),
			'TR_ADDITIONAL_DATA' => tr('Additional data'),
			'TR_SENDER_EMAIL' => tr('Senders email'),
			'TR_SENDER_NAME' => tr('Senders name'),
			'TR_SEND_MESSAGE' => tr('Send message'),
			'TR_SENDER_NAME' => tr('Senders name'),
			)
		);

send_circular($tpl, $sql);

gen_page_data ($tpl, $sql);

gen_page_message($tpl);

$tpl->parse('PAGE', 'page');
$tpl->prnt();

if (Config::get('DUMP_GUI_DEBUG'))
	dump_gui_debug();

unset_messages();

