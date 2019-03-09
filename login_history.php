<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php"; ?>
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
	if (empty($user_id))
		$message = "No user specified.";
	else {
		$user_rcd = new user;
		$user_rcd->load_user($user_id);
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
	if ($user->type_id >= 40) {
	echo "<a href=\"login_history.php?user_id=-1\">[All Logins]</a>&nbsp;&nbsp;";
	echo "<a href=\"login_history.php?user_id=0\">[Invalid Logins]</a><BR>";
	}
	if (!empty($config_history_paragraph1)) {
		include "inc/box_begin.htm";
		echo "$config_history_paragraph1";
		include "inc/box_end.htm";
	}
	include "inc/dark_box_begin.htm";
	echo "<center><h3>Login History</h3></center>";
?>
	<table bgcolor="#ffffff" width='100%' cellpadding=1 cellspacing=0 border=0><tr><td>
<?
	echo "</td></tr></table>";
?>
	<table width='100%' border=0 cellspacing=1 cellpadding=1>
		<tr>
			<td bgcolor="#ffffff"><b>Name</b></td>
			<td bgcolor="#ffffff"><b>Verified</b></td>
			<td bgcolor="#ffffff"><b>Login Date</b></td>
			<td bgcolor="#ffffff"><b>Invalid ID</b></td>
		</td>
<?
	$sql = "select user.user_id, user.first_name, user.last_name, verified, login_date, invalid_id from login_log";
	$sql .= " left join user on user.user_id = login_log.user_id";
	if ($user_id>=0)
		$where = "login_log.user_id = '$user_id'";
	else {
		$where = "login_log.user_id > '0'";
	}
	if ($from_date) {
		if ($where)
			$where .= " and ";
		$where .= "logdate >= '".date("Y-m-d", strtotime($from_date));
	}
	$sql = $sql." where ".$where." order by login_date desc limit 0,100";
	$results = $db_link->query($sql);
	if (mysqli_num_rows($results) == 0)
		echo "<tr><td bgcolor='#ffffff' colspan='4'>No Logins</td></tr>\n";
	else
		while(list($userid, $firstname, $lastname, $verified, $logdate, $invalidid) = mysqli_fetch_row($results)) {
			echo "<tr><td bgcolor='#ffffff'><a href=\"login_history.php?user_id=$userid\">$firstname $lastname</a></td>\n";
			echo "<td bgcolor='#ffffff'>$verified</td>\n";
			echo "<td bgcolor='#ffffff'>".date("M d, Y g:i:s A",strtotime($logdate))."</td>\n";
			echo "<td bgcolor='#ffffff'>$invalidid</td>\n";
			echo "</tr>\n";
		}

	mysqli_free_result($results);
?>
	</table>
<?
		include "inc/box_end.htm";
?>
                  </td>
<? require "inc/body_end.inc"; ?>
</html>
