<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";
	require_once "inc/class_users.php";
	require_once "inc/class_school.php";
	require_once "inc/class_schools.php";
	require_once "inc/class_referral_email.php";

if ($debug) {
	echo "loading user<br>";
	flush();
}
	$user = new user();
	$user->load_user($User_ID);

if ($debug) {
	echo "loading schools<br>";
	flush();
}
	$schools = new schools();
	$schools->load_schools();

	$message_received = $message;
	$message = "";

	if ($_SERVER['REQUEST_METHOD'] == "POST" && $Submit != "Cancel") {
		if ($Submit == "Save") {
			// Validate the fields.
			$errors = "";
			$i = 1;
			while ($i <= count($referral_email)) {
				if (!empty($referral_email[$i])) {

				if (empty($f_first_name[$i]))
					$errors .= (empty($errors) ? "" : "<BR>")."First Name is needed for referral #$i.";
				if (empty($f_last_name[$i]))
					$errors .= (empty($errors) ? "" : "<BR>")."Last Name is needed for referral #$i.";

		} elseif ($Submit == "Delete") {
			$user_rcd = new user();
			$user_rcd->load_user($User_ID);
			if ($user_rcd->password != $f_password) {
				$errors .= (empty($errors) ? "" : "<BR>")."Password is incorrect.";
				$Submit = "";
			}
		}
		if (empty($errors)) {
			// Save user
			if ($Submit == "Delete") {
				if ($delete_confirm == "YES") {
					$Submit = "";
					$user_affiliations = new affiliations;
					$user_affiliations->load_affiliations($User_ID);
					$user_affiliations->delete_affiliations();
					if ($user_rcd->delete_user()) {
						$message .= "User $user_rcd->login deleted.";
						unset($User_ID);
					} else
						$message .= "<p>Errors occured deleting this user!<br>$user_rcd->error_message</p>";
				}
			} elseif ($Submit == "Save") {
				$user_rcd->login = $f_login;
				if ($f_type_id != 10) {
					if (!isset($User_ID)) {
						$user_rcd->password = $better_token = md5(uniqid(rand()));
						$user_rcd->password = substr($user_rcd->password, strlen($user_rcd->password)-10, 10);
						$user_rcd->verified = "N";
					} else {
						$user_rcd->verified = "Y";
						if (!empty($f_password_new))
							$user_rcd->password = $f_password_new;
					}
				} else {
					$user_rcd->verified = "Y";
					if (!empty($f_password_new))
						$user_rcd->password = $f_password_new;
				}
				$user_rcd->first_name = $f_first_name;
				$user_rcd->last_name = $f_last_name;
				$user_rcd->street = $f_street;
				$user_rcd->city = $f_city;
				$user_rcd->state = $f_state;
				$user_rcd->zip = $f_zip;
				$user_rcd->email = $f_email;
				$user_rcd->phone = $f_phone;
				$user_rcd->fax = $f_fax;
				$user_rcd->type_id = $f_type_id;
				$user_rcd->newsletter = $f_newsletter;
				if ($f_type_id == 10) {
					$user_rcd->referral_firstname = $f_referral_firstname;
					$user_rcd->referral_lastname = $f_referral_lastname;
					$user_rcd->referral_schoolid = $f_referral_schoolid;
				}
				if ($user_rcd->save_user()) {
					if ($f_type_id != 10) {
						# Now save affiliations.
						$user_affiliations = new affiliations;
						$user_affiliations->load_affiliations($user_id);
						$i = 1;
						while ($i <= count($aff_school)) {
							if ($aff_id[$i] != 0) {
								if ($aff_school[$i] == 0) {
									if (!$user_affiliations->delete_affiliation($aff_id[$i]))
										$message .= "Error deleting affiliation $aff_id[$i]: ".$user_affiliations->error_message."<BR>";
								} elseif ($aff_oldschool[$i] != $aff_school[$i]) {
									if (!$user_affiliations->change_affiliation($aff_id[$i], $user_rcd->user_id, $aff_school[$i], $aff_admin[$i]))
										$message .= "Error changing affiliation $aff_id[$i]: ".$user_affiliations->error_message."<BR>";
								}
							} else {
								if ($aff_school[$i] != 0) {
									if (!$user_affiliations->new_affiliation($user_rcd->user_id, $aff_school[$i], empty($aff_admin[$i]) ? "N" : "Y"))
										$message .= "Error adding affiliation to school $aff_school[$i]: ".$user_affiliations->error_message."<BR>";
								}
							}
							$i += 1;
						}
					}
					if (!isset($User_ID)) {
						if ($f_type_id != 10) {
							$message .= "Your registration has been saved.<BR>An email has been sent to you with your initial password.<BR>You can select your password the first time you login.";
							$body = 	preg_replace("%LOGIN","$user_rcd->login",
							preg_replace("%FIRST_NAME","$user_rcd->first_name",
							preg_replace("%LAST_NAME","$user_rcd->last_name",
							preg_replace("%PASSWORD","$user_rcd->password",
							$config_registration_message))));
							$headers = "From: Donate2Educate <support@donate2educate.org>\r\n";
							if (!mail($f_email, $config_registration_subject, $body, $headers))
								$message .= "Your registration email failed to send.";
						} else {
							$message .= "Your registration has been saved.";
							setcookie ("User_ID", $user_rcd->user_id, time()+3600);  /* expire in 1 hour */
						}
					} else
						$message .= "User $user_rcd->login has been saved.";
 					if (empty($target))
						echo "<script type=\"text/javascript\">\nlocation.href=\"donation_search.php?sortorder=type&message=".urlencode($message)."\"\n</script>";
					else
						echo "<script type=\"text/javascript\">\nlocation.href=\"".urldecode($target)."\"\n</script>";
				} else
					$message .= "<p>Errors occured saving this user!<br>$user_rcd->error_message</p>";
			}
		} else {
			$message .= "<p>Please correct the following items and then retry:<br>$errors</p>";
		}
	} else {
		if (!empty($User_ID)) {
			$user_rcd = new user();
			if ($user_rcd->load_user($User_ID)) {
				$f_first_name		= $user_rcd->first_name;
				$f_last_name		= $user_rcd->last_name;
				$f_street			= $user_rcd->street;
				$f_city				= $user_rcd->city;
				$f_state			= $user_rcd->state;
				$f_zip				= $user_rcd->zip;
				$f_country			= $user_rcd->country;
				$f_login			= $user_rcd->login;
				$f_password			= $user_rcd->password;
				$f_email			= $user_rcd->email;
				$f_phone			= $user_rcd->phone;
				$f_fax				= $user_rcd->fax;
				$f_type_id			= $user_rcd->type_id;
				$f_newsletter		= $user_rcd->newsletter;
				$f_ip_address		= $user_rcd->ip_address;
				$f_opt_date			= $user_rcd->opt_date;
				$f_referral_firstname= $user_rcd->referral_firstname;
				$f_referral_lastname= $user_rcd->referral_lastname;
				$f_referral_schoolid= $user_rcd->referral_schoolid;
				$user_affiliations	= new affiliations;
				$user_affiliations->load_affiliations($User_ID);
			} else {
				unset($User_ID);
				$user_affiliations = new affiliations;
			}
		}
	}
?>
<html>
<head>
<?
	include "inc/cssstyle.php";
	$pagename = "$config_registration_page_name";
	$help_msg_name = "config_registration_help";
	$help_msg = "$config_registration_help";
	$help_width = "$config_registration_help_width";
	$help_height = "$config_registration_help_height";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <td width="495" align="left" valign="top">
<?
	if (!empty($config_registration_paragraph1)) {
		include "inc/box_begin.htm";
		echo "$config_registration_paragraph1";
		include "inc/box_end.htm";
	}
	if (!empty($message_received)) {
		include "inc/box_begin.htm";
		echo "<center><b><font color='$color_error_message'>".stripslashes($message_received)."</font></b></center>";
		include "inc/box_end.htm";
	}
	if (!empty($message)) {
		include "inc/box_begin.htm";
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center>";
		include "inc/box_end.htm";
	}
	if ($Submit == "Delete") {
		include "inc/dark_box_begin.htm";
		echo "<font size='+1' color=\"$color_table_hdg_font\">Delete Login</font>";
		include "inc/box_middle.htm";
?>
			<TABLE ALIGN="left" COLS=2 BORDER=0 CELLSPACING=10 CELLPADDING=0 WIDTH="100%">
				<Form Name='frmDelete' Method='POST'>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='100' Align='Right'><b>Login ID</b></TD>
						<TD><?=$f_login;?></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='100' Align='Right'><b>First Name</b></TD>
						<TD><?=$f_first_name;?></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='100' Align='Right'><b>Last Name</b></TD>
						<TD><?=$f_last_name;?></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='center' Colspan='2'>
							<Font Size='+1' Color='Red'><b>Are you sure you want to delete this login?</b></Font>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='center' Colspan='2'>
<?
			echo "<Input Type='Hidden' Class='nicebtns' Name='delete_confirm' Value='YES'>";
			echo "<Input Type='Submit' Class='nicebtns' Name='Submit' Value='Cancel'>&nbsp;&nbsp;";
			echo "<Input Type='Submit' Class='nicebtns' Name='Submit' Value='Delete'>";
?>
						</TD>
					</TR>
				</Form>
			</TABLE>
<?
		include "inc/box_end.htm";
	} else {
		include "inc/dark_box_begin.htm";
		echo "<font size='+1' color=\"$color_table_hdg_font\">$config_registration_page_name</font>";
		include "inc/box_middle.htm";
?>
					<TABLE ALIGN="left" BORDER=0 CELLSPACING=10 CELLPADDING=0 WIDTH="100%">
					<Form Name='frmUser' Method='POST'>
<?			if (isset($User_ID)) { ?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><b>User ID</b></TD>
						<TD><B><?=$User_ID;?></B></TD>
					</TR>
<?			}	?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' width='240'><font color='red'>*</font>&nbsp;<b>Login ID</b><br><font size="1">Your Choice</font></TD>
						<TD><input type='text' size='20' maxlength='50' name='f_login' value='<?=$f_login;?>'></TD>
					</TR>
<? if (isset($User_ID)) {	?>
<?	if ($newuser == '1') { ?>
					<input type="hidden" name="f_password" value="<?=$f_password; ?>">
<?	} else { ?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;<b>Current Password</b></TD>
						<TD><input type='password' size='20' maxlength='100' name='f_password' value=''></TD>
					</TR>
<?	}  ?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><b>New Password</b></TD>
						<TD><input type='password' size='20' maxlength='100' name='f_password_new' value=''></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><b>Verify New Password</b></TD>
						<TD><input type='password' size='20' maxlength='100' name='f_password_verify' value=''></TD>
					</TR>
<?	} else {
		if ($f_type_id == 10) {
?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;<b>Password</b></TD>
						<TD><input type='password' size='20' maxlength='100' name='f_password_new' value=''></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;<b>Verify Password</b></TD>
						<TD><input type='password' size='20' maxlength='100' name='f_password_verify' value=''></TD>
					</TR>
<?
		}
	}
?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;<b>First Name</b></TD>
						<TD><input type='text' size='30' maxlength='50' name='f_first_name' value='<?=$f_first_name;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;<b>Last Name</b></TD>
						<TD><input type='text' size='30' maxlength='50' name='f_last_name' value='<?=$f_last_name;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;<b>Street</b></TD>
						<TD><input type='text' size='30' maxlength='60' name='f_street' value='<?=$f_street;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;<b>City</b></TD>
						<TD><input type='text' size='30' maxlength='30' name='f_city' value='<?=$f_city;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;<b>State</b></TD>
						<TD><Select name='f_state'>
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
				echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"$statecode\"".($statecode == $f_state ? " SELECTED" : "").">$state->state_name</OPTION>\n");
			}
			if (!empty($prev_grp))
				echo "</optgroup>";
?>
							</select>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;<b>Zip Code</b></TD>
						<TD><input type='text' size='10' maxlength='10' name='f_zip' value='<?=$f_zip;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>Country</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><Select name='f_country'>
<?
			if (empty($f_country))
				$f_country = $config_default_country;
			reset($country->country_list);
			$prev_grp = "";
			echo "<option>Count = ".$countries->count()."</option>\n";
			while (list($country_code, $country) = each($countries->country_list)) {
				echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"$country_code\"".($country_code == $f_country ? " SELECTED" : "").">$country->country_name</OPTION>\n");
			}
