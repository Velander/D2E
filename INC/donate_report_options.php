				<font size="+1" color="<?=$color_table_hdg_font;?>">DONATION AUDIT REPORT</font>
				<?	include "inc/box_middle.htm";	?>
				<table>
				<Form Name="DonateReport" Method="POST">
					<tr>
						<td align="right">District</td>
						<td align="left">
							<select name="f_districtid">
								<option value="0">All Districts</option>
<?			reset($districts->district_list);
			while (list($districtid, $district) = each($districts->district_list)) {
				echo ("\t\t\t\t\t\t\t\t<OPTION VALUE=\"$district->district_id\"".($district->district_id == $f_districtid ? " SELECTED" : "") .">$district->district_name</OPTION>\n");
			}
?>
							</select>
						</td>
					</tr>

					<tr>
						<td align="right">School</td>
						<td align="left">
							<select name="f_schoolid">
								<option value="0">All Schools</option>
<?			reset($schools->school_list);
			while (list($schoolid, $school) = each($schools->school_list)) {
				echo ("\t\t\t\t\t\t\t\t<OPTION VALUE=\"$school->school_id\"".($school->school_id == $f_schoolid ? " SELECTED" : "") .">$school->school_name</OPTION>\n");
			}
?>
							</select>
						</td>
					</tr>

					<tr>
						<td align="right">Grade Level</td>
						<td align="left">
							<SELECT NAME="f_gradelevel" SIZE=1>
								<OPTION value="0">Any Grade Level</OPTION>
<?	reset($gradelevels->grade_level_list);
	while (list($gradelevelid, $gradelevel) = each($gradelevels->grade_level_list)) {
		echo ("\t\t\t\t\t\t\t\t<OPTION VALUE=\"$gradelevel->grade_level_id\"".($gradelevel->grade_level_id == $f_gradelevel ? " SELECTED" : "") .">$gradelevel->grade_level_description</OPTION>\n");
	}
?>
							</select>
						</td>
					</tr>

					<tr>
						<td align="right">Project Type</td>
						<td align="left">
							<SELECT NAME="f_projecttype" SIZE=1>
								<OPTION value="0">All Types</OPTION>
<?	reset($projecttypes->project_type_list);
	while (list($projecttypeid, $projecttype) = each($projecttypes->project_type_list)) {
		echo ("\t\t\t\t\t\t\t\t<OPTION VALUE=\"$projecttype->project_type_id\"".($projecttype->project_type_id == $f_projecttype ? " SELECTED" : "") .">$projecttype->project_type_description</OPTION>\n");
	}
?>
							</SELECT>
						</td>
					</tr>
					<tr>
						<td align="right">Date Range</td>
						<td align="left">Between
							<input type="text" name="min_date" size="12" value="<?=$min_date;?>"> and
							<input type="text" name="max_date" size="12" value="<?=$max_date;?>">
						</td>
					</tr>
					<tr>
						<td align="left" colspan="2">
							<input type="hidden" name="reportno" value="<?=$reportno;?>">
							<input type="submit" class="nicebtns" name="submit" value="Run Report">
						</td>
					</tr>
				</FORM>
				</table>
