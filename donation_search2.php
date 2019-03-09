<?	require_once "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";
	require_once "inc/class_users.php";
	require_once "inc/class_school.php";
	require_once "inc/class_schools.php";
	require_once "inc/class_district.php";
	require_once "inc/class_districts.php";
	require_once "inc/class_grade_level.php";
	require_once "inc/class_grade_levels.php";
	require_once "inc/class_project_type.php";
	require_once "inc/class_project_types.php";
	require_once "inc/class_project.php";
	require_once "inc/class_projects.php";
	require_once "inc/class_project_status.php";
	require_once "inc/class_project_statuses.php";
	require_once "inc/class_matching.php";

	$project_status = new project_status;
	$projectstatuses = new project_statuses;
	$projectstatuses->load_project_statuses();

	$all_schools = new schools;
	$all_schools->load_donation_schools();

	$districts = new districts;
	$districts->load_donation_districts();

	$grade_levels = new grade_levels;
	$grade_levels->load_grade_levels();

	$project_types = new project_types;
	$project_types->load_project_search_types();

	$projectlist = new projects;

	$user_rcd = new user;
	$user_rcd->load_user($User_ID);

	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		if ($f_district_id != "0") {
				$district = new district;
			if ($district->load_district($f_district_id)) {
			if (!empty($district->alt_donation_url)) {
		echo "<script type=\"text/javascript\">\nlocation.href='".$district->alt_donation_url."?message=".htmlentities(urlencode("You have been redirect here from Donate2Educate.com."))."'\n</script>";
				}
			}
		}
	}
?>
<html>
<head>
<? require "inc/cssstyle.php"; ?>
<?
	$pagename = "$config_donationsearch_page_name";
	$help_msg_name = "config_donationsearch_help";
	$help_msg = "$config_donationsearch_help";
	$help_width = "$config_donationsearch_help_width";
	$help_height = "$config_donationsearch_help_height";
	require "inc/title.php";

	if ($f_school_id=="ALL")
		$f_school_id = "0";

