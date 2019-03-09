


											<TR ALIGN='Left' VALIGN=Top>
												<TD Align='Left' Colspan='3'><B>If the project is never fully funded, my donation should be:</B></TD>
											</TR>
											<TR ALIGN='Left' VALIGN=Top>
												<TD Width='5'>&nbsp;</TD>
												<TD Align='Left' Colspan='2'><input type='radio' name='f_donation_option' value='Choose'<?=($d_donation_option != "Refund" ? " Checked" : "");?>>
												&nbsp;Applied to another project chosen by <?=$districts->district_administrator($schools->school_district_id($f_schoolid));?>.</TD>
											</TR>
											<TR ALIGN='Left' VALIGN=Top>
												<TD Width='5'>&nbsp;</TD>
												<TD Align='Left' Colspan='2'><input type='radio' name='f_donation_option' value='Refund'<?=($d_donation_option == "Refund" ? " Checked" : "");?>>
												&nbsp;Refunded to me.</TD>
											</TR>
											<TR ALIGN='Left' VALIGN=Top>
												<TD Align='Left' Colspan='3'><B>Payment Method</B></TD>
											</TR>
<?
	if ($district->accept_cc == "Y")	{
		if (empty($f_payment_choice)) {
			$f_payment_choice = "Credit";
		}
?>
											<TR ALIGN='Left' VALIGN=Top>
												<TD Width='5'>&nbsp;</TD>
												<TD Align='Left' Colspan='2'><input type='radio' name='f_payment_choice' value='Credit'<?=($f_payment_choice == "Credit" ? " Checked" : "");?>>
												&nbsp;<b>Credit Card</b>
												</TD>
											</TR>
											<TR ALIGN='Left' VALIGN=Top>
												<TD Width='5'>&nbsp;</TD>
												<TD Align='Left' Colspan='2'>
													<TABLE>
														<TR ALIGN='Left' VALIGN=Top>
															<TD Width='25'>&nbsp;</TD>
															<TD Align='Right'>Credit Card Type:</TD>
															<TD Align='Left'>
																<select name="f_credit_card_type">
<?
			$prev_grp = "";
			if (empty($f_credit_card_type))
				$f_credit_card_type = $config_default_credit_card_type;
			while (list($payment_code, $payment_type) = each($payment_types->payment_type_list)) {
				if ($payment_type->credit_card_flag == "Y")
					echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"$payment_code\"".($payment_code == $f_credit_card_type ? " SELECTED" : "").">$payment_type->payment_type_description</OPTION>\n");
			}
?>
																</select>
															</TD>
														</TR>
														<TR ALIGN='Left' VALIGN=Top>
															<TD Width='25'>&nbsp;</TD>
															<TD Align='Right'>Card Number:</TD>
															<TD Align='Left'><input type='text' name='f_credit_card_no' size='20'></TD>
														</TR>
														<TR ALIGN='Left' VALIGN=Top>
															<TD Width='25'>&nbsp;</TD>
															<TD Align='Right'>Expiration Date:</TD>
															<TD Align='Left'><select name='f_expdt_month'>
<?		$mon = 1;
		while ($mon <= 12) {
?>
														<option value="<?=$mon;?>"<?=(($f_expdt_month == $mon)?" SELECTED":"");?>><?=sprintf("%02d",$mon);?></option>
<?			$mon += 1;
		}
?>
													</select>
													<select name="f_expdt_year">
<?		$yr = date("Y");
		while ($yr <= date("Y")+6) {
?>
														<option value="<?=$yr;?>"<?=(($f_expdt_year == $yr)?" SELECTED":"");?>><?=$yr;?></option>
<?			$yr += 1;
		}