?>
							</select>
						</TD>
					</TR>

					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;<b>Email</b></TD>
						<TD><input type='text' size='40' maxlength='100' name='f_email' value='<?=$f_email;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><b>Phone</b></TD>
						<TD><input type='text' size='20' maxlength='50' name='f_phone' value='<?=$f_phone;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><b>Fax</b></TD>
						<TD><input type='text' size='20' maxlength='50' name='f_fax' value='<?=$f_fax;?>'></TD>
					</TR>
<?		if ($f_type_id == 10) {
?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><b>Referred By</b></TD>
						<TD>
						<TABLE border=0 cellspacing=2><TR>
						<TD>Student First Name:</TD><TD><input type='text' size='15' maxlength='25' name='f_referral_firstname' value='<?=$f_referral_firstname;?>'></TD></TR>
						<TD>Student Last Name:</TD><TD><input type='text' size='20' maxlength='40' name='f_referral_lastname' value='<?=$f_referral_lastname;?>'></TD></TR>
						<TD>Student School:</TD><TD><Select name='f_referral_schoolid'>
<?
					reset($schools->school_list);
					echo "\t\t\t<Option value='0'>Unknown</Option>\n";
					while (list($schoolid, $school) = each($schools->school_list))
						echo "\t\t\t<Option value='$school->school_id'".($f_referral_schoolid == $school->school_id ? " SELECTED":"").">$school->school_name</option>\n";
					echo "\t\t</select></td>\n";
?>
						</TD></TR>
						</TABLE>
						</TD>
					</TR>
<?		}
		if (isset($User_ID)) { ?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><b>Type</b></TD>
						<TD><?=$usertypes->user_type_description($f_type_id);?><input Type="hidden" NAME="f_type_id" value="<?=$f_type_id;?>">
						</TD>
					</TR>
<?		} ?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><b>Receive Newsletter</b></TD>
						<TD>
							<input Type="radio" NAME="f_newsletter" value="Y"<? echo (($f_newsletter == "Y")?" CHECKED":"");?>>Yes
							<input Type="radio" NAME="f_newsletter" value="N"<? echo (($f_newsletter != "Y")?" CHECKED":"");?>>No
							<input Type="hidden" NAME="f_prev_newsletter" value="<?=$f_newsletter;?>">
							<input Type="hidden" NAME="f_ip_address" value="<?=$f_ip_address;?>">
							<input Type="hidden" NAME="f_opt_date" value="<?=$f_opt_date;?>">
						</TD>
					</TR>