?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? if (empty($resultsonly)) {
		require "inc/body_begin.inc";
		include "inc/nav.php";
		echo "		<td width=\"655\" align=\"left\" valign=\"top\">";
		if (!empty($message)) {
			include "inc/box_begin.htm";
			echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center><br>";
			include "inc/box_end.htm";
		}
	} else {
		echo "<table>\n";
	}
	$step = 1;
	if ($_SERVER['REQUEST_METHOD'] == "POST" || ISSET($sortorder) || $f_school_id || $projectlist->approved_project_count() < 3) {
	include "inc/progress.php";
?>
	<table width=100% cellspacing=1 cellpadding=1>
		<tr>
			<td align=left width=50% bgcolor="<?=$color_table_hdg_bg;?>">
		<TABLE DIR=ltr ID="project_list" ALIGN=bleedright WIDTH="100%" COLS="4" BORDER="0" CELLSPACING="1" CELLPADDING="2">
<?		if (!empty($config_donationsearch_projlist) && empty($resultsonly)) {
			echo "\t\t\t<TR VALIGN=bottom>\n";
			echo "\t\t\t\t<TD Colspan='4' Align='Left' vAlign='Middle' BGCOLOR=\"$color_table_col_bg\">$config_donationsearch_projlist</TD>\n";
			echo "\t\t\t</TR>\n";
		}
?>
		<?
		echo "	<TR VALIGN=bottom>";
		if (!isset($sortorder)) $sortorder = "type";
		$f_args = "";
		if (!empty($f_title_words)) {
			$f_args .= "&f_title_words=".htmlentities(urlencode($f_title_words));
		}
		if ($f_district_id != "0") {
			$f_args .= "&f_district_id=".htmlentities(urlencode($f_district_id));
		}
		if ($f_school_id != "0") {
			$f_args .= "&f_school_id=".htmlentities(urlencode($f_school_id));
		}
		if ($f_teacher_id != "0") {
			$f_args .= "&f_teacher_id=".htmlentities(urlencode($f_teacher_id));
		}
		if ($f_statusidnot != "") {
			$f_args .= "&f_statusidnot=".htmlentities(urlencode($f_statusidnot));
		}
		if ($f_grade_level_id != "0") {
			$f_args .= "&f_grade_level_id=".htmlentities(urlencode($f_grade_level_id));
		}
		if ($f_project_type_id != "0") {
			$f_args .= "&f_project_type_id=".htmlentities(urlencode($f_project_type_id));
		}
		if ($f_funds_required != "0") {
			$f_args .= "&f_funds_required=".htmlentities(urlencode($f_funds_required));
		}
#		if ($f_school_id == "ORCITY") {
#			echo "<script type=\"text/javascript\">\nlocation.href='http://www.donate2educate.org/donation_search.php?sortorder=".htmlentities(urlencode("name".($sortorder == "name" ? " desc" : ""))).($f_args == "" ? "" : "&$f_args")."'\n</script>";
#		}
#		echo "\t\t\t\t<TD Name=\"Details\" ALIGN=center VALIGN=bottom BGCOLOR=\"$color_table_hdg_bg\"><font color=\"$color_table_hdg_font\" fontsize=\"11px\">Details</font></TD>";
#		echo "\t\t\t\t<TD Name=\"SortByName\" ALIGN=center VALIGN=bottom BGCOLOR=\"$color_table_hdg_bg\"><a href=\"donation_search.php?sortorder=".htmlentities(urlencode("name".($sortorder == "name" ? " desc" : ""))).($f_args == "" ? "" : "&$f_args")."\">".(($sortorder == "name") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "name desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\">Project Name</font></A></TD>";
#		echo "\t\t\t\t<TD Name=\"SortBySchool\" ALIGN=center VALIGN=bottom BGCOLOR=\"$color_table_hdg_bg\"><a href=\"donation_search.php?sortorder=".htmlentities(urlencode("school".($sortorder == "school" ? " desc" : ""))).($f_args == "" ? "" : "&$f_args")."\">".(($sortorder == "school") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "school desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\">School</font></A></TD>";
#		echo "\t\t\t\t<TD Name=\"SortByType\" ALIGN=center VALIGN=bottom BGCOLOR=\"$color_table_hdg_bg\"><a href=\"donation_search.php?sortorder=".htmlentities(urlencode("type".($sortorder == "type" ? " desc" : ""))).($f_args == "" ? "" : "&$f_args")."\">".(($sortorder == "type") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "type desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\">Category</font></A></TD>";
		#echo "\t\t\t\t<TD Name=\"SortByNeeded\" ALIGN=center VALIGN=bottom BGCOLOR=\"$color_table_hdg_bg\"><a href=\"donation_search.php?sortorder=".htmlentities(urlencode("amount".($sortorder == "amount" ? " desc" : ""))).($f_args == "" ? "" : "&$f_args")."\">".(($sortorder == "amount") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "amount desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR>Amount<BR>Needed</font></A></TD>";
		$f_args = "?sortorder=".$sortorder.$f_args;
#		echo "	</TR>";

		$matchingfunds = new matching();
			$projectlist->search_projects($f_title_words, $f_district_id, $f_school_id, $f_teacher_id, $f_grade_level_id, $f_project_type_id, $f_funds_required, $f_endangered, $sortorder, "3");
			if ($projectlist->count() > 0) {
				while (list($projectid, $project) = each($projectlist->project_list)) {
		?>
			<TR VALIGN=top VALIGN=middle>
				<TD ALIGN=left   VALIGN=left BGCOLOR="<?=$color_table_col_bg;?>">
					<TABLE BORDER=0 WIDTH="100%">
					<TR>
						<TD Align="Left">
						<a href="<?=$http_location;?>donation.php?projectid=<?=$project->project_id;?>&search_arg=<?=htmlentities(urlencode("$f_args")).($HTTPS == "on" ? "&uniqueid=".htmlentities(urlencode($uniqueid)):"");?>">
						<image border=0 src="images/buttons/arrow.gif">&nbsp;<Font Color=#336600><B><?=$project->project_name;?></B></Font></a>
						</TD>
						<TD Align="Right">
<?  if ($matchingfunds->matching_amount($projectid) > 0) { ?>
						<FONT COLOR=RED>Matching Funds Available</FONT>
<?  }  ?>
						</TD>
					</TR>
					<TR>
						<TD Align="Left" Colspan=2>
						&nbsp;&nbsp;&nbsp;&nbsp;<?=substr($project->project_description, 0, 270).(strlen($project->project_description)>270?"...":"");?>
						</TD>
					</TR>
					<TR>
						<TD><I><?=($all_schools->school_homepage($project->school_id) ? "<a target=\"school\" href=\"".$all_schools->school_homepage($project->school_id)."\">" : "");?><?=$all_schools->school_name($project->school_id);?><?=($all_schools->school_homepage($project->school_id) ? "</a>":"");?></I></TD>
						<TD Align=Right>Funds still needed: $<?=sprintf("%01.2f",$project->amount_needed - $project->amount_donated() - $project->amount_pledged());?></TD>
					</TR>
					</TABLE>
				</TD>
			</TR>
		<?
					if (!($user_rcd->type_id > 1))
						$projectlist->project_searched($projectid);
				}
			} else {
				echo "<TR><TD Colspan=4 ALIGN='center'>No Projects Found</TD></TR>";
			}
		?>
		</TABLE>
			</td>
		</tr>
	</table>
<?
	if (!empty($resultsonly)) {
		echo "</table>\n";
	}
	} else {
		if (!empty($config_donationsearch_paragraph1)) {
			echo "$config_donationsearch_paragraph1";
		}
		include "inc/progress.php";
	}
	if (empty($resultsonly)) {
?>
					<table>
						<tr>
							<td><b>Category</b></td>
							<td>
<?
	$i=0;
	while (list($type_id, $project_type) = each($project_types->project_type_list)) {
		echo "\t\t\t\t\t\t\t\t".($i == 1 ? "<BR>" : "").($type == $project_type->project_type_id ? "<B>":"")."<a href=\"donation_search2.php?type=".$project_type->project_type_id."\">".$project_type->project_type_description."</a>".($type == $project_type->project_type_id ? "</B>":"")."\n";
		$i = 1;
	}
?>
							</td><td></td>
						</tr>
						<tr>
							<td><b>District</b></td>
							<td>
<?
			echo "\t\t\t\t\t\t\t\t".($dist == "all" ? "<B>":"")."<a href=\"donation_search2.php?dist=all\">All Districts</a>".($dist == "all" ? "<\B>":"")."\n";
	reset($districts->district_list);
	$i=1;
	while (list($districtid, $district) = each($districts->district_list)) {
		if ($district->inactive != "Y") {
			echo "\t\t\t\t\t\t\t\t".($i == 1 ? "<BR>" : "").($dist == $district->district_id ? "<B>":"")."<a href=\"donation_search2.php?dist=".$district->district_id."\">".$district->district_name."</a>".($dist == $district->district_id ? "</B>":"")."\n";
			$i = 1;
		}
	}
?>
								</td><td></td>
							</td>
						</tr>
						<tr>
							<td><b>School</b></td>
							<td>
<?
			echo "\t\t\t\t\t\t\t\t".($sch == "all" ? "<B>":"")."<a href=\"donation_search2.php?sch=all\">All Schools</a>".($sch == "all" ? "<\B>":"")."\n";
	reset($all_schools->school_list);
	$i = 1;
	while (list($schoolid, $school) = each($all_schools->school_list)) {
		if ($school->inactive != "Y") {
			echo "\t\t\t\t\t\t\t\t".($i == 1 ? "<BR>" : "").($sch == $school->school_id ? "<B>":"")."<a href=\"donation_search2.php?sch=".$school->school_id."\">".$school->school_name."</a>".($sch == $school->school_id ? "</B>":"")."\n";
			$i = 1;
		}
	}
?>
								</td><td></td>
							</td>
						</tr>
					</table>
				</td>
<?
		require "inc/body_end.inc";
	}
?>
</html>
