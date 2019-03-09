<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";
	require_once "inc/class_donation.php";
	require_once "inc/class_donations.php";
	require_once "inc/class_donation_project.php";
	require_once "inc/class_donation_change.php";
	require_once "inc/class_donation_project_change.php";
	require_once "inc/class_payment_type.php";
	require_once "inc/class_payment_types.php";
	require_once "inc/class_state.php";
	require_once "inc/class_states.php";
	require_once "inc/class_country.php";
	require_once "inc/class_countries.php";

	$user = new user();
	$user->load_user($User_ID);

	require "inc/validate_teacher.php";

if ($debug) {
	echo "loading schools<br>";
	flush();
}
	$states = new states();
	$states->load_states();

	$countries = new countries();
	$countries->load_countries();

	$payment_types = new payment_types();
	$payment_types->load_payment_types();

	$message = $_GET["message"];
	$donationid = $_GET["donationid"];
	if (!empty($donationid)) {
		$donation = new donation();
		$donation->load_donation($donationid);
	}

	if ($_SERVER['REQUEST_METHOD'] == "POST") {

if ($debug) {
	echo "Posting<br>";
	flush();
}
		// Get form fields
		$Submit = $_POST["Submit"];

		$f_donation_id = $_POST["f_donation_id"];
		$f_donation_user_id = $_POST["f_donation_user_id"];
		$f_donation_date = $_POST["f_donation_date"];
		$f_donation_amount = $_POST["f_donation_amount"];
		$f_fees_paid = $_POST["f_fees_paid"];
		$f_actual_fees = $_POST["f_actual_fees"];
		$f_refund_flag = $_POST["f_refund_flag"];
		$f_payment_type_id = $_POST["f_payment_type_id"];
		$f_payment_no = $_POST["f_payment_no"];
		$f_payment_authorized = $_POST["f_payment_authorized"];
		$f_payment_received = $_POST["f_payment_received"];
		$f_payment_received_date = $_POST["f_payment_received_date"];
		$f_contact_flag = $_POST["f_contact_flag"];
		$f_gift_first_name = $_POST["f_gift_first_name"];
		$f_gift_last_name = $_POST["f_gift_last_name"];
		$f_gift_street = $_POST["f_gift_street"];
		$f_gift_city = $_POST["f_gift_city"];
		$f_gift_state = $_POST["f_gift_state"];
		$f_gift_country = $_POST["f_gift_country"];
		$f_gift_zip = $_POST["f_gift_zip"];
		$f_matching_donation = $_POST["f_matching_donation"];
		$f_show_donation = $_POST["f_show_donation"];
		$f_direct_donation = $_POST["f_direct_donation"];
		$f_noncash_donation = $_POST["f_noncash_donation"];
		$f_donation_projects = $_POST["f_donation_projects"];

		// Validate the fields.
		$errors = "";
		$message = "";
		$f_donation_amount = str_replace(",","",str_replace("$","",$f_donation_amount));
		$f_fees_paid = str_replace(",","",str_replace("$","",$f_fees_paid));
		$f_actual_fees = str_replace(",","",str_replace("$","",$f_actual_fees));
		if ($Submit == "Apply Changes" || $Submit == "Save Changes") {
			if (empty($f_donation_date))
			{
				$errors .= (empty($errors) ? "" : "<BR>")."Donation date is required.";
				$f_donation_date = $donation->donation_date;
			}
			if (empty($f_donation_amount))
			{
				$errors .= (empty($errors) ? "" : "<BR>")."Donation amount is required.";
				$f_donation_amount = $donation->donation_amount;
			} else if (!is_numeric($f_donation_amount))
			{
				$errors .= (empty($errors) ? "" : "<BR>")."Donation amount must be numeric.";
				$f_donation_amount = $donation->donation_amount;
			}
			if (empty($f_fees_paid))
			{
				$errors .= (empty($errors) ? "" : "<BR>")."Fees paid amount is required.";
				$f_fees_paid = $donation->fees_paid;
			} else if (!is_numeric($f_fees_paid))
			{
				$errors .= (empty($errors) ? "" : "<BR>")."Fees paid amount must be numeric.";
				$f_fees_paid = $donation->fees_paid;
			}
			if (!is_numeric($f_actual_fees))
			{
				$errors .= (empty($errors) ? "" : "<BR>")."Actual fees amount must be numeric.";
				$f_actual_fees = $donation->actual_fees;
			}
			if (!empty($f_payment_received_date))
			{
				if (!strtotime($f_payment_received_date))
				{
					$errors .= (empty($errors) ? "" : "<BR>")."Invalid Date Received value.";
					$f_payment_received_date = $donation->payment_received_date;
				}
			}
			$f_donation_projects = array();
			while (list($id)= each($f_donationproject_id))
			{
				# Don't add to the list of Requests if the ID is zero and the amount is zero.
				#if ($f_donationproject_amount[$id] > 0 or $f_donationproject_original_amount[$id] > 0) {
					if ($f_donationproject_original_amount[$id] == 0) {
						# Make sure that the Request ID entered is valid.
						$newprject = new project();
						if (!$newprject->load_project($f_donationproject_project_id[$id])) {
							$errors .= (empty($errors) ? "" : "<BR>")."Invalid Request ID: ".$f_donationproject_project_id[$id];
							$f_donationproject_project_id[$id] = 0;
						}
					}
					if ($f_donationproject_project_id[$id] != 0) {
						$donation_project = new donation_project();
						$donation_project->donation_project_id = $f_donationproject_id[$id];
						$donation_project->project_id = $f_donationproject_project_id[$id];
						$donation_project->donation_amount = $f_donationproject_amount[$id];
						$donation_project->original_amount = $f_donationproject_original_amount[$id];
						$donation_project->matching_donation_id = $f_donationproject_matching_donation_id[$id];
						$donation_project->show_donation 	 = $f_donationproject_show_donation[$id];
						$donation_project->direct_donation     = $f_donationproject_direct_donation[$id];
						$donation_project->noncash_donation  = $f_donationproject_noncash_donation[$id];
						$f_donation_projects[] = $donation_project;
					}
				#}
			}
		}
		if (!empty($errors))
		{
			$message .= $errors;
		} else if ($Submit == "Save Changes") {
			// Save donation
			$donation = new donation();
			if (!empty($f_donation_id))
			{
				$donation->load_donation($f_donation_id);

				$changes_made = false;
				$donation_changes = new donation_change();
				$donation_changes->change_user_id 		= $User_ID;
				$donation_changes->donation_id 			= $donation->donation_id;
				$donation_changes->user_id 				= $donation->user_id;
				$donation_changes->donation_date 		= $donation->donation_date;
				$donation_changes->donation_amount 		= $donation->donation_amount;
				$donation_changes->refund_flag 			= $donation->refund_flag;
				$donation_changes->payment_type_id 		= $donation->payment_type_id;
				$donation_changes->payment_number		= $donation->payment_no;
				$donation_changes->payment_authorized 	= $donation->payment_authorized;
				$donation_changes->amount_received 		= $donation->amount_donated;
				$donation_changes->payment_received 	= $donation->payment_received;
				$donation_changes->payment_received_date = $donation->payment_received_date;
				$donation_changes->contact_flag 		= $donation->contact_flag;
				$donation_changes->gift_first_name 		= $donation->gift_first_name;
				$donation_changes->gift_last_name 		= $donation->gift_last_name;
				$donation_changes->gift_street 			= $donation->gift_street;
				$donation_changes->gift_city 			= $donation->gift_city;
				$donation_changes->gift_state 			= $donation->gift_state;
				$donation_changes->gift_country 		= $donation->gift_country;
				$donation_changes->gift_zip 			= $donation->gift_zip;
				$donation_changes->matching_donation	= $donation->matching_donation;
				$donation_changes->show_donation 		= $donation->show_donation;
				$donation_changes->direct_donation 		= $donation->direct_donation;
				$donation_changes->noncash_donation 	= $donation->noncash_donation;

				if (!$donation_changes->save_donation_change())
				{
					$message .= "Donation change saved failed.<BR>".$donation_changes->error_message;
				} else {
					reset($f_donation_projects);
					while (list($id, $donation_project) = each($f_donation_projects)) {
						if ($donation_project->donation_project_id != 0) {
							$currect_project = new donation_project();
							if ($currect_project->load_donation_project($donation_project->donation_project_id)) {
								// Check to see if the Donation_Project has changed.
								if ($donation_project->project_id != $currect_project->project_id ||
									$donation_project->donation_amount != $currect_project->donation_amount ||
									$donation_project->matching_donation_id != $currect_project->matching_donation_id ||
									$donation_project->show_donation != $currect_project->show_donation ||
									$donation_project->direct_donation != $currect_project->direct_donation ||
									$donation_project->noncash_donation != $currect_project->noncash_donation)
								{
									// Save a record of what donation_project was changed from.
									$donation_project_change = new donation_project_change();
									$donation_project_change->donation_project_id	= $donation_project->donation_project_id;
									$donation_project_change->donation_id			= $donation_project->donation_id;
									$donation_project_change->user_id				= $User_ID;
									$donation_project_change->change_date			= $donation_project->change_date;
									$donation_project_change->project_id			= $donation_project->project_id;
									$donation_project_change->donation_amount		= $donation_project->donation_amount;
									$donation_project_change->matching_donation_id	= $donation_project->matching_donation_id;
									$donation_project_change->show_donation			= $donation_project->show_donation;
									$donation_project_change->direct_donation		= $donation_project->direct_donation;
									$donation_project_change->noncash_donation		= $donation_project->noncash_donation;
									$donation_project_change->save_donation_project_change();
								}
							}
						}
					}
				}
			}
			if (empty($message)) {
				if ($f_new_donation_user_id) {
					$newdonor = new user();
					if ($newdonor->load_user($f_new_donation_user_id)) {
						$donation->user_id			= $f_new_donation_user_id;
					}
				}
				reset($f_donation_projects);
				foreach($f_donation_projects as $id => $donation_project) {
					if ($donation_project->donation_amount > 0)
					{
						if ($donation_project->donation_project_id == 0) {
							# Need to add Request
							$donation->add_project($donation_project->project_id, $donation_project->donation_amount, $donation_project->matching_donation_id);
						} else {
							$donation_project->save_donation_project();
						}
					} else {
						if (!$donation_project->delete_donation_project($donation_project->donation_project_id))
							echo " Error $donation->error_message<BR>";
					}
				}

				$donation->donation_date 			= date("Y-m-d", strtotime($f_donation_date));
				$donation->donation_amount			= $f_donation_amount;
				$donation->fees_paid				= $f_fees_paid;
				$donation->actual_fees				= $f_actual_fees;
				$donation->refund_flag				= ($f_refund_flag == "Y" ? "Y" : "N");
				$donation->payment_type_id			= $f_payment_type_id;
				$donation->payment_no				= $f_payment_no;
				$donation->payment_authorized		= ($f_payment_authorized == "Y" ? "Y" : "N");
				$donation->payment_received			= ($f_payment_received == "Y" ? "Y" : "N");
				$donation->payment_received_date	= date("Y-m-d", strtotime($f_payment_received_date));
				$donation->contact_flag				= $f_contact_flag;
				$donation->gift_first_name			= $f_gift_first_name;
				$donation->gift_last_name			= $f_gift_last_name;
				$donation->gift_street				= $f_gift_street;
				$donation->gift_city				= $f_gift_city;
				$donation->gift_state				= $f_gift_state;
				$donation->gift_country				= $f_gift_country;
				$donation->gift_zip					= $f_gift_zip;
				$donation->matching_donation		= $f_matching_donation;
				$donation->show_donation			= $f_show_donation;
				$donation->direct_donation			= $f_direct_donation;
				$donation->noncash_donation			= $f_noncash_donation;

				if ($donation->save_donation()) {
					$message .= "Donation changes saved.";
					$f_donation_id = $donation->donation_id;
					$f_donation_user_id = $donation->user_id;
					$f_donation_date = $donation->donation_date;
					$f_donation_amount = $donation->donation_amount;
					$f_fees_paid = $donation->fees_paid;
					$f_actual_fees = $donation->actual_fees;
					$f_refund_flag = $donation->refund_flag;
					$f_payment_type_id = $donation->payment_type_id;
					$f_payment_no = $donation->payment_no;
					$f_payment_authorized = $donation->payment_authorized;
					$f_payment_received = $donation->payment_received;
					$f_payment_received_date = $donation->payment_received_date;
					$f_contact_flag = $donation->contact_flag;
					$f_gift_first_name = $donation->gift_first_name;
					$f_gift_last_name = $donation->gift_last_name;
					$f_gift_street = $donation->gift_street;
					$f_gift_city = $donation->gift_city;
					$f_gift_state = $donation->gift_state;
					$f_gift_country = $donation->gift_country;
					$f_gift_zip = $donation->gift_zip;
					$f_matching_donation = $donation->matching_donation;
					$f_show_donation = $donation->show_donation;
					$f_direct_donation = $donation->direct_donation;
					$f_noncash_donation = $donation->noncash_donation;
					$f_donation_projects = $donation->donation_project_list;
				} else
					$message .= "Donation save failed: $donation->error_message<BR>";
			}
		} else if ($Submit == "Delete Donation") {
			if ($donation->delete_donation())
			{
				$message .= "Donation $donation->donation_id deleted.";
				echo "<script type=\"text/javascript\">\nlocation.href='donation_history.php?message=".htmlentities(urlencode($message))."'\n</script>\n";
			}
		}
	} else {
		$f_donation_id = $donation->donation_id;
		$f_donation_user_id = $donation->user_id;
		$f_donation_date = $donation->donation_date;
		$f_donation_amount = $donation->donation_amount;
		$f_fees_paid = $donation->fees_paid;
		$f_actual_fees = $donation->actual_fees;
		$f_refund_flag = $donation->refund_flag;
		$f_payment_type_id = $donation->payment_type_id;
		$f_payment_no = $donation->payment_no;
		$f_payment_authorized = $donation->payment_authorized;
		$f_payment_received = $donation->payment_received;
		$f_payment_received_date = $donation->payment_received_date;
		$f_contact_flag = $donation->contact_flag;
		$f_gift_first_name = $donation->gift_first_name;
		$f_gift_last_name = $donation->gift_last_name;
		$f_gift_street = $donation->gift_street;
		$f_gift_city = $donation->gift_city;
		$f_gift_state = $donation->gift_state;
		$f_gift_country = $donation->gift_country;
		$f_gift_zip = $donation->gift_zip;
		$f_matching_donation = $donation->matching_donation;
		$f_show_donation = $donation->show_donation;
		$f_direct_donation = $donation->direct_donation;
		$f_noncash_donation = $donation->noncash_donation;
		$f_donation_projects = $donation->donation_project_list;
	}
