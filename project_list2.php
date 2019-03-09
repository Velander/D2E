<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";
	require_once "inc/class_project.php";
	require_once "inc/class_projects.php";
	require_once "inc/class_project_status.php";
	require_once "inc/class_project_statuses.php";

	$projectlist = new projects;
	$project_status = new project_status;
	$projectstatuses = new project_statuses;
	$projectstatuses->load_project_statuses();

?>
<html>
<head>
<? require "inc/cssstyle.php"; ?>
<?
	$pagename = "$config_project_list_name";
	$help_msg_name = "config_project_list_help";
	$help_msg = "$config_project_list_help";
	$help_width = "$config_project_list_help_width";
	$help_height = "$config_project_list_help_height";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <td width="655" align="left" valign="top">
<?

	if ($user->type_id == 10) {
		include "inc/box_begin.htm";
		echo "$config_project_list_donor_paragraph1";
		include "inc/box_end.htm";
		$help_msg = "";
	} elseif (!empty($config_project_list_paragraph1)) {
		include "inc/box_begin.htm";
		echo "$config_project_list_paragraph1";
		include "inc/box_end.htm";
	}
	if (!empty($message))
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center><br>";
?>
<?
	if ($user->type_id > 10)
		include "inc/list_filter.php";
?>
	<table width=100% cellspacing=1 cellpadding=1>
		<tr>
			<td align=left width=50% bgcolor="<?=$color_table_hdg_bg;?>">
<TABLE DIR=ltr ID="project_list" ALIGN=bleedright WIDTH="100%" COLS=4 BORDER="0" CELLSPACING="2" CELLPADDING="2">
	<TR VALIGN=bottom>
<?
if (!isset($sortorder)) $sortorder = "name";
		if ($user->type_id > 10)
			echo "<TH Name=\"SortByID\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"project_list2.php?sortorder=".htmlentities(urlencode("id".($sortorder == "id" ? " desc" : ""))).($f_args == "" ? "" : "&$f_args")."\">".(($sortorder == "id") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "id desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR><BR>ID</font></A></TH>";
		echo "<TH Name=\"SortByName\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"project_list2.php?sortorder=".htmlentities(urlencode("name".($sortorder == "name" ? " desc" : ""))).($f_args == "" ? "" : "&$f_args")."\">".(($sortorder == "name") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "name desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR><BR>Project Name</font></A></TH>";
		if ($user->type_id > 10) {
			echo "<TH Name=\"SortByDate\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"project_list2.php?sortorder=".htmlentities(urlencode("date".($sortorder == "date" ? " desc" : ""))).($f_args == "" ? "" : "&$f_args")."\">".(($sortorder == "date") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "date desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR>Date<br>Submitted</font></A></TH>";
			echo "<TH Name=\"SortByStatus\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"project_list2.php?sortorder=".htmlentities(urlencode("status".($sortorder == "status" ? " desc" : ""))).($f_args == "" ? "" : "&$f_args")."\">".(($sortorder == "status") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "status desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR><BR>Status</font></A></TH>";
		}
		echo "<TH Name=\"SortByAmount\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"project_list2.php?sortorder=".htmlentities(urlencode("amount".($sortorder == "amount" ? " desc" : ""))).($f_args == "" ? "" : "&$f_args")."\">".(($sortorder == "amount") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "amount desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR>Amount<BR>Requested</font></A></TH>";

		if ($user->type_id > 10) {
			echo "<TH Name=\"SortByDonations\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"project_list2.php?sortorder=".htmlentities(urlencode("donations".($sortorder == "donations" ? " desc" : ""))).($f_args == "" ? "" : "&$f_args")."\">".(($sortorder == "donations") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "donations desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR>Amount<BR>Donated</font></A></TH>";
			echo "<TH Name=\"SortBySearchs\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"project_list2.php?sortorder=".htmlentities(urlencode("searchs".($sortorder == "searchs" ? " desc" : ""))).($f_args == "" ? "" : "&$f_args")."\">".(($sortorder == "searchs") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "searchs desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR>S<BR>C</font></A></TH>";
			echo "<TH Name=\"SortByViews\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"project_list2.php?sortorder=".htmlentities(urlencode("views".($sortorder == "views" ? " desc" : ""))).($f_args == "" ? "" : "&$f_args")."\">".(($sortorder == "views") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "views desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR>V<BR>C</font></A></TH>";
		}
		echo "<TH Name=\"SortByViews\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><font color=\"$color_table_hdg_font\">%<BR>Funded</font></A></TH>";
?>
	</TR>
<?
	if ($funded == 1)
		$projectlist->load_projects("0", array("4","5"), $sortorder);
	elseif ($user->type_id == 10)
		$projectlist->load_projects("0", $f_status_id, $sortorder, $User_ID);
	else
		$projectlist->load_projects($User_ID, $f_status_id, $sortorder);
	if ($projectlist->count() > 0) {
		while (list($projectid, $project) = each($projectlist->project_list)) {
			$approved = true;
			if (!empty($f_mindate) && (strtotime($project->submitted_date) <= strtotime($f_mindate))) $approved = false;
			if (!empty($f_maxdate) && (strtotime($project->submitted_date) >= strtotime($f_maxdate))) $approved = false;
			if (($f_statusid != "") && ($project->project_status_id != $f_statusid)) $approved = false;
			if (($f_statusidnot != "") && ($project->project_status_id == $f_statusidnot)) $approved = false;
			if (($f_amt != "") && ((($f_amtarg == "LT") && ($project->amount_needed >= $f_amt)) || (($f_amtarg == "GT") && ($project->amount_needed <= $f_amt)))) $approved = false;
			if (($f_amtdonated != "") && ((($f_amtdonatedarg == "LT") && ($project->amount_donated >= $f_amtdonated)) || (($f_amtdonatedarg == "GT") && ($project->amount_donated <= $f_amtdonated)))) $approved = false;

			if ($approved) {
				if ($user->type_id == 10)
					$target = "donation.php";
				else
					$target = "proposal.php";
?>
	<TR VALIGN=top>
<? 		if ($user->type_id > 10) { ?>
		<TD ALIGN="left" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>><?=$project->project_id;?></a></TD>
<?		}	?>
		<TD ALIGN="left" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>><?=$project->project_name;?></a></TD>
<?		if ($user->type_id > 10) {	?>
		<TD ALIGN="center" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>><?=empty($project->submitted_date) ? "" : date("m/d/y",strtotime($project->submitted_date));?></a></TD>
		<TD ALIGN="left" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>><?=$projectstatuses->project_status_description($project->project_status_id);?></a></TD>
<?		}	?>
		<TD ALIGN="right" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>><?=sprintf("%01.2f", $project->amount_needed);?></a></TD>
<?
		if ($user->type_id > 10) {
?>
		<TD ALIGN="right" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>><?=sprintf("%01.2f", $project->amount_donated);?></a></TD>
		<TD ALIGN="right" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><?=$project->search_count;?></TD>
		<TD ALIGN="right" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><?=$project->view_count;?></TD>
<?
		}
?>
		<TD ALIGN="center" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><?=sprintf("%01.0f",($project->amount_donated/$project->amount_needed)*100);?>%</TD>
	</TR>
<?
			}
		}
	} else {
		echo "<TR><TD Colspan=9 ALIGN='center' BGCOLOR=\"$color_table_col_bg\">No Projects Found</TD></TR>";
	}
?>
</TABLE>
			</td>
		</tr>
	</table>

                  </td>
<? require "inc/body_end.inc"; ?>
</html>
