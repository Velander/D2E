<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";
	require_once "inc/class_users.php";
	require_once "inc/class_project.php";
	require_once "inc/class_projects.php";
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

	$users = new users;

	$projects = new projects;

	$f_args = "";
	if ($f_maxdate) $f_args .= "&f_maxdate=".urlencode($f_maxdate);
	if ($f_statusid) $f_args .= "&f_statusid=".urlencode($f_statusid);
	if ($f_statusidnot) $f_args .= "&f_statusidnot=".urlencode($f_statusidnot);
	if ($f_school_id) $f_args .= "&f_school_id=".urlencode($f_school_id);
	if ($f_district_id) $f_args .= "&f_district_id=".urlencode($f_district_id);
	if ($pagesize) $f_args .= "&pagesize=$pagesize";
	reset($projectstatuses->project_status_list);
	while (list($statusid, $project_status) = each($projectstatuses->project_status_list)) {
		$a="f_status_id".$statusid;
		if(!empty($f_status_id[$statusid]) || ${$a}=="1") $f_args .= "&f_status_id".$statusid."=1";
	}

	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		# Build a list of selection projects.
		$selprojects = array();
		while (list($idx, $projectid) = each($project_sel)) {
			$selprojects[] = $projectid;
		}
	}
?>
<html>
<head>
<?
	$pagename = "$config_expired_request_list_name";
	$help_msg_name = "config_expired_request_list_help";
	$help_msg = "$config_expired_request_list_help";
	$help_width = "$config_expired_request_list_help_width";
	$help_height = "$config_expired_request_list_help_height";
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
	if (empty($pagesize)) $pagesize = 20;
	if (empty($page)) $page = 1;
	if (!empty($config_expired_request_list_paragraph1)) {
		include "inc/box_begin.htm";
		echo "$config_expired_request_list_paragraph1";
		include "inc/box_end.htm";
	}

	if (!empty($message))
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center><br>";