<?		if ($f_type_id >= 20) {
?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='left' Colspan='2'>
<?	# List of affilations
			include "inc/dark_box_begin.htm";
			echo "<font color='red'>*</font>&nbsp;<font size='+1' color=\"$color_table_hdg_font\">School Affiliations</font>\n";
			include "inc/box_middle.htm";
			echo "<table border=0>\n";
			echo "\t<tr>\n";
			echo "\t\t<td valign='top'>$config_registration_affiliations</td>";
			echo "\t</tr>\n";
			$i = 1;
			if (count($user_affiliations->affiliation_list) > 0) {
				while (list($affid, $affiliation) = each($user_affiliations->affiliation_list)) {
					echo "\t<tr>\n\t\t<td>\n";
					echo "\t<input type='hidden' name='aff_id[$i]' value='$affiliation->affiliation_id'>\n";
					echo "\t<input type='hidden' name='aff_oldschool[$i]' value='$affiliation->school_id'>\n";
					echo "\t\t<Select name='aff_school[$i]'>\n";
					reset($schools->school_list);
					echo "\t\t\t<Option value='0'></Option>\n";
					while (list($schoolid, $school) = each($schools->school_list))
						echo "\t\t\t<Option value='$school->school_id'".($affiliation->school_id == $school->school_id ? " SELECTED":"").">$school->school_name</option>\n";
					echo "\t\t</select></td>\n";
					echo "\t</TR>";
					$i += 1;
				}
			}
			echo "\t<TR><TD>\n";
			echo "\t<input type='hidden' name='aff_id[$i]' value='0'>\n";
			echo "\t<Select name='aff_school[$i]'>\n";
			reset($schools->school_list);
			echo "\t\t<Option value='0'></Option>\n";
			while (list($schoolid, $school) = each($schools->school_list))
				echo "\t\t<Option value='$school->school_id'".($aff_school[$i] == $school->school_id ? " SELECTED":"").">$school->school_name</option>\n";
			echo "\t</select></td>\n";
			echo "\t</TR>\n";
			echo "</TABLE>\n";
			include "inc/box_end.htm";
?>
						</TD>
					</TR>
<?		}	?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='left' Colspan='2'>
						<p><font color='red'>*</font>&nbsp;Indicates a required field.</p>
<?
			echo "<Input Type='Submit' Name='Submit' Value='Save'>&nbsp;&nbsp;";
			if (!empty($User_ID))
				echo "<Input Type='Submit' Name='Submit' Value='Delete'>";
?>
						</TD>
					</TR>
				</Form>
			</TABLE>
<?
		include "inc/box_end.htm";
	}
?>
                  </td>
<? require "inc/body_end.inc"; ?>
</html>
