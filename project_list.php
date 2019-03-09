<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";
	require_once "inc/class_project.php";
	require_once "inc/class_projects.php";
	require_once "inc/class_project_status.php";
	require_once "inc/class_project_statuses.php";
	require_once "inc/class_school.php";
	require_once "inc/class_schools.php";
	require_once "inc/class_district.php";
	require_once "inc/class_districts.php";

	session_start();
	if (empty($sortorder))
		$sortorder = $_SESSION['s_sortorder'];
	else
		$_SESSION['s_sortorder'] = $sortorder;

	$projectlist = new projects;
	$project_status = new project_status;
	$projectstatuses = new project_statuses;
	$projectstatuses->load_project_statuses();
	$schools = new schools;
	$schools->load_schools();
	$districts = new districts;
	$districts->load_districts();

	if ($REQUEST_METHOD == "POST") {
		$sortorder		= $_POST["sortorder"];
		$page			= $_POST["page"];
		$pagesize		= $_POST["pagesize"];
		$f_status_id	= $_POST["f_status_id"];
		$f_author_id	= $_POST["f_author_id"];
		$f_school_id	= $_POST["f_school_id"];
		$f_district_id	= $_POST["f_district_id"];
		$f_requestid	= $_POST["f_requestid"];
		$f_mindate		= $_POST["f_mindate"];
		$f_maxdate		= $_POST["f_maxdate"];
		$f_statusidnot	= $_POST["f_statusidnot"];
		$f_amt			= $_POST["f_amt"];
		$f_amtarg		= $_POST["f_amtarg"];
		$f_amtdonated	= $_POST["f_amtdonated"];
		$f_args			= $_POST["f_args"];
	}
	else
	{
		$sortorder		= $_GET["sortorder"];
		$page			= $_GET["page"];
		$pagesize		= $_GET["pagesize"];
		$message		= $_GET["message"];
		$f_status_id	= $_GET["f_status_id"];
		$f_author_id	= $_GET["f_author_id"];
		$f_school_id	= $_GET["f_school_id"];
		$f_district_id	= $_GET["f_district_id"];
		$f_requestid	= $_GET["f_requestid"];
		$f_mindate		= $_GET["f_mindate"];
		$f_maxdate		= $_GET["f_maxdate"];
		$f_statusidnot	= $_GET["f_statusidnot"];
		$f_amt			= $_GET["f_amt"];
		$f_amtarg		= $_GET["f_amtarg"];
		$f_amtdonated	= $_GET["f_amtdonated"];
		$f_args			= $_GET["f_args"];
	}
