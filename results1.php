<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";
	require_once "inc/class_school.php";
	require_once "inc/class_schools.php"; ?>
<html>
<head>
<? require "inc/cssstyle.php"; ?>
<?
	$pagename = "$config_results_page_name";
	$help_msg_name = "config_results_help";
	$help_msg = "$config_results_help";
	$help_width = "$config_results_help_width";
	$help_height = "$config_results_help_height";
	$schools = new schools();
	$schools->load_schools();
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <td width="655" align="left" valign="top">
<?	if (!empty($message))
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center><br>";
	if (!empty($config_results_paragraph1)) {
		include "inc/box_begin.htm";
		echo "$config_results_paragraph1";
		include "inc/box_end.htm";

		include "inc/box_begin.htm";
		$dateinfo = getdate();
		$monthname = $dateinfo[month];
		$year = $dateinfo[year];
		echo "<table width='100%' border=0 cellspacing=3 cellpadding=4><tr><td align='center' colspan=2><b>Donations in $monthname $year</b></td></tr>";
		$results = $db_link->query("select count(donation_id) Dcount, Sum(donation_amount) damount from donation where matching_donation ='N' and payment_received is not null and DATE_FORMAT(donation_date,'%M') = '".$monthname."' and DATE_FORMAT(donation_date,'%Y') = '$year'");
		list($cnt, $total) = mysqli_fetch_row($results);
		mysqli_free_result($results);

		echo "<tr><td width='50%' align='right'>Donations Made</td><td align='left'>$cnt</td></tr>\n";
		echo "<tr><td width='50%' align='right'>Total Donations</td><td align='left'>$".sprintf("%01.2f",$total)."</td></tr>\n";
		echo "</table>\n";
		include "inc/box_end.htm";

		include "inc/box_begin.htm";
		echo "<table width='100%' border=0 cellspacing=3 cellpadding=4><tr><td align='center' colspan=2><b>Donations to Date</b></td></tr>";
		$results = $db_link->query("select count(donation_id) Dcount, Sum(donation_amount) damount from donation where payment_received = 'Y' and payment_authorized = 'Y'");
		list($cnt, $total) = mysqli_fetch_row($results);
		mysqli_free_result($results);

		echo "<tr><td width='50%' align='right'>Donations Made</td><td align='left'>$cnt</td></tr>\n";
		echo "<tr><td width='50%' align='right'>Total Donations</td><td align='left'>$".sprintf("%01.2f",$total)."</td></tr>\n";
		echo "</table>\n";
		include "inc/box_end.htm";

		include "inc/box_begin.htm";
		echo "<table width='100%' border=0 cellspacing=3 cellpadding=4><tr><td align='center' colspan=2><b>Project Totals to Date</b></td></tr>";
		$results = $db_link->query("select count(project_id) ProjectCount from Project where project_status_id=1 or project_status_id=3 or project_status_id=4 or project_status_id=5");
		list($cnt) = mysqli_fetch_row($results);
		mysqli_free_result($results);
		echo "<tr><td width='50%' align='right'>Projects Submitted</td><td align='left'>$cnt</td></tr>\n";
		$results = $db_link->query("select count(project_id) ProjectCount from Project where project_status_id = 4 or project_status_id=5 or project_status_id=3");
		list($cnt) = mysqli_fetch_row($results);
		mysqli_free_result($results);
		echo "<tr><td align='right'>Projects Approved</td><td align='left'>$cnt</td></tr>\n";
		$results = $db_link->query("select count(project_id) ProjectCount from Project where project_status_id = 4 or project_status_id=5");
		list($cnt) = mysqli_fetch_row($results);
		mysqli_free_result($results);
		echo "<tr><td align='right'><a href=\"results_search.php?f_school_id=0\"><font size=\"-1\">Projects Funded</font></a></td><td align='left'>$cnt</td></tr>\n";
		$results = $db_link->query("select count(distinct school_id) ProjectCount from Project where project_status_id = 1 or project_status_id=3 or project_status_id = 4 or project_status_id=5");
		list($cnt) = mysqli_fetch_row($results);
		mysqli_free_result($results);
		echo "<tr><td align='right'>Schools Represented</td><td align='left'>$cnt</td></tr>\n";
		echo "</table>\n";
		include "inc/box_end.htm";

		include "inc/box_begin.htm";
		echo "<table width='100%' border=0 cellspacing=3 cellpadding=4><tr><td align='center' colspan=4><b>Contest Results</b></td></tr>";
		echo "<tr><td width='25%' align='right'><b>School</b></td><td align='right'><b>Goal</b></td><td align='right'><b>Points</b></td><td width='50%' align='Center'><b>Progress</b></td></tr>\n";
		#select school.school_id , ifnull(cnt,0) cnt, ifnull(amt, 0) amt from school
		#left join (select referral_schoolid, count(user_id) cnt from user
		#where referral_schoolid <> 0 and setup_date >= '2005-12-12' group by referral_schoolid ) s on school.school_id = s.referral_schoolid
		#left join (select project.school_id, sum(donation_amount) amt from donation
		#inner join project on donation.project_id = project.project_id
		#where donation_date >= '2005-12-12' AND payment_received = 'Y' group by project.school_id ) d on school.school_id = d.school_id
		#order by school.school_name

		$sql = "select school.school_id, school.contest_goal from school where contest_goal > 0";
		$sql .= " order by school.school_name";
		$results = $db_link->query($sql);
		while (list($schoolid, $goal) = mysqli_fetch_row($results)) {
			# Count Donors Registered
			$sql = "select count(user_id) cnt from user where setup_date >= '2005-12-12' and referral_schoolid = '$schoolid'";
			$cntresults = $db_link->query($sql);
			list($usercount) = mysqli_fetch_row($cntresults);
			# Count Teachers Logged In
			$sql = "select U.User_id, max(LL.`login_date`) logindate from User as U inner join `user_affiliation` as UA on U.user_id = UA.User_id inner join login_log as LL on U.user_id = LL.user_id and LL.login_date >= '2005-12-12' Where UA.School_id = '$schoolid' And (U.user_type_id = 20 or U.user_type_id = 25) group by U.user_id";
			if ($cntresults = $db_link->query($sql))
				$teachercount = mysqli_num_rows($cntresults);
			else
				$teachercount = 0;
			# Count Donations Made
			$sql = "SELECT sum( donation_amount ) amt FROM donation INNER JOIN project ON donation.project_id = project.project_id WHERE donation_date >= '2005-12-12' AND payment_received = 'Y' AND project.school_id = '$schoolid'";
			$donresults = $db_link->query($sql);
			list($donations) = mysqli_fetch_row($donresults);
			echo "<tr><td width='25%' align='right'>".$schools->school_name($schoolid)."</td><td align='right'>".$goal."</td><td align='right'>".round($usercount + $donations + $teachercount)."</td>";
			#echo "<td align='center'>".round($progress * 170)."</td>";
			echo "<td align='center'><img src=\"images/progress_end_gray.bmp\" width=2 height=15>";
			$progress = ($usercount + $donations + $teachercount)/$goal;
			if ($progress > 2) $progress = 2;
			if ($progress >= 1) {
				echo "<img src=\"images/gray_dot.bmp\" height=15 width=".round((1 * 85)-1).">";
				echo "<img src=\"images/red_dot.bmp\" height=15 width=2>";
				echo "<img src=\"images/gray_dot.bmp\" height=15 width=".round(($progress * 85)-86).">";
				if ($progress < 2)
					echo "<img src=\"images/progress_middle_gray.bmp\" height=15 width=".round(85-(($progress * 85)-85)).">";
			} else {
				echo "<img src=\"images/gray_dot.bmp\" height=15 width=".round($progress * 85).">";
				echo "<img src=\"images/progress_middle_gray.bmp\" height=15 width=".round(85-($progress * 85)).">";
				echo "<img src=\"images/red_dot.bmp\" height=15 width=2>";
				echo "<img src=\"images/progress_middle_gray.bmp\" height=15 width=85>";
			}
			echo "<img src=\"images/progress_end_gray.bmp\" width=2 height=15></td>";
			echo "<td align='right'>".sprintf("%01.1f", ((($usercount + $donations + $teachercount)/$goal)*100))."%</td>";
			echo "</tr>\n";
		}
			echo "<tr><td colspan=4 align='right'><Font color='red'>* Red line indicates minimum goal.</font></td><td>&nbsp;</td></tr>";
		echo "</table>\n";
		include "inc/box_end.htm";
		mysqli_free_result($results);

		include "inc/box_begin.htm";
		echo "<table width='100%' border=0 cellspacing=6 cellpadding=1>";
		echo "<tr><td width='50%' align='center' colspan=2><b>Project Totals by Category</b></td></tr>";
		echo "<tr><td width='50%' align='right'><b>Catgegory</b></td><td align='left'><b>Projects</b></td></tr>\n";
		$results = $db_link->query("select project_type_description, count(project_id) ProjectCount from project inner join project_type on project.project_type_id = project_type.project_type_id where project_status_id=1 or project_status_id=3 or project_status_id=4 or project_status_id=5 group by project_type_description");
		while (list($cat, $cnt) = mysqli_fetch_row($results)) {
			echo "<tr><td  width='50%' align='right'>$cat</td><td align='left'>$cnt</td></tr>\n";
		}
		mysqli_free_result($results);
		echo "</table>\n";
		include "inc/box_end.htm";
		include "inc/box_begin.htm";
		echo "<table width='100%' border=0 cellspacing=6 cellpadding=1>";
		echo "<tr><td width='50%' align='center' colspan=2><b>Project Totals by School</b></td></tr>";
		echo "<tr><td width='50%' align='right'><b>School</b></td><td align='left'><b>Projects</b></td></tr>\n";
		$results = $db_link->query("select project.school_id, school_name, count(project_id) ProjectCount from project inner join school on project.school_id = school.school_id where project_status_id=1 or project_status_id=3 or project_status_id=4 or project_status_id=5 group by project.school_id, school_name");
		while (list($sid, $cat, $cnt) = mysqli_fetch_row($results)) {
			echo "<tr><td  width='50%' align='right'>$cat</td><td align='left'>$cnt</td>";
			echo "<td><a href=\"results_search.php?f_school_id=$sid\"><font size=\"-1\">Funded</font></a></td></tr>\n";
		}
		mysqli_free_result($results);
		echo "</table>\n";
		include "inc/box_end.htm";
	}
?>
				</td>
<? require "inc/body_end.inc"; ?>
</html>
