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
	require_once "inc/class_banner.php";

	$project_status = new project_status;
	$projectstatuses = new project_statuses;
	$projectstatuses->load_project_statuses();

	$all_schools = new schools;
	$all_schools->load_donation_schools("ALL");

	$donation_schools = new schools;
	$donation_schools->school_list = $all_schools->school_list;

	$districts = new districts;
	$districts->load_donation_districts();

	$grade_levels = new grade_levels;
	$grade_levels->load_grade_levels();

	$all_project_types = new project_types;
	$all_project_types->load_project_search_types();

	$project_types = new project_types;
	$project_types->load_project_search_types($f_district_id, $f_school_id);

	$projectlist = new projects;

	$user_rcd = new user;
	$user_rcd->load_user($User_ID);

    setlocale(LC_MONETARY, 'en_US');

	$f_district_id		= $_GET["f_district_id"];
	$f_school_id		= $_GET["f_school_id"];
	$sortorder			= $_GET["sortorder"];
	$f_title_words 		= $_GET["f_title_words"];
	$f_teacher_id		= $_GET["f_teacher_id"];
	$f_grade_level_id	= $_GET["f_grade_level_id"];
	$f_project_type_id 	= $_GET["f_project_type_id"];
	$f_funds_required	= $_GET["f_funds_required"];
	$f_endangered		= $_GET["f_endangered"];
	$f_submitted_date	= $_GET["f_submitted_date"];

	if ($_SERVER['REQUEST_METHOD'] == "POST")
	{
		$f_district_id		= $_POST["f_district_id"];
		$f_school_id		= $_POST["f_school_id"];
		$f_title_words 		= $_POST["f_title_words"];
		$f_submitted_date	= $_POST["f_submitted_date"];
		$f_project_type_id 	= $_POST["f_project_type_id"];

		if ($q_school_id) $f_district_id = $q_school_id;
		if ($f_district_id != "0")
		{
			$district = new district;
			if ($district->load_district($f_district_id))
			{
				if (!empty($district->alt_donation_url))
				{
					echo "<script type=\"text/javascript\">\nlocation.href='".$district->alt_donation_url."?message=".htmlentities(urlencode("You have been redirect here from Donate2Educate.org."))."'\n</script>";
				}
			}
		}
	}
	if ($f_school_id=="ALL")
            $f_school_id = "0";

	if (session_id() == "")
            if (!session_start())
                $message = "Unable to start session.";

	if ($_SERVER['REQUEST_METHOD'] == "POST" || ISSET($sortorder) || $f_district_id || $f_school_id || $projectlist->approved_project_count() < 3)
	{
            if (!isset($sortorder)) $sortorder = "type";
            $projectlist->search_projects($f_title_words, $f_district_id, $f_school_id, $f_teacher_id, $f_grade_level_id, $f_project_type_id, $f_funds_required, $f_endangered, $sortorder, "3", $f_submitted_date);
            $search_results = array();
            if ($projectlist->count() > 0) {
                while (list($projectid, $project) = each($projectlist->project_list)) {
                    $search_results[] = $project;
                }
            }
            $_SESSION['search_results'] = $search_results;
            $_SESSION['title_words'] 	= $f_title_words;
            $_SESSION['district_id']	= $f_district_id;
            $_SESSION['school_id']		= $f_school_id;
            $_SESSION['submitted_date']	= $f_submitted_date;

            $_SESSION['search_idx'] = 0;
	} else {
            $search_results = $_SESSION['search_results'];
            $f_title_words 	= $_SESSION['title_words'];
            $f_district_id 	= $_SESSION['district_id'];
            $f_school_id	= $_SESSION['school_id'];
            $f_submitted_date 	= $_SESSION['submitted_date'];
	}

	if ($download==1) {
            $prev_school = "";
            if (count($search_results) > 0) {
                $htmlout = true;
                $pdfout = false;
                $textout = false;
                $textdoc = "";
if($htmlout) echo "<style>HR {page-break-after:always}</style>";
                if($pdfout)
                {
                    $mypdf = PDF_new();
                    PDF_begin_document($mypdf, "", "");
                    if (empty($user_rcd->company)) {
                        PDF_set_info($mypdf, "Author", "$user_rcd->first_name $user_rcd->last_name");
                    } else {
                        PDF_set_info($mypdf, "Author", "$user_rcd->company");
                    }
                    PDF_set_info($mypdf, "Creator", "www.donate2educate.org");
                    PDF_set_info($mypdf, "Title", "Donation Request List");
                    PDF_set_parameter($mypdf, "openmode", "bookmarks");
                    $myfont = PDF_findfont($mypdf, "Times-Roman", "host", 0);
                }
                while (list($projectid, $project) = each($search_results))
                {
                    if($pdfout)
                    {
                        PDF_begin_page_ext($mypdf, 595, 842, "");
                        PDF_setfont($mypdf, $myfont, 16);
                        PDF_show_xy($mypdf, $project->project_name, 60, 800);
                    }
if($htmlout) echo "<H1>$project->project_name</H1>";
if($textout) chr(12).$textdoc .= $project->project_name."\r";
                    if ($all_schools->school_name($project->school_id) != $prev_school)
                    {
                        if($pdfout)
                            $mytopparent = PDF_add_bookmark($mypdf, $all_schools->school_name($project->school_id), 0, 1);
                        $prev_school = $all_schools->school_name($project->school_id);
                    }
                    if($pfdout)
                    {
                        $mychild = PDF_add_bookmark($mypdf, $project->project_name, $mytopparent, 1);
                        PDF_setfont($mypdf, $myfont, 14);
                        PDF_continue_text($mypdf, ""); # Blank Line
                        PDF_continue_text($mypdf, "Request ID: ".$project->project_id);
                        PDF_continue_text($mypdf, "School: ".$all_schools->school_name($project->school_id));
                        PDF_continue_text($mypdf, "Category: ".$all_project_types->project_type_description($project->project_type_id));
                        PDF_continue_text($mypdf, "Grade Level: ".$grade_levels->grade_level_description($project->grade_level_id));
                        PDF_continue_text($mypdf, "Teacher: ".$project->submitted_by_name());
                        PDF_continue_text($mypdf, ""); # Blank Line
                        PDF_continue_text($mypdf, "Description");
                        PDF_continue_text($mypdf, ""); # Blank Line
                    }
if($htmlout)
    {
    echo "<P><table><tr><td align='right'>Request ID:</td><td>".$project->project_id."</td></tr>";
    echo "<tr><td align='right'>School:</td><td>".$all_schools->school_name($project->school_id)."</td></tr>";
    echo "<tr><td align='right'>Category:</td><td>".$all_project_types->project_type_description($project->project_type_id)."</td></tr>";
    echo "<tr><td align='right'>Grade Level:</td><td>".$grade_levels->grade_level_description($project->grade_level_id)."</td></tr>";
    echo "<tr><td align='right'>Teacher:</td><td>".$project->submitted_by_name()."</td></tr></table></p>";
    echo "<BR><P>Description:</P><P>";
    }
if($textout)
    {
    $textdoc .= "Request ID: ".$project->project_id."\r";
    $textdoc .= "School: ".$all_schools->school_name($project->school_id)."\r";
    $textdoc .= "Category: ".$all_project_types->project_type_description($project->project_type_id)."\r";
    $textdoc .= "Grade Level: ".$grade_levels->grade_level_description($project->grade_level_id)."\r";
    $textdoc .= "Teacher: ".$project->submitted_by_name()."\r\r";
    $textdoc .= "Description:\r\r";
    }

                     # Need to display the description one line at a time.
                    $par = str_replace("\r\n\r\n","^",stripslashes($project->project_description));
                    $Apar = explode("^", $par);
                    foreach($Apar as $foo)
                    {
                        $foo = str_replace("\r\n"," ",$foo);
                        $foo = str_replace("  "," ",$foo);
                        $foo = str_replace("  "," ",$foo);
                        $foo = str_replace("  "," ",$foo);
                        $foo = wordwrap($foo,80,"|");
                        $Arr = explode("|",$foo);
                        foreach($Arr as $line)
                        {
                         if($pdfout)
                           PDF_continue_text($mypdf,$line);
if($htmlout) echo ("$line ");
if($textout) $textdoc .= "$line\r";
                        }
                         if($pdfout)
                            PDF_continue_text($mypdf, ""); # Blank Line
if($htmlout) echo "</P>";
if($textout) $textdoc .= "\r";
                    }
                    if($pdfout)
                    {
                        PDF_continue_text($mypdf, ""); # Blank Line
                        PDF_continue_text($mypdf, "Materials Needed");
                        PDF_continue_text($mypdf, ""); # Blank Line
                    }
if($htmlout) echo "<P>Materials Needed</P><P>";
if($textout) $textdoc .= "Materials Needed\r\r";
                    # Need to display the description one line at a time.
                    $foo = str_replace("\r\n","|",stripslashes($project->materials_needed));
                    $foo = wordwrap($foo,80,"|");
                    $Arr = explode("|",$foo);
                    foreach($Arr as $line) {
                        if($pdfout)
                            PDF_continue_text($mypdf,$line);
if($htmlout) echo ("$line<BR>");
if($textout) $textdoc .= "$line\r";
                    }
                    if($pdfout)
                    {
                        PDF_continue_text($mypdf, ""); # Blank Line
                        PDF_continue_text($mypdf, "Request Cost: ".money_format('%n', $project->amount_needed));
                        PDF_continue_text($mypdf, "Donations Received: ".money_format('%n', $project->amount_donated()));
                    }
if($htmlout)
    {
    echo ("</P>");
    echo "<P><table><tr><td align='right'>Request Cost:</td><td align='right'>".money_format('%n', $project->amount_needed)."</td></tr>";
    echo "<tr><td align='right'>Donations Received:</td><td align='right'>".money_format('%n', $project->amount_donated())."</td></tr>";
    }
if($textout)
    {
    $textdoc .= "\r";
    $textdoc .= "Request Cost: ".money_format('%n', $project->amount_needed)."\r";
    $textdoc .= "Donations Received: ".money_format('%n', $project->amount_donated())."\r";
    }

                    if ($project->amount_pledged())
                    {
                        if($pdfout)
                            PDF_continue_text($mypdf, "Donations Pledged: ".money_format('%n', $project->amount_pledged()));
if($htmlout) echo "<tr><td align='right'>Donations Pledged:</td><td align='right'>".money_format('%n', $project->amount_pledged())."</tr></td>";
if($textout) $textdoc .= "Donations Pledged: ".money_format('%n', $project->amount_pledged())."\r";

                    }
if($htmlout) echo "</table></p>";
                    if($pdfout)
                        PDF_end_page($mypdf);
if($htmlout) echo "</P><HR>";
if($textout) $textdoc .= "\r";
                }
                if (empty($prev_school))
                {
                    # Nothing was in the select list.
                    if($pdfout)
                    {
                        PDF_begin_page_ext($mypdf, 595, 842, "");
                        PDF_setfont($mypdf, $myfont, 14);
                        PDF_show_xy($mypdf, "No Requests Selected", 30, 830);
                        PDF_end_page($mypdf);
                    }
                }
            } else {
                    echo "No search results found.<BR>$message";
            }
            if($pdfout)
                {
                    PDF_end_document($mypdf, "");
                    $mybuf = PDF_get_buffer($mypdf);
                    $mylen = strlen($mybuf);
                    header("Content-type: application/pdf");
                    header("Content-Length: $mylen");
                    header("Content-Disposition: inline; filename=request_list.pdf");
                    print $mybuf;
                    PDF_delete($mypdf);
                }
            if($textout)
                {
#                    header("Content-type: application/octet-stream");
#                    echo($textdoc);

                    $mylen = strlen($textdoc);
                    header("Content-type: text/plain");
                    header("Content-Length: $mylen");
                    header("Content-Disposition: inline; filename=request_list.txt");
                    print $textdoc;
                }

            exit();
	}
	require_once "inc/jscript.jav";
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