if (count($selprojects) && $buttonsubmit) {
	if ($buttonsubmit == "Send Warning Notice") {
		$cnt = 0;
		while (list($idx, $id) = each($selprojects)) {
			$project = new project;
			$project->load_project($id);

			if (!$project->warning_key)
				$project->warning_key = $better_token = md5(uniqid(rand()));

			$body = preg_replace("%PROJECT_ID","$project->project_id",
			preg_replace("%PROJECT_NAME",$project->project_name,
			preg_replace("%PROJECT_DESCRIPTION",$project->project_description,
			preg_replace("%AMOUNT_NEEDED",sprintf("%01.2f", $project->amount_needed),
			preg_replace("%AMOUNT_DONATED",sprintf("%01.2f", $project->amount_donated()),
			preg_replace("%MATERIALS_NEEDED",$project->materials_needed,
			preg_replace("%DATE",date("m/d/Y"),
			preg_replace("%URL","http://www.donate2educate.org/proposal.php?projectid=$project->project_id",
			preg_replace("%UPDATE_LINK","http://www.donate2educate.org/updateproject.php?id=$project->project_id&key=$project->warning_key",
			$email_body)))))))));

			$subject = preg_replace("%PROJECT_ID","$project->project_id",
			preg_replace("%PROJECT_NAME",$project->project_name,
			preg_replace("%DATE",date("m/d/Y"),
			$email_subject)));

			$headers = "From: Donate2Educate <support@donate2educate.org>\r\nBCC: support@donate2educate.org\r\n";
			if (mail($users->user_email($project->submitted_user_id), $subject, $body, $headers)) {
				$project->date_last_warning = date("Y-m-d");
				if ($project->save_project())
					$cnt += 1;
				else
					$message .= "Error saving request $id: $project->error_message<BR>";
			} else
				$message .= "Error sending email for request $id.<BR>";
			// wait for .002 seconds
			usleep(2000);
		}
		$message .= "$cnt warning emails sent.";
		echo "<script type=\"text/javascript\">\nlocation.href='$http_location"."expired_request_list.php?message=".htmlentities(urlencode($message))."&page=".$page.$f_args."'\n</script>\n";

	} elseif ($buttonsubmit == "Suspend Requests") {
		$cnt = 0;
		while (list($idx, $id) = each($selprojects)) {
			$project = new project;
			$project->load_project($id);

			if (!$project->warning_key)
				$project->warning_key = $better_token = md5(uniqid(rand()));

			$body = preg_replace("%PROJECT_ID","$project->project_id",
			preg_replace("%PROJECT_NAME",$project->project_name,
			preg_replace("%PROJECT_DESCRIPTION",$project->project_description,
			preg_replace("%AMOUNT_NEEDED",sprintf("%01.2f", $project->amount_needed),
			preg_replace("%AMOUNT_DONATED",sprintf("%01.2f", $project->amount_donated()),
			preg_replace("%MATERIALS_NEEDED",$project->materials_needed,
			preg_replace("%DATE",date("m/d/Y"),
			preg_replace("%URL","http://www.donate2educate.org/proposal.php?projectid=$project->project_id",
			preg_replace("%UPDATE_LINK","http://www.donate2educate.org/updateproject.php?id=$project->project_id&key=$project->warning_key",
			$email_body)))))))));

			$subject = preg_replace("%PROJECT_ID","$project->project_id",
			preg_replace("%PROJECT_NAME",$project->project_name,
			preg_replace("%DATE",date("m/d/Y"),
			$email_subject)));

			$headers = "From: Donate2Educate <support@donate2educate.org>\r\nBCC: support@donate2educate.org\r\n";
			if (mail($users->user_email($project->submitted_user_id), $subject, $body, $headers)) {
				$project->date_last_warning = date("Y-m-d");
				$project->project_status_id = 10;
				$project->date_status_changed = date("Y-m-d");
				if ($project->save_project())
					$cnt += 1;
				else
					$message .= "Error saving request $id: $project->error_message<BR>";
			} else
				$message .= "Error sending email for request $id.<BR>";
			// wait for .002 seconds
			usleep(2000);
		}
		$message .= "$cnt warning emails sent.";
		echo "<script type=\"text/javascript\">\nlocation.href='$http_location"."expired_request_list.php?message=".htmlentities(urlencode($message))."&page=".$page.$f_args."'\n</script>\n";

	} elseif ($buttonsubmit == "Preview Warning Notice") {
?>
		<Form Name='Preview' Method='POST' Action='expired_request_list.php'>
		<B>Email Subject:</B>&nbsp;<input type='text' size='80' name='email_subject' value="<?=$config_expired_warning_subject;?>"><BR>
		<B>Email Message</B><BR>
		<textarea name="email_body" rows="10" cols="83"><?=$config_expired_warning_email;?></textarea><BR>
		<B>Project List</B><BR>
<?
		while (list($idx, $id) = each($selprojects)) {
			$project = new project;
			$project->load_project($id);
			echo "&nbsp;&nbsp;$project->project_id - ".$project->project_name."<BR>";
			echo "<input type='hidden' name='project_sel[\"$project->project_id\"]' value='$id'>\n";
		}
		echo "<input type='hidden' name='page' value='$page'>\n";
		echo "<input type='hidden' name='f_school_id' value='$f_school_id'>\n";
		echo "<input type='hidden' name='f_district_id' value='$f_district_id'>\n";
		echo "<input type='hidden' name='f_maxdate' value='$f_maxdate'>\n";
		echo "<input type='hidden' name='sortorder' value='$sortorder'>\n";
		reset($projectstatuses->project_status_list);
		while (list($statusid, $project_status) = each($projectstatuses->project_status_list)) {
			$a="f_status_id".$statusid;
			if(!empty($f_status_id[$statusid]) || ${$a}=="1")
				echo "<input type='hidden' name=f_status_id".$statusid." value='1'>\n";
		}
		echo "<BR>";
		echo "<Input Type='submit' Name='buttonsubmit' class='nicebtns' Value='Send Warning Notice'>";
		echo "</form>";
	} else if ($buttonsubmit == "Preview Suspension Notice") {
?>
		<Form Name='Preview' Method='POST' Action='expired_request_list.php'>
		<B>Email Subject:</B>&nbsp;<input type='text' size='80' name='email_subject' value="<?=$config_suspended_subject;?>"><BR>
		<B>Email Message</B><BR>
		<textarea name="email_body" rows="10" cols="83"><?=$config_suspended_email;?></textarea><BR>
		<B>Project List</B><BR>
<?
		while (list($idx, $id) = each($selprojects)) {
			$project = new project;
			$project->load_project($id);
			echo "&nbsp;&nbsp;$project->project_id - ".$project->project_name."<BR>";
			echo "<input type='hidden' name='project_sel[\"$project->project_id\"]' value='$id'>\n";
		}
		echo "<input type='hidden' name='page' value='$page'>\n";
		echo "<input type='hidden' name='f_school_id' value='$f_school_id'>\n";
		echo "<input type='hidden' name='f_district_id' value='$f_district_id'>\n";
		echo "<input type='hidden' name='f_maxdate' value='$f_maxdate'>\n";
		echo "<input type='hidden' name='sortorder' value='$sortorder'>\n";
		reset($projectstatuses->project_status_list);
		while (list($statusid, $project_status) = each($projectstatuses->project_status_list)) {
			$a="f_status_id".$statusid;
			if(!empty($f_status_id[$statusid]) || ${$a}=="1")
				echo "<input type='hidden' name=f_status_id".$statusid." value='1'>\n";
		}
		echo "<BR>";
		echo "<Input Type='submit' Name='buttonsubmit' class='nicebtns' Value='Suspend Requests'>";
		echo "</form>";
	}
} else {
	if (!$f_maxdate)
		$f_maxdate = date("Y-m-d", mktime(0, 0, 0, date("m")-3, date("d"),   date("Y")));

	if (!$f_status_id)
		$f_status_id[3] = "Y";

	if ($user->type_id > 10)
		include "inc/expired_list_filter.php";
?>
	<table width=100% cellspacing=1 cellpadding=1>
		<tr>
			<td align=left width=50% bgcolor="<?=$color_table_hdg_bg;?>">
<TABLE DIR=ltr ID="expired_request_list" ALIGN=bleedright WIDTH="100%" COLS=4 BORDER="0" CELLSPACING="2" CELLPADDING="2">
	<TR VALIGN=bottom>
<?
if (!isset($sortorder)) $sortorder = "name";
		if ($user->type_id > 10)
			echo "<TH Name=\"SortByID\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><BR><BR><font color=\"$color_table_hdg_font\">Sel</font></TH>";
			echo "<TH Name=\"SortByID\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"expired_request_list.php?page=$page&sortorder=".htmlentities(urlencode("id".($sortorder == "id" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "id") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "id desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR><BR>ID</font></A></TH>";
		echo "<TH Name=\"SortByName\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"expired_request_list.php?page=$page&sortorder=".htmlentities(urlencode("name".($sortorder == "name" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "name") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "name desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR><BR>Project Name</font></A></TH>";
		if ($user->type_id > 10) {
			echo "<TH Name=\"SortByDate\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"expired_request_list.php?page=$page&sortorder=".htmlentities(urlencode("updatedate".($sortorder == "updatedate" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "updatedate") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "updatedate desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR>Date<br>Updated</font></A></TH>";
			echo "<TH Name=\"SortByDate\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"expired_request_list.php?page=$page&sortorder=".htmlentities(urlencode("warningdate".($sortorder == "warningdate" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "warningdate") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "warningdate desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR>Warning<BR>Sent</font></A></TH>";
			echo "<TH Name=\"SortByStatus\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"expired_request_list.php?page=$page&sortorder=".htmlentities(urlencode("status".($sortorder == "status" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "status") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "status desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR><BR>Status</font></A></TH>";
		}
		echo "<TH Name=\"SortByAmount\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"expired_request_list.php?page=$page&sortorder=".htmlentities(urlencode("amount".($sortorder == "amount" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "amount") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "amount desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR>Amount<BR>Requested</font></A></TH>";

		if ($user->type_id > 10) {
			echo "<TH Name=\"SortByDonations\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"expired_request_list.php?page=$page&sortorder=".htmlentities(urlencode("donations".($sortorder == "donations" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "donations") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "donations desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR>Amount<BR>Donated</font></A></TH>";
			echo "<TH Name=\"SortByPledged\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"expired_request_list.php?page=$page&sortorder=".htmlentities(urlencode("pledged".($sortorder == "pledged" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "pledged") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "pledged desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR>Amount<BR>Pledged</font></A></TH>";
		}
		echo "<TH Name=\"SortByFunded\" ALIGN=\"center\" VALIGN=\"bottom\" BGCOLOR=\"$color_table_hdg_bg\"><a href=\"expired_request_list.php?sortorder=".htmlentities(urlencode("funded".($sortorder == "funded" ? " desc" : ""))).($f_args == "" ? "" : "$f_args")."\">".(($sortorder == "funded") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "funded desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR>%<BR>Funded</font></A></TH>";
?>
	</TR>
<?
	if ($sortorder) $f_args .= "&sortorder=".urlencode($sortorder);
	if ($user->type_id <= 10)
		$projectlist->load_projects("0", $f_status_id, $sortorder, $User_ID, 0, $f_school_id, $f_district_id, $f_maxdate);
	else
		$projectlist->load_projects($User_ID, $f_status_id, $sortorder, 0, 0, $f_school_id, $f_district_id, $f_maxdate);

	if (($page-1) * $pagesize > $projectlist->count())
		$page = floor($projectlist->count()/$pagesize);

	if ($projectlist->count() > 0) {
		echo "<Form Name='Filter' Method='POST' Action='expired_request_list.php'>\n";
		$project_count = 0;
		while (list($projectid, $project) = each($projectlist->project_list)) {
			$approved = true;
			$project_count += 1;
			if ($project_count >= (($page-1) * $pagesize)) {
				if ($project_count >= ($page * $pagesize))
					break;
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
				<TD ALIGN="left" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><input type='checkbox' value='<?=$project->project_id;?>' name='project_sel[<?=$project->project_id;?>]'<?=(array_key_exists($project->project_id,$project_sel)?" CHECKED":"");?>></TD>
				<TD ALIGN="left" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>><?=$project->project_id;?></a></TD>
	<?				}	?>
				<TD ALIGN="left" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>><?=$project->project_name;?></a></TD>
	<?				if ($user->type_id > 10) {	?>
				<TD ALIGN="center" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>><?=empty($project->date_last_updated) ? date("m/d/y",strtotime($project->review_date)) : date("m/d/y",strtotime($project->date_last_updated));?></a></TD>
				<TD ALIGN="center" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>><?=($project->date_last_warning ? date("m/d/y",strtotime($project->date_last_warning)):"");?></a></TD>
				<TD ALIGN="left" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>><?=$projectstatuses->project_status_description($project->project_status_id);?></a></TD>
	<?				}	?>
				<TD ALIGN="right" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>><?=sprintf("%01.2f", $project->amount_needed);?></a></TD>
	<?
					if ($user->type_id > 10) {
	?>
				<TD ALIGN="right" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>><?=sprintf("%01.2f", $project->amount_donated());?></a></TD>
				<TD ALIGN="right" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><a href=<?=$target;?>?projectid=<?=$project->project_id;?>><?=sprintf("%01.2f", $project->amount_pledged());?></a></TD>
	<?
					}
	?>
				<TD ALIGN="center" VALIGN="top" BGCOLOR="<?=$color_table_col_bg;?>"><?=sprintf("%01.0f",($project->amount_donated()/$project->amount_needed)*100);?>%</TD>
			</TR>
	<?
				}
			}
		}
		echo "\t\t<TR>\n\t\t\t\t<TD Colspan='100%'>\n";
		echo "<table width='100%'><tr>\n";
		echo "<td align='left' height=1>\n";
		$min = 1;
		$lpg = floor($projectlist->count()/$pagesize+.999);
		echo "<font size='-1'>Page:";
		if ($page > 6) {
			$min = $page - 3;
			echo "&nbsp;<a href=\"expired_request_list.php?page=1".($f_args == "" ? "" : "$f_args")."\"><<</a>&nbsp;\n";
		}
		for($pg = $min; $pg <= $min+6; $pg++) {
			if ($pg == $page)
				echo "&nbsp;<B>$pg</B>&nbsp;\n";
			else
				echo "&nbsp;<a href=\"expired_request_list.php?page=$pg".($f_args == "" ? "" : "$f_args")."\">$pg</a>&nbsp;\n";
			if ($pg == $lpg) {
				$pg += 1;
				break;
			}
		}
		$pg -= 1;
		if ($page < $lpg) {
			if ($lpg > $pg)
				echo "&nbsp;<a href=\"expired_request_list.php?page=$lpg".($f_args == "" ? "" : "$f_args")."\">>></a>&nbsp;\n";
		}
		echo "\t\t\t\t</font></TD>\n";
		echo "<td align='center' height=1><font size='-1'>\n";
		if ($page > 1)
			echo "<a href=\"expired_request_list.php?page=".($page - 1).($f_args == "" ? "" : "$f_args")."\"><< PREVIOUS</a>\n";
		else
			echo "<< PREVIOUS\n";
		echo "&nbsp;|&nbsp;";
		if ($page < $lpg)
			echo "\t\t\t\t\t<a href=\"expired_request_list.php?page=".($page + 1).($f_args == "" ? "" : "$f_args")."\">NEXT >></a>\n";
		else
			echo "\t\t\t\t\tNEXT >>\n";
		echo "</font></td>\n";
		echo "<td align='right' height=1><font size='-1'>\n";
		echo "<input type='hidden' name='page' value='$page'>\n";
		echo "<input type='hidden' name='f_school_id' value='$f_school_id'>\n";
		echo "<input type='hidden' name='f_district_id' value='$f_district_id'>\n";
		echo "<input type='hidden' name='f_maxdate' value='$f_maxdate'>\n";
		echo "<input type='hidden' name='sortorder' value='$sortorder'>\n";
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
		echo "</table>\n";
		echo "</TD>\n";
		echo "</TR>\n";
		echo "<TR>\n";
		echo "<td colspan='100%' height=1 align='center'>";
		echo "<Input Type='submit' Name='buttonsubmit' class='nicebtns' Value='Preview Warning Notice'>&nbsp;&nbsp;<Input Type='submit' Name='buttonsubmit' class='nicebtns' Value='Preview Suspension Notice'>";
		echo "</TD>\n";
		echo "</TR>\n";

		echo "</FORM>\n";
	} else {
		echo "\t\t\t<TR>\n\t\t\t\t<TD Colspan=100% ALIGN='center' BGCOLOR=\"$color_table_col_bg\">No Projects Found</TD>\n\t\t\t</TR>\n";
	}
?>
			</TABLE>
		</td>
	</tr>
</table>
<? } ?>
                  </td>
<? require "inc/body_end.inc"; ?>
</html>
