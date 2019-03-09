<?	require "inc/db_inc.php";
	require_once "inc/func.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";
	require_once "inc/class_users.php";
	require_once "inc/class_project.php";
	require_once "inc/class_district.php";
	require_once "inc/class_districts.php";
	require_once "inc/class_matching.php";
	require_once "inc/class_project.php";
	require_once "inc/class_projects.php";
	require_once "inc/class_project_type.php";
	require_once "inc/class_project_types.php";
	require_once "inc/class_school.php";
	require_once "inc/class_schools.php";
	require_once "inc/class_state.php";
	require_once "inc/class_states.php";


if ($debug) {
	echo "loading user<br>";
	flush();
}
	$user = new user();
	$user->load_user($User_ID);

#	require "inc/validate_admin.php";

if ($debug) {
	echo "loading schools<br>";
	flush();
}
	$schools = new schools();
	$schools->load_schools();

if ($debug) {
	echo "loading states<br>";
	flush();
}
	$states = new states();
	$states->load_states("1");

if ($debug) {
	echo "loading districts<br>";
	flush();
}
	$districts = new districts();
	$districts->load_districts();

if ($debug) {
	echo "loading project_types<br>";
	flush();
}
	$project_types = new project_types();
	$project_types->load_project_types();

	$users = new users();

	if (($user->type_id != 50) && ($user->allow_matching != "Y")) {
		# Matching maint is not allowed.
		echo "<script type=\"text/javascript\">\nlocation.href='index.php?message=".htmlentities(urlencode("Access Restricted."))."'\n</script>\n";
	}
	if ($deletecommentid) {
		$matching_comment = new matching_comment;
		$matching_comment->load_matching_comment($deletecommentid);
		$matching_id = $matching_comment->matching_id;
		if (($user->type_id == 50) || ($user->user_id == $matching_comment->user_id)) {
			if ($matching_comment->delete_matching_comment())
			{
				$message .= "Comment deleted.<BR>";
			} else {
				$message .= "Comment delete failed.<BR>Error: $matching_comment->error_message<BR>";
			}
		} else {
			$message .= "Access denied.<BR>";
		}
	}
?>
<html>
<head>
<?
	include "inc/cssstyle.php";
	$pagename = "$config_matchmaint_page_name";
	$help_msg_name = "config_matchmaint_help";
	$help_msg = "$config_matchmaint_help";
	$help_width = "$config_matchmaint_help_width";
	$help_height = "$config_matchmaint_help_height";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <td width="655" align="left" valign="top">
