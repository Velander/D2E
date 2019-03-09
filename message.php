<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php"; ?>
<html>
<head>
<? require "inc/cssstyle.php"; ?>
<?
	$pagename = "$config_message_page_name";
	$help_msg_name = "config_message_help";
	$help_msg = "$config_message_help";
	$help_width = "$config_message_help_width";
	$help_height = "$config_message_help_height";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <td width="655" align="left" valign="top">
<?	echo "$message"; ?>
                  </td>
<? require "inc/body_end.inc"; ?>
</html>