?>
<html>
<head>
<?
	include "inc/cssstyle.php";
	$pagename = "$config_donation_edit_page_name";
	$help_msg_name = "config_donation_edit_help";
	$help_msg = "$config_donation_edit_help";
	$help_width = "$config_donation_edit_help_width";
	$help_height = "$config_donation_edit_help_height";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <td width="655" align="left" valign="top">
<?
	if (!empty($config_donation_edit_paragraph1)) {
		include "inc/box_begin.htm";
		echo "$config_donation_edit_paragraph1";
		include "inc/box_end.htm";
	}
	$editable = ($user->type_id >= 40 || (($User_ID == $project->submitted_user_id || empty($project->submitted_user_id) || ($user->type_id == 25 && $user_affiliations->is_affiliated($project->school_id))) && ($f_status_id == 0 || $f_status_id == 2)));
	if (!empty($message))
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center><br>";
		include "inc/box_begin.htm";
?>
					<TABLE ALIGN="left" BORDER=0 CELLSPACING=10 CELLPADDING=0 WIDTH="100%">
					<Form Name='Proposal' Method='POST'>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>Donation ID</TD>
						<TD>
							<input type='hidden' name='f_donation_id' value='<?=$f_donation_id;?>'>
							<?=$f_donation_id;?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>&nbsp;&nbsp;Donated By</TD>
						<TD>
							<input type='hidden' name='f_donation_user_id' value='<?=$f_donation_user_id;?>'>
