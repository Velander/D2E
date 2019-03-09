<?	require "inc/db_inc.php";
	include "inc/cssstyle.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";

	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$user = new user();
		if (!empty($login_username) || !empty($login_email)) {
			$userids = ($user->lookup_login($login_username, $login_email));
		} else {
			$message = "A Login ID or Email is required.";
			$userid = 0;
		}
		if (is_array($userids)) {
			if (count($userids) == 0) {
				$message = "No match found on file.";
			} else {
				reset($userids);
				while (list($idx, $userid) = each($userids)) {
					$user->load_user($userid);
					$message = $config_login_lookup_confirmation;
					$body 	 = preg_replace("%LOGIN","$user->login",
					preg_replace("%FIRST_NAME","$user->first_name",
					preg_replace("%LAST_NAME","$user->last_name",
					preg_replace("%PASSWORD","$user->password",
					preg_replace("%URL","http://www.donate2educate.org",
					$config_login_lookup_message)))));

					$subject = preg_replace("%LOGIN","$user->login",
					preg_replace("%FIRST_NAME","$user->first_name",
					preg_replace("%LAST_NAME","$user->last_name",
					preg_replace("%PASSWORD","$user->password",
					$config_login_lookup_subject))));

					$headers = "From: support@donate2educate.org\r\n";
					if (!mail($user->email, $subject, str_replace("\n.", "\n..", $body), $headers))
						$message = "An error occured trying to send the email.  Try again later.";
				}
				#echo "<script type=\"text/javascript\">\nlocation.href=\"login.php?message=".urlencode($message)."\"\n</script>";
			}
		} else
			$message = "No match found on file.";
	}
?>
<html>
<head>
<?
	$pagename = "$config_login_lookup_page_name";
	$help_msg_name = "config_login_lookup_help";
	$help_msg = "$config_login_lookup_help";
	$help_width = "$config_login_lookup_help_width";
	$help_height = "$config_login_lookup_help_height";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <td width="655" align="left" valign="top">
<?
	if ($config_login_lookup_banners == "Y") include "inc/banner_ads.php";
	if (!empty($message))
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center><br>";
	echo "$config_login_lookup_paragraph1";
	include "inc/dark_box_begin.htm";
	echo "<font size='+1' color=\"$color_table_hdg_font\"><b>Retrieve Login Information</b></font>";
	include "inc/box_middle.htm";
?>
				<TABLE ALIGN="center" BORDER=0 CELLSPACING=10 CELLPADDING=0 WIDTH="100%">
					<Form Name='Login_lookup' Method='POST'>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='center' Colspan=2><b>Enter a Login ID or E-Mail Address</b></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><b>Login ID</b></TD>
						<TD><input type='text' name='login_username' value='<?=$login_username;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><b>E-Mail</b></TD>
						<TD><input type='text' name='login_email' value='<?=$login_email;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Center' Colspan='2'><Input Type='Submit' Class='nicebtns' Name='Submit' Value='Submit'></TD>
					</TR>
					<input type='hidden' name='target' value='<?=$target;?>'>
					</Form>
					<TR ALIGN="Center" VALIGN="middle">
						<TD Colspan="2"><?=$config_login_paragraph2;?></TD>
					</TR>
					<TR ALIGN="Center" VALIGN="middle">
						<TD Colspan="2">
							<TABLE ALIGN="center" BORDER=0 CELLSPACING=10 CELLPADDING=0 WIDTH="100%">
								<TR ALIGN="Center" VALIGN="middle">
									<TD Align='Center'><A href="<?=$https_location;?>registration.php?f_type_id=10"><b>Donor Registration</b></a></TD>
									<TD Align='Center'><A href="<?=$https_location;?>registration.php?f_type_id=20"><b>Teacher Registration</b></a></TD>
								</TR>
							</TABLE>
		                  </td>
					</TR>
				</TABLE>
<?	include "inc/box_end.htm"; ?>
                  </td>
<? require "inc/body_end.inc"; ?>
</html>
