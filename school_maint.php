<?	require "inc/db_inc.php";
	require_once "inc/func.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";
	require_once "inc/class_users.php";
	require_once "inc/class_grade_level.php";
	require_once "inc/class_grade_levels.php";
	require_once "inc/class_school.php";
	require_once "inc/class_schools.php";
	require_once "inc/class_district.php";
	require_once "inc/class_districts.php";
	require_once "inc/class_user_type.php";
	require_once "inc/class_user_types.php";
	require_once "inc/class_affiliation.php";
	require_once "inc/class_affiliations.php";
	require_once "inc/class_state.php";
	require_once "inc/class_states.php";
	require_once "inc/class_country.php";
	require_once "inc/class_countries.php";

if ($debug) {
	echo "loading user<br>";
	flush();
}

	require_once "inc/admin_user.php";

	$users = new users();

if ($debug) {
	echo "loading schools<br>";
	flush();
}
	$schools = new schools();
	$schools->load_schools();

if ($debug) {
	echo "loading districts<br>";
	flush();
}
	$districts = new districts();
	$districts->load_districts();

if ($debug) {
	echo "loading states<br>";
	flush();
}
	$states = new states();
	$states->load_states();

	$countries = new countries();
	$countries->load_countries();

	$gradelevels = new grade_levels();
	$gradelevels->load_grade_levels();

if ($debug) {
	echo "loading user_types<br>";
	flush();
}
	$usertypes = new user_types();
	$usertypes->load_user_types();

	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		if ($Submit == "New") {
			$school_id = "";
			$f_school_id = "";
		} else {
			if ($Submit == "Save") {
				// Validate the fields.
				$errors = "";
				if (empty($f_school_name))
					$errors .= (empty($errors) ? "" : "<BR>")."School Name is required.";
				if (empty($f_district_id))
					$errors .= (empty($errors) ? "" : "<BR>")."A distrinct must be selected.";
				if (!empty($f_existing_school_id)) {
					$f_school_id = $f_existing_school_id;
				}
			}
			if (empty($errors)) {
				// Save district
				$school_rcd = new school();
				if (!empty($f_existing_school_id)) {
					$school_rcd->load_school($f_existing_school_id);
					$f_school_id = $f_existing_school_id;
				}
				if ($Submit == "Search") {
					$schoolsearch = new schools;
					$schoolsearch->find_schools($f_school_id, $f_school_name, $f_street, $f_city, $f_state, $f_zip, $f_phone, $f_contact_user_id, $f_number_of_students_min, $f_number_of_students_max, $f_percent_free_lunch_min, $f_percent_free_lunch_max, $f_grade_level_id, $f_district_id, $f_contest_goal_min, $f_contest_goal_max, $f_expiration_date_min, $f_expiration_date_max, $f_inactive, $f_sponsor_user_id);
					if ($schoolsearch->count() == 0)
						$message = "No matching schools found.";
				} elseif ($Submit == "Cancel") {
					$f_school_id = 0;
					$f_existing_school_id = 0;
				} elseif ($Submit == "Delete") {
					if ($delete_confirm == "YES") {
						$Submit = "";
						if ($school_rcd->delete_school()) {
							$message .= "School $school_rcd->school_name deleted.";
							$f_school_id = 0;
							$f_existing_school_id = 0;
						} else
							$message .= "<p>Errors occured deleting this school!<br>$school_rcd->error_message</p>";
					}
				} elseif ($Submit == "Save") {
					$school_rcd->school_name 		= $f_school_name;
					$school_rcd->street				= $f_street;
					$school_rcd->city				= $f_city;
					$school_rcd->state				= $f_state;
					$school_rcd->zip				= $f_zip;
					$school_rcd->phone				= $f_phone;
					$school_rcd->homepage			= $f_homepage;
					$school_rcd->contact_user_id	= $f_contact_user_id;
					$school_rcd->volunteer_user_id 	= $f_volunteer_user_id;
					$school_rcd->number_of_students	= $f_number_of_students;
					$school_rcd->percent_free_lunch = $f_percent_free_lunch;
					$school_rcd->grade_level_id		= $f_grade_level_id;
					$school_rcd->district_id		= $f_district_id;
					$school_rcd->contest_goal		= $f_contest_goal;
					$school_rcd->sponsor_user_id	= $f_sponsor_user_id;
					$school_rcd->expiration_date	= (empty($f_expiration_date) ? "" : date("Y-m-d", strtotime($f_expiration_date)));
					$school_rcd->inactive			= (empty($f_inactive) ? "N" : $f_inactive);

					if ($school_rcd->save_school()) {
						$message .= "School $school_rcd->school_name saved.<br>$school_rcd->error_message";
						$school_id = $school_rcd->school_id;
						$f_school_id = $school_id;
					} else
						$message .= "<p>Errors occured saving this school!<br>$school_rcd->error_message</p>";
				}
			} else {
				$message .= "<p>Please correct the following items and then resubmit:<br>$errors</p>";
			}
		}
	} else {
		if (!empty($school_id)) {
			$school_rcd = new school();
			if ($school_rcd->load_school($school_id)) {
				$f_school_id			= $school_rcd->school_id;
				$f_school_name			= $school_rcd->school_name;
				$f_street				= $school_rcd->street;
				$f_city					= $school_rcd->city;
				$f_state				= $school_rcd->state;
				$f_zip					= $school_rcd->zip;
				$f_phone				= $school_rcd->phone;
				$f_homepage				= $school_rcd->homepage;
				$f_contact_user_id		= $school_rcd->contact_user_id;
				$f_volunteer_user_id	= $school_rcd->volunteer_user_id;
				$f_number_of_students 	= $school_rcd->number_of_students;
				$f_percent_free_lunch 	= $school_rcd->percent_free_lunch;
				$f_grade_level_id		= $school_rcd->grade_level_id;
				$f_district_id			= $school_rcd->district_id;
				$f_contest_goal			= $school_rcd->contest_goal;
				$f_sponsor_user_id		= $school_rcd->sponsor_user_id;
				$f_expiration_date		= (empty($school_rcd->expiration_date) ? "" : date("Y-m-d", strtotime($school_rcd->expiration_date)));
				$f_inactive				= $school_rcd->inactive;
			} else {
				$f_school_id = "";
				$f_district_id = "0";
			}
		}
	}