?>
													</select>
															</TD>
														</TR>
														<TR ALIGN='Left' VALIGN=Top>
															<TD Width='25'>&nbsp;</TD>
															<TD Align='Right'>Card Verification #:</TD>
															<TD Align='Left'><input type='text' name='f_cvv2' size='4'>&nbsp;
																<font size='-2'><a href="javascript:display_msg('<?=$http_location;?>/purchase_cv.htm','395','430')">What's this?</a></font>
															</TD>
														</TR>
													</TABLE>
												</TD>
											</TR>
<?	}	elseif (empty($f_payment_choice)) {
			$f_payment_choice = "Check";
	}
?>
											<TR ALIGN='Left' VALIGN=Top>
												<TD Width='5'>&nbsp;</TD>
												<TD Align='Left' Colspan='2'><input type='radio' name='f_payment_choice' value='Check'<?=($f_payment_choice == "Check" ? " Checked" : "");?>>&nbsp;<b>Check</b></TD>
											</TR>
											<TR ALIGN='Left' VALIGN=Top>
												<TD Width='5'>&nbsp;</TD>
												<TD Align='Left' Colspan='2'>
													<TABLE>
														<TR ALIGN='Left' VALIGN=Top>
															<TD Width='25'>&nbsp;</TD>
															<TD Align='Left'>Make payable to <b><?=$districts->district_administrator($schools->school_district_id($f_schoolid));?></b>.</TD>
														</TR>
													</TABLE>
												</TD>
											</TR>
<?	if (!empty($district->payment_faxno)) {	?>
											<TR ALIGN='Left' VALIGN=Top>
												<TD Width='5'>&nbsp;</TD>
												<TD Align='Left' Colspan='2'><input type='radio' name='f_payment_choice' value='Fax'<?=($f_payment_choice == "Fax" ? " Checked" : "");?>>&nbsp;<b>Fax</b></TD>
											</TR>
											<TR ALIGN='Left' VALIGN=Top>
												<TD Width='5'>&nbsp;</TD>
												<TD Align='Left' Colspan='2'>
													<TABLE>
														<TR ALIGN='Left' VALIGN=Top>
															<TD Width='25'>&nbsp;</TD>
															<TD Align='Left'>Credit Card information will be faxed to <b><?=$districts->district_administrator($schools->school_district_id($f_schoolid));?></b>.</TD>
														</TR>
													</TABLE>
												</TD>
											</TR>
<?	}	?>
											<TR ALIGN='Left' VALIGN=Top>
												<TD Align='Left' Colspan='3'><B>Verify Contact Information</B></TD>
											</TR>
<?
	if (empty($f_contact_choice))
		$f_contact_choice = "D";
	if (empty($gift_state))
		$gift_state = $config_default_state;
?>
											<TR ALIGN='Left' VALIGN=Top>
												<TD Width='5'>&nbsp;</TD>
												<TD Align='Left' Colspan='2'><input type='radio' name='f_contact_choice' value='D'<?=($f_contact_choice == "D" ? " Checked" : "");?>>
												&nbsp;Please send all correspondence directly to me.</TD>
											</TR>
											<TR ALIGN='Left' VALIGN=Top>
												<TD Width='5'>&nbsp;</TD>
												<TD Align='Left' Colspan='2'><input type='radio' name='f_contact_choice' value='G'<?=($f_contact_choice == "G" ? " Checked" : "");?>>
												&nbsp;This donation is a gift in someone else's name.</TD>
											</TR>
											<TR ALIGN='Left' VALIGN=Top>
												<TD Width='5'>&nbsp;</TD>
												<TD Align='Left' Colspan='2'>
													<Table>
														<TR ALIGN='Left' VALIGN=Top>
															<TD Width='25'>&nbsp;</TD>
															<TD ALIGN='Right'>First Name</TD>
															<TD ALIGN='Left'><Input Type='Text' Size='30' Maxlength='40' name='gift_first_name' Value='<?=$gift_first_name;?>'></TD>
														</TR>
														<TR ALIGN='Left' VALIGN=Top>
															<TD Width='25'>&nbsp;</TD>
															<TD ALIGN='Right'>Last Name</TD>
															<TD ALIGN='Left'><Input Type='Text' Size='30' Maxlength='40' name='gift_last_name' Value='<?=$gift_last_name;?>'></TD>
														</TR>
														<TR ALIGN='Left' VALIGN=Top>
															<TD Width='25'>&nbsp;</TD>
															<TD ALIGN='Right'>Street</TD>
															<TD ALIGN='Left'><Input Type='Text' Size='40' Maxlength='50' name='gift_street' Value='<?=$gift_street;?>'></TD>
														</TR>
														<TR ALIGN='Left' VALIGN=Top>
															<TD Width='25'>&nbsp;</TD>
															<TD ALIGN='Right'>City</TD>
															<TD ALIGN='Left'><Input Type='Text' Size='30' Maxlength='30' name='gift_city' Value='<?=$gift_city;?>'></TD>
														</TR>
														<TR ALIGN='Left' VALIGN=Top>
															<TD Width='25'>&nbsp;</TD>
															<TD ALIGN='Right'>State</TD>
															<TD ALIGN='Left'>
																<Select name='gift_state'>