<?	echo "$config_matchmaint_paragraph1";
	if (!empty($message)) {
		include "inc/box_begin.htm";
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center>";
		include "inc/box_end.htm";
		$message = "";
	}
	$matching = new matching();
 	if ($matching_id) {
 		$matching->load_matching($matching_id);
 		if (($user->type_id != 50) && ($matching->user_id != $user->user_id)) {
 			$message = "Access restricted.";
 			$matching_id = "0";
 		}
 	}
 	if ($matching_id) {
		$matching->load_matching($matching_id);
		$f_matching_id		= $matching_id;
		$f_existing_matching_id = $matching_id;
		$f_user_id			= $matching->user_id;
		$f_date_created		= $matching->date_created;
		$f_begin_date		= $matching->begin_date;
		$f_end_date			= $matching->end_date;
		$f_district_list	= $matching->matching_district_list;
		$f_state_list		= $matching->matching_state_list;
		$f_school_list		= $matching->matching_school_list;
		$f_project_type_list= $matching->matching_project_type_list;
		$f_project_list		= $matching->matching_project_list;
		$f_max_amount		= $matching->max_amount;
		$f_donation_id		= $matching->donation_id;
	} else {
		$f_district_list	= array();
		while(list($idx,$district_id) = each($f_district_id)) {
			$matching_district = new matching_district();
			$matching_district->district_id = $district_id;
			$f_district_list[] = $matching_district;
		}
		$f_state_list		= array();
		while(list($idx,$statecode) = each($f_state)) {
			$matching_state = new matching_state();
			$matching_state->state = $statecode;
			$f_state_list[] = $matching_state;
		}
		$f_school_list		= array();
		while(list($idx,$schoolid) = each($f_school)) {
			$matching_school = new matching_school();
			$matching_school->school_id = $schoolid;
			$f_school_list[] = $matching_school;
		}
		$f_project_type_list= array();
		while(list($idx,$projecttypeid)=each($f_project_type)) {
			$matching_project_type = new matching_project_type();
			$matching_project_type->project_type_id = $projecttypeid;
			$f_project_type_list[] = $matching_project_type;
		}
		$f_project_list		= array();
		while(list($idx,$projectid) = each($f_project_id)) {
			if(!$f_project_delete[$idx]) {
				if ($projectid) {
					$project = new project();
					if ($project->load_project($projectid)) {
						$matching_project = new matching_project();
						$matching_project->project_id = $projectid;
						$f_project_list[] = $matching_project;
					}
				}
			}
		}
	}
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		if ($Submit == "Add Requests from Cart")
		{
			reset($user->cart_item_list);
			while (list($cartid, $cartitem) = each($user->cart_item_list)) {
				reset($f_project_list);
				$found = false;
				while(list($matchingprojectid, $matchingproject) = each($f_project_list)) {
					if ($matchingproject->project_id == $cartitem->project_id) {
						$found = true;
						break;
					}
				}
				if (!$found) {
					$matching_project = new matching_project();
					$matching_project->project_id = $cartitem->project_id;
					$f_project_list[] = $matching_project;
				}
			}
		} else if ($Submit == "Save")
		{
			$matching = new matching();
			if ($f_existing_matching_id) {
				$matching->load_matching($f_existing_matching_id);
			} else {
				$matching->user_id 	= $f_user_id;
			}
			$matching->begin_date 	= date("Y-m-d", strtotime($f_begin_date));
			if ($f_end_date)
				$matching->end_date		= date("Y-m-d", strtotime($f_end_date));
			else
				$matching->end_date		= "";
			# Don't allow the max_amount on an existing record to be reduced below the total already matched.
			if ($matching->donation_id) {
				$donation = new donation();
				if ($donation->donation_total > $max_amount) {
					$max_amount = $donation->donation_total;
					$message .= "Max Amount cannot be less that amount already matched.<BR>";
				}
			}
			$matching->max_amount	= $f_max_amount;
			# Add States
			$matching->matching_state_list = array();
			reset($f_state_list);
			while(list($matchingstateid, $matchingstate) = each($f_state_list)) {
				$matching->add_state($matchingstate->state);
			}
			# Add Districts
			$matching->matching_district_list = array();
			reset($f_district_list);
			while(list($matchingdistrictid, $matchingdistrict) = each($f_district_list)) {
				$matching->add_district($matchingdistrict->district_id);
			}
			# Add Schools
			$matching->matching_school_list = array();
			reset($f_school_list);
			while (list($matchingschoolid, $matchingschool) = each($f_school_list)) {
				$matching->add_school($matchingschool->school_id);
			}
			# Add Project_Types
			$matching->matching_project_type_list = array();
			reset($f_project_type_list);
			while (list($matchingprojecttypeid, $matchingprojecttype) = each($f_project_type_list)) {
				$matching->add_project_type($matchingprojecttype->project_type_id);
			}
			# Add Requests
			$matching->matching_project_list = array();
			reset($f_project_list);
			while(list($matchingprojectid, $matchingproject) = each($f_project_list)) {
				$matching->add_project($matchingproject->project_id);
			}
			if ($matching->save_matching())
			{
				$f_matching_id = $matching->matching_id;
				$f_existing_matching_id = $f_matching_id;
				$message .= "Matching record saved.<BR>";
			} else {
				$message .= "Save Error: $matching->error_message<BR>";
			}
		} else if ($Submit == "Add Comment")
		{
			if ($matching_id && $f_newcomment) {
				if ($matching->add_comment($f_newcomment,$user->user_id)) {
					$message .= "Comment saved.<BR>";

				} else {
					$message .= "Comment save failed.<BR>Error: $matching->error_message<BR>";
				}
			}
		} else if ($Submit == "Delete")
		{
			$matching = new matching();
			if ($f_existing_matching_id) {
				if ($matching->load_matching($f_existing_matching_id)) {
					if ($matching->delete_matching()) {
						$message .= "Matching record deleted.<BR>";
						echo "<script type=\"text/javascript\">\nlocation.href='matching_maint.php?message=".htmlentities(urlencode($message))."'\n</script>\n";
					} else {
						$message = "$matching->error_message";
						echo "<script type=\"text/javascript\">\nlocation.href='matching_maint.php?message=".htmlentities(urlencode($message))."'\n</script>\n";
					}
				} else {
					$message = "Unable to load matching record.";
					echo "<script type=\"text/javascript\">\nlocation.href='matching_maint.php?message=".htmlentities(urlencode($message))."'\n</script>\n";
				}
			} else {
				$message = "No matching ID provided.";
				echo "<script type=\"text/javascript\">\nlocation.href='matching_maint.php?message=".htmlentities(urlencode($message))."'\n</script>\n";
			}
		} else if ($Submit == "Search")
		{
			flush;
			if (($user->type_id != 50) && ($matching->user_id != $user->user_id)) {
				$f_userid = $user->user_id;
			} else {
				$f_userid = "0";
			}
			if ($matching_list = $matching->search($f_userid, $f_matching_id, $f_begin_date, $f_begin_date_to, $f_end_date, $f_end_date_to, $f_district_list, $f_school_list, $f_state_list, $f_project_type_list, $f_project_list))
			{
				$muser = new user();
				include "inc/dark_box_begin.htm";
				echo "<center><font size=\"+1\" color=\"$color_table_hdg_font\">Search Results</font></center>";
				include "inc/box_middle.htm";
				echo "<table width=\"100%\"><tr><td><b><br>ID</b></td><td><b><br>User</b></td>\n";
				echo "<td align='center'><b>Begin<br>Date</b></td>\n";
				echo "<td align='center'><b>End<br>Date</b></td>\n";
				echo "<td align='center'><b>Max<br>Amount</b></td>\n";
				echo "<td align='center'><b>Matched<br>Amt</b></td>\n";
				echo "</tr>\n";
				while (list($matchid, $match) = each($matching_list))
				{
					$muser->load_user($match->user_id);
					$donation = new donation();
					if($match->donation_id)
						$donation->load_donation($match->donation_id);
					echo "<tr><td><a href=\"matching_maint.php?matching_id=$match->matching_id\">$match->matching_id</a></td>\n";
					echo "<td><a href=\"matching_history.php?userid=$match->user_id\">".($muser->company ? "$muser->company<br>" : "")."$muser->first_name $muser->last_name</a></td>\n";
					echo "<td align='center'>".date("m/d/Y",strtotime($match->begin_date))."</td>\n";
					echo "<td align='center'>".($match->end_date ? date("m/d/Y",strtotime($match->end_date)) : "")."</td>\n";
					echo "<td align='right'>".sprintf("%01.2f", $match->max_amount)."</td>\n";
					echo "<td align='right'><a href=\"matching_history.php?matching_id=$match->matching_id\">".sprintf("%01.2f", $donation->donation_total())."</a></td>\n";
					echo "</tr>\n";
				}
				echo "</table>";
				include "inc/box_end.htm";
			} else {
				# an error occured in the matching search.
				include "inc/box_begin.htm";
				if ($matching->error_message)
					echo "Search Error Occured.<BR>$matching->error_message";
				else
					echo "No Matches found.";
				include "inc/box_end.htm";
			}
		}
	}
	if (!empty($message)) {
		include "inc/box_begin.htm";
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center>";
		include "inc/box_end.htm";
	}
