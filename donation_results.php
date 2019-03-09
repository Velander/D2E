<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php"; ?>
<html>
<head>
<? require "inc/cssstyle.php"; ?>
<?
	$pagename = "$config_results_page_name";
	$help_msg_name = "config_results_help";
	$help_msg = "$config_resuts_help";
	$help_width = "$config_results_help_width";
	$help_height = "$config_results_help_height";

	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <td width="640" align="left" valign="top">
<?	if (!empty($message))
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center><br>";
	if (!empty($config_results_paragraph1)) {
		echo "$config_results_paragraph1";
	}
	flush;
	echo "<img src=\"images/kidspanoramic.jpg\" width=\"640\"><BR>";
	echo "<img src=\"images/green_line.gif\" width=\"640\"><BR><BR>";
#	# Oregon City School Results
#	list($http_headers, $page_src) = func_http_get_request("www.donate2educate.org", "http://www.donate2educate.org/results_summary.php","",array());
#	echo $page_src;
#	echo "<center><table><tr><td>";
#	include "inc/box_begin.htm";
#	echo "<p align=\"center\"><a target=\"results\" href=\"http://www.donate2educate.org/results.php\"><img src=\"images/buttons/arrow.gif\" border=0>&nbsp;<b>Details</b></a></p>";
#	include "inc/box_end.htm";
#	echo "</td></tr></table></center>";
#	flush;
	echo "<BR><img src=\"images/green_line.gif\" width=\"640\"><BR><BR>";
	$results = $db_link->query("SELECT count( donation_project_id ) Dcount, Sum( donation_project.amount ) damount FROM donation INNER JOIN donation_project ON donation.donation_id = donation_project.donation_id WHERE payment_received IS NOT NULL AND payment_authorized = 'Y'");
	$dateinfo = getdate();
	$monthname = $dateinfo[month];
	$year = $dateinfo[year];
	list($TDcnt, $TDtotal) = mysqli_fetch_row($results);
	mysqli_free_result($results);

	$results = $db_link->query("select count(donation_project_id) Dcount, Sum(donation_project.amount) damount from donation inner join donation_project on donation.donation_id = donation_project.donation_id where payment_received is not null and DATE_FORMAT(donation_date,'%Y') = '$year'");
	list($YTDcnt, $YTDtotal) = mysqli_fetch_row($results);
	mysqli_free_result($results);

	$results = $db_link->query("SELECT count( donation_project_id ) Dcount, Sum( donation_project.amount ) damount FROM donation INNER JOIN donation_project ON donation.donation_id = donation_project.donation_id where payment_received is not null and DATE_FORMAT(donation_date,'%M') = '".$monthname."' and DATE_FORMAT(donation_date,'%Y') = '$year'");
	list($MONcnt, $MONtotal) = mysqli_fetch_row($results);
	mysqli_free_result($results);

	echo "<table width='100%' border=0 cellspacing=3 cellpadding=4><tr><td align='center'><H2>More Oregon Schools</H2></td><td align='center'><b>Since<BR>Jan 2007</b></td><td align='center'><b>$year<BR>To Date</b></td><td align='center'><b>$monthname<BR>$year</b></td></tr>";
	echo "<tr><td align='center'><b>Donations Made</b></td><td align='center'>$TDcnt</td><td align='center'>$YTDcnt</td><td align='center'>$MONcnt</td></tr>";
	echo "<tr><td align='center'><b>Total Donations</b></td><td align='center'>$".sprintf("%01.0f",$TDtotal)."</td><td align='center'>$".sprintf("%01.0f",$YTDtotal)."</td><td align='center'>$".sprintf("%01.0f",$MONtotal)."</td></tr>";
	echo "</table>\n";
	echo "<center><table><tr><td>";
	include "inc/box_begin.htm";
	echo "<p align=\"center\"><a href=\"results_search.php?f_school_id=0\"><img src=\"images/buttons/arrow.gif\" border=0>&nbsp;<b>Details</b></a></p>";
	include "inc/box_end.htm";
	echo "</td></tr></table></center>";
	echo "<img src=\"images/green_line.gif\" width=\"640\">";

?>
                  </td>
<? require "inc/body_end.inc"; ?>
</html>
