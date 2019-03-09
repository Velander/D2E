<?
  	if (isset($User_ID)) {
  		include $path_root."inc/box_begin.htm";
?>
	<table width="100%" cellspacing=0 cellpadding=1 border=0 align=left>
		<TR>
			<TD Align='Center' colspan=6><b>Donation Details</b></TD>
		</TR><TR>
			<TD Align='Left' Valign='Bottom'><b>ID</b></TD>
			<TD Align='Left' Valign='Bottom'><b>Request Name</b></TD>
			<TD Align='Left' Valign='Bottom'><b>Category</b></TD>
			<TD Align='Left' Valign='Bottom'><b>School</b></TD>
			<TD Align='Center' Valign='Bottom'><b>Funds<BR>Needed</b></TD>
			<TD Align='Center' Valign='Bottom'><b>Donation<BR>Amount</b></TD></TR>
		</TR>
<?
	reset($donation->donation_project_list);
	$total = 0;
	while (list($donation_projectid, $donation_project) = each($donation->donation_project_list)) {
		$project = new project();
		if ($project->load_project($donation_project->project_id)) {
			$total += $donation_project->donation_amount;
			$districtid = $schools->school_district_id($project->school_id);
			$district = new district();
			$district->load_district($districtid);
			$checks_payable_to = $districts->district_administrator($schools->school_district_id($f_schoolid));
			$district_fax_number = $districts->district_administrator($schools->school_district_id($f_schoolid));
?>

		<TR>
			<TD Align='Left' Valign='Top'><?=$project->project_id;?></TD>
			<TD Align='Left' Valign='Top'><?=$project->project_name;?></TD>
			<TD Align='Left' Valign='Top'><?=$projecttypes->project_type_description($project->project_type_id);?></TD>
			<TD Align='Left' Valign='Top'><?=$schools->school_name($project->school_id);?></TD>
			<TD Align='Right' Valign='Top'><?=sprintf("%01.2f", ($project->amount_needed - $project->amount_donated()));?></TD>
			<TD Align='Right' Valign='Top'><?=sprintf("%01.2f", $donation_project->donation_amount);?></TD></TR>
		</TR>
<?
		}
	}
    if ($donation->fees_paid) {
?>
		<TR>
			<TD Align='Left' Valign='Top' Colspan=5>Payment Processing Fees</TD>
			<TD Align='Right' Valign='Top'><?=sprintf("%01.2f", $donation->fees_paid);?></TD></TR>
		</TR>
<?
        $total += $donation->fees_paid;
    }
?>
		<TR>
			<TD Align='Left' Valign='Top' Colspan=5><b>Total Donation Amount</b></TD>
			<TD Align='Right' Valign='Top'><b><?=sprintf("%01.2f", $total);?></b></TD></TR>
		</TR>
	</table>
<?
  		include $path_root."inc/box_end.htm";
  	}
?>