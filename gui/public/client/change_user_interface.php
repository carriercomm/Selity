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

if (isset($_SESSION['logged_from']) && isset($_SESSION['logged_from_id']) &&
	isset($_GET['action']) && $_GET['action'] == "go_back") {

	$from_id = $_SESSION['user_id'];
	$to_id   = $_SESSION['logged_from_id'];

	change_user_interface($from_id, $to_id);

} else {
	header('Location: index.php');
	die();
}

