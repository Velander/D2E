<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";
	require_once "inc/class_project.php";
	require_once "inc/class_donation.php";
	require_once "inc/class_matching.php";
	require_once "inc/class_schools.php";
?>
<html>
<head>
<? require "inc/cssstyle.php"; ?>
<?
	$pagename = "$config_history_page_name";
	$help_msg_name = "config_history_help";
	$help_msg = "$config_history_help";
	$help_width = "$config_history_help_width";
	$help_height = "$config_history_help_height";

	$schools = new schools();
	$schools->load_schools();

	require "inc/title.php";
	if (empty($User_ID))
		$message = "No user specified.";
	else {
		$user_rcd = new user;
		$user_rcd->load_user($User_ID);
	}
	if ($user_rcd->type_id >= 40 || ($userid == $User_ID)) {
		if ($userid) {
			$userrcd = new user();
			$userrcd->load_user($userid);
			$userrcd->load_matching_list();
			$matching_list = $userrcd->matching_list;
		} else {
			$matching = new matching();
			$matching->load_matching($matching_id);
			$userrcd = new user();
			$userrcd->load_user($matching->user_id);
			$matching_list = array();
			$matching_list[] = $matching;
		}
	} else {
		$userrcd = $user_rcd;
		$matching_list = $userrcd->matching_list;
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
	echo "<center><h3><font color=\"#ffffff\">Matching Donation History</font></h3></center>";
?>
	<table bgcolor="#ffffff" width='100%' cellpadding=1 cellspacing=0 border=0><tr><td>
<?
	echo "User: $userrcd->first_name $userrcd->last_name<br>";
	echo "</td></tr></table>";
?>
	<table width='100%' border=0 cellspacing=1 cellpadding=1>
		<tr>
			<td bgcolor="#ffffff" valign='bottom' align='center'><b><BR>ID</b></td>
			<td bgcolor="#ffffff" valign='bottom' align='center'><b>Begin<BR>Date</b></td>
			<td bgcolor="#ffffff" valign='bottom' align='center'><b>End<BR>Date</b></td>
			<td bgcolor="#ffffff" valign='bottom' align='center'><b>Max<BR>Amount</b></td>
			<td bgcolor="#ffffff" valign='bottom'><b><BR>School</b></td>
			<td bgcolor="#ffffff" valign='bottom'><b><BR>Request</b></td>
			<td bgcolor="#ffffff" valign='bottom' align='center'><b><BR>Amount</b></td>
		</td>
<?
	$total_donations = 0;
	$total_max_amount = 0;
	if ($user->type_id >= 40)
		$target = "proposal.php?projectid=";
	else
		$target = "donation.php?projectid=";

	while (list($matching_id, $matching) = each($matching_list)) {
		echo "<tr><td bgcolor='#ffffff' align='center'>$matching->matching_id</td>\n";
		echo "<td bgcolor='#ffffff' align='center'>".($matching->begin_date != "" ? date("m/d/Y",strtotime($matching->begin_date)) : "")."</td>\n";
		echo "<td bgcolor='#ffffff' align='center'>".($matching->end_date != "" ? date("m/d/Y",strtotime($matching->end_date)) : "")."</td>\n";
		echo "<td bgcolor='#ffffff' align='right'>".sprintf("%01.2f",$matching->max_amount)."&nbsp;</td>\n";
		$total_max_amount += $matching->max_amount;
		$donation = new donation();
		$donation->load_donation($matching->donation_id);
		$first = true;
		$pledged_amt = 0;
		while (list($donation_project_id, $donation_project) = each($donation->donation_project_list)) {
			if (!$first)
				echo "<tr><td bgcolor='#ffffff' colspan='4'>&nbsp;</td>";
			$first = false;
			$project = new project();
			$project->load_project($donation_project->project_id);
			echo "<td bgcolor='#ffffff'>".$schools->school_name($project->school_id)."&nbsp;</td>\n";
			echo "<td bgcolor='#ffffff'><a href=\"$target$project->project_id\">$project->project_name&nbsp;</a></td>\n";
			echo "<td bgcolor='#ffffff' align='right'>".sprintf("%01.2f",$donation_project->donation_amount)."&nbsp;</td>\n";
			$pledged_amt += $donation_project->donation_amount;
			$total_donations += $donation_project->donation_amount;
			echo "</tr>\n";
		}
		if ($first)
			echo "<td bgcolor='#ffffff' colspan='2'>&nbsp;</td></tr>\n";
		else
			echo "<tr><td bgcolor='#ffffff' colspan='4'>&nbsp;</td><td bgcolor='#ffffff' colspan='2'><b>Total</b></td><td bgcolor='#ffffff' align='right'><b>".sprintf("%01.2f",$pledged_amt)."</b>&nbsp;</td></tr>\n";
	}
	echo "<tr><td bgcolor='#ffffff' colspan='3'><B>Total</B></td>";
	echo "<td bgcolor='#ffffff' align='right'><b>".sprintf("%01.2f",$total_max_amount)."</b>&nbsp;</td><td colspan='2' bgcolor='#ffffff' align='right'>&nbsp;</td>";
	echo "<td bgcolor='#ffffff' align='right'><b>".sprintf("%01.2f",$total_donations)."</b>&nbsp;</td>\n";
	echo "</tr>\n";

	mysqli_free_result($results);
?>
	</table>
<?
		include "inc/box_end.htm";
?>
                  </td>
<? require "inc/body_end.inc"; ?>
</html>
