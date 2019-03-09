<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";
	require_once "inc/class_users.php";
	require_once "inc/class_school.php";
	require_once "inc/class_schools.php";
	require_once "inc/class_user_type.php";
	require_once "inc/class_user_types.php";
	require_once "inc/class_affiliation.php";
	require_once "inc/class_affiliations.php";
	require_once "inc/class_state.php";
	require_once "inc/class_states.php";
	require_once "inc/class_country.php";
	require_once "inc/class_countries.php";
	require_once "inc/class_project.php";

if ($debug) {
	echo "loading user<br>";
	flush();
}
	$user = new user();
	$user->load_user($User_ID);
	$user_affiliations	= new affiliations;
	$user_affiliations->load_affiliations($User_ID);

if ($debug) {
	echo "loading schools<br>";
	flush();
}
	$schools = new schools();
	$schools->load_schools();

if ($debug) {
	echo "loading user_types<br>";
	flush();
}
	$usertypes = new user_types();
	$usertypes->load_user_types();

if ($debug) {
	echo "loading states<br>";
	flush();
}
	$states = new states();
	$states->load_states();

	$countries = new countries();
	$countries->load_countries();

	$message_received = $message;
	$message = "";


	if ($_SERVER['REQUEST_METHOD'] == "POST" && $Submit != "Cancel") {
		if ($Submit == "Submit") {
			// Validate the fields.
			$errors = "";
			if (empty($f_first_name))
				$errors .= (empty($errors) ? "" : "<BR>")."First Name is required.";
			if (empty($f_last_name))
				$errors .= (empty($errors) ? "" : "<BR>")."Last Name is required.";
			if (empty($f_street))
				$errors .= (empty($errors) ? "" : "<BR>")."Street is required.";
			if (empty($f_city))
				$errors .= (empty($errors) ? "" : "<BR>")."City is required.";
			if (empty($f_state))
				$errors .= (empty($errors) ? "" : "<BR>")."State is required.";
			if (empty($f_zip))
				$errors .= (empty($errors) ? "" : "<BR>")."Zip Code is required.";
			$email_domain = substr(strstr($f_email,"@"),1);
			if (empty($f_email)) {
				$errors .= (empty($errors) ? "" : "<BR>")."E-Mail address is required.";
			} elseif (empty($email_domain)) {
				$errors .= (empty($errors) ? "" : "<BR>")."Invalid E-Mail address.";
			}
			if (empty($f_districtname))
				$errors .= (empty($errors) ? "" : "<BR>")."School District is required.";
			if (empty($f_schoolname))
				$errors .= (empty($errors) ? "" : "<BR>")."School Name is required.";


			$user_rcd = new user();
			if ($user_rcd->email_exists($f_email)) {
				$user_rcd->lookup_login("", $email);
				$f_login = $user_rcd->login;
			} else {
				$f_login = $f_first_name."_".$f_last_name;
				$idx = 0;
				while ($user_rcd->login_exists($f_login)) {
					$idx = $idx + 1;
					$f_login = $f_first_name."_".$f_last_name.$idx;
				}

				$user_rcd = new user();
			}
		}
		if (empty($errors)) {
			// Save user
			if ($Submit == "Submit") {
				$user_rcd->login = $f_login;
				$user_rcd->password = $better_token = md5(uniqid(rand()));
				$user_rcd->password = substr($user_rcd->password, strlen($user_rcd->password)-10, 10);
				$user_rcd->verified = "N";
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
				if ($district_id)
					$user_rcd->district_id = $district_id;
				if ($user_rcd->save_user()) {
					if ($f_type_id != 10) {
						# Now save affiliations.
						$user_affiliations = new affiliations;
						$user_affiliations->load_affiliations($user_id);
						if (!$user_affiliations->new_affiliation($user_rcd->user_id, "64", "N"))
							$message .= "Error adding affiliation to school $aff_school[$i]: ".$user_affiliations->error_message."<BR>";
					}
					$message .= "User $user_rcd->login has been saved.";
					# Now save the project.
					# Check to see if this user already has a project.
					if ($results = $db_link->query("select project_id from project where submitted_user_id = '".$user_rcd->user_id."'")) {
						$row = mysqli_fetch_assoc($results);
						$project_id = $row["project_id"];
					} else {
						$project_id = 0;
					}
					$project = new project();
					$project->load_project("2193");

					$project->project_name = preg_replace("%TEACHER","$f_first_name $f_last_name",
					preg_replace("%SCHOOL","$f_schoolname",
					$project->project_name));

					$project->project_description = $f_reason_message;
					$project->project_status_id = 1;
					$project->submitted_date = date("Y-m-d H:i:s");
					$project->submitted_user_id = $user_rcd->user_id;
					$project->entered_user_id = $user_rcd->user_id;
					$project->district_id = "22";
					$project->project_id = $project_id;
					if ($project->save_project()) {
						if ($project_id) {
							$message .= " Project ".$project->project_id." created.";
						} else {
							$message .= " Project ".$project->project_id." replaced.";
						}
						$subject = "Art project #".$project->project_id." submitted.";
						$body = $http_location."proposal.php?projectid=".$project->project_id;
						$email_to = $project->reviewer();
						if (empty($email_to))
							$email_to = "support@donate2educate.com";
						$headers = "From: Donate2Educate <support@donate2educate.org>\r\n";
						if (!empty($email_to))
							mail($email_to, $subject, $body, $headers);
					}

 					if (empty($target)) {
						echo "<script type=\"text/javascript\">\nlocation.href=\"registration.php?message=".urlencode($message)."\"\n</script>";
					} else {
						echo "<script type=\"text/javascript\">\nlocation.href=\"".urldecode($target)."\"\n</script>";
					}
				} else
					$message .= "<p>Errors occured saving this user!<br>$user_rcd->error_message</p>";
			}
		} else {
			$message .= "<p>Please correct the following items and then retry:<br>$errors</p>";
		}
	} else {
		$project = new project();
		if ($project->load_project("2193")) {
			$f_reason_message = $project->project_description;
		}
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
	$pagename = "$config_aa_registration_page_name";
	$help_msg_name = "config_aa_registration_help";
	$help_msg = "$config_aa_registration_help";
	$help_width = "$config_aa_registration_help_width";
	$help_height = "$config_aa_registration_help_height";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <td width="655" align="left" valign="top">
<?
	if (!empty($config_aa_registration_paragraph1)) {
		include "inc/box_begin.htm";
		echo "$config_aa_registration_paragraph1";
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
		echo "<font size='+1' color=\"$color_table_hdg_font\">$config_aa_registration_page_name</font>";
		include "inc/box_middle.htm";
?>
					<TABLE ALIGN="left" BORDER=0 CELLSPACING=10 CELLPADDING=0 WIDTH="100%">
					<Form Name='frmUser' Method='POST'>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><b>First Name</b></TD>
						<TD Align='center'><font color='red'>*</font></TD>
						<TD><input type='text' size='30' maxlength='50' name='f_first_name' value='<?=$f_first_name;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><b>Last Name</b></TD>
						<TD Align='center'><font color='red'>*</font></TD>
						<TD><input type='text' size='30' maxlength='50' name='f_last_name' value='<?=$f_last_name;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><b>Street</b></TD>
						<TD Align='center'><font color='red'>*</font></TD>
						<TD><input type='text' size='30' maxlength='60' name='f_street' value='<?=$f_street;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><b>City</b></TD>
						<TD Align='center'><font color='red'>*</font></TD>
						<TD><input type='text' size='30' maxlength='30' name='f_city' value='<?=$f_city;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><b>State</b></TD>
						<TD Align='center'><font color='red'>*</font></TD>
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
						<TD Align='Right'><b>Zip Code</b></TD>
						<TD Align='center'><font color='red'>*</font></TD>
						<TD><input type='text' size='10' maxlength='10' name='f_zip' value='<?=$f_zip;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>Country</b></TD>
						<TD Align='center'><font color='red'>*</font></TD>
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
						<TD Align='Right'><b>Email</b></TD>
						<TD Align='center'><font color='red'>*</font></TD>
						<TD><input type='text' size='40' maxlength='100' name='f_email' value='<?=$f_email;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><b>Phone</b></TD>
						<TD Align='center'><font color='red'></font></TD>
						<TD><input type='text' size='20' maxlength='50' name='f_phone' value='<?=$f_phone;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><b>Fax</b></TD>
						<TD Align='center'><font color='red'></font></TD>
						<TD><input type='text' size='20' maxlength='50' name='f_fax' value='<?=$f_fax;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><b>School District Name</b></TD>
						<TD Align='center'><font color='red'>*</font></TD>
						<TD><input type='text' size='40' maxlength='100' name='f_districtname' value='<?=$f_districtname;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><b>School Name</b></TD>
						<TD Align='center'><font color='red'>*</font></TD>
						<TD><input type='text' size='40' maxlength='100' name='f_schoolname' value='<?=$f_schoolname;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><b>Receive Newsletter</b></TD>
						<TD Align='center'><font color='red'></font></TD>
						<TD>
							<input Type="radio" NAME="f_newsletter" value="Y"<? echo (($f_newsletter == "Y")?" CHECKED":"");?>>Yes
							<input Type="radio" NAME="f_newsletter" value="N"<? echo (($f_newsletter != "Y")?" CHECKED":"");?>>No
							<input Type="hidden" NAME="f_prev_newsletter" value="<?=$f_newsletter;?>">
							<input Type="hidden" NAME="f_ip_address" value="<?=$f_ip_address;?>">
							<input Type="hidden" NAME="f_opt_date" value="<?=$f_opt_date;?>">
							<input Type="hidden" NAME="f_type_id" value="20">
						</TD>
					</TR>
<?	if (!empty($config_aa_registration_paragraph2)) { ?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='left' Colspan='3'>
<?
		include "inc/box_begin.htm";
		echo "$config_aa_registration_paragraph2";
		include "inc/box_end.htm";
?>
						</TD>
					</TR>
<?	} ?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='left' Colspan='3'>
							<TEXTAREA NAME="f_reason_message" cols=62 rows=5><?=$f_reason_message;?></TEXTAREA>
						</TD>
					</TR>

					<TR ALIGN="left" VALIGN="middle">
						<TD Align='left' Colspan='3'>
						<p><font color='red'>*</font>&nbsp;Indicates a required field.</p>
<?
			echo "<Input Type='Submit' Name='Submit' class=\"nicebtns\" Value='Submit'>&nbsp;&nbsp;";
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