<?
			if (empty($f_state))
				$f_state = $config_default_state;
			reset($states->state_list);
			$prev_grp = "";
			while (list($statecode, $state) = each($states->state_list)) {
				if ($state->state_group != $prev_grp) {
					if (!empty($prev_grp))
						echo "</optgroup>";
					$prev_grp = $state->state_group;
					echo ("\t\t\t\t\t\t\t\t\t\t<optgroup label=\"$state->state_group\">\n");
				}
				echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"$statecode\"".($statecode == $gift_state ? " SELECTED" : "").">$state->state_name</OPTION>\n");
			}
			if (!empty($prev_grp))
				echo "</optgroup>";
?>
																</select>
															</TD>
														</TR>
														<TR ALIGN='Left' VALIGN=Top>
															<TD Width='25'>&nbsp;</TD>
															<TD ALIGN='Right'>Zip Code</TD>
															<TD ALIGN='Left'><Input Type='Text' Size='10' Maxlength='10' name='gift_zip' Value='<?=$gift_zip;?>'></TD>
														</TR>
														<TR ALIGN="left" VALIGN="middle">
															<TD Width='25'>&nbsp;</TD>
															<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>">Country</TD>
															<TD Align='Left'>
																<Select name='gift_country'>
<?
			if (empty($f_country))
				$f_country = $config_default_country;
			reset($country->country_list);
			$prev_grp = "";
			while (list($country_code, $country) = each($countries->country_list)) {
				echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"$country_code\"".($country_code == $gift_country ? " SELECTED" : "").">$country->country_name</OPTION>\n");
			}
?>
																</select>
															</TD>
														</TR>
													</table>
												</TD>
											</TR>

											<TR ALIGN='Left' VALIGN=Top>
												<TD Width='5'>&nbsp;</TD>
												<TD Align='Left' Colspan='2'><input type='radio' name='f_contact_choice' value='A'<?=($f_contact_choice == "A" ? " Checked" : "");?>>
												&nbsp;I prefer to remain anonymous.  Do not forward my name along with the funds.  I do not wish to receive thank yous.  Any questions about my donation,
												however, can be addressed per my contact information.
												</TD>
											</TR>
											<TR ALIGN="left" VALIGN="middle">
												<input type='hidden' name='f_project_id' value='<?=$f_project_id;?>'>
												<input type='hidden' name='search_arg' value='<?=$search_arg;?>'>
												<TD Align='Center' Colspan='3'>
		<Input Type="Submit" Name="Submit" class="nicebtns" Value="Next Step">
		<Input Type="Submit" Name="Submit" class="nicebtns" Value="Previous Step">
		<Input Type="Submit" Name="Submit" class="nicebtns" Value="Cancel">
<?
		}
?>
												</TD>
												</Form>
											</TR>
