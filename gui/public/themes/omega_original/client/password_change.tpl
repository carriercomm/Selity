<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset={THEME_CHARSET}">
<title>{TR_CLIENT_CHANGE_PASSWORD_PAGE_TITLE}</title>
  <meta name="robots" content="noindex">
  <meta name="robots" content="nofollow">
<link href="{THEME_COLOR_PATH}/css/selity.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="{THEME_COLOR_PATH}/css/selity.js"></script>
</head>

<body onload="MM_preloadImages('{THEME_COLOR_PATH}/images/icons/database_a.gif','{THEME_COLOR_PATH}/images/icons/domains_a.gif','{THEME_COLOR_PATH}/images/icons/ftp_a.gif','{THEME_COLOR_PATH}/images/icons/general_a.gif' ,'{THEME_COLOR_PATH}/images/icons/email_a.gif','{THEME_COLOR_PATH}/images/icons/webtools_a.gif','{THEME_COLOR_PATH}/images/icons/statistics_a.gif','{THEME_COLOR_PATH}/images/icons/support_a.gif')">
<!-- BDP: logged_from --><table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td height="20" nowrap="nowrap" class="backButton">&nbsp;&nbsp;&nbsp;<a href="change_user_interface.php?action=go_back"><img src="{THEME_COLOR_PATH}/images/icons/close_interface.png" width="16" height="16" border="0" align="absmiddle"></a> {YOU_ARE_LOGGED_AS}</td>
	  </tr>
	</table>
	<!-- EDP: logged_from -->
<table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%" style="border-collapse: collapse;padding:0;margin:0;">
	<tr>
		<td style="width: 195px; vertical-align: top;">{MENU}</td>
		<td colspan="2" style="vertical-align: top;"><table style="width: 100%; border-collapse: collapse;padding:0;margin:0;">
		  <tr height="95">
			<td style="padding-left:30px; width: 100%; background-image: url({THEME_COLOR_PATH}/images/top/middle_bg.jpg);">{MAIN_MENU}</td>
			<td style="padding:0;margin:0;text-align: right; width: 73px;vertical-align: top;"><img src="{THEME_COLOR_PATH}/images/top/middle_right.jpg" border="0"></td>
		  </tr>
		  <tr>
			<td colspan="3"><table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr>
				<td align="left"><table width="100%" cellpadding="5" cellspacing="5">
					<tr>
					  <td width="25"><img src="{THEME_COLOR_PATH}/images/content/table_icon_password.png" width="25" height="25"></td>
					  <td colspan="2" class="title">{TR_CHANGE_PASSWORD}</td>
					</tr>
				</table></td>
				<td width="27" align="right">&nbsp;</td>
			  </tr>
			  <tr>
				<td valign="top"><form name="client_change_pass_frm" method="post" action="password_change.php">
					<table width="100%" cellpadding="5" cellspacing="5">
					  <!-- BDP: page_message -->
					  <tr>
						<td width="25">&nbsp;</td>
						<td colspan="2" class="title"><span class="message">{MESSAGE}</span></td>
					  </tr>
					  <!-- EDP: page_message -->
					  <tr>
						<td width="25">&nbsp;</td>
						<td class="content2"><label for="curr_pass">{TR_CURR_PASSWORD}</label></td>
						<td class="content"><input type="password" name="curr_pass" id="curr_pass" value="" style="width:170px" class="textinput"></td>
					  </tr>
					  <tr>
						<td width="20">&nbsp;</td>
						<td width="203" class="content2"><label for="pass">{TR_PASSWORD}</label></td>
						<td class="content"><input type="password" name="pass" id="pass" value="" style="width:170px" class="textinput"></td>
					  </tr>
					  <tr>
						<td>&nbsp;</td>
						<td class="content2"><label for="pass_rep">{TR_PASSWORD_REPEAT}</label></td>
						<td width="516" class="content"><input type="password" name="pass_rep" id="pass_rep" value="" style="width:170px" class="textinput"></td>
					  </tr>
					  <tr>
						<td>&nbsp;</td>
						<td colspan="2"><input type="hidden" name="uaction" value="updt_pass">
							<input name="Submit" type="submit" class="button" value="{TR_UPDATE_PASSWORD}"></td>
					  </tr>
					</table>
				</form></td>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			  </tr>
			</table></td>
		  </tr>
		</table></td>
	</tr>
</table>
</body>
</html>