<?
	$donor = new user();
	$donor->load_user($f_donation_user_id);
	echo ("$donor->first_name $donor->last_name".($donor->company ? "<BR>$donor->company" : "")."<BR>");
	echo ("$donor->street<BR>");
	echo ("$donor->city $donor->state $donor->zip<BR>");
?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>&nbsp;</font>&nbsp;New Donor ID</TD>
						<TD><input type='text' name='f_new_donation_user_id' size='6'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;Donation Date</TD>
						<TD>
							<input type='text' size='12' name='f_donation_date' value='<?=date("m/d/Y", strtotime($f_donation_date)); ?>'>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;Donation Amount</TD>
						<TD>
							<INPUT TYPE="text" NAME="f_donation_amount" VALUE="<?=sprintf("%01.2f", $f_donation_amount);?>" SIZE=8 MAXLENGTH=8>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;Fees Paid</TD>
						<TD>
							<INPUT TYPE="text" NAME="f_fees_paid" VALUE="<?=sprintf("%01.2f", $f_fees_paid);?>" SIZE=8 MAXLENGTH=8>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>&nbsp&nbsp;Actual Fees</TD>
						<TD>
							<INPUT TYPE="text" NAME="f_actual_fees" VALUE="<?=sprintf("%01.2f", $f_actual_fees);?>" SIZE=8 MAXLENGTH=8>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;Matching Donation</TD>
						<TD>
							<input type='checkbox' name='f_matching_donation' value='Y'<?=($f_matching_donation == "Y" ? " Checked" : ""); ?>>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;Show Donation</TD>
						<TD>
							<input type='checkbox' name='f_show_donation' value='Y'<?=($f_show_donation == "Y" ? " Checked" : ""); ?>>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;Direct Donation</TD>
						<TD>
							<input type='checkbox' name='f_direct_donation' value='Y'<?=($f_direct_donation == "Y" ? " Checked" : ""); ?>>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;Non-Cash Donation</TD>
						<TD>
							<input type='checkbox' name='f_noncash_donation' value='Y'<?=($f_noncash_donation == "Y" ? " Checked" : ""); ?>>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;Refund Flag</TD>
						<TD>
							<input type='checkbox' name='f_refund_flag' value='Y'<?=($f_refund_flag == "Y" ? " Checked" : ""); ?>>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>Payment Type</TD>
						<TD>
							<select name="f_payment_type_id">
							<OPTION VALUE=""></OPTION>
