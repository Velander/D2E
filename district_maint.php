<?	require "inc/db_inc.php";
	require_once "inc/func.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";
	require_once "inc/class_users.php";
	require_once "inc/class_school.php";
	require_once "inc/class_schools.php";
	require_once "inc/class_district.php";
	require_once "inc/class_districts.php";
	require_once "inc/class_user_type.php";
	require_once "inc/class_user_types.php";
	require_once "inc/class_affiliation.php";
	require_once "inc/class_affiliations.php";
	require_once "inc/class_state.php";
	require_once "inc/class_states.php";
	require_once "inc/class_country.php";
	require_once "inc/class_countries.php";

if ($debug) {
	echo "loading user<br>";
	flush();
}
	require_once "inc/admin_user.php";


if ($debug) {
	echo "loading schools<br>";
	flush();
}
	$schools = new schools();
	$schools->load_schools();

if ($debug) {
	echo "loading districts<br>";
	flush();
}
	$districts = new districts();
	$districts->load_districts();

if ($debug) {
	echo "loading states<br>";
	flush();
}
	$states = new states();
	$states->load_states();

	$countries = new countries();
	$countries->load_countries();

if ($debug) {
	echo "loading user_types<br>";
	flush();
}
	$usertypes = new user_types();
	$usertypes->load_user_types();

	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		if ($Submit == "Save") {
			// Validate the fields.
			$errors = "";
			if (empty($f_district_name))
				$errors .= (empty($errors) ? "" : "<BR>")."Distrinct Name is required.";
			if (empty($f_administrator))
				$errors .= (empty($errors) ? "" : "<BR>")."Administrator is required.";
			if (empty($f_email_domain))
				$errors .= (empty($errors) ? "" : "<BR>")."Distrinct email_domain is required.";
			$f_district_id = $f_existing_district_id;
		}
		if (empty($errors)) {
			// Save district
			$district_rcd = new district();
			if (!empty($f_existing_district_id)) {
				$district_rcd->load_district($f_existing_district_id);
				$f_district_id = $f_existing_district_id;
			}
			if ($Submit == "Search") {
				$districtsearch = new districts;
				$districtsearch->find_districts($f_district_id, $f_district_name, $f_district_state, $f_administrator, $f_email, $f_email_domain, $f_phone, $f_tax_id, $f_accept_cc, $f_cc_login, $f_cc_transactionid, $f_cc_prefix, $f_cc_currency, $f_cc_live, $f_inactive, $f_receives_funds, $f_payment_contact, $f_payment_street, $f_payment_city, $f_payment_state, $f_payment_zip, $f_payment_faxno, $f_payment_phone);
				if ($districtsearch->count() == 0)
					$message = "No matching districts found.";
			} elseif ($Submit == "Cancel") {
				$f_district_id = 0;
				$f_existing_district_id = 0;
			} elseif ($Submit == "Delete") {
				if ($delete_confirm == "YES") {
					$Submit = "";
					if ($district_rcd->delete_district()) {
						$message .= "District $district_rcd->district_name deleted.";
						$f_district_id = 0;
						$f_existing_district_id = 0;
					} else
						$message .= "<p>Errors occured deleting this district!<br>$district_rcd->error_message</p>";
				}
			} elseif ($Submit == "Save") {
				$district_rcd->district_name 	= $f_district_name;
				$district_rcd->district_state 	= $f_district_state;
				$district_rcd->administrator 	= $f_administrator;
				$district_rcd->contact_name 	= $f_contact_name;
				$district_rcd->homepage		= $f_homepage;
				$district_rcd->email 		= $f_email;
				$district_rcd->email_domain 	= $f_email_domain;
				$district_rcd->phone		= $f_phone;
				$district_rcd->district_contact = $f_district_contact;
				$district_rcd->district_street 	= $f_district_street;
				$district_rcd->district_street2 = $f_district_street2;
				$district_rcd->district_city 	= $f_district_city;
				$district_rcd->district_zip 	= $f_district_zip;
				$district_rcd->district_faxno 	= $f_district_faxno;
				$district_rcd->district_phone 	= $f_district_phone;
				$district_rcd->payment_contact 	= $f_payment_contact;
				$district_rcd->payment_street 	= $f_payment_street;
				$district_rcd->payment_city 	= $f_payment_city;
				$district_rcd->payment_state 	= $f_payment_state;
				$district_rcd->payment_zip 	= $f_payment_zip;
				$district_rcd->payment_faxno 	= $f_payment_faxno;
				$district_rcd->payment_phone 	= $f_payment_phone;
				$district_rcd->alt_donation_url = $f_alt_donation_url;
				$district_rcd->tax_id           = $f_tax_id;
				$district_rcd->inactive         = $f_inactive;
                                $district_rcd->funded_email_override= $f_funded_email_override;
	                        $district_rcd->funded_email_subject = $f_funded_email_subject;
	                        $district_rcd->funded_email_body    = $f_funded_email_body;
                                $district_rcd->funded_email_cc      = $f_funded_email_cc;
				$district_rcd->receives_funds       = $f_receives_funds;

				if ($district_rcd->save_district()) {
					# Now save affiliations.
					$f_district_id = $district_rcd->district_id;
					$f_existing_district_id = $district_rcd->district_id;
					$message .= "District $district_rcd->district_name saved.";
				} else
					$message .= "<p>Errors occured saving this district!<br>$district_rcd->error_message</p>";
			}
		} else {
			$message .= "<p>Please correct the following items and then resubmit:<br>$errors</p>";
		}
	} else {
		if (!empty($district_id)) {
			$district_rcd = new district();
			if ($district_rcd->load_district($district_id)) {
				$f_district_id		= $district_rcd->district_id;
				$f_district_name	= $district_rcd->district_name;
				$f_district_state	= $district_rcd->district_state;
				$f_administrator	= $district_rcd->administrator;
				$f_homepage		= $district_rcd->homepage;
				$f_contact_name		= $distrcit_rcd->contact_name;
				$f_email		= $district_rcd->email;
				$f_email_domain		= $district_rcd->email_domain;
				$f_phone		= $district_rcd->phone;
				$f_district_contact	= $district_rcd->district_contact;
				$f_district_street	= $district_rcd->district_street;
				$f_district_street2	= $district_rcd->district_street2;
				$f_district_city	= $district_rcd->district_city;
				$f_district_zip		= $district_rcd->district_zip;
				$f_district_faxno	= $district_rcd->district_faxno;
				$f_district_phone	= $district_rcd->district_phone;
				$f_payment_contact	= $district_rcd->payment_contact;
				$f_payment_street	= $district_rcd->payment_street;
				$f_payment_city		= $district_rcd->payment_city;
				$f_payment_state	= $district_rcd->payment_state;
				$f_payment_zip		= $district_rcd->payment_zip;
				$f_payment_faxno	= $district_rcd->payment_faxno;
				$f_payment_phone	= $district_rcd->payment_phone;
				$f_alt_donation_url = $district_rcd->alt_donation_url;
				$f_tax_id		= $district_rcd->tax_id;
				$f_inactive		= $district_rcd->inactive;
				$f_receives_funds	= $district_rcd->receives_funds;
                                $f_funded_email_override= $district_rcd->funded_email_override;
	                        $f_funded_email_subject = $district_rcd->funded_email_subject;
                                $f_funded_email_body    = $district_rcd->funded_email_body;
                                $f_funded_email_cc      = $district_rcd->funded_email_cc;

			} else {
				$f_district_id = "";
				$f_tax_id = "0";
			}
		}
	}
