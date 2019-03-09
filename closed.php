<?	require "inc/db_inc.php";
	require_once "inc/class_user.php";
	if ($config_maintenance_flag != "Y")
		echo "<script type=\"text/javascript\">\nlocation.href='$http_location"."index.php'\n</script>\n";
?>
<html>
<head>
<? require "inc/cssstyle.php"; ?>
<META NAME="Keywords" CONTENT="School, Schools, Donate, Donation, Education">
<?
	$pagename = "$config_home_page_name";
	$help_msg_name = "config_home_help";
	$help_msg = "$config_home_help";
	$help_width = "$config_home_help_width";
	$help_height = "$config_home_help_height";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/home_body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
					<td width="655" align="center" valign="top">
<?	if (!empty($message))
		echo "<center><b><font color='$color_error_message'>$message</font></b></center><br>";
	if (!empty($config_maintenance_message)) {
		echo "$config_maintenance_message";
		echo "<center>";
		include "inc/box_begin.htm";

		include "inc/box_end.htm";
		echo "</center>";
	}
?>
                  </td>
<? require "inc/body_end.inc"; ?>
</html>
