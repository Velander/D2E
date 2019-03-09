<?
  	if (isset($User_ID)) {
  		include $path_root."inc/box_begin.htm";
?>
	<table width="100%" cellspacing=0 cellpadding=1 border=0 align=left>
		<TR>
			<TD Align='Center' colspan=5><b>Request Details</b></TD>
		</TR><TR>
			<TD Align='Left' Valign='Bottom'><b>Request Name</b></TD>
			<TD Align='Left' Valign='Bottom'><b>Category</b></TD>
			<TD Align='Left' Valign='Bottom'><b>School</b></TD>
			<TD Align='Center' Valign='Bottom'><b>Funds<BR>Needed</b></TD>
			<TD Align='Center' Valign='Bottom'><b>Donation<BR>Amount</b></TD>
		</TR>
<?
	reset($user->cart_item_list);
	$total = 0;
	while (list($cartid, $cartitem) = each($user->cart_item_list)) {
		$project = new project();
		if ($project->load_project($cartitem->project_id)) {
			$total += $cartitem->donation_amount;
			$districtid = $schools->school_district_id($project->school_id);
			$district = new district();
			$district->load_district($districtid);
			$checks_payable_to = $districts->district_administrator($schools->school_district_id($f_schoolid));
			$district_fax_number = $districts->district_administrator($schools->school_district_id($f_schoolid));
?>

		<TR>
			<TD Align='Left' Valign='Top'><?=$project->project_name;?></TD>
			<TD Align='Left' Valign='Top'><?=$projecttypes->project_type_description($project->project_type_id);?></TD>
			<TD Align='Left' Valign='Top'><?=($schools->school_homepage($project->school_id) ? "<a target=\"school\" href=\"".$schools->school_homepage($project->school_id)."\">" : "");?><?=$schools->school_name($project->school_id);?><?=($schools->school_homepage($project->school_id) ? "</a>":"");?></TD>
			<TD Align='Right' Valign='Top'><?=sprintf("%01.2f", ($project->amount_needed - $project->amount_donated()));?></TD>
			<TD Align='Right' Valign='Top'><?=sprintf("%01.2f", $cartitem->donation_amount);?></TD>
		</TR>
<?
		}
	}
	if ($f_include_cc_fee == "Y" && $f_payment_choice == "Credit") {

?>
		<TR>
			<TD Align='Left' Valign='Top' colspan='4'>Credit Card Processing Fees</TD>
			<TD Align='Right' Valign='Top'><?=sprintf("%01.2f", $f_include_cc_fee_amount);?></TD>
		</TR>
<?
		$total += $f_include_cc_fee_amount;
	}
	if ($f_include_paypal_fee == "Y" && $f_payment_choice == "Paypal/Credit Card") {

?>
		<TR>
			<TD Align='Left' Valign='Top' colspan='4'>Payment Processing Fees</TD>
			<TD Align='Right' Valign='Top'><?=sprintf("%01.2f", $f_include_paypal_fee_amount);?></TD>
		</TR>
<?
		$total += $f_include_paypal_fee_amount;
	}
?>
		<TR>
			<TD Align='Left' Valign='Top' Colspan=4><b>Total Donation Amount</b></TD>
			<TD Align='Right' Valign='Top'><b><?=sprintf("%01.2f", $total);?></b></TD>
		</TR>
	</table>
<?
  		include $path_root."inc/box_end.htm";
  	}
?>