?>
<html>
<head>
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
<? require "inc/cssstyle.php"; ?>
<? require "inc/title.php"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <td width="100%" align="left" valign="top">
<?

	if (empty($page)) $page = 1;
	if (empty($pagesize)) $pagesize = 20;
	if (!empty($config_project_list_paragraph1)) {
		include "inc/box_begin.htm";
		if ($user->type_id == 10) {
			echo "$config_project_list_donor_paragraph1";
			$help_msg = "";
		} else
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
			echo "<TH Name=\"SortByID\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"project_list.php?page=$page&sortorder=".htmlentities(urlencode("id".($sortorder == "id" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "id") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "id desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR><BR>ID</font></A></TH>";
		echo "<TH Name=\"SortByTeacher\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"project_list.php?page=$page&sortorder=".htmlentities(urlencode("teacher".($sortorder == "teacher" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "teacher") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "teacher desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR><BR>Submitted By</font></A></TH>";
		echo "<TH Name=\"SortByName\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"project_list.php?page=$page&sortorder=".htmlentities(urlencode("name".($sortorder == "name" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "name") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "name desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR><BR>Project Name</font></A></TH>";
		if ($user->type_id > 10) {
			echo "<TH Name=\"SortByDate\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"project_list.php?page=$page&sortorder=".htmlentities(urlencode("date".($sortorder == "date" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "date") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "date desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR>Date<br>Submitted</font></A></TH>";
			echo "<TH Name=\"SortByStatus\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"project_list.php?page=$page&sortorder=".htmlentities(urlencode("status".($sortorder == "status" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "status") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "status desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR><BR>Status</font></A></TH>";
		}
		echo "<TH Name=\"SortByAmount\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"project_list.php?page=$page&sortorder=".htmlentities(urlencode("amount".($sortorder == "amount" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "amount") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "amount desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR>Amount<BR>Requested</font></A></TH>";

		if ($user->type_id > 10) {
			echo "<TH Name=\"SortByDonations\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"project_list.php?page=$page&sortorder=".htmlentities(urlencode("donations".($sortorder == "donations" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "donations") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "donations desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR>Amount<BR>Donated</font></A></TH>";
			echo "<TH Name=\"SortByPledged\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"project_list.php?page=$page&sortorder=".htmlentities(urlencode("pledged".($sortorder == "pledged" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "pledged") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "pledged desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR>Amount<BR>Pledged</font></A></TH>";
			echo "<TH Name=\"SortBySearchs\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"project_list.php?page=$page&sortorder=".htmlentities(urlencode("searchs".($sortorder == "searchs" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "searchs") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "searchs desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR>S<BR>C</font></A></TH>";
			echo "<TH Name=\"SortByViews\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"project_list.php?page=$page&sortorder=".htmlentities(urlencode("views".($sortorder == "views" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "views") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "views desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR>V<BR>C</font></A></TH>";
		}
		echo "<TH Name=\"SortByFunded\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"project_list.php?sortorder=".htmlentities(urlencode("funded".($sortorder == "funded" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "funded") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "funded desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR>%<BR>Funded</font></A></TH>";
?>
	</TR>
<?
	if ($user->type_id == 10)
		$projectlist->load_projects("0", $f_status_id, $sortorder, $User_ID, $f_author_id, $f_school_id, $f_district_id, "", $f_requestid);
	else
		$projectlist->load_projects($User_ID, $f_status_id, $sortorder, 0, $f_author_id, $f_school_id, $f_district_id, "", $f_requestid);
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
					if ($user->type_id == 10)
						$target = "donation.php";
					else
						$target = "proposal.php";
	?>
		<TR VALIGN=top>
	<? 				if ($user->type_id > 10) { ?>
				<TD ALIGN="left" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>><?=$project->project_id;?></a></TD>
	<?				}	?>
				<TD ALIGN="left" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>><?=$project->submitted_by_name();?></a></TD>
				<TD ALIGN="left" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>><?=$project->project_name;?></a></TD>
	<?				if ($user->type_id > 10) {	?>
				<TD ALIGN="center" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>><?=empty($project->submitted_date) ? "" : date("m/d/y",strtotime($project->submitted_date));?></a></TD>
				<TD ALIGN="left" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>><?=$projectstatuses->project_status_description($project->project_status_id);?></a></TD>
	<?				}	?>
				<TD ALIGN="right" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>><?=sprintf("%01.2f", $project->amount_needed);?></a></TD>
	<?
					if ($user->type_id > 10) {
	?>
				<TD ALIGN="right" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>><?=sprintf("%01.2f", $project->amount_donated());?></a></TD>
				<TD ALIGN="right" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>><?=sprintf("%01.2f", $project->amount_pledged());?></a></TD>
				<TD ALIGN="right" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><?=$project->search_count;?></TD>
				<TD ALIGN="right" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><?=$project->view_count;?></TD>
	<?
					}
	?>
				<TD ALIGN="center" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><?=sprintf("%01.0f",($project->amount_donated()/$project->amount_needed)*100);?>%</TD>
			</TR>
	<?
				}
			}
		}
		if ($project_count <= ($page * $pagesize)) {

		echo "<Form Name='Filter' Method='POST' Action='project_list.php'>\n";

                echo "\t\t<TR>\n\t\t\t\t<TD Colspan='100%'>\n";
		echo "<table width='100%'><tr>\n";
		echo "<td align='left' height=1>\n";
		$min = 1;
		$lpg = floor($projectlist->count()/$pagesize+.999);
		echo "<font size='-1'>Page:";
		if ($page > 6) {
			$min = $page - 3;
			echo "&nbsp;<a href=\"project_list.php?page=1".($f_args == "" ? "" : "$f_args")."\"><<</a>&nbsp;\n";
		}
		for($pg = $min; $pg <= $min+6; $pg++) {
			if ($pg == $page)
				echo "&nbsp;<B>$pg</B>&nbsp;\n";
			else
				echo "&nbsp;<a href=\"project_list.php?page=$pg".($f_args == "" ? "" : "$f_args")."\">$pg</a>&nbsp;\n";
			if ($pg == $lpg) {
				$pg += 1;
				break;
			}
		}
		$pg -= 1;
		if ($page < $lpg) {
			if ($lpg > $pg)
				echo "&nbsp;<a href=\"project_list.php?page=$lpg".($f_args == "" ? "" : "$f_args")."\">>></a>&nbsp;\n";
		}
		echo "\t\t\t\t</font></TD>\n";
		echo "<td align='center' height=1><font size='-1'>\n";
		if ($page > 1)
			echo "<a href=\"project_list.php?page=".($page - 1).($f_args == "" ? "" : "$f_args")."\"><< PREVIOUS</a>\n";
		else
			echo "<< PREVIOUS\n";
		echo "&nbsp;|&nbsp;";
		if ($page < $lpg)
			echo "\t\t\t\t\t<a href=\"project_list.php?page=".($page + 1).($f_args == "" ? "" : "$f_args")."\">NEXT >></a>\n";
		else
			echo "\t\t\t\t\tNEXT >>\n";
		echo "</font></td>\n";
		echo "<td align='right' height=1><font size='-1'>\n";
		echo "<input type='hidden' name='page' value='$page'>\n";

                if ($f_mindate) echo "<input type='hidden' name='f_mindate' value='$f_mindate'>\n";
                if ($f_maxdate) echo "<input type='hidden' name='f_maxdate' value='$f_maxdate'>\n";
                reset($projectstatuses->project_status_list);
        	while (list($statusid, $project_status) = each($projectstatuses->project_status_list)) {
                    $a="f_status_id".$statusid;
                    if(!empty($f_status_id[$statusid]) || ${$a}=="1")
                        echo "<input type='hidden' name='$a' value='1'>\n";
                }

                if ($f_statusidnot) echo "<input type='hidden' name='f_statusidnot' value='$f_statusidnot'>\n";
                if ($f_school_id) echo "<input type='hidden' name='f_school_id' value='$f_school_id'>\n";
                if ($f_district_id) echo "<input type='hidden' name='f_district_id' value='$f_district_id'>\n";
                if ($f_amt)  echo "<input type='hidden' name='f_amt' value='$f_amt'>\n";
                if ($f_amtdonated)  echo "<input type='hidden' name='f_amtdonated' value='$f_amtdonated'>\n";

		echo "<TD COLSPAN=3 ALIGN='RIGHT'>Page Size&nbsp;<SELECT name='pagesize' onChange='this.form.submit();'>\n";
		echo "<OPTION".($pagesize == 10 ? " SELECTED":"").">10</OPTION>\n";
		echo "<OPTION".($pagesize == 20 ? " SELECTED":"").">20</OPTION>\n";
		echo "<OPTION".($pagesize == 30 ? " SELECTED":"").">30</OPTION>\n";
		echo "<OPTION".($pagesize == 40 ? " SELECTED":"").">40</OPTION>\n";
		echo "<OPTION".($pagesize == 50 ? " SELECTED":"").">50</OPTION>\n";
		echo "<OPTION".($pagesize == 60 ? " SELECTED":"").">60</OPTION>\n";
		echo "<OPTION".($pagesize == 70 ? " SELECTED":"").">70</OPTION>\n";
		echo "<OPTION".($pagesize == 80 ? " SELECTED":"").">80</OPTION>\n";
		echo "<OPTION".($pagesize == 90 ? " SELECTED":"").">90</OPTION>\n";
		echo "<OPTION".($pagesize == 100 ? " SELECTED":"").">100</OPTION>\n";
		echo "</SELECT>\n";
		echo "</font></td></tr>";
		echo "<tr><td colspan='100%' height=1 align='left'>Search results found ".$projectlist->count()." requests</td></tr>\n";
		echo "</form>\n";
		echo "</table>\n";
		echo "</TD>\n";
		echo "</TR>\n";



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
	if ($config_project_list_banners == "Y") include "inc/banner_ads.php";
?>
                  </td>
<? require "inc/body_end.inc"; ?>
</html>