?>
<table width=100% cellspacing=1 cellpadding=1>
  <TR ALIGN="left" VALIGN="middle">
	<td align=left width=50% bgcolor="<?=$color_table_hdg_bg;?>">
	<table width=100% cellspacing=1 cellpadding=1>
		<TR ALIGN="left" VALIGN="middle">
			<TD Align='center' bgcolor="<?=$color_table_hdg_bg;?>"><font size="+1" color="<?=$color_table_hdg_font;?>">Matching Maintenance</font></TD>
		</TR>
		<TR ALIGN="left" VALIGN="middle">
			<TD Align='center' bgcolor="<?=$color_table_hdg_bg;?>"><font size="-1" color="<?=$color_table_hdg_font;?>">Enter a new match and click <b>Save</b>, or enter search criteria and click <b>Search</b>.</font></TD>
		</TR>
		<tr>
			<td align=left width=50% bgcolor="<?=$color_table_col_bg;?>">
				<TABLE ALIGN="left" BORDER=0 CELLSPACING=10 CELLPADDING=0 WIDTH="100%">
					<Form Name='frmMatching' Action="matching_maint.php" Method='POST'>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>Match ID</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>">
<? if ($f_matching_id) { ?>
						<b><?=$f_matching_id;?></b>
						<input type='hidden' name='f_matching_id' value='<?=$f_matching_id;?>'>
						<input type='hidden' name='f_existing_matching_id' value='<?=$f_existing_matching_id;?>'>
<? } else { ?>
						<input type='text' size='20' maxlength='50' name='f_matching_id' value='<?=$f_matching_id;?>'>
<? } ?>
						</TD>
					</TR>
