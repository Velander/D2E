<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";
	require_once "inc/class_project.php";
	require_once "inc/class_projects.php";
	require_once "inc/class_project_type.php";
	require_once "inc/class_project_types.php";
	require_once "inc/class_project_status.php";
	require_once "inc/class_project_statuses.php";
	require_once "inc/class_school.php";
	require_once "inc/class_schools.php";
	require_once "inc/class_district.php";
	require_once "inc/class_districts.php";

	$projectlist = new projects;
	$project_status = new project_status;
	$projectstatuses = new project_statuses;
	$projectstatuses->load_project_statuses();
	$schools = new schools;
	$schools->load_schools();
	$districts = new districts;
	$districts->load_districts();
	$project_types = new project_types;
	$project_types->load_project_types();

	session_start();
	if (empty($sortorder))
		$sortorder = $_SESSION['s_sortorder'];
	else
		$_SESSION['s_sortorder'] = $sortorder;

?>
<html>
<head>
<?
	$pagename = "$config_request_catalog_page_name";
	$help_msg_name = "config_request_catalog_help";
	$help_msg = "$config_request_catalog_help";
	$help_width = "$config_request_catalog_help_width";
	$help_height = "$config_request_catalog_help_height";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
<? require "inc/cssstyle.php"; ?>
<? require "inc/title.php"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <td width="100%" align="left" valign="top">
<?

	if (empty($page)) $page = 1;
	if (empty($pagesize)) $pagesize = 20;
	if (!empty($config_request_catalog_paragraph1)) {
		#include "inc/box_begin.htm";
		if ($user->type_id == 10) {
			echo "$config_request_catalog_donor_paragraph1";
			$help_msg = "";
		} else
			echo "$config_request_catalog_paragraph1";
		#include "inc/box_end.htm";
	}
	if (!empty($message))
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center><br>";
?>
<? include "hdg_box_begin1.htm"; ?>
	<table width=100% cellspacing=1 cellpadding=1>
		<tr>
			<td align=left width=50% bgcolor="<?=$color_table_hdg_bg;?>">
<TABLE DIR=ltr ID="request_catalog" ALIGN=bleedright WIDTH="100%" COLS=4 BORDER="0" CELLSPACING="2" CELLPADDING="2">
	<TR VALIGN=bottom>
<?
if (!isset($sortorder)) $sortorder = "name";
		echo "<TH Name=\"SortByCategory\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"request_catalog.php?page=$page&sortorder=".htmlentities(urlencode("category".($sortorder == "category" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "category") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "category desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR><BR>Category</font></A></TH>\n";
		echo "<TH Name=\"SortByName\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"request_catalog.php?page=$page&sortorder=".htmlentities(urlencode("name".($sortorder == "name" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "name") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "name desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR><BR>Project Name</font></A></TH>\n";
		echo "<TH Name=\"SortByAmount\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"request_catalog.php?page=$page&sortorder=".htmlentities(urlencode("amount".($sortorder == "amount" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "amount") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "amount desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR>Amount<BR>Requested</font></A></TH>\n";
		echo "<TH>&nbsp;</TH>\n";
?>
	</TR>
<?
	if ($user->type_id == 10)
		$projectlist->load_catalog($sortorder, $f_requestid);
	else
		$projectlist->load_catalog($sortorder, 0);
	if ($projectlist->count() > 0) {
		$project_count = 0;
		while (list($projectid, $project) = each($projectlist->project_list)) {
			$approved = true;
			$project_count += 1;
			if ($project_count >= (($page-1) * $pagesize)) {
				if ($project_count >= ($page * $pagesize))
					break;
				if (!empty($f_mindate) && (strtotime($project->submitted_date) <= strtotime($f_mindate))) $approved = false;
				if (!empty($f_maxdate) && (strtotime($project->submitted_date) >= strtotime($f_maxdate))) $approved = false;
				if (($f_statusid != "") && ($project->project_status_id != $f_statusid)) $approved = false;
				if (($f_statusidnot != "") && ($project->project_status_id == $f_statusidnot)) $approved = false;
				if (($f_amt != "") && ((($f_amtarg == "LT") && ($project->amount_needed >= $f_amt)) || (($f_amtarg == "GT") && ($project->amount_needed <= $f_amt)))) $approved = false;
				if (($f_amtdonated != "") && ((($f_amtdonatedarg == "LT") && ($project->amount_donated() >= $f_amtdonated)) || (($f_amtdonatedarg == "GT") && ($project->amount_donated() <= $f_amtdonated)))) $approved = false;

				if ($approved) {
					$target = "proposal.php";
	?>
		<TR VALIGN=top>
				<TD ALIGN="left" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>&createcopy=1><?=$project_types->project_type_description($project->project_type_id);?></a></TD>
				<TD ALIGN="left" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>&createcopy=1><?=$project->project_name;?></a></TD>
				<TD ALIGN="right" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>&createcopy=1><?=sprintf("%01.2f", $project->amount_needed);?></a></TD>
				<TD ALIGN="center" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=donation.php?projectid=<?=$project->project_id;?> target="Preview">Preview</a></TD>
			</TR>
	<?
				}
			}
		}
		if ($project_count <= ($page * $pagesize)) {
			echo "\t\t<TR>\n\t\t\t\t<TD Colspan=100%>\n";
			if ($page > 1)
				echo "\t\t\t\t\t<a href=\"request_catalog.php?page=".($page - 1).($f_args == "" ? "" : "$f_args")."\">Prev</a>\n";
			echo "\t\t\t\t\t<a href=\"request_catalog.php?page=".($page + 1).($f_args == "" ? "" : "$f_args")."\">Next</a>\n";
			echo "\t\t\t\t</TD>\n";
			echo "\n\t\t\t</TR>\n";
		}
	} else {
		echo "\t\t\t<TR>\n\t\t\t\t<TD Colspan=100% ALIGN='center' BGCOLOR=\"$color_table_col_bg\">No Projects Found</TD>\n\t\t\t</TR>\n";
	}

?>
			</TABLE>
		</td>
	</tr>
</table>
<?
	if ($config_request_catalog_banners == "Y") include "inc/banner_ads.php";
?>
                  </td>
<? require "inc/body_end.inc"; ?>
</html>
