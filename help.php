<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php"; ?>
<html>
<head>
<? require "inc/cssstyle.php"; ?>
<?
	$pagename = "Help: $section";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<body>
<?	include "inc/dark_box_begin.htm";
	echo "<font size=\"+1\" color=\"$color_table_hdg_font\"><B>$section Help</B></font>";
	include "inc/box_middle.htm";
	echo ${$helpid}."<br>";
	#echo "$config_login_help";
	echo "<p align='center'>";
	include "inc/buttons/close_button.htm";
	echo "</p>";
	include "inc/box_end.htm";
?>
</body>
</html>
