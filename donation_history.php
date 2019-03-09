<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";
	require_once "inc/class_project.php";
	require_once "inc/class_donation.php"; ?>
<html>
<head>
<? require "inc/cssstyle.php"; ?>
<?
	$pagename = "$config_history_page_name";
	$help_msg_name = "config_history_help";
	$help_msg = "$config_history_help";
	$help_width = "$config_history_help_width";
	$help_height = "$config_history_help_height";
	require "inc/title.php";
	if (empty($User_ID))
		$message = "No user specified.";
	else {
		$user_rcd = new user;
		$user_rcd->load_user($User_ID);
	}
	if (!empty($userid) && $user_rcd->type_id >= 40) {
		$userrcd = new user;
		$userrcd->load_user($userid);
	} else {
		$userrcd = $user_rcd;
	}
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <td width="640" align="left" valign="top">
<?	if (!empty($message))
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center><br>";
	if (!empty($config_donation_history_paragraph1)) {
		include "inc/box_begin.htm";
		echo "$config_donation_history_paragraph1";
		include "inc/box_end.htm";
	}
	include "inc/dark_box_begin.htm";
	echo "<center><h3>Donation History</h3></center>";
?>
	<table bgcolor="#ffffff" width='100%' cellpadding=1 cellspacing=0 border=0><tr><td>
<?
	echo "User: $userrcd->first_name $userrcd->last_name<br>";
	echo "</td></tr></table>";
?>
	<table width='100%' border=0 cellspacing=1 cellpadding=1>
		<tr>
			<td bgcolor="#ffffff" align='center'><b>ID</b></td>
			<td bgcolor="#ffffff" align='center'><b>Date</b></td>
			<td bgcolor="#ffffff" align='center'><b>Status</b></td>
			<td bgcolor="#ffffff"><b>Req#</b></td>
			<td bgcolor="#ffffff"><b>Request</b></td>
			<td bgcolor="#ffffff" align='center'><b>Amount</b></td>
		</td>
<?
	$userrcd->load_donation_list();
	$total_donations = 0;
	$target = "donation.php";

	while (list($donation_id, $donation) = each($userrcd->donation_list)) {
		echo "<tr><td bgcolor='#ffffff' align='center'>";
		if ($user_rcd->type_id >= 40) echo "<a href=\"donation_edit.php?donationid=$donation->donation_id\">";
		echo $donation->donation_id;
		if ($user_rcd->type_id >= 40) echo "</a>";
		echo "</td>\n";
		echo "<td bgcolor='#ffffff' align='center'>".date("m/d/Y",strtotime($donation->donation_date))."</td>\n";
		echo "<td bgcolor='#ffffff' align='center'>";
		if ($donation->payment_received == "Y")
			echo "Paid";
		else
			echo "Pledged";
		echo "</td>\n";
		$first = true;
		while (list($donation_project_id, $donation_project) = each($donation->donation_project_list)) {
			if (!$first)
				echo "<tr><td bgcolor='#ffffff' colspan='3'>&nbsp;</td>";
			$first = false;
			$project = new project();
			$project->load_project($donation_project->project_id);
			echo "<td bgcolor='#ffffff'><a href=\"$target?projectid=$project->project_id\">$project->project_id&nbsp;</a></td>\n";
			echo "<td bgcolor='#ffffff'><a href=\"$target?projectid=$project->project_id\">$project->project_name&nbsp;</a></td>\n";
			echo "<td bgcolor='#ffffff' align='right'>".sprintf("%01.2f",$donation_project->donation_amount)."&nbsp;&nbsp;</td>\n";
			$total_donations += $donation_project->donation_amount;
			echo "</tr>\n";
		}
		if ($first)
			echo "<td bgcolor='#ffffff' colspan='3'>&nbsp;</td></tr>\n";
	}
	echo "<tr><td bgcolor='#ffffff' colspan='5'><B>Total</B></td>";
	echo "<td bgcolor='#ffffff' align='right'><b>".sprintf("%01.2f",$total_donations)."</b>&nbsp;&nbsp;</td>\n";
	echo "</tr>\n";
?>
	</table>
<?
		include "inc/box_end.htm";
?>
                  </td>
<? require "inc/body_end.inc"; ?>
</html>
