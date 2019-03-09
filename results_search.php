<?	require_once "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";
	require_once "inc/class_users.php";
	require_once "inc/class_school.php";
	require_once "inc/class_schools.php";
	require_once "inc/class_grade_level.php";
	require_once "inc/class_grade_levels.php";
	require_once "inc/class_project_type.php";
	require_once "inc/class_project_types.php";
	require_once "inc/class_project.php";
	require_once "inc/class_projects.php";
	require_once "inc/class_project_status.php";
	require_once "inc/class_project_statuses.php";

	$project_status = new project_status;
	$projectstatuses = new project_statuses;
	$projectstatuses->load_project_statuses();

	$all_schools = new schools;
	$all_schools->load_schools();

	$grade_levels = new grade_levels;
	$grade_levels->load_grade_levels();

	$project_types = new project_types;
	$project_types->load_project_types();

	$projectlist = new projects;

	$user_rcd = new user;
	$user_rcd->load_user($User_ID);
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
	# Default school to Ogden.
	if ($f_school_id=="ALL")
		$f_school_id = "0";
	else
		$schoolname = $all_schools->school_name($f_school_id);

?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
				<td width="655" align="left" valign="top">
<?	if (!empty($message)) {
		include "inc/box_begin.htm";
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center><br>";
		include "inc/box_end.htm";
	}
?>
	<table width=100% cellspacing=1 cellpadding=1>
		<tr>
			<td align=left width=50% bgcolor="<?=$color_table_hdg_bg;?>">
		<TABLE DIR=ltr ID="project_list" ALIGN=bleedright WIDTH="100%" COLS=4 BORDER="0" CELLSPACING="1" CELLPADDING="2">
<?	if (!empty($config_resultssearch_projlist)) {
		echo "\t\t\t<TR VALIGN=bottom>\n";
		echo "\t\t\t\t<TD Align='Left' vAlign='Middle' BGCOLOR=\"$color_table_col_bg\">$config_resutssearch_projlist</TD>\n";
		echo "\t\t\t</TR>\n";
	}
?>
			<TR VALIGN=bottom>
		<?
		if (!isset($sortorder)) $sortorder = "name";
		echo "\t\t\t\t<TD Name=\"SortByName\" ALIGN=center VALIGN=bottom BGCOLOR=\"$color_table_hdg_bg\"><font color=\"$color_table_hdg_font\"><b>Funded Projects".($schoolname == "" ? "" : " for $schoolname")."</b></font></TD>";
		?>
			</TR>
		<?
			$projectlist->search_projects($f_title_words, $f_district_id, $f_school_id, $f_teacher_id, $f_grade_level_id, $f_project_type_id, $f_funds_required, $f_endangered, $sortorder, "4");
			if ($projectlist->count() > 0) {
				while (list($projectid, $project) = each($projectlist->project_list)) {
		?>
			<TR VALIGN=top>
				<TD ALIGN=left VALIGN=top BGCOLOR="<?=$color_table_col_bg;?>"><a href="<?=$https_location;?>donation.php?uniqueid=<?=htmlentities(urlencode($user->unique_id));?>&projectid=<?=$project->project_id;?>&search_arg=<?=htmlentities(urlencode("$f_args"));?>"><B><?=$project->project_name;?></b></a>
				<BR>&nbsp;&nbsp;<?=substr($project->project_description, 0, 85).(strlen($project->project_description)>85?"...":"");?>
				</TD>
			</TR>
		<?
					if (!($user_rcd->type_id > 1))
						$projectlist->project_searched($projectid);
				}
			} else {
				echo "<TR><TD ALIGN='center'>No Funded Projects Found</TD></TR>";
			}
		?>
		</TABLE>
			</td>
		</tr>
	</table>
				</td>
<?	require "inc/body_end.inc"; ?>
</html>