?>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
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
		if ($REQUEST_METHOD != "POST" && !ISSET($sortorder) && !$f_district_id && !$f_school_id && $projectlist->approved_project_count() > 3) {
                    if (!empty($config_donationsearch_paragraph1)) {
                        echo "$config_donationsearch_paragraph1";
                    }
		}
?>
					<table width='100%'>
					<FORM NAME="frmProjectSearch" ACTION="donation_search.php" METHOD="POST">
						<tr>
                                                    <td  width="200" align='right'><b>Keyword Search</b></td>
                                                    <td><input type='text' name='f_title_words' value='<?=$f_title_words;?>' size='32'></td>
						</tr>
						<tr>
                                                    <td align='right'><b>Entered since</b></td>
                                                    <td><input type='text' name='f_submitted_date' value='<?=$f_submitted_date;?>' size='20'></td>
						</tr>
						<tr>
                                                    <td align='right'>Limit search to a <b>District</b></td>
                                                    <td><select name="f_district_id" id="f_district_id" onchange="changeDist();">
                                                        <option value='ALL'>All Districts</option>
<?
		reset($districts->district_list);
		while (list($districtid, $district) = each($districts->district_list)) {
			if ($district->inactive != "Y") {
				echo "\t\t\t\t\t\t\t\t<Option value='$district->district_id'".($f_district_id == $district->district_id ? " SELECTED":"").">$district->district_name</option>\n";
			}
		}
