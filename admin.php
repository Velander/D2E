<?	require_once "inc/db_inc.php";
	require_once "inc/class_user.php";
	require_once "inc/admin_user.php";
?>
<html>
<head>
<?
	$pagename = "$config_adminhome_page_name";
	$help_msg_name = "config_adminhome_help";
	$help_msg = "$config_adminhome_help";
	$help_width = "$config_adminhome_help_width";
	$help_height = "$config_adminhome_help_height";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require_once "inc/jscript.inc"; ?>
<? require_once "inc/cssstyle.php"; ?>
</head>
<? require_once "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <td width="655" align="left" valign="top">
<?	if (!empty($message))
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center><br>";
	if (!empty($config_adminhome_paragraph1)) {
		include "inc/box_begin.htm";
		echo "$config_adminhome_paragraph1";
		include "inc/box_end.htm";
	}
?>
				  </td>
<? require "inc/body_end.inc"; ?>
</html>
