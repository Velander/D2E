<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php"; ?>
<html>
<head>
<? require "inc/cssstyle.php"; ?>
<?
	$pagename = "$config_how_page_name";
	$help_msg_name = "config_how_help";
	$help_msg = "$config_how_help";
	$help_width = "$config_how_help_width";
	$help_height = "$config_how_help_height";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <td width="640" align="left" valign="top">
<?

	if (!empty($message))
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center><br>";
	if (!empty($config_how_paragraph1)) {
		echo "$config_how_paragraph1";
	}

	if ($config_how_banners == "Y") include "inc/banner_ads.php";
?>
                  </td>
<? require "inc/body_end.inc"; ?>
</html>
