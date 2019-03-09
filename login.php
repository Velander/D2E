<?	require "inc/db_inc.php";
	require_once "inc/func.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";

	$message	= $_GET["message"];

	if ($_POST["login_username"]) {
		$user = new user();
		if ($user->validate_login($_POST["login_username"], $_POST["login_password"])) {
			if ($_POST["RememberMe"] == "Y") {
				setcookie("RememberMe", "Y", time()+60*60*24*30);  /* expire in 30 days */
				setcookie("User_ID", $user->user_id, time()+60*60*24*30);  /* expire in 30 days */
			} else {
				setcookie("RememberMe", "N",0);
				setcookie("User_ID", $user->user_id, time()+3600);  /* expire in 1 hour */
			}
			setcookie("login_username", $user->login_username, time()+60*60*24*30); /* No Expiration */
			setcookie("UserLogin", $user->login_username, time()+60*60*24*30); /* No Expiration */
			setcookie("HttpLogout","",0);
			setcookie("HttpsLogout","",0);
			if (empty($target)) {
				if ($user->type_id < 20) {
					echo "<script type=\"text/javascript\">\nlocation.href='donation_search.php'\n</script>";
				} else if ($user->verified != "Y" && $user->type_id == 20) {
					echo "<script type=\"text/javascript\">\nlocation.href='registration.php?message=".urlencode($config_registration_confirmation)."&newuser=1'\n</script>";
				} else {
					echo "<script type=\"text/javascript\">\nlocation.href='project_list.php'\n</script>";
				}
			} else {
				$target = urldecode($target);
				if (!strstr($target, "donation.php?projectid")) {
					echo "<script type=\"text/javascript\">\nlocation.href='".$target."'\n</script>";
				} else {
					# Need to insert the UniqueID in the Target.
					$target = $https_location."donation.php?uniqueid=$user->unique_id&".strstr($target, "projectid");
					echo "<script type=\"text/javascript\">\nlocation.href='".$target."'\n</script>";
				}
			}
		} else
			$message = "Invalid Login ID or Password, try again.";
	}
?>
<html>
<head>
<?
	include "inc/cssstyle.php";
	$pagename = "$config_login_page_name";
	$help_msg_name = "config_login_help";
	$help_msg = "$config_login_help";
	$help_width = "$config_login_help_width";
	$help_height = "$config_login_help_height";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <td width="655" align="left" valign="top">
<?
	if ($config_login_banners == "Y") include "inc/banner_ads.php";
	if (!empty($message)) {
		include "inc/box_begin.htm";
		echo "<center><font size=\"+1\" color='$color_error_message'>".stripslashes($message)."</font></center>";
		include "inc/box_end.htm";
	}
	if (!empty($config_login_paragraph1)) {
		include "inc/box_begin.htm";
		echo "$config_login_paragraph1";
		include "inc/box_end.htm";
	}
?>
	<table width=100% cellspacing=1 cellpadding=1 valign="top">
		<tr>
			<td bgcolor="<?=$color_table_col_bg;?>" valign="top" align=left>
				<?=$config_login_new_msg;?>
			</td>
		</tr>
		<tr>
			<td bgcolor="<?=$color_table_col_bg;?>" valign="top" align=left>
			<img src="images/green_line.gif" width="640">
			</td>
		</tr>
		<tr>
			<td bgcolor="<?=$color_table_col_bg;?>" valign="top" align=center valign='middle'>
				<table width='100%'><tr>
				<td width=60></td>
					<td align='center' valign='middle'>
					<p><font size=2>
					<BR><h3>Click below to Register Now!</h3>
					</font>
					</p>
					<p>
	<? include "inc/box_begin.htm"; ?>
					<center><font size=6><a href="<?=$https_location;?>registration.php?f_type_id=10">&nbsp;Donor&nbsp;</a></font></center>
	<? include "inc/box_end.htm"; ?>
					<P>&nbsp;or&nbsp;</P>
	<? include "inc/box_begin.htm"; ?>
					<center><font size=6><a href="<?=$https_location;?>registration.php?f_type_id=20">Teacher</a></font></center>
	<? include "inc/box_end.htm"; ?>
					</p>
				</td>
				<td width=20></td>
				<td align='right'>
					<img src="images/5girls.jpg">
				</td></tr>
				</table>
			</td>
		</tr>
		<tr>
			<td bgcolor="<?=$color_table_col_bg;?>" valign="top" align=left>
			<img src="images/green_line.gif" width="640">
			</td>
		</tr>
	</table>
	<?=$config_login_paragraph2;?>
</td>
<? require "inc/body_end.inc"; ?>
</html>