<?
reset($payment_types->payment_type_list);
while (list($payment_code, $payment_type) = each($payment_types->payment_type_list)) {
	echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"$payment_code\"".($payment_code == $f_payment_type_id ? " SELECTED" : "").">$payment_type->payment_type_description</OPTION>\n");
}
echo "\t\t\t\t\t\t\t</select>".($donation->payment_auth_code ? "&nbsp;&nbsp;Auth: ".$donation->payment_auth_code."  ID: ".$donation->payment_auth_id : "");
?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>Payment Number</TD>
						<TD>
							<input type='text' name='f_payment_no' value='<?=$f_payment_no;?>'>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>&nbsp;&nbsp;Payment Authorized</TD>
						<TD>
							<input type='checkbox' name='f_payment_authorized' value='Y'<?=($f_payment_authorized == "Y" ? " Checked" : ""); ?>>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>&nbsp;&nbsp;Donation Received</TD>
						<TD>
							<input type='checkbox' name='f_payment_received' value='Y'<?=($f_payment_received == "Y" ? " Checked" : ""); ?>>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;Date Receivied</TD>
						<TD>
							<INPUT TYPE="text" NAME="f_payment_received_date" VALUE="<?=date("m/d/Y", strtotime($f_payment_received_date));?>" SIZE=20 MAXLENGTH=20>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;Contact Flag</TD>
						<TD>
							<select name="f_contact_flag">
