<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
?>
<html>
<head>
<?	require "inc/cssstyle.php"; ?>
<META NAME="Keywords" CONTENT="Oregon City, Schools, Donate, Donation, Education">
<?
	$pagename = "$config_testimonials_page_name";
	$help_msg_name = "config_testimonials_help";
	$help_msg = "$config_testimonials_help";
	$help_width = "$config_testimonials_help_width";
	$help_height = "$config_testimonials_help_height";
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
	if (!empty($config_testimonials_paragraph1)) {
		echo "$config_testimonials_paragraph1";
	}
	if (!empty($config_testimonials_paragraph2)) {
		echo "$config_testimonials_paragraph2";
	}
	if ($config_testimonials_banners == "Y") include "inc/banner_ads.php";
?>
					</td>
<? require "inc/body_end.inc"; ?>
</html>
