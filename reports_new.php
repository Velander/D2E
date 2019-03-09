<?
	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";
	require_once "inc/class_users.php";
	require_once "inc/class_school.php";
	require_once "inc/class_schools.php";
	require_once "inc/class_district.php";
	require_once "inc/class_districts.php";
	require_once "inc/class_grade_level.php";
	require_once "inc/class_grade_levels.php";
	require_once "inc/class_project.php";
	require_once "inc/class_project_type.php";
	require_once "inc/class_project_types.php";
	require_once "inc/class_project_status.php";
	require_once "inc/class_project_statuses.php";
	require_once "inc/class_user_report.php";
	require_once "inc/class_user_reports.php";

	$user = new user();
	$user->load_user($User_ID);

	require "inc/validate_admin.php";
?>
<html>
<head>
<? require "inc/cssstyle.php"; ?>
<?
	$users = new users;

	$schools = new schools();
	$schools->load_schools();

	$districts = new districts();
	$districts->load_districts();

	$gradelevels = new grade_levels();
	$gradelevels->load_grade_levels();

	$projecttypes = new project_types();
	$projecttypes->load_project_types();

	$projectstatuses = new project_statuses();
	$projectstatuses->load_project_statuses();

	$pagename = "$config_reports_page_name";
	$help_msg_name = "config_reports_help";
	$help_msg = "$config_reports_help";
	$help_width = "$config_reports_help_width";
	$help_height = "$config_reports_help_height";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/home_body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
				<td width="100%" align="left" valign="top">
<?	if (!empty($message))
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center><br>";
	if (!empty($config_reports_paragraph1)) {
		include "inc/box_begin.htm";
		echo "$config_reports_paragraph1";
		include "inc/box_end.htm";
	}
	# Capture Report Options
	include "inc/dark_box_begin.htm";
	if (empty($reportno)) {
		echo "<font size='+1' color='$color_table_hdg_font'>REPORTS</font>\n";
		include "inc/box_middle.htm";
		echo "<table>\n";
		echo "\t<tr>\n";
		echo "\t\t<td><a href='reports.php?reportno=1'><b>Donation Audit Report</b></a></td><td>&nbsp;Report of all donations received.</td>\n";
		echo "\t</tr>\n";
		echo "\t<tr>\n";
		echo "\t\t<td><a href='reports.php?reportno=4'><b>Donation Distribution Report</b></a></td><td>&nbsp;Report of all donations distributed.</td>\n";
		echo "\t</tr>\n";
		echo "\t<tr>\n";
		echo "\t\t<td><a href='reports.php?reportno=2'><b>Request List Report</b></a></td><td>&nbsp;Report of all requests by school.</td>\n";
		echo "\t</tr>\n";
		echo "\t<tr>\n";
		echo "\t\t<td><a href='reports.php?reportno=3'><b>Referral Report</b></a></td><td>&nbsp;Summary of registered donors by referral name.</td>\n";
		echo "\t</tr>\n";
		echo "\t<tr>\n";
		echo "\t\t<td><a href='reports.php?reportno=5'><b>Funded Requests</b></a></td><td>&nbsp;Report of funded requests.</td>\n";
		echo "\t</tr>\n";
		echo "\t\t<td><a href='reports.php?reportno=6'><b>Generic Report</b></a></td><td>&nbsp;Report generated from supplied SQL.</td>\n";
		echo "\t</tr>\n";
		echo "</table>";
	} else {
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			if ($reportno == 1) {
				include "inc/donate_audit_output.php";
			} elseif ($reportno == 2) {
				include "inc/project_list_output.php";
			} elseif ($reportno == 3) {
				include "inc/referral_report_output.php";
			} elseif ($reportno == 4) {
				include "inc/donation_distribution_output.php";
			} elseif ($reportno == 5) {
				include "inc/funded_request_output.php";
			} elseif ($reportno == 6) {
				include "inc/generic_report_output.php";
			}
		} else {
			if ($reportno == 1) {
				# Donation Audit Report
				include "inc/donate_report_options.php";
			} elseif ($reportno == 2) {
				# Project List Report
				include "inc/project_list_options.php";
			} elseif ($reportno == 3) {
				# Referral Report
				include "inc/referral_report_options.php";
			} elseif ($reportno == 4) {
				# Referral Report
				include "inc/donation_distribution_options.php";
			} elseif ($reportno == 5) {
				# Referral Report
				include "inc/funded_request_options.php";
			} elseif ($reportno == 6) {
				# Generic Report
				include "inc/generic_report_options.php";
			}
		}
	}
	include "inc/box_end.htm";
?>
                  </td>
<? require "inc/body_end.inc"; ?>
</html>