?>
                                                        </select></td>
                                                    </td>
						</tr>
						<tr>
                                                    <td align='right'>Limit search to a <b>School</b></td>
                                                    <td><select name='f_school_id' id="f_school_id" onchange="changeSch();">
                                                        <option value='ALL'>All Schools</option>
<?
		$donation_schools->load_donation_schools($f_district_id);
		reset($donation_schools->school_list);
		while (list($schoolid, $school) = each($donation_schools->school_list)) {
			if ($school->inactive != "Y") {
				echo "\t\t\t\t\t\t\t\t<Option value='$school->school_id'".($f_school_id == $school->school_id ? " SELECTED":"").">$school->school_name</option>\n";
			}
		}
?>
                                                        </select></td>
                                                    </td>
						</tr>
						<tr>
                                                    <td align='right'>Limit search to a <b>Category</b></td>
                                                    <td><select name='f_project_type_id' id="f_project_type_id">
                                                        <option value='0'>All Categories</option>
<?
		$project_types->load_project_search_types($f_district_id, $f_school_id);
		while (list($type_id, $project_type) = each($project_types->project_type_list))
			echo "\t\t\t\t\t\t\t\t<Option value='$project_type->project_type_id'".($f_project_type_id == $project_type->project_type_id ? " SELECTED":"").">$project_type->project_type_description</option>\n";