?>
<html>
<head>
<?
	include "inc/cssstyle.php";
	$pagename = "$config_districtmaint_page_name";
	$help_msg_name = "config_districtmaint_help";
	$help_msg = "$config_districtmaint_help";
	$help_width = "$config_districtmaint_help_width";
	$help_height = "$config_districtmaint_help_height";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <td width="655" align="left" valign="top">
<?	echo "$config_districtmaint_paragraph1";
	if (!empty($message)) {
		include "inc/box_begin.htm";
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center>";
		include "inc/box_end.htm";
	}
	if ($Submit == "Delete") {
?>
	<table width=100% cellspacing=1 cellpadding=1>
		<tr>
			<td align=left width=50% bgcolor="<?=$color_table_hdg_bg;?>">
	<table width=100% cellspacing=1 cellpadding=1>
		<tr>
			<td align=left width=50% bgcolor="<?=$color_table_col_bg;?>">
				<TABLE DIR=ltr ID="district_list" ALIGN=bleedright WIDTH="100%" COLS=4 BORDER="0" CELLSPACING="2" CELLPADDING="2">
<?		if ($district_rcd->schools->count() > 0) { ?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='100' Align='Right'><b>District ID</b></TD>
						<TD>
							<?
								echo "$f_district_id";
							?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='100' Align='Right'><b>District Name</b></TD>
						<TD><?=$f_district_name;?></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='100' Align='Right' Valign='Top'><b>Schools</b></TD>
						<TD>
<?
			reset($district_rcd->schools->school_list);
			while (list($school_id, $school) = each($district_rcd->schools->school_list)) {
				echo "$school->school_name<BR>";
			}
?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle" bgcolor="<?=$color_table_hdg_bg;?>">
						<TD Align='center' Colspan='2'><Font size='+1' color="<?=$color_table_hdg_font;?>">You cannot delete a District that has schools assigned to it.</font></TD>
					</TR>
<?
} else {
?>
					<Form Name='frmDelete' Method='POST'>
					<TR ALIGN="left" VALIGN="middle" bgcolor="<?=$color_table_hdg_bg;?>">
						<TD Align='center' Colspan='2'><Font size='+1' color="<?=$color_table_hdg_font;?>">District Delete Confirmation</font></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='100' Align='Right'><b>District ID</b></TD>
						<TD>
							<?
								echo "$f_district_id";
								echo "<input type='hidden' name='f_existing_district_id' value='$f_district_id'>";
							?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='100' Align='Right'><b>District Name</b></TD>
						<TD><?=$f_district_name;?></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='100' Align='Right'><b>Administrator</b></TD>
						<TD><?=$f_administrator;?></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='100' Align='Right'><b>Contact Name</b></TD>
						<TD><?=$f_contact_name;?></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='100' Align='Right'><b>Home Page</b></TD>
						<TD><?=$f_homepage;?></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='100' Align='Right'><b>Payment Contact</b></TD>
						<TD><?=$f_payment_contact;?></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='center' Colspan='2'>
							<Font Size='+1' Color='Red'><b>Are you sure you want to delete this district?</b></Font>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='center' Colspan='2'>
<?
			echo "<Input Type='Hidden' Name='delete_confirm' Value='YES'>";
			echo "<Input Type='Submit' Class='nicebtns' Name='Submit' Value='Cancel'>&nbsp;&nbsp;";
			echo "<Input Type='Submit' Class='nicebtns' Name='Submit' Value='Delete'>";
?>
						</TD>
					</TR>
				</Form>
<?
	}
?>
				</TABLE>
			</td>
		</tr>
	</table>
			</td>
		</tr>
	</table>
<?
	} else {
		if ($Submit == "Search" && $districtsearch->count() > 0) {
?>
	<table width=100% cellspacing=1 cellpadding=1>
		<tr>
			<td align=left width=50% bgcolor="<?=$color_table_hdg_bg;?>">
				<TABLE DIR=ltr ID="project_list" ALIGN=bleedright WIDTH="100%" COLS=4 BORDER="0" CELLSPACING="2" CELLPADDING="2">
					<Form Name='frmDelete' Method='POST'>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='center' Colspan='4'><Font size='+1' color="<?=$color_table_hdg_font;?>">District Search Results</font></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='left' Colspan='4'><Font size='-1' color="<?=$color_table_hdg_font;?>">Click on the district's name to edit that district.</font></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD><Font color="<?=$color_table_hdg_font;?>">DistictID</font></TD>
						<TD><Font color="<?=$color_table_hdg_font;?>">District Name</font></TD>
						<TD><Font color="<?=$color_table_hdg_font;?>">Administrator</font></TD>
						<TD><Font color="<?=$color_table_hdg_font;?>">State</font></TD>
					</TR>
<?
			reset($districtsearch->district_list);
			while (list($districtid, $sdistrict) = each($districtsearch->district_list)) {
				echo "\t\t\t\t<TR>\n";
				echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"district_maint.php?district_id=$sdistrict->district_id\">$sdistrict->district_id</a></TD>\n";
				echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"district_maint.php?district_id=$sdistrict->district_id\">$sdistrict->district_name</a></TD>\n";
				echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"district_maint.php?district_id=$sdistrict->district_id\">$sdistrict->administrator</a></TD>\n";
				echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"district_maint.php?district_id=$sdistrict->district_id\">$sdistrict->district_state</a></TD>\n";
				echo "\t\t\t\t</TR>\n";
			}
?>
				</TABLE>
			</td>
		</tr>
	</table>
<?
		} else {
?>
<table width=100% cellspacing=1 cellpadding=1>
  <TR ALIGN="left" VALIGN="middle">
	<td align=left width=50% bgcolor="<?=$color_table_hdg_bg;?>">
	<table width=100% cellspacing=1 cellpadding=1>
		<TR ALIGN="left" VALIGN="middle">
			<TD Align='center' bgcolor="<?=$color_table_hdg_bg;?>"><font size="+1" color="<?=$color_table_hdg_font;?>">District Maintenance</font></TD>
		</TR>
		<TR ALIGN="left" VALIGN="middle">
			<TD Align='center' bgcolor="<?=$color_table_hdg_bg;?>"><font size="-1" color="<?=$color_table_hdg_font;?>">Enter a new district and click <b>Save</b>, or enter search criteria and click <b>Search</b>.</font></TD>
		</TR>
		<tr>
			<td align=left width=50% bgcolor="<?=$color_table_col_bg;?>">
				<TABLE ALIGN="left" BORDER=0 CELLSPACING=10 CELLPADDING=0 WIDTH="100%">
					<Form Name='frmUser' Method='POST'>
					<TR ALIGN="left" VALIGN="middle">
						<TD Width='20%' Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>District ID</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>">
							<? 	if (empty($f_district_id))
									echo "<input type='text' name='f_district_id' size=5>";
								else {
									echo "<B>$f_district_id</B>";
									echo "<input type='hidden' name='f_existing_district_id' value='$f_district_id'>";
								}
							?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>District Name</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='20' maxlength='50' name='f_district_name' value='<?=$f_district_name;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>Administrator</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='30' maxlength='80' name='f_administrator' value='<?=$f_administrator;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Contact Name</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='30' maxlength='60' name='f_contact_name' value='<?=$f_contact_name;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Home Page</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='60' maxlength='128' name='f_homepage' value='<?=$f_homepage;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Phone</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='30' maxlength='100' name='f_phone' value='<?=$f_phone;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Email</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='30' maxlength='100' name='f_email' value='<?=$f_email;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>Email Domain</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='30' maxlength='100' name='f_email_domain' value='<?=$f_email_domain;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>District Contact</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='30' maxlength='50' name='f_district_contact' value='<?=$f_district_contact;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>District Street</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='30' maxlength='50' name='f_district_street' value='<?=$f_district_street;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>District Street2</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='30' maxlength='30' name='f_district_street2' value='<?=$f_district_street2;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>District City</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='30' maxlength='30' name='f_district_city' value='<?=$f_district_city;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>District State</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><Select name='f_district_state'>
<?
			#if (empty($f_district_state))
			#	$f_district_state = $config_default_state;
			reset($states->state_list);
			$prev_grp = "";
			while (list($statecode, $state) = each($states->state_list)) {
				if ($state->state_group != $prev_grp) {
					if (!empty($prev_grp))
						echo "</optgroup>";
					$prev_grp = $state->state_group;
					echo ("\t\t\t\t\t\t\t\t\t\t<optgroup label=\"$state->state_group\">\n");
				}
				echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"$statecode\"".($statecode == $f_district_state ? " SELECTED" : "").">$state->state_name</OPTION>\n");
			}
			if (!empty($prev_grp))
				echo "</optgroup>";
?>
							</select>
 						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>District Zip</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='10' maxlength='10' name='f_district_zip' value='<?=$f_district_zip;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>District Phone</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='20' maxlength='50' name='f_district_phone' value='<?=$f_district_phone;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>District Fax Number</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='12' maxlength='50' name='f_district_faxno' value='<?=$f_district_faxno;?>'></TD>
					</TR>

                                        <TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Payment Contact</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='30' maxlength='50' name='f_payment_contact' value='<?=$f_payment_contact;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Payment Street</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='30' maxlength='50' name='f_payment_street' value='<?=$f_payment_street;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Payment City</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='30' maxlength='30' name='f_payment_city' value='<?=$f_payment_city;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Payment State</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><Select name='f_payment_state'>
<?
			#if (empty($f_payment_state))
			#	$f_state = $config_default_state;
			reset($states->state_list);
			$prev_grp = "";
			while (list($statecode, $state) = each($states->state_list)) {
				if ($state->state_group != $prev_grp) {
					if (!empty($prev_grp))
						echo "</optgroup>";
					$prev_grp = $state->state_group;
					echo ("\t\t\t\t\t\t\t\t\t\t<optgroup label=\"$state->state_group\">\n");
				}
				echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"$statecode\"".($statecode == $f_payment_state ? " SELECTED" : "").">$state->state_name</OPTION>\n");
			}
			if (!empty($prev_grp))
				echo "</optgroup>";