<?	if ($user->type_id >= 40) {	?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='20%' Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>Member</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>">
							<? 	if (empty($f_user_id))
									echo "<input type='text' name='f_user_id' size=5>";
								else {
									$muser = new user();
									$muser->load_user($f_user_id);
									echo "<B>$muser->first_name $muser->last_name ($f_user_id)</B>".($muser->company ? "<br>".$muser->company:"");
									echo "<input type='hidden' name='f_user_id' value='$f_user_id'>";
								}
							?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='20%' Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>Donation ID</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>">
							<?
							echo "<a href='donation_edit.php?donationid=$f_donation_id' target='donation'>$f_donation_id</a>";
							?>
						</TD>
					</TR>
<?	} else {
		echo "<input type='hidden' name='f_user_id' value='$User_ID'>";
	}
	if ($f_date_created) {
?>

					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>">&nbsp;<b>Created</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><?=date("m/d/Y h:i:s A",strtotime($f_date_created));?></TD>
					</TR>
<?	}  ?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>Begin Date</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='20' name='f_begin_date' value='<?=($f_begin_date ? date("m/d/Y",strtotime($f_begin_date)) : "");?>'><?=(empty($f_matching_id) ? "&nbsp;to&nbsp;<input type='text' size='20' name='f_begin_date_to'>" : "");?></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>">&nbsp;&nbsp;<b>End Date</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='20' name='f_end_date' value='<?=($f_end_date ? date("m/d/Y",strtotime($f_end_date)) : "");?>'><?=(empty($f_matching_id) ? "&nbsp;to&nbsp;<input type='text' size='20' name='f_end_date_to'>" : "");?></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>">&nbsp;&nbsp;<b>Districts</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>">
						<SELECT MULTIPLE SIZE="<?=min(10,count($districts->district_list));?>" NAME="f_district_id[]">
<?
		reset($districts->district_list);
		while (list($districtid, $district) = each($districts->district_list)) {
			reset($f_district_list);
			$selected = "";
			while(list($matchingdistrictid, $matchingdistrict) = each($f_district_list)) {
				if ($district->district_id == $matchingdistrict->district_id) {
					$selected = " SELECTED";
					break;
				}
			}
			echo ("<OPTION VALUE=\"$district->district_id\"".$selected.">$district->district_name</OPTION>\n");
		}
?>
						</SELECT>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>">&nbsp;&nbsp;<b>Schools</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>">
						<SELECT MULTIPLE size=10 NAME="f_school[]" SIZE=1>
<?	reset($schools->school_list);
	while (list($schoolid, $school) = each($schools->school_list)) {
		// Check to see if this school is seleced.
		$selected = "";
		reset($f_school_list);
		while (list($matchingschoolid, $matchingschool) = each($f_school_list)) {
			if ($school->school_id == $matchingschool->school_id) {
				$selected = " SELECTED";
				break;
			}
		}
		echo ("<OPTION VALUE=\"$school->school_id\"".$selected.">$school->school_name</OPTION>\n");
	}
?>
						</SELECT><BR>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>">&nbsp;&nbsp;<b>States</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>">
<?
		echo "<Select MULTIPLE size=".count($states->state_list)." name='f_state[]'>";
		reset($states->state_list);
		$prev_grp = "";
		while (list($statecode, $state) = each($states->state_list)) {
/*
			if ($state->state_group != $prev_grp) {
				if (!empty($prev_grp))
					echo "</optgroup>";
				$prev_grp = $state->state_group;
				echo ("\t\t\t\t\t\t\t\t\t\t<optgroup label=\"$state->state_group\">\n");
			}
*/
			// Check to see if the state is selected.
			$selected = "";
			reset($f_state_list);
			while(list($matchingstateid, $matchingstate) = each($f_state_list)) {
				if ($statecode == $matchingstate->state) {
					$selected = " SELECTED";
					break;
				}
			}

			echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"$statecode\"".$selected.">$state->state_name</OPTION>\n");
		}
		if (!empty($prev_grp))
			echo "</optgroup>\n";
		echo "</select>\n";

?>

						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>">&nbsp;&nbsp;<b>Request Types</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>">
						<SELECT MULTIPLE size=10 NAME="f_project_type[]" SIZE=1>
<?	reset($project_types->project_type_list);
	while (list($projecttypeid, $project_type) = each($project_types->project_type_list)) {
		// Check to see if this project type is seleced.
		$selected = "";
		reset($f_project_type_list);
		while (list($matchingprojecttypeid, $matchingprojecttype) = each($f_project_type_list)) {
			if ($project_type->project_type_id == $matchingprojecttype->project_type_id) {
				$selected = " SELECTED";
				break;
			}
		}
		echo ("<OPTION VALUE=\"$project_type->project_type_id\"".$selected.">$project_type->project_type_description</OPTION>\n");
	}
?>
						</SELECT><BR>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>">&nbsp;&nbsp;<b>Requests</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>">
						<Input Type='Submit' Class='nicebtns' Name='Submit' Value='Add Requests from Cart'><BR>
