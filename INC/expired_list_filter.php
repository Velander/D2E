<Form Name='Filter' Method='POST' Action="expired_request_list.php">
<? include "hdg_box_begin1.htm"; ?>
<Font size='+1' color="<?=$color_table_hdg_font;?>">Use these Filters to limit the Request List</font>
<? include "hdg_box_begin2.htm"; ?>

	<TABLE bgcolor="<?=$color_table_col_bg;?>" DIR=ltr ID="expired_request_list" ALIGN=bleedright WIDTH="100%" COLS=3 BORDER="0" CELLSPACING="1" CELLPADDING="2">
	<TR>
		<TD align='right' nowrap><B>Date Updated</B></TD>
		<TD colspan=2><b>Max Date:&nbsp;</b><input type='text' size='12' name='f_maxdate' value='<?=$f_maxdate;?>'></td>
	</TR>

<?/* old code ------------- //
	<TR>
		<TD align='right'><B>Status</B></TD>
		<TD><Select name='f_statusid'>
		<OPTION VALUE="">Any</OPTION>
<?	reset($projectstatuses->project_status_list);
	while (list($statusid, $project_status) = each($projectstatuses->project_status_list)) {
		echo ("<OPTION VALUE=\"$project_status->project_status_id\"".($project_status->project_status_id == $f_statusid ? " SELECTED" : "") .">$project_status->project_status_description</OPTION>\n");
	}
?>
		</TD>
		<TD><B>NOT&nbsp;</B><Select name='f_statusidnot'>
		<OPTION VALUE=""></OPTION>
<?	reset($projectstatuses->project_status_list);
	while (list($statusid, $project_status) = each($projectstatuses->project_status_list)) {
		echo ("<OPTION VALUE=\"$project_status->project_status_id\"".($project_status->project_status_id == $f_statusidnot ? " SELECTED" : "") .">$project_status->project_status_description</OPTION>\n");
	}
?>
		</TD>
// ---------------------- */?>
	<TR>
		<TD align='right'><B>Include Status</B></TD>
		<TD Colspan=2>
<?	reset($projectstatuses->project_status_list);
	while (list($statusid, $project_status) = each($projectstatuses->project_status_list)) {
		$a="f_status_id".$statusid;
		if (${$a}=="1") $f_status_id[$statusid] = "On";
		echo ("<Input Type=\"checkbox\" Class=\"\" Name=\"f_status_id[$statusid]\" ".(!empty($f_status_id[$statusid]) ? " CHECKED" : "") .">&nbsp;$project_status->project_status_description&nbsp;&nbsp;");
	}
?>
		</TD>
	</TR>
	<TR>
		<TD align='right'><B>School</B></TD>
		<TD Colspan=2>
<?	reset($schools->school_list);
	echo "<Select name=\"f_school_id\">\n";
	echo "<Option value=\"\">\n";
	while (list($schoolid, $school) = each($schools->school_list)) {
		echo ("<Option value=\"$schoolid\" ".($f_school_id == $schoolid ? " SELECTED" : "") .">&nbsp;$school->school_name&nbsp;&nbsp;");
	}
?>
		</TD>
	</TR>
	<TR>
		<TD align='right'><B>District</B></TD>
		<TD Colspan=2>
<?	reset($districts->district_list);
	echo "<Select name=\"f_district_id\">\n";
	echo "<Option value=\"\">\n";
	while (list($districtid, $district) = each($districts->district_list)) {
		echo ("<Option value=\"$districtid\" ".($f_district_id == $districtid ? " SELECTED" : "") .">&nbsp;$district->district_name&nbsp;&nbsp;");
	}
?>
		</TD>
	</TR>
	<TR>
		<TD Align='center' Colspan='3'>
		<Input Type='Submit' Class='nicebtns' Name='Submit' Value='Apply Filter'>&nbsp;
		<Input Type='Reset' Class='nicebtns' Name='Reset' Value='Resest Filter'>
		</TD>
	</TR>
</TABLE>
<? include "box_end.htm"; ?>
<input type='hidden' name='sortorder' value='<?=$sortorder;?>'>
<input type='hidden' name='pagesize' values='<?=$pagesize;?>'>
</FORM>