?>
<html>
<head>
<?
	include "inc/cssstyle.php";
	$pagename = "$config_schoolmaint_page_name";
	$help_msg_name = "config_schoolmaint_help";
	$help_msg = "$config_schoolmaint_help";
	$help_width = "$config_schoolmaint_help_width";
	$help_height = "$config_schoolmaint_help_height";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <td width="655" align="left" valign="top">
<?	echo "$config_schoolmaint_paragraph1";
	if (!empty($message)) {
		include "inc/box_begin.htm";
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center>";
		include "inc/box_end.htm";
	}
	if ($Submit == "Delete") {
?>
	<table width=100% cellspacing=1 cellpadding=1>
		<tr>
			<td align=left width=50% bgcolor="<?=$color_table_hdg_bg;?>">
	<table width=100% cellspacing=1 cellpadding=1>
		<tr>
			<td align=left width=50% bgcolor="<?=$color_table_col_bg;?>">
				<TABLE DIR=ltr ID="school_list" ALIGN=bleedright WIDTH="100%" COLS=4 BORDER="0" CELLSPACING="2" CELLPADDING="2">
<?		if ($school->projectcount > 0) { ?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='100' Align='Right'><b>School ID</b></TD>
						<TD>
							<?
								echo "$f_school_id";
							?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='100' Align='Right'><b>School Name</b></TD>
						<TD><?=$f_school_name;?></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='100' Align='Right' Valign='Top'><b>Projects</b></TD>
						<TD>
<?
			reset($school->project_list);
			while (list($project_id, $project) = each($school->project_list)) {
				echo "$project->project_name<BR>";
			}
?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle" bgcolor="<?=$color_table_hdg_bg;?>">
						<TD Align='center' Colspan='2'><Font size='+1' color="<?=$color_table_hdg_font;?>">You cannot delete a School that has projects assigned to it.</font></TD>
					</TR>
<?
} else {
?>
					<Form Name='frmDelete' Method='POST'>
					<TR ALIGN="left" VALIGN="middle" bgcolor="<?=$color_table_hdg_bg;?>">
						<TD Align='center' Colspan='2'><Font size='+1' color="<?=$color_table_hdg_font;?>">School Delete Confirmation</font></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='100' Align='Right'><b>School ID</b></TD>
						<TD>
							<?
								echo "$f_school_id";
								echo "<input type='hidden' name='f_existing_school_id' value='$f_school_id'>";
							?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='100' Align='Right'><b>School Name</b></TD>
						<TD><?=$f_school_name;?></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='100' Align='Right'><b>City</b></TD>
						<TD><?=$f_city;?></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='100' Align='Right'><b>State</b></TD>
						<TD><?=$f_state;?></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='center' Colspan='2'>
							<Font Size='+1' Color='Red'><b>Are you sure you want to delete this school?</b></Font>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='center' Colspan='2'>
<?
			echo "<Input Type='Hidden' Name='delete_confirm' Value='YES'>";
			echo "<Input Type='Submit' Class='nicebtns' Name='Submit' Value='Cancel'>&nbsp;&nbsp;";
			echo "<Input Type='Submit' Class='nicebtns' Name='Submit' Value='Delete'>";
?>
						</TD>
					</TR>
				</Form>
<?
	}
?>
				</TABLE>
			</td>
		</tr>
	</table>
			</td>
		</tr>
	</table>
<?
	} else {
		if ($Submit == "Search" && $schoolsearch->count() > 0) {
?>
	<table width=100% cellspacing=1 cellpadding=1>
		<tr>
			<td align=left width=50% bgcolor="<?=$color_table_hdg_bg;?>">
				<TABLE DIR=ltr ID="project_list" ALIGN=bleedright WIDTH="100%" COLS=4 BORDER="0" CELLSPACING="2" CELLPADDING="2">
					<Form Name='frmDelete' Method='POST'>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='center' Colspan='4'><Font size='+1' color="<?=$color_table_hdg_font;?>">School Search Results</font></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='left' Colspan='4'><Font size='-1' color="<?=$color_table_hdg_font;?>">Click on the school's name to edit that school.</font></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD><Font color="<?=$color_table_hdg_font;?>">SchoolID</font></TD>
						<TD><Font color="<?=$color_table_hdg_font;?>">School Name</font></TD>
						<TD><Font color="<?=$color_table_hdg_font;?>">City</font></TD>
						<TD><Font color="<?=$color_table_hdg_font;?>">State</font></TD>
					</TR>
<?
			reset($schoolsearch->school_list);
			while (list($schoolid, $sschool) = each($schoolsearch->school_list)) {
				echo "\t\t\t\t<TR>\n";
				echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"school_maint.php?school_id=$sschool->school_id\">$sschool->school_id</a></TD>\n";
				echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"school_maint.php?school_id=$sschool->school_id\">$sschool->school_name</a></TD>\n";
				echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"school_maint.php?school_id=$sschool->school_id\">$sschool->city</a></TD>\n";
				echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"school_maint.php?school_id=$sschool->school_id\">$sschool->state</a></TD>\n";
				echo "\t\t\t\t</TR>\n";
			}
?>
				</TABLE>
			</td>
		</tr>
	</table>
<?
		} else {
?>
<table width=100% cellspacing=1 cellpadding=1>
  <TR ALIGN="left" VALIGN="middle">
	<td align=left width=50% bgcolor="<?=$color_table_hdg_bg;?>">
	<table width=100% cellspacing=1 cellpadding=1>
		<TR ALIGN="left" VALIGN="middle">
			<TD Align='center' bgcolor="<?=$color_table_hdg_bg;?>"><font size="+1" color="<?=$color_table_hdg_font;?>">School Maintenance</font></TD>
		</TR>
		<TR ALIGN="left" VALIGN="middle">
			<TD Align='center' bgcolor="<?=$color_table_hdg_bg;?>"><font size="-1" color="<?=$color_table_hdg_font;?>">Enter a new school and click <b>Save</b>, or enter search criteria and click <b>Search</b>.</font></TD>
		</TR>
		<tr>
			<td align=left width=50% bgcolor="<?=$color_table_col_bg;?>">
				<TABLE ALIGN="left" BORDER=0 CELLSPACING=10 CELLPADDING=0 WIDTH="100%">
					<Form Name='frmUser' Method='POST'>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='30%' Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>School ID</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>">
							<? 	if (empty($f_school_id))
									echo "<input type='text' name='f_school_id' size=5>";
								else {
									echo "<B>$f_school_id</B>";
									echo "<input type='hidden' name='f_existing_school_id' value='$f_school_id'>";
								}
							?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>School Name</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='50' maxlength='50' name='f_school_name' value='<?=$f_school_name;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Street</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='40' maxlength='100' name='f_street' value='<?=$f_street;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>City</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='40' maxlength='50' name='f_city' value='<?=$f_city;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>State</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><Select name='f_state'>
<?
			#if (empty($f_state))
			#	$f_state = $config_default_state;
			reset($states->state_list);
			$prev_grp = "";
			while (list($statecode, $state) = each($states->state_list)) {
				if ($state->state_group != $prev_grp) {
					if (!empty($prev_grp))
						echo "</optgroup>";
					$prev_grp = $state->state_group;
					echo ("\t\t\t\t\t\t\t\t\t\t<optgroup label=\"$state->state_group\">\n");
				}
				echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"$statecode\"".($statecode == $f_state ? " SELECTED" : "").">$state->state_name</OPTION>\n");
			}
			if (!empty($prev_grp))
				echo "</optgroup>";
?>
							</select>
 						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Zip Code</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='10' maxlength='10' name='f_zip' value='<?=$f_zip;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Phone</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='30' maxlength='100' name='f_phone' value='<?=$f_phone;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Home Page</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='60' maxlength='128' name='f_homepage' value='<?=$f_homepage;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>District</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>">
						<SELECT NAME="f_district_id" SIZE=1>
							<OPTION value='0'></OPTION>
<?	reset($districts->district_list);
	while (list($districtid, $district) = each($districts->district_list)) {
		echo ("<OPTION VALUE=\"$district->district_id\"".($district->district_id == $f_district_id ? " SELECTED" : "") .">$district->district_name</OPTION>\n");
	}
?>
						</SELECT>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Contact User ID</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='10' maxlength='10' name='f_contact_user_id' value='<?=$f_contact_user_id;?>'>
<? 	if ($f_contact_user_id) {
		echo "&nbsp;&nbsp;<a target=\"sponsor\" href=\"user_maint.php?user_id=$f_contact_user_id\">".$users->user_name($f_contact_user_id)."</a>";
	}
?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Volunteer User ID</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='10' maxlength='10' name='f_volunteer_user_id' value='<?=$f_volunteer_user_id;?>'>
<? 	if ($f_volunteer_user_id) {
		echo "&nbsp;&nbsp;<a target=\"sponsor\" href=\"user_maint.php?user_id=$f_volunteer_user_id\">".$users->user_name($f_volunteer_user_id)."</a>";
	}
?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Number of Students</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='12' maxlength='12' name='f_number_of_students' value='<?=$f_number_of_students;?>'>
<? if (!$f_school_id) { ?>
						&nbsp;Search Range:&nbsp;<input type='text' size='12' maxlength='12' name='f_number_of_students_min' value='<?=$f_number_of_students_min;?>'>&nbsp;to&nbsp;<input type='text' size='12' maxlength='12' name='f_number_of_students_max' value='<?=$f_number_of_students_max;?>'>
<? } ?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Percent Free Lunch</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='12' maxlength='12' name='f_percent_free_lunch' value='<?=$f_percent_free_lunch;?>'>
<? if (!$f_school_id) { ?>
						&nbsp;Search Range:&nbsp;<input type='text' size='12' maxlength='12' name='f_percent_free_lunch_min' value='<?=$f_percent_free_lunch_min;?>'>&nbsp;to&nbsp;<input type='text' size='12' maxlength='12' name='f_percent_free_lunch_max' value='<?=$f_percent_free_lunch_max;?>'>
<? } ?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Grade Level</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>">
						<SELECT NAME="f_grade_level_id" SIZE=1>
							<OPTION value='0'></OPTION>
<?	reset($gradelevels->grade_level_list);
	while (list($gradelevelid, $gradelevel) = each($gradelevels->grade_level_list)) {
		echo ("<OPTION VALUE=\"$gradelevel->grade_level_id\"".($gradelevel->grade_level_id == $f_grade_level_id ? " SELECTED" : "") .">$gradelevel->grade_level_description</OPTION>\n");
	}
?>
						</SELECT>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Contest Goal</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='12' maxlength='12' name='f_contest_goal' value='<?=$f_contest_goal;?>'>
<? if (!$f_school_id) { ?>
						&nbsp;Search Range:&nbsp;<input type='text' size='12' maxlength='12' name='f_contest_goal_min' value='<?=$f_contest_goal_min;?>'>&nbsp;to&nbsp;<input type='text' size='12' maxlength='12' name='f_contest_goal_max' value='<?=$f_contest_goal_max;?>'>
<? } ?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Sponsor User ID</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='10' maxlength='10' name='f_sponsor_user_id' value='<?=$f_sponsor_user_id;?>'>
<? 	if ($f_sponsor_user_id) {
		echo "&nbsp;&nbsp;<a target=\"sponsor\" href=\"user_maint.php?user_id=$f_sponsor_user_id\">".$users->user_name($f_sponsor_user_id)."</a>";
	}
?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>Expiration Date</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>">
						<input type='text' size='12' maxlength='12' name='f_expiration_date' value='<?=(empty($f_expiration_date) ? "" : date("m/d/Y",strtotime($f_expiration_date)));?>'>
<? if (!$f_school_id) { ?>
						&nbsp;Search Range:&nbsp;<input type='text' size='12' maxlength='12' name='f_expiration_date_min' value='<?=(empty($f_expiration_date_min) ? "" : date("m/d/Y",strtotime($f_expiration_date_min)));?>'>&nbsp;to&nbsp;<input type='text' size='12' maxlength='12' name='f_expiration_date_max' value='<?=(empty($f_expiration_date_max)? "" : date("m/d/Y",strtotime($f_expiration_date_max)));?>'>
<? } ?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>Inactive</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><select name='f_inactive'>
						<option></option>
						<option<?=(($f_inactive == "N") ? " SELECTED" : "");?>>N</option>
						<option<?=(($f_inactive == "Y") ? " SELECTED" : "");?>>Y</option>
						</select>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='left' Colspan='2' bgcolor="<?=$color_table_col_bg;?>">
<?
			if (empty($f_school_id))
				echo "<Input Type='Submit' Class='nicebtns' Name='Submit' Value='Search'>\n|&nbsp;&nbsp;";
			echo "<Input Type='Submit' Class='nicebtns' Name='Submit' Value='Save'>&nbsp;&nbsp;\n";
			if (!empty($f_school_id))
				echo "|&nbsp;&nbsp;<Input Type='Submit' Class='nicebtns' Name='Submit' Value='Delete'>&nbsp;&nbsp;\n";
			if (!empty($f_school_id))
				echo "|&nbsp;&nbsp;<Input Type='Submit' Class='nicebtns' Name='Submit' Value='New'>\n";
?>
						</TD>
					</TR>
					</Form>
				</TABLE>
			</td>
		</tr>
	</table>
	</td>
  </tr>
</table>
<?
		}
	}
?>
                  </td>
<? require "inc/body_end.inc"; ?>
</html>