<?
	echo "<table>";
	echo "<tr><td valign='bottom'><b><br>ID</b></td><td valign='bottom'><b><br>Request Name</b></td><td align='right' valign='bottom'><b>Amount<BR>Needed</b></td><td valign='bottom'><b><br>Remove</b></td></tr>";
	reset($f_project_list);
	$matchtotal = 0;
	while(list($matchingprojectid, $matchingproject) = each($f_project_list)) {
		$project_id = $matchingproject->project_id;
		$matchproject = new project();
		$matchproject->load_project($project_id);
		echo "<tr><td><input type='hidden' name='f_project_id[]' value='$project_id'>".$matchproject->project_id."</td><td>".$matchproject->project_name."</td><td align='right'>".sprintf("%01.2f",$matchproject->amount_needed)."</td><td align='center'><input type='checkbox' name='f_project_delete[]' value='Y'></td></tr>";
		$matchtotal += $matchproject->amount_needed;
	}
	echo "<tr><td></td><td><b>Total Needed</b></td><td align='right'><b>".sprintf("%01.2f",$matchtotal)."</b></td><td></td></tr>";
	echo "<tr><td>Add Request: <input type='text' name='f_project_id[]' size='5' value=''></td><td></td></tr>\n";
	echo "</table>";
?>
						</TD>
					</TR>

					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>">&nbsp;&nbsp;<b>Max Amount</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>">
						<Input type="text" value="<?=$f_max_amount;?>" name="f_max_amount">
						</TD>
					</TR>

					<TR ALIGN="left" VALIGN="middle">
						<TD Align='left' Colspan='2' bgcolor="<?=$color_table_col_bg;?>">
<?
			echo "<Input Type='Submit' Class='nicebtns' Name='Submit' Value='Save'>&nbsp;&nbsp;\n";
			if (!empty($f_matching_id))
				echo "|&nbsp;&nbsp;<Input Type='Submit' Class='nicebtns' Name='Submit' Value='Delete'>\n";
			else
				echo "|&nbsp;&nbsp;<Input Type='Submit' Class='nicebtns' Name='Submit' Value='Search'>\n";
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
<? if ($f_matching_id) { ?>
<table width=100% cellspacing=1 cellpadding=1>
  <TR ALIGN="left" VALIGN="middle">
	<td align=left width=50% bgcolor="<?=$color_table_hdg_bg;?>">
	<table width=100% cellspacing=1 cellpadding=1>
		<TR ALIGN="left" VALIGN="middle">
			<TD Align='center' bgcolor="<?=$color_table_hdg_bg;?>"><font size="+1" color="<?=$color_table_hdg_font;?>">Comments</font></TD>
		</TR>
		<tr>
			<td align=left width=50% bgcolor="<?=$color_table_col_bg;?>">
				<TABLE ALIGN="left" BORDER=0 CELLSPACING=10 CELLPADDING=0 WIDTH="100%">
				<tr><td><b>ID</b></td><td><b>Created</b></td><td><b>User</b></td><td></td></tr>
<?
	reset($matching->matching_comment_list);
	while(list($commentid, $matching_comment)=each($matching->matching_comment_list)) {
		echo "<tr><td>$matching_comment->matching_comment_id</td>\n";
		echo "<td>".date("m/d/Y H:i:s A",$matching_comment->datecreated)."</td>\n";
		echo "<td>".$users->user_name($matching_comment->user_id)."</td>\n";
		echo "<td><a href=\"matching_maint.php?deletecommentid=$matching_comment->matching_comment_id\"><font size=\"-1\">DELETE</font></a></td>\n";
		echo "</tr>\n";
		echo "<tr><td colspan='4'>$matching_comment->comment</td>\n";
		echo "</tr>\n";

	}
?>
				<tr><td colspan='3'><hl></td></tr>
				<tr><td colspan='3'>New Comment</td></tr>
				<tr><td colspan='3'>
					<Form Name='frmComment' Action="matching_maint.php" Method='POST'>
					<TEXTAREA NAME="f_newcomment" ROWS="5" COLS="78" WRAP="physical"><?=$f_newcomment;?></TEXTAREA>
					</td></tr>
				<tr><td colspan='3'>
					<input type='hidden' name='matching_id' value='<?=$f_matching_id;?>'>
					<Input Type='Submit' Class='nicebtns' Name='Submit' Value='Add Comment'>
					</form>
					</td>
				</tr>
				</TABLE>
			</td>
		</tr>
	</table>
	</td>
  </tr>
</table>
<? } ?>
                  </td>
<? require "inc/body_end.inc"; ?>
</html>
