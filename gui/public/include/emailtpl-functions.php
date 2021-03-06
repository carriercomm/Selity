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

function get_email_tpl_data($admin_id, $tpl_name) {

	$sql = Database::getInstance();

	$query = '
		 		SELECT
				fname, lname, firm, email
			FROM
				admin
			WHERE
			 	admin_id = ?
';

	$rs = exec_query($sql, $query, array($admin_id));

	if ( (trim($rs->fields('fname')) != '') && (trim($rs->fields('lname')) != '') ) {

		$data['sender_name'] = $rs->fields('fname') . ' ' . $rs->fields('lname');

	} else if (trim($rs->fields('fname')) != '') {

		$data['sender_name'] = $rs->fields('fname');

	} else if (trim($rs->fields('lname')) != '') {

		$data['sender_name'] = $rs->fields('lname');

	} else {

		$data['sender_name'] = '';

	}

	if ($rs->fields('firm') != '') {

		if ($data['sender_name'] != '') {

			$data['sender_name'] = $data['sender_name'] . ' ' . '[' . $rs->fields('firm') . ']';

		} else {

			$data['sender_name'] = $rs->fields('firm');

		}

	}

	$data['sender_email'] = $rs->fields('email');

	$query = '
						SELECT
			  	subject, message
	  		FROM
			  	email_tpls
		  	WHERE
		  		owner_id = ?
		  	AND
		  		name = ?
';

	$rs = exec_query($sql, $query, array($admin_id, $tpl_name));

	if ($rs ->RowCount() == 1 ) {

		$data['subject'] = $rs->fields['subject'];

		$data['message'] = $rs->fields['message'];

	} else {

		$data['subject'] = '';

		$data['message'] = '';

	}

	return $data;

}

function set_email_tpl_data($admin_id, $tpl_name, $data) {

	$sql = Database::getInstance();

	$query = '
  					SELECT
				subject, message
			FROM
				email_tpls
			WHERE
				owner_id = ?
		   	AND
		   		name = ?
';

	$rs = exec_query($sql, $query, array($admin_id, $tpl_name));

	if ($rs ->RowCount() == 0 ) {

		$query = '
  						INSERT INTO email_tpls
					(subject, message, owner_id, name)
	  	  		VALUES
					(?, ?, ?, ?)
';

	} else {

		$query = '
  						UPDATE
  							email_tpls
  						SET
  							subject = ?,
		  		message = ?
				WHERE
  			  	owner_id = ?
				AND
	  		  	name = ?
';

	}

	exec_query($sql, $query, array($data['subject'], $data['message'], $admin_id, $tpl_name));

}

function get_welcome_email($admin_id) {

	$data = get_email_tpl_data($admin_id, 'add-user-auto-msg');

	if (!$data['subject']) {

		$data['subject'] = tr('Welcome {USERNAME} to Selity!', true);

	}

	if (!$data['message']) {

		$data['message'] = tr('

Hello {NAME}!

A new Selity account has been created for you.
Your account information:

User type: {USERTYPE}
User name: {USERNAME}
Password: {PASSWORD}

Remember to change your password often and the first time you login.

You can login right now at http://{BASE_SERVER_VHOST}

Statistics: http://{USERNAME}/stats/
User name: {USERNAME}
Password: {PASSWORD}

Best wishes with Selity!
The Selity Team.

', true);

	}

	return $data;

}

function set_welcome_email($admin_id, $data) {

	set_email_tpl_data($admin_id, 'add-user-auto-msg', $data);

}

function get_lostpassword_activation_email($admin_id) {

	$data = get_email_tpl_data($admin_id, 'lostpw-msg-1');

	if (!$data['subject']) {

		$data['subject'] = tr('Please activate your new Selity password!', true);

	}

	if (!$data['message']) {

		$data['message'] = tr('

Hello {NAME}!
Use this link to activate your new Selity password:

{LINK}

Good Luck with the Selity System
The Selity Team

', true);

	}

	return $data;

}

function set_lostpassword_activation_email($admin_id, $data) {

	set_email_tpl_data($admin_id, 'lostpw-msg-1', $data);

}

function get_lostpassword_password_email($admin_id) {

	$data = get_email_tpl_data($admin_id, 'lostpw-msg-2');

	if (!$data['subject']) {

		$data['subject'] = tr('Your new Selity login!', true);

	}

	if (!$data['message']) {

		$data['message'] = tr('

Hello {NAME}!

Your user name is: {USERNAME}
Your password is: {PASSWORD}

You can login at http://{BASE_SERVER_VHOST}

Best wishes with Selity!
The Selity Team

', true);

	}

	return $data;

}

function set_lostpassword_password_email($admin_id, $data) {

	set_email_tpl_data($admin_id, 'lostpw-msg-2', $data);

}

function get_order_email($admin_id) {

	$data = get_email_tpl_data($admin_id, 'after-order-msg');

	if (!$data['subject']) {

		$data['subject'] = tr('Confirmation for domain order {DOMAIN}!', true);

	}

	if (!$data['message']) {

		$data['message'] = tr('

Dear {NAME},
This is an automatic confirmation for the order of the domain:

{DOMAIN}

Thank you for using Selity services.
The Selity Team

', true);

	}

	return $data;

}

function set_order_email($admin_id, $data) {

	set_email_tpl_data($admin_id, 'after-order-msg', $data);

}

function get_alias_order_email($admin_id) {

	$data = get_email_tpl_data($admin_id, 'alias-order-msg');

	if (!$data['subject']) {

		$data['subject'] = tr('New alias order for {CUSTOMER}!', true);

	}

	if (!$data['message']) {

		$data['message'] = tr('

Dear {RESELLER},
Your customer {CUSTOMER} is awaiting for the approval of his new alias:

{ALIAS}

Once logged in, you can activate his new alias at
http://{BASE_SERVER_VHOST}/reseller/domain-alias.php

Thank you for using Selity services.
The Selity Team

', true);

	}

	return $data;

}

function set_alias_order_email($admin_id, $data) {

	set_email_tpl_data($admin_id, 'alias-order-msg', $data);

}