<?
	echo ("\t\t\t\t\t\t\t<OPTION VALUE='A' ".($f_contact_flag == "A" ? " SELECTED" : "").">Anonymous</OPTION>\n");
	echo ("\t\t\t\t\t\t\t<OPTION VALUE='G' ".($f_contact_flag == "G" ? " SELECTED" : "").">Gift</OPTION>\n");
	echo ("\t\t\t\t\t\t\t<OPTION VALUE='D' ".($f_contact_flag == "D" ? " SELECTED" : "").">Donor</OPTION>\n");
?>
							</select>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>&nbsp;&nbsp;Gift First Name</TD>
						<TD>
							<INPUT TYPE="text" NAME="f_gift_first_name" VALUE="<?=$f_gift_first_name;?>" SIZE=20 MAXLENGTH=20>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>&nbsp;&nbsp;Gift Last Name</TD>
						<TD>
							<INPUT TYPE="text" NAME="f_gift_last_name" VALUE="<?=$f_gift_last_name;?>" SIZE=20 MAXLENGTH=20>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>&nbsp;&nbsp;Gift Street</TD>
						<TD>
							<INPUT TYPE="text" NAME="f_gift_street" VALUE="<?=$f_gift_street;?>" SIZE=20 MAXLENGTH=20>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>&nbsp;&nbsp;Gift City</TD>
						<TD>
							<INPUT TYPE="text" NAME="f_gift_city" VALUE="<?=$f_gift_city;?>" SIZE=20 MAXLENGTH=20>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>&nbsp;&nbsp;Gift State</TD>
						<TD>
											<Select name='f_gift_state'>