?>
							</select>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Payment Zip</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='10' maxlength='10' name='f_payment_zip' value='<?=$f_payment_zip;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>Payment Phone</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='20' maxlength='50' name='f_payment_phone' value='<?=$f_payment_phone;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Payment Fax Number</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='12' maxlength='50' name='f_payment_faxno' value='<?=$f_payment_faxno;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Tax ID</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='15' maxlength='15' name='f_tax_id' value='<?=$f_tax_id;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>Inactive</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='checkbox' name='f_inactive' value='Y' <?=($f_inactive== "Y" ? "Checked" : "");?>></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>Receives Funds</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='checkbox' name='f_receives_funds' value='Y' <?=($f_receives_funds== "Y" ? "Checked" : "");?>></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>Funded Email Override</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='checkbox' name='f_funded_email_override' value='Y' <?=($f_funded_email_override== "Y" ? "Checked" : "");?>></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Funded Email Subject</b></TD>
                                                <TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='55' maxlength='125' name='f_funded_email_subject' value='<?=$f_funded_email_subject;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Funded Email Body</b></TD>
                                                <TD bgcolor="<?=$color_table_col_bg;?>">
                                                    <textarea rows="5" cols='55' name='f_funded_email_body'><?=$f_funded_email_body;?></textarea>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Funded Email Copy</b></TD>
                                                <TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='55' maxlength='125' name='f_funded_email_cc' value='<?=$f_funded_email_cc;?>'></TD>
					</TR>
<? if (!empty($f_district_id)) { ?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>Schools</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>">
<?
			reset($district_rcd->schools->school_list);
			while (list($school_id, $school) = each($district_rcd->schools->school_list)) {
				echo "$school->school_name<BR>";
			}
?>
						</TD>
					</TR>
<?	}	?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>Alt Donation URL</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='60' maxlength='200' name='f_alt_donation_url' value='<?=$f_alt_donation_url;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='left' Colspan='2' bgcolor="<?=$color_table_col_bg;?>">
<?
			echo "<Input Type='Submit' Class='nicebtns' Name='Submit' Value='Save'>&nbsp;&nbsp;\n";
			if (!empty($f_district_id))
				echo "|&nbsp;&nbsp;<Input Type='Submit' Class='nicebtns' Name='Submit' Value='Delete'>&nbsp;&nbsp;\n";
			if (empty($f_district_id))
				echo "|&nbsp;&nbsp;<Input Type='Submit' Class='nicebtns' Name='Submit' Value='Search'>\n";
?>
						</TD>
					</TR>
					</Form>
				</TABLE>
			</td>
		</tr>
	</table>
	</td>
  </tr>
</table>
<?
		}
	}
?>
                  </td>
<? require "inc/body_end.inc"; ?>
</html>
