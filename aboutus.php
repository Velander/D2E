<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php"; ?>
<html>
<head>
<?	require "inc/cssstyle.php"; ?>
<META NAME="Keywords" CONTENT="Oregon City, Schools, Donate, Donation, Education">
<?
	$pagename = "$config_about_page_name";
	$help_msg_name = "config_about_help";
	$help_msg = "$config_about_help";
	$help_width = "$config_about_help_width";
	$help_height = "$config_about_help_height";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/home_body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
					<td width="655" align="left" valign="top">
<?	if (!empty($message))
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center><br>";
	if ($config_about_banners == "Y") include "inc/banner_ads.php";
	if (!empty($config_home_paragraph1)) {
		echo "$config_about_paragraph1";
	}
?>
					</td>
<? require "inc/body_end.inc"; ?>
</html>