<?
	reset($states->state_list);
	$prev_grp = "";
	while (list($statecode, $state) = each($states->state_list)) {
		if ($state->state_group != $prev_grp)
		{
			if (!empty($prev_grp))
			echo "</optgroup>";
			$prev_grp = $state->state_group;
			echo ("\t\t\t\t\t\t\t\t<optgroup label=\"$state->state_group\">\n");
		}
		echo ("\t\t\t\t\t\t\t\t<OPTION VALUE=\"$statecode\"".($statecode == $f_gift_state ? " SELECTED" : "").">$state->state_name</OPTION>\n");
	}
	if (!empty($prev_grp))
		echo "</optgroup>";
?>
											</select>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>&nbsp;&nbsp;Gift Country</TD>
						<TD>
							<Select name='f_gift_country'>
<?
if (empty($f_country))
$f_country = $config_default_country;
reset($country->country_list);
$prev_grp = "";
while (list($country_code, $country) = each($countries->country_list)) {
echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"$country_code\"".($country_code == $f_gift_country ? " SELECTED" : "").">$country->country_name</OPTION>\n");
}
?>
							</select>
						</TD>
					</TR>
<?
		$balance = $f_donation_amount;
		if(isset($f_donation_projects)) reset($f_donation_projects);
//		if (count($f_donation_projects) > 0) {
			echo "\t\t\t<TR ALIGN=\"left\" VALIGN=\"middle\">";
			echo "\t\t\t\t<TD Align='center' Colspan=2>\n";
			include "inc/box_begin.htm";
			echo "\t<table border='0' width='100%'>\n";
			echo "\t\t<tr><td colspan='100%' align='center'><b><font size='+1'>Requests</font></b></td></tr>\n";
			echo "\t\t<tr><td valign='bottom'><b><br>ID</d></td>";
			echo "<td valign='bottom'><b><br>Request</d></td>";
			echo "<td align='center' valign='bottom'><b>Original<BR>Amount</b></td>";
			echo "<td align='center' valign='bottom'><b>Donation<BR>Amount</b></td>";
			echo "<td align='center' valign='bottom'><b>Match<BR>ID</b></td></tr>\n";
			if(isset($f_donation_projects))
			{
				while (list($donationprojectid, $donation_project)= each($f_donation_projects)) {
					$project = new project();
					$project->load_project($donation_project->project_id);
					echo "<input type='hidden' name='f_donationproject_id[]' value='$donation_project->donation_project_id'>";
					echo "\t\t<tr>\n\t\t\t<td>".$donation_project->project_id."</td>\n";
					echo "<input type='hidden' name='f_donationproject_project_id[]' value='$donation_project->project_id'>";
					echo "\t\t\t<td>".$project->project_name."</td>\n";
					echo "\t\t\t<td align='center'>".sprintf("%01.2f", $donation_project->original_amount)."<input type='hidden' name='f_donationproject_original_amount[]' value='$donation_project->original_amount'></td>\n";
					echo "\t\t\t<td align='center'><INPUT TYPE=\"text\" NAME=\"f_donationproject_amount[]\" VALUE='".sprintf("%01.2f", $donation_project->donation_amount)."' SIZE=8 MAXLENGTH=8></td>\n";
					echo "\t\t\t<td align='center'>".($donation_project->matching_donation_id!="0" ? "<a href='donation_edit.php?donationid=$donation_project->matching_donation_id' target='_new'>":"").$donation_project->matching_donation_id."<input type='hidden' name='f_donationproject_matching_donation_id[]' value='$donation_project->matching_donation_id'>".($donation_project->matching_donation_id!="0" ? "</a>":"")."</td>\n";
					echo "\t\t</tr>\n";
					$balance -= $donation_project->donation_amount;
				}
			}
			if (sprintf("%01.2f", $balance) != "0.00") {
				echo "\t\t<tr>\n\t\t\t<td><input type='text' size='5' name='f_donationproject_project_id[]' value=''></td>\n";
				echo "<input type='hidden' name='f_donationproject_id[]' value='0'>";
				echo "\t\t\t<td>Add a project</td>\n";
				echo "\t\t\t<td align='center'><input type='hidden' name='f_donationproject_original_amount[]' value='0'>0.00</td>\n";
				echo "\t\t\t<td align='center'><INPUT TYPE='text' NAME='f_donationproject_amount[]' VALUE='' SIZE=8 MAXLENGTH=8></td>\n";
				echo "\t\t\t<td align='center'><input type='text' size='4' name='f_donationproject_matching_donation_id[]' value=''></td>\n";
				echo "\t\t</tr>\n";


				echo "\t\t<tr>\n\t\t\t<td></td>\n";
				echo "\t\t\t<td><b>Balance</b></td>\n";
				echo "\t\t\t<td align='center'></td>\n";
				echo "\t\t\t<td align='center'><b>".sprintf("%01.2f", $balance)."</b></td>\n";
				echo "\t\t\t<td align='center'></td>\n";
				echo "\t\t</tr>\n";
			}
			echo "\t</table>\n";
			include "inc/box_end.htm";
			echo "\t\t\t\t</TD>\n\t\t\t</TR>\n";
//		}
		echo "<TR ALIGN=\"left\" VALIGN=\"middle\">\n";
		echo "<TD Align='Center' colspan='2'>";
		echo "<Input Type='Submit' Name='Submit' class='nicebtns' Value='Apply Changes'>";
		if (sprintf("%01.2f", $balance) == "0.00")
			echo "&nbsp;&nbsp;<Input Type='Submit' Name='Submit' class='nicebtns' Value='Save Changes'>";
		if ($f_donation_id)
			echo "&nbsp;&nbsp;<Input Type='Submit' Name='Submit' class='nicebtns' Value='Delete Donation'>";
		echo "</TD>\n</TR>\n";
?>
					</Form>
					</TABLE>
<?		include "inc/box_end.htm"; ?>
                  </td>
<? require "inc/body_end.inc"; ?>
</html>