?>
                                                        </select></td>
						</tr>
						<tr>
                                                    <td align='center' colspan='100%'>
                                                        <INPUT TYPE="submit" NAME="sumbit" VALUE="Submit Search" class="nicebtns" border='0'>
                                                    </td>
						</tr>
					</form>
					</table>
<?
	} else {
		echo "<table>\n";
	}
	if ($search_results) {
?>
	<table width=100% cellspacing=0 cellpadding=1 border=0>
		<tr>
			<td align=left width=50% bgcolor="<?=$color_table_hdg_bg;?>">
		<TABLE DIR=ltr ID="project_list" ALIGN=bleedright WIDTH="100%" COLS="4" BORDER="0" CELLSPACING="0" CELLPADDING="2">
<?		if (!empty($config_donationsearch_projlist) && empty($resultsonly)) {
                    echo "\t\t\t<TR VALIGN=bottom>\n";
                    echo "\t\t\t\t<TD Colspan='4' Align='Left' vAlign='Middle' BGCOLOR=\"$color_table_col_bg\">$config_donationsearch_projlist</TD>\n";
                    echo "\t\t\t</TR>\n";
		}
?>
		<?
		echo "	<TR VALIGN=bottom>";
#		echo "\t\t\t\t<TD Name=\"Details\" ALIGN=center VALIGN=bottom BGCOLOR=\"$color_table_hdg_bg\"><font color=\"$color_table_hdg_font\" fontsize=\"11px\">Details</font></TD>";
#		echo "\t\t\t\t<TD Name=\"SortByName\" ALIGN=center VALIGN=bottom BGCOLOR=\"$color_table_hdg_bg\"><a href=\"donation_search.php?sortorder=".htmlentities(urlencode("name".($sortorder == "name" ? " desc" : "")))."\">".(($sortorder == "name") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "name desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\">Project Name</font></A></TD>";
#		echo "\t\t\t\t<TD Name=\"SortBySchool\" ALIGN=center VALIGN=bottom BGCOLOR=\"$color_table_hdg_bg\"><a href=\"donation_search.php?sortorder=".htmlentities(urlencode("school".($sortorder == "school" ? " desc" : "")))."\">".(($sortorder == "school") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "school desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\">School</font></A></TD>";
#		echo "\t\t\t\t<TD Name=\"SortByType\" ALIGN=center VALIGN=bottom BGCOLOR=\"$color_table_hdg_bg\"><a href=\"donation_search.php?sortorder=".htmlentities(urlencode("type".($sortorder == "type" ? " desc" : "")))."\">".(($sortorder == "type") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "type desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\">Category</font></A></TD>";
		#echo "\t\t\t\t<TD Name=\"SortByNeeded\" ALIGN=center VALIGN=bottom BGCOLOR=\"$color_table_hdg_bg\"><a href=\"donation_search.php?sortorder=".htmlentities(urlencode("amount".($sortorder == "amount" ? " desc" : "")))."\">".(($sortorder == "amount") ? "<img border=0 src=\"images/view-sortup.gif\">" : (($sortorder == "amount desc") ? "<img border=0 src=\"images/view-sortdown.gif\">" : ""))."<font color=\"$color_table_hdg_font\"><BR>Amount<BR>Needed</font></A></TD>";
#		echo "	</TR>";

		$matchingfunds = new matching();
			if (count($search_results) > 0) {
				while (list($projectid, $project) = each($search_results)) {
					$projectid = $project->project_id;

		?>
			<TR VALIGN=top VALIGN=middle>
				<TD ALIGN=left   VALIGN=left BGCOLOR="<?=$color_table_col_bg;?>">
					<TABLE BORDER=0 WIDTH="100%" cellspacing=0>
					<TR>
						<TD Align="Left">
						<a href="<?=$http_location;?>donation.php?projectid=<?=$project->project_id;?>">
						<image border=0 src="images/buttons/arrow.gif">&nbsp;<Font Color=#336600><B><?=$project->project_name;?> (<?=$project->project_id;?>)</B></Font></a>
						</TD>
						<TD Align="Right">
<?  if ($matchingfunds->matching_amount($projectid) > 0) { ?>
						<FONT COLOR=RED>Matching Funds Available</FONT>
<?  }  ?>
						</TD>
					</TR>
					<TR>
						<TD Align="Left" Colspan=2>
						&nbsp;&nbsp;&nbsp;&nbsp;<?=substr(stripslashes($project->project_description), 0, 170).(strlen($project->project_description)>170?"...":"");?>
						</TD>
					</TR>
					<TR>
						<TD><I><?=($all_schools->school_homepage($project->school_id) ? "<a target=\"school\" href=\"".$all_schools->school_homepage($project->school_id)."\">" : "");?><?=$all_schools->school_name($project->school_id);?><?=($all_schools->school_homepage($project->school_id) ? "</a>":"");?></I></TD>
						<TD Align=Right>Funds still needed: $<?=sprintf("%01.2f",$project->amount_needed - $project->amount_donated(null, null) - $project->amount_pledged());?></TD>
					</TR>
					<TR>
						<TD COLSPAN=2></TD>
					</TR>
					</TABLE>
				</TD>
			</TR>
		<?
					if (!($user_rcd->type_id > 1))
						$projectlist->project_searched($projectid);
				}
		?>
			<TR VALIGN=top VALIGN=middle>
				<TD ALIGN=left   VALIGN=left BGCOLOR="<?=$color_table_col_bg;?>">
				<a href="donation_search.php?download=1" target="export">Export</a>
				</TD>
			</TR>
		<?
			} else {
				echo "<TR><TD Colspan=4 ALIGN='center'>No Projects Found</TD></TR>";
			}
		?>
		</TABLE>
			</td>
		</tr>
	</table>
<?
		if ($config_donationsearch_banners == 'Y') include "inc/banner_ads.php";

		if (!empty($resultsonly)) {
			echo "</table>\n";
		}
	}
	if (empty($resultsonly)) {
		require "inc/body_end.inc";
	}
?>
</html>