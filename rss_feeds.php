<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";
	require_once "inc/class_school.php";
	require_once "inc/class_schools.php";
	require_once "inc/class_district.php";
	require_once "inc/class_districts.php";
	$schools = new schools();
	$districts = new districts();
	$districts->load_donation_districts();
?>
<html>
<head>
<?	require "inc/cssstyle.php"; ?>
<META NAME="Keywords" CONTENT="Oregon City, Schools, Donate, Donation, Education">
<?
	$pagename = "$config_rss_feeds_page_name";
	$help_msg_name = "config_rss_feeds_help";
	$help_msg = "$config_rss_feeds_help";
	$help_width = "$config_rss_feeds_help_width";
	$help_height = "$config_rss_feeds_help_height";
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
	if (!empty($config_rss_feeds_paragraph1)) {
		echo "$config_rss_feeds_paragraph1";
	}
	echo "<table><tr><td width=\"20%\">&nbsp;</td><td>";
	while (list($districtid, $district) = each($districts->district_list)) {
		echo "<p><a class='rss_district_name' href=\"rss.php?districtid=$districtid\" target=\"_rss\">$district->district_name</a></br>\n";
		$schools->load_donation_schools($districtid);
		while (list($schoolid, $school) = each($schools->school_list)) {
			echo "&nbsp;&nbsp;&nbsp;<a  class='rss_school_name' href=\"rss.php?schoolname=".str_replace(" ","+",$school->school_name)."\" target=\"_rss\">$school->school_name</a></br>\n";
		}
		echo "</p>\n";
	}
	echo "</td><td width=\"20%\">&nbsp;</td>";
	echo "</tr></table>";
	if ($config_rss_feeds_banners == "Y") include "inc/banner_ads.php";
?>
					</td>
<? require "inc/body_end.inc"; ?>
</html>
&nbsp;