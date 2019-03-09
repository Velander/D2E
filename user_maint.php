<?
    require "inc/db_inc.php";
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
    require_once "inc/class_user_note.php";
    require_once "inc/class_affiliation.php";
    require_once "inc/class_affiliations.php";
    require_once "inc/class_state.php";
    require_once "inc/class_states.php";
    require_once "inc/class_country.php";
    require_once "inc/class_countries.php";
    require_once "inc/class_projects.php";

if ($debug) {
    echo "loading user<br>";
    flush();
}
    $user = new user();
    $user->load_user($User_ID);

    require "inc/validate_admin.php";

if ($debug) {
    echo "loading schools<br>";
    flush();
}
    $schools = new schools();
    $schools->load_schools();

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

    $user_id	= $_GET["user_id"];

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

		$Submit					= $_POST["Submit"];
    	$f_user_id				= $_POST["f_user_id"];
    	$f_login				= $_POST["f_login"];
    	$f_password				= $_POST["f_password"];
    	$f_company				= $_POST["f_company"];
    	$f_first_name			= $_POST["f_first_name"];
    	$f_last_name			= $_POST["f_last_name"];
		$f_street				= $_POST["f_street"];
		$f_city					= $_POST["f_city"];
		$f_state				= $_POST["f_state"];
		$f_country				= $_POST["f_country"];
		$f_zip					= $_POST["f_zip"];
		$f_login				= $_POST["f_login"];
		$f_password				= $_POST["f_password"];
		$f_email				= $_POST["f_email"];
		$f_verified				= $_POST["f_verified"];
		$f_bad_email			= $_POST["f_bad_email"];
		$f_phone				= $_POST["f_phone"];
		$f_fax					= $_POST["f_fax"];
		$f_url					= $_POST["f_url"];
		$f_banner_link			= $_POST["f_banner_link"];
		$f_half_banner_link		= $_POST["f_half_banner_link"];
		$f_type_id				= $_POST["f_type_id"];
		$f_district_id			= $_POST["f_district_id"];
		$f_newsletter			= $_POST["f_newsletter"];
		$f_email_verified		= $_POST["f_email_verified"];
		$f_opt_date				= $_POST["f_opt_date"];
		$f_ip_address			= $_POST["f_ip_address"];
		$f_notes				= $_POST["f_notes"];
		$f_allow_matching		= $_POST["f_allow_matching"];
		$f_direct_donations		= $_POST["f_direct_donations"];
		$f_referral_firstname	= $_POST["f_referral_firstname"];
		$f_referral_lastname	= $_POST["f_referral_lastname"];
		$f_usernotes			= $_POST["f_usernotes"];
		$f_existing_user_id		= $_POST["f_existing_user_id"];
		$aff_school				= $_POST["aff_school"];
		$aff_admin				= $_POST["aff_admin"];

		$note_id	= $_POST["note_id"];
		$note_delete= $_POST["note_delete"];
        $note_text	= $_POST["note_text"];

        $delete_confirm		= $_POST["delete_confirm"];
        $chkFields			= $_POST["chkFields"];

        if ($Submit == "Save") {
            // Validate the fields.
            $errors = "";
            if (empty($f_first_name) && empty($f_company))
                $errors .= (empty($errors) ? "" : "<BR>")."User First Name is required.";
            if (empty($f_last_name) && empty($f_company))
                $errors .= (empty($errors) ? "" : "<BR>")."User Last Name is required.";
            if (empty($f_login))
                $errors .= (empty($errors) ? "" : "<BR>")."Login name is required.";
            if (empty($f_password))
                $errors .= (empty($errors) ? "" : "<BR>")."Password is required.";
        } elseif ($Submit == "Upload File") {
            $file_upload = true;
            $user_id = $f_user_id;
            if ($HTTP_POST_FILES['imgfile']['size'] >250000) {
                $errors .= (empty($errors) ? "" : "<BR>")."Your uploaded file size is more than 250KB so please reduce the file size and then upload.";
                $file_upload="false";
            }
            if (!($HTTP_POST_FILES['imgfile']['type'] =="image/jpeg" || $HTTP_POST_FILES['imgfile']['type']=="image/gif")) {
                $errors .= (empty($errors) ? "" : "<BR>")."Your uploaded file must be of JPG or GIF. Other file types '".$HTTP_POST_FILES['imgfile']['type']."' are not allowed.";
                $file_upload="false";
            }
            if ($file_upload) {
                $add="banners/".$HTTP_POST_FILES['imgfile']['name']; // the path with the file name where the file will be stored, banners is the directory name.
                if (is_uploaded_file($imgfile))
                {
                    # Check dimensions of image.
                    list($width, $height, $type) = getimagesize($imgfile);
                    if ($width > 620)
                        $errors .= (empty($errors) ? "" : "<BR>")."Banner image cannot be over 620 pixels width.";
                    if ($height > 100)
                        $errors .= (empty($errors) ? "" : "<BR>")."Banner image cannot be over 100 pixels high.";
                    if (empty($errors)) {
                            if (copy($imgfile, $add)) {
                                $user_rcd = new user();
                                if ($user_rcd->load_user($user_id)) {
                                    if ($width <=310)
                                        $user_rcd->half_banner_link = "$add";
                                    else
                                        $user_rcd->banner_link = "$add";
                                    $f_user_id		= $user_rcd->user_id;
                                    $f_first_name	= $user_rcd->first_name;
                                    $f_last_name	= $user_rcd->last_name;
                                    $f_company		= $user_rcd->company;
                                    $f_street		= $user_rcd->street;
                                    $f_city			= $user_rcd->city;
                                    $f_state		= $user_rcd->state;
                                    $f_country		= $user_rcd->country;
                                    $f_zip			= $user_rcd->zip;
                                    $f_login		= $user_rcd->login;
                                    $f_password		= $user_rcd->password;
                                    $f_email		= $user_rcd->email;
                                    $f_verified		= $user_rcd->verified;
                                    $f_bad_email	= $user_rcd->bad_email;
                                    $f_phone		= $user_rcd->phone;
                                    $f_fax			= $user_rcd->fax;
                                    $f_url			= $user_rcd->url;
                                    $f_banner_link	= $user_rcd->banner_link;
                                    $f_half_banner_link	= $user_rcd->half_banner_link;
                                    $f_type_id		= $user_rcd->type_id;
                                    $f_district_id  = $user_rcd->district_id;
                                    $f_newsletter	= $user_rcd->newsletter;
                                    $f_email_verified = $user_rcd->email_verified;
                                    $f_opt_date		= $user_rcd->opt_date;
                                    $f_ip_address	= $user_rcd->ip_address;
                                    $f_notes		= $user_rcd->note_list;
                                    $f_allow_matching 	= $user_rcd->allow_matching;
                                    $f_direct_donations	= $user_rcd->direct_donations;
                                    $f_referral_firstname	= $user_rcd->referral_firstname;
                                    $f_referral_lastname	= $user_rcd->referral_lastname;
                                    $user_affiliations = new affiliations;
                                    $user_affiliations->load_affiliations($user_id);
                                    $f_usernotes		= $user_rcd->notes;
                                    $user_rcd->save_user();
                                } else {
                                    $errors .= (empty($errors) ? "" : "<BR>")."Error Updating user.";
                                }
                            } else {
                                // if an error occurs the file could not
                                // be written, read or possibly does not exist
                                $errors .= (empty($errors) ? "" : "<BR>")."Error Uploading File.";
                            }
                    }
                }
            }
        }
        if (empty($errors)) {
            $user_rcd = new user();
            if (!empty($f_existing_user_id)) {
                $user_rcd->load_user($f_existing_user_id);
                $f_user_id = $f_existing_user_id;
            }
            if ($Submit == "Search") {
                $affschools = new affiliations;
                $affschools->add_affiliation(1, 1, $aff_school[1], $aff_admin[1]);
                $usersearch = new users;
                $usersearch->find_users($f_user_id, $f_login, $f_first_name, $f_last_name, $f_type_id, $f_street, $f_city, $f_state, $f_zip, $f_phone, $f_referral_firstname, $f_referral_lastname, $affschools->affiliation_list, false, $f_email, $f_district_id, $f_company, $aff_admin[1]);
                if ($usersearch->count() == 0)
                    $message = "No matching users found.";
            } elseif ($Submit == "Cancel") {
                $f_user_id = 0;
                $f_existing_user_id = 0;
            } elseif ($Submit == "Update Note Changes") {
                $i = 1;
                $message = "";
                while ($i <= count($note_id)) {
                    if ($note_id[$i] > 0) {
                        // Update existing note.
                        $note = new user_note();
                        if ($note->load_user_note($note_id[$i])) {
                            if ($note_delete[$i] == "Y") {
                                // delete note.
                                if ($note->delete_user_note())
                                    $message .= "Note deleted.<BR>";
                                else
                                    $message .= "Delete failed.<BR>".$note->error_message;
                            } else {
                                $note->note = $note_text[$i];
                                $note->save_user_note();
                            }
                        } else {
                            $message .= "Unable to load note ".$note_id[$i]."<BR>";
                        }
                    } elseif (!empty($note_text[$i])) {
                        // Add a new note
                        $message .= "Adding a note.<BR>";
                        $note = new user_note();
                        $note->user_id = $user_rcd->user_id;
                        $note->entered_by = $User_ID;
                        $note->note = $note_text[$i];
                        $note->save_user_note();
                        $message .= $note->error_message;
                    }
                    $i += 1;
                }
                echo "<script type=\"text/javascript\">\nlocation.href='user_maint.php?user_id=$f_user_id&message=".htmlentities(urlencode($message))."'\n</script>";
                exit;
            } elseif ($Submit == "Delete") {
                if ($delete_confirm == "YES") {
                    $Submit = "";
                    $user_affiliations = new affiliations;
                    $user_affiliations->load_affiliations($user_rcd->user_id);
                    $user_affiliations->delete_affiliations();
                    if ($user_rcd->delete_user()) {
                        $message .= "User $f_user_id deleted.";
                        $f_user_id = 0;
                        $f_existing_user_id = 0;
                    } else
                        $message .= "<p>Errors occured deleting this user!<br>$user_rcd->error_message</p>";
                }
            } elseif ($Submit == "Save") {
                // Save user
                $user_rcd->login = $f_login;
                $user_rcd->password = $f_password;
                $user_rcd->first_name = $f_first_name;
                $user_rcd->last_name = $f_last_name;
                $user_rcd->company = $f_company;
                $user_rcd->street = $f_street;
                $user_rcd->city = $f_city;
                $user_rcd->state = $f_state;
                $user_rcd->country = $f_country;
                $user_rcd->zip = $f_zip;
                $user_rcd->email = $f_email;
                $user_rcd->verified = ($f_verified == "Y" ? "Y" : "N");
                $user_rcd->bad_email = ($f_bad_email=="Y") ? $f_bad_email : "N";
                $user_rcd->phone = $f_phone;
                $user_rcd->fax = $f_fax;
                $user_rcd->url = $f_url;
                $user_rcd->banner_link = $f_banner_link;
                $user_rcd->half_banner_link = $f_half_banner_link;
                $user_rcd->type_id = $f_type_id;
                $user_rcd->district_id = $f_district_id;
                $user_rcd->referral_firstname = $f_referral_firstname;
                $user_rcd->referral_lastname = $f_referral_lastname;
                $user_rcd->allow_matching = $f_allow_matching;
                $user_rcd->direct_donations = $f_direct_donations;
                $user_rcd->notes = $f_usernotes;
                if ($user_rcd->save_user()) {
                    # Now save affiliations.
                    $f_user_id = $user_rcd->user_id;
                    $f_existing_user_id = $user_rcd->user_id;
                    $user_affiliations = new affiliations;
                    $user_affiliations->load_affiliations($user_id);
                    $i = 1;
                    while ($i <= count($aff_school)) {
                        if ($aff_id[$i] != 0) {
                            if ($aff_school[$i] == 0) {
                                if (!$user_affiliations->delete_affiliation($aff_id[$i]))
                                    $message .= "Error deleting affiliation $aff_id[$i]: ".$user_affiliations->error_message."<BR>";
                            } elseif ($aff_oldschool[$i] != $aff_school[$i] || $aff_oldadmin[$i] != $aff_admin[$i]) {
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
                    $message .= "User $user_rcd->user_id saved.";
                } else
                    $message .= "<p>Errors occured saving this user!<br>$user_rcd->error_message</p>";
            }
        } else {
            $message .= "<p>Please correct the following items and then resubmit:<br>$errors</p>";
        }
    } else {
        if (!empty($user_id)) {
            $user_rcd = new user();
            if ($user_rcd->load_user($user_id)) {
                $f_user_id		= $user_rcd->user_id;
                $f_first_name	= $user_rcd->first_name;
                $f_last_name	= $user_rcd->last_name;
                $f_company		= $user_rcd->company;
                $f_street		= $user_rcd->street;
                $f_city			= $user_rcd->city;
                $f_state		= $user_rcd->state;
                $f_country		= $user_rcd->country;
                $f_zip			= $user_rcd->zip;
                $f_login		= $user_rcd->login;
                $f_password		= $user_rcd->password;
                $f_email		= $user_rcd->email;
                $f_verified     = $user_rcd->verified;
                $f_bad_email    = $user_rcd->bad_email;
                $f_phone		= $user_rcd->phone;
                $f_fax			= $user_rcd->fax;
                $f_url			= $user_rcd->url;
                $f_banner_link	= $user_rcd->banner_link;
                $f_half_banner_link	= $user_rcd->half_banner_link;
                $f_type_id		= $user_rcd->type_id;
                $f_district_id  = $user_rcd->district_id;
                $f_newsletter	= $user_rcd->newsletter;
                $f_email_verified = $user_rcd->email_verified;
                $f_opt_date		= $user_rcd->opt_date;
                $f_ip_address	= $user_rcd->ip_address;
                $f_notes		= $user_rcd->note_list;
                $f_usernotes    = $user_rcd->notes;
                $f_allow_matching 	= $user_rcd->allow_matching;
                $f_direct_donations	= $user_rcd->direct_donations;

                $f_referral_firstname	= $user_rcd->referral_firstname;
                $f_referral_lastname	= $user_rcd->referral_lastname;
                $user_affiliations = new affiliations;
                $user_affiliations->load_affiliations($user_id);
                $user_rcd->load_donation_list();
                $f_donation_list = $user_rcd->donation_list;
            } else {
                $f_user_id = "";
                $f_type_id = "0";
                $user_affiliations = new affiliations;
            }
        }
    }
?>
<html>
<head>
<?
    include "inc/cssstyle.php";
    $pagename = "$config_usermaint_page_name";
    $help_msg_name = "config_usermaint_help";
    $help_msg = "$config_usermaint_help";
    $help_width = "$config_usermaint_help_width";
    $help_height = "$config_usermaint_help_height";
    require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <td width="100%" align="left" valign="top">
<?
    if ($config_usermaint_banners == "Y") include "inc/banner_ads.php";
    echo "$config_usermaint_paragraph1";
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
                    <TABLE DIR=ltr ID="project_list" ALIGN=bleedright WIDTH="100%" COLS=4 BORDER="0" CELLSPACING="2" CELLPADDING="2">
                        <Form Name='frmDelete' Method='POST'>
                            <TR ALIGN="left" VALIGN="middle" bgcolor="<?=$color_table_hdg_bg;?>">
                                <TD Align='center' Colspan='2'><Font size='+1' color="<?=$color_table_hdg_font;?>">User Delete Confirmation</font></TD>
                            </TR>
                            <TR ALIGN="left" VALIGN="middle">
                                <TD Width='100' Align='Right'><b>User ID</b></TD>
                                <TD>
                                <?
                                echo "$f_user_id";
                                echo "<input type='hidden' name='f_existing_user_id' value='$f_user_id'>";
                                ?>
                                </TD>
                            </TR>
                            <TR ALIGN="left" VALIGN="middle">
                                <TD Width='100' Align='Right'><b>Login</b></TD>
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
<? if (!empty($f_company)) { ?>
                            <TR ALIGN="left" VALIGN="middle">
                                <TD Width='100' Align='Right'><b>Company</b></TD>
                                <TD><?=$f_company;?></TD>
                            </TR>
<? } ?>
                            <TR ALIGN="left" VALIGN="middle">
                                <TD Align='center' Colspan='2'>
                                    <Font Size='+1' Color='Red'><b>Are you sure you want to delete this user?</b></Font>
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
                    </TABLE>
                </td>
            </tr>
	</table>
        </td>
    </tr>
</table>
<?
	} else {
		if ($Submit == "Search" && $usersearch->count() > 0) {
?>
            <table width=100% cellspacing=1 cellpadding=1>
		<tr>
                    <td align=left width=50% bgcolor="<?=$color_table_hdg_bg;?>">
                        <TABLE DIR=ltr ID="project_list" ALIGN=bleedright WIDTH="100%" COLS=4 BORDER="0" CELLSPACING="2" CELLPADDING="2">
                            <? $cols = count($chkFields) ?>
                            <TR ALIGN="left" VALIGN="middle">
                                    <TD Align='center' Colspan='<?=$cols;?>'><Font size='+1' color="<?=$color_table_hdg_font;?>">User Search Results</font></TD>
                            </TR>
                            <TR ALIGN="left" VALIGN="middle">
                                    <TD Align='left' Colspan='<?=$cols;?>'><Font size='-1' color="<?=$color_table_hdg_font;?>">Click on the user's name to edit that user.</font></TD>
                            </TR>
                            <TR ALIGN="left" VALIGN="middle">
                            <? if (in_array("chk_user_id",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">UserID</font></TD>
                            <? } ?>
                            <? if (in_array("chk_login",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">Login</font></TD>
                            <? } ?>
                            <? if (in_array("chk_password",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">Password</font></TD>
                            <? } ?>
                            <? if (in_array("chk_first_name",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">First Name</font></TD>
                            <? } ?>
                            <? if (in_array("chk_last_name",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">Last Name</font></TD>
                            <? } ?>
                            <? if (in_array("chk_company",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">Company</font></TD>
                            <? } ?>
                            <? if (in_array("chk_street",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">Street</font></TD>
                            <? } ?>
                            <? if (in_array("chk_city",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">City</font></TD>
                            <? } ?>
                            <? if (in_array("chk_state",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">State</font></TD>
                            <? } ?>
                            <? if (in_array("chk_zip",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">Zip</font></TD>
                            <? } ?>
                            <? if (in_array("chk_country",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">Country</font></TD>
                            <? } ?>
                            <? if (in_array("chk_email",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">Email</font></TD>
                            <? } ?>
                            <? if (in_array("chk_phone",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">Phone</font></TD>
                            <? } ?>
                            <? if (in_array("chk_fax",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">Fax</font></TD>
                            <? } ?>
                            <? if (in_array("chk_url",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">URL</font></TD>
                            <? } ?>
                            <? if (in_array("chk_banner_link",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">Banner Link</font></TD>
                            <? } ?>
                            <? if (in_array("chk_half_banner_link",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">Half Banner Link</font></TD>
                            <? } ?>
                            <? if (in_array("chk_referrer",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">Referrer</font></TD>
                            <? } ?>
                            <? if (in_array("chk_newsletter",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">Newsletter</font></TD>
                            <? } ?>
                            <? if (in_array("chk_email_verified",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">Email Verified</font></TD>
                            <? } ?>
                            <? if (in_array("chk_type",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">Type</font></TD>
                            <? } ?>
                            <? if (in_array("notes",$chkFields)) { ?>
                            		<TD><Font
                            		color="<?=$color_table_hdg_font;?>">Notes</font></TD>
                            <? } ?>
                            <? if (in_array("chk_district",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">District</font></TD>
                            <? } ?>
                            <? if (in_array("chk_last_login",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">Last Login</font></TD>
                            <? } ?>
                            <? if (in_array("chk_affiliations",$chkFields)) { ?>
                                    <TD><Font color="<?=$color_table_hdg_font;?>">Affiliations</font></TD>
                            <? } ?>
                            </TR>
<?
			if(count($usersearch->user_list))
				reset($usersearch->user_list);
			$email_addresses = "";
			$existing_emails = "";
			# <adamsg@colton.k12.or.us>; <Adamst@colton.k12.or.us>; <aertsm@molallariv.k12.or.us>; <Sarah.Agee@orecity.k12.or.us>; <terry.ahlgrim@orecity.k12.or.us>; <aldermanj@estacada.k12.or.us>; <Steve.Allen@orecity.k12.or.us>; <allermac@ocsd.orecity.k12.or.us>; <cindy.allerman@orecity.k12.or.us>; <benjamin.altiero@orecity.k12.or.us>; <martha.amick@orecity.k12.or.us>; <andersoa@colton.k12.or.us>; <andersoc@molallariv.k12.or.us>; <andersone@estacada.k12.or.us>; <mary.anderson@orecity.k12.or.us>; <anderss1@ocsd.orecity.k12.or.us>; <siri.anderson@orecity.k12.or.us>; <heidilangton@yahoo.com>; <heidi.andre@orecity.k12.or.us>; <heidi.andre@orecity.k12.or.us>; <tami.archer@orecity.k12.or.us>; <armstrongj@estacada.k12.or.us>; <arnettm@estacada.k12.or.us>; <austina@estacada.k12.or.us>; <bakera@molallariv.k12.or.us>; <Deleen.Baker@orecity.k12.or.us>; <Hanne.Baker@orecity.k12.or.us>; <michael_baker@licnoln.k12.or.us>; <valerie.baker@lincoln.k12.or.us>; <julie.Balch@orecity.k12.or.us>; <tobeyba@oregoncity.k12.or.us>; <baleyt@orecity.k12.or.us>; <tbaley@orecity.k12.or.us>; <lynn.barry@orecity.k12.or.us>; <bartlettc@estacada.k12.or.us>; <william.bartman@orecity.k12.or.us>; <bartmanw@ocsd.orecity.k12.or.us>; <tammy.baty@lincoln.k12.or.us>; <jody.bean@orecity.k12.or.us>; <cami.beatty@orecity.k12.or.us>; <beckb@ocsd.orecity.k12.or.us>; <borden.beck@orecity.k12.or.us>; <borden.beck@orecity.k12.or.us>; <steven.becker@orecity.k12.or.us>; <kristan_beckwith@orecity.k12.or.us>; <jill.bedortha@orecity.k12.or.us>; <behnkeb@estacada.k12.or.us>; <bendelem@colton.k12.or.us>; <obendele@colton.k12.or.us>; <racer.az@hotmail.com>; <berginr@molallariv.k12.or.us>; <nicole.bernardi@lincoln.k12.or.us>; <dorothy.berry@orecity.k12.or.us>; <birchb@colton.k12.or.us>; <blaird@molallariv.k12.or.us>; <tracy.blakeman@lincoln.k12.or.us>; <michelle.blanchard@orecity.k12.or.us>; <caren.blass@orecity.k12.or.us>; <bledsoeg@molallariv.k12.or.us>; <Tim.Bless@orecity.k12.or.us>; <bryan.blix@orecity.k12.or.us>; <shenais.bock@orecity.k12.or.us>; <beth.bollinger@orecity.k12.or.us>; <karmay.kegg@orecity.k12.or.us>; <cyndi.borgmeier@orecity.k12.or.us>; <botsforl@molallariv.k12.or.us>; <bouckv@colton.k12.or.us>; <Scott.Boxell@orecity.k12.or.us>; <mona.boyd@crookcounty.k12.or.us>; <brauckmj@molallariv.k12.or.us>; <maryellenbray@msn.com>; <matt.briggs@lincoln.k12.or.us>; <matthew.briggs@lincoln.k12.or.us>; <shannon.brogden@orecity.k12.or.us>; <brokawe@estacada.k12.or.us>; <molly.brooks@lincoln.k12.or.us>; <brownc@estacada.k12.or.us>; <kristina.brown@orecity.k12.or.us>; <melinda.brown@crookcounty.k12.or.us>; <Sally.Brown@orecity.k12.or.us>; <yvette.brown@orecity.k12.or.us>; <jamie.browning@orecity.k12.or.us>; <browning.jamie@orecity.k12.or.us>; <browning@colton.k12.or.us>; <brunera@colton.k12.or.us>; <buck-oldsb@estacada.k12.or.us>; <bucklesc@estacada.k12.or.us>; <buehlerm@colton.k12.or.us>; <michelle.bugni@orecity.k12.or.us>; <michellebu.bugni@orecity.k12.or.us>; <herb@estacada.k12.or.us>; <burdal@molallariv.k12.or.us>; <William.Burel@orecity.k12.or.us>; <mindy.burel@orecity.k12.or.us>; <Mary.Burgin@orecity.k12.or.us>; <burkek@colton.k12.or.us>; <burnsj@colton.k12.or.us>; <burnsj@colton.k12.or.us>; <angela.busenbark@orecity.k12.or.us>; <laura.bush@orecity.k12.or.us>; <nancy.bush-lange@orecity.k12.or.us>; <mary.butler@orecity.k12.or.us>; <amy.calavan@lincoln.k12.or.us>; <calderl@molallariv.k12.or.us>; <rae@springwaterschool.com>; <Andy.Canales-Reyes@orecity.k12.or.us>; <andres.canalesreyes@orecity.k12.or.us>; <jenmarie@springwaterschool.com>; <angella.carey@orecity.k12.or.us>; <matt.carlson@orecity.k12.or.us>; <carltona@estacada.k12.or.us>; <carriggs@molallariv.k12.or.us>; <cori.carroll@orecity.k12.or.us>; <jenny.carroll@lincoln.k12.or.us>; <christina.case@orecity.k12.or.us>; <javier.castaneda@orecity.k12.or.us>; <casterc1@colton.k12.or.us>; <linda.cella@orecity.k12.or.us>; Craig Cervantes <cervantc@colton.k12.or.us>; <coastgriz@yahoo.com>; <karen.chadwick@orecity.k12.or.us>; <chancellork@estacada.k12.or.us>; <chandlel@ocsd.orecity.k12.or.us>; <Rose.Chapin@orecity.k12.or.us>; <chapmanc@colton.k12.or.us>; <debra.chase@orecity.k12.or.us>; <christis@estacada.k12.or.us>; <cj.church@orecity.k12.or.us>; <gregory.cimmiyotti@orecity.k12.or.us>; <Byron.Clark@orecity.k12.or.us>; <byron.clark@orecity.k12.or.us>; <julie.clark@lincoln.k12.or.us>; <stephanie.clark@orecity.k12.or.us>; <chelsea.clay@orecity.k12.or.us>; <Pauline.Clegg@orecity.k12.or.us>; <Bonnieclement@orecity.k12.or.us>; <lisa.clingan@orecity.k12.or.us>; <cochrant@estacada.k12.or.us>; <ccockrell@orecity.k12.or.us>; <colemank@colton.k12.or.us>; <chris.collard@orecity.k12.or.us>; <renee.collins@orecity.k12.or.us>; <pj.collson@lincoln.k12.or.us>; <lin.colwell@lincoln.k12.or.us>; <jerry.conrady@lincoln.k12.or.us>; <Jim.Contois@lincoln.k12.or.us>; <karenco@orecity.k12.or.us>; <Terry.Cooper@orecity.k12.or.us>; <coppingp@molallariv.k12.or.us>; <tociaco@orecity.k12.or.us>; <teecia.cornelius@lincoln.k12.or.us>; <Debra.Cox@orecity.k12.or.us>; <coxl@estacada.k12.or.us>; <cindy.coy@orecity.k12.or.us>; <jcoyle@orecity.k12.or.us>; <bruce.cramer@orecity.k12.or.us>; <CraneT@colton.k12.or.us>; <beth.cross@orecity.k12.or.us>; <crowster2000@hotmail.com>; <mike.crowe@lincoln.k12.or.us>;Mike Crowe <mike.crowe@lincoln.k12.or.us>; <allison.currier@orecity.k12.or.us>; <curtemanm@colton.k12.or.us>; <dave.dahlberg@lincoln.k12.or.us>; <heidi.dahlin@orecity.k12.or.us>; <Jeff.Dahlin@orecity.k12.or.us>; <Elizabeth.Damon@orecity.k12.or.us>; <sherril_daniels@orecity.k12.or.us>; <Marianne.Davey@orecity.k12.or.us>; <davidsonl@estacada.k12.or.us>; <todd.davidson@lincoln.k12.or.us>; <davidsonz@estacada.k12.or.us>; <Chris.Davies@orecity.k12.or.us>; <chrystna.davis@lincoln.k12.or.us>; <davisj@colton.k12.or.us>; <maureen@teleport.com>; <Maureen.Davis@orecity.k12.or.us>; <Sue.debelloy@lincoln.k12.or.us>; <MaryAnn.Decker@orecity.k12.or.us>; <dedlowv@estacada.k12.or.us>; <dellerd@molallariv.k12.or.us>; <denneyj@colton.k12.or.us>; <Joellen.Deverell@orecity.k12.or.us>; <Anthonyd@AlbertinaKerr.org>; ";
			while (list($userid, $suser) = each($usersearch->user_list))
                        {
                            if (!strstr($email_addresses, $suser->email) && $suser->bad_email == "N")
                            {
                                if (!strstr($existing_emails, $suser->email)) {
                                    if (!empty($email_addresses)) $email_addresses .= ";";
                                    $email_addresses .= $suser->email;
                                }
                            }
                            echo "\t\t\t\t<TR>\n";
                            if (in_array("chk_user_id",$chkFields))
                                echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">$suser->user_id</a></TD>\n";
                            if (in_array("chk_login",$chkFields))
                                echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">$suser->login</a></TD>\n";
                            if (in_array("chk_password",$chkFields))
                                echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">".text_decrypt($suser->password)."</a></TD>\n";
                            if (in_array("chk_first_name",$chkFields))
                                echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">$suser->first_name</a></TD>\n";
                            if (in_array("chk_last_name",$chkFields))
                                echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">$suser->last_name</a></TD>\n";
                            if (in_array("chk_company",$chkFields))
                                echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">$suser->company</a></TD>\n";
                            if (in_array("chk_street",$chkFields))
                                echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">$suser->street</a></TD>\n";
                            if (in_array("chk_city",$chkFields))
                                echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">$suser->city</a></TD>\n";
                            if (in_array("chk_state",$chkFields))
                                echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">$suser->state</a></TD>\n";
                            if (in_array("chk_zip",$chkFields))
                                echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">$suser->zip</a></TD>\n";
                            if (in_array("chk_country",$chkFields))
                                echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">$suser->country</a></TD>\n";
                            if (in_array("chk_email",$chkFields))
                                echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">$suser->email</a></TD>\n";
                            if (in_array("chk_phone",$chkFields))
                                echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">$suser->phone</a></TD>\n";
                            if (in_array("chk_fax",$chkFields))
                                echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">$suser->fax</a></TD>\n";
                            if (in_array("chk_url",$chkFields))
                                echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">$suser->url</a></TD>\n";
                            if (in_array("chk_banner_link",$chkFields))
                                echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">$suser->banner_link</a></TD>\n";
                            if (in_array("chk_half_banner_link",$chkFields))
                                echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">$suser->half_banner_link</a></TD>\n";
                            if (in_array("chk_referrer",$chkFields)) {
                                echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">";
                                if ($suser->referral_firstname.$suser->referral_lastname != "")
                                    echo "$suser->referral_firstname $suser->referral_lastname".($suser->referral_schoolid ? "<BR>".$schools->school_name($suser->referral_schoolid) : "");
                                else
                                    echo ($suser->referral_schoolid ? $schools->school_name($suser->referral_schoolid) : "");
                                echo "</a></TD>\n";
                            }
                            if (in_array("chk_newsletter",$chkFields))
                                echo "\t\t\t\t\t<TD ALIGN=\"center\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">$suser->newsletter</a></TD>\n";
                            if (in_array("chk_email_verified",$chkFields))
                                echo "\t\t\t\t\t<TD ALIGN=\"center\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">$suser->email_verified</a></TD>\n";
                            if (in_array("chk_type",$chkFields))
                                echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">".$usertypes->user_type_description($suser->type_id)."</a></TD>\n";
                            if (in_array("chk_district",$chkFields))
                                echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">".$districts->district_name($suser->district_id)."</a></TD>\n";
                            if (in_array("chk_last_login",$chkFields))
                                echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">".$suser->last_login()."</a></TD>\n";
                            if (in_array("chk_affiliations",$chkFields)) {
                                echo "\t\t\t\t\t<TD ALIGN=\"left\" VALIGN=\"top\" BGCOLOR=\"$color_table_col_bg\"><a href=\"user_maint.php?user_id=$suser->user_id\">";
                                while (list($affid, $affiliation) = each($user_affiliations->affiliation_list)) {
                                    echo $schools->school_name($affiliation->school_id).($affiliation->admin_flag ? "(Admin)" : "");
                                }
                                echo "</a></TD>\n";
                            }
                            echo "\t\t\t\t</TR>\n";
			}
                        echo "<form method=\"Post\" action=\"user_email.php\">";
			echo "<TR><TD Colspan=\"100%\"><table width=\"100%\"><tr><td>Email Addresses for selected users:</td><td align=\"right\"><input type=\"submit\" value=\"Send Email\"></td></tr></table></td></tr>";
                        echo "<TR><TD Colspan=\"100%\"><textarea name=\"email_addresses\" rows=\"6\" cols=\"90\">$email_addresses</textarea></td></tr>";
                        echo "</form>";
?>
                        </table>
                    </td>
                </tr>
            </table>
<?
		} else {
			if (!$chkFields)
				$chkFields = array("chk_user_id","chk_login","chk_first_name","chk_last_name","chk_last_login","chk_district","chk_type","chk_email");
?>
<table width=100% cellspacing=1 cellpadding=1>
  <TR ALIGN="left" VALIGN="middle">
	<td align=left width=50% bgcolor="<?=$color_table_hdg_bg;?>">
	<table width=100% cellspacing=1 cellpadding=1>
		<TR ALIGN="left" VALIGN="middle">
			<TD Align='center' bgcolor="<?=$color_table_hdg_bg;?>"><font size="+1" color="<?=$color_table_hdg_font;?>">User Maintenance</font></TD>
		</TR>
		<TR ALIGN="left" VALIGN="middle">
			<TD Align='center' bgcolor="<?=$color_table_hdg_bg;?>"><font size="-1" color="<?=$color_table_hdg_font;?>">Enter a new user and click <b>Save</b>, or enter search criteria and click <b>Search</b>.</font></TD>
		</TR>
		<tr>
			<td align=left width=50% bgcolor="<?=$color_table_col_bg;?>">
				<TABLE ALIGN="left" BORDER=0 CELLSPACING=10 CELLPADDING=0 WIDTH="100%">
					<Form Name='frmUser' Method='POST'>
					<TR ALIGN="left" VALIGN="middle">
						<? 	if (empty($f_user_id)) { ?>
						<TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_user_id"<?=(in_array("chk_user_id",$chkFields) ? " Checked":"");?>></TD>
						<? } ?>
						<TD Width='20%' Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>User ID</b></TD>
						<TD bgcolor="<?=$color_table_col_bg;?>">
							<? 	if (empty($f_user_id))
									echo "<input type='text' name='f_user_id' size=5>";
								else {
									echo "<B>$f_user_id</B>";
									echo "<input type='hidden' name='f_existing_user_id' value='$f_user_id'>";
								}
							?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
                                            <? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_login" Checked></TD>
                                            <? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>Login</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='20' maxlength='50' name='f_login' value='<?=$f_login;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
                                            <? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_password"<?=(in_array("chk_password",$chkFields) ? " Checked":"");?>></TD>
                                            <? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>Password</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='20' maxlength='100' name='f_password' value='<?=$f_password;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
                                            <? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_first_name" Checked></TD>
                                            <? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>First Name</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='30' maxlength='50' name='f_first_name' value='<?=$f_first_name;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
                                            <? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_last_name" Checked></TD>
                                            <? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>Last Name</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='30' maxlength='50' name='f_last_name' value='<?=$f_last_name;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
                                            <? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_company"<?=(in_array("chk_company",$chkFields) ? " Checked":"");?>></TD>
                                            <? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>Company</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='30' maxlength='80' name='f_company' value='<?=$f_company;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
                                            <? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_street"<?=(in_array("chk_street",$chkFields) ? " Checked":"");?>></TD>
                                            <? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>Street</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='30' maxlength='60' name='f_street' value='<?=$f_street;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
                                            <? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_city"<?=(in_array("chk_city",$chkFields) ? " Checked":"");?>></TD>
                                            <? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>City</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='30' maxlength='30' name='f_city' value='<?=$f_city;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
                                            <? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_state"<?=(in_array("chk_state",$chkFields) ? " Checked":"");?>></TD>
                                            <? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>State</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>"><Select name='f_state'>
<?
			#if (empty($f_state))
			#	$f_state = $config_default_state;
			if(count($states->state_list))
			{
				reset($states->state_list);
				$prev_grp = "";
				while (list($statecode, $state) = each($states->state_list))
				{
					if ($state->state_group != $prev_grp) {
						if (!empty($prev_grp))
							echo "</optgroup>";
						$prev_grp = $state->state_group;
						echo ("\t\t\t\t\t\t\t\t\t\t<optgroup label=\"$state->state_group\">\n");
					}
					echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"$statecode\"".($statecode == $f_state ? " SELECTED" : "").">$state->state_name</OPTION>\n");
				}
			}
			if (!empty($prev_grp))
				echo "</optgroup>";
?>
                                                </select>
                                            </TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
                                            <? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_zip"<?=(in_array("chk_zip",$chkFields) ? " Checked":"");?>></TD>
                                            <? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>Zip</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='10' maxlength='10' name='f_zip' value='<?=$f_zip;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
                                            <? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_country"<?=(in_array("chk_country",$chkFields) ? " Checked":"");?>></TD>
                                            <? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>Country</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>"><Select name='f_country'>
<?
			if (empty($f_country))
            	$f_country = $config_default_country;
            if(count($country->country_list))
            {
				reset($country->country_list);
				$prev_grp = "";
				while (list($country_code, $country) = each($countries->country_list))
				{
					echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"$country_code\"".($country_code == $f_country ? " SELECTED" : "").">$country->country_name</OPTION>\n");
				}
			}
?>
                                                </select>
                                            </TD>
					</TR>

					<TR ALIGN="left" VALIGN="middle">
                                            <? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_email"<?=(in_array("chk_email",$chkFields) ? " Checked":"");?>></TD>
                                            <? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>Email</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='60' maxlength='100' name='f_email' value='<?=$f_email;?>'><input type="checkbox" name="f_bad_email" value="Y" <?=($f_bad_email=="Y"?" Checked":"")?>>Bad Email</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
                                            <? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_verified"<?=(in_array("chk_verified",$chkFields) ? " Checked":"");?>></TD>
                                            <? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>Verified</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>"><input type='checkbox' name='f_verified' value='Y'<?=($f_verified=="Y"?" Checked":"");?>></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
                                            <? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_phone"<?=(in_array("chk_phone",$chkFields) ? " Checked":"");?>></TD>
                                            <? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>Phone</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='20' maxlength='50' name='f_phone' value='<?=$f_phone;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
                                            <? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_fax"<?=(in_array("chk_fax",$chkFields) ? " Checked":"");?>></TD>
                                            <? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>Fax</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='20' maxlength='50' name='f_fax' value='<?=$f_fax;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
                                            <? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_url"<?=(in_array("chk_url",$chkFields) ? " Checked":"");?>></TD>
                                            <? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>URL</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='40' maxlength='255' name='f_url' value='<?=$f_url;?>'></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
                                            <? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_banner_link"<?=(in_array("chk_banner_link",$chkFields) ? " Checked":"");?>></TD>
                                            <? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>Banner Link</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='40' maxlength='255' name='f_banner_link' value='<?=$f_banner_link;?>'>
                                            </TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
                                            <? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_half_banner_link"<?=(in_array("chk_half_banner_link",$chkFields) ? " Checked":"");?>></TD>
                                            <? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>Half Banner Link</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>"><input type='text' size='40' maxlength='255' name='f_half_banner_link' value='<?=$f_half_banner_link;?>'>
                                            </TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
<? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_referrer"<?=(in_array("chk_referrer",$chkFields) ? " Checked":"");?>></TD>
<? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>Referrer</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>">
                                                    <TABLE>
                                                    <tr><td><b>First Name</b></td><td>
                                                    <input type='text' size='10' maxlength='25' name='f_referral_firstname' value='<?=$f_referral_firstname;?>'>
                                                    </td></tr>
                                                    <tr><td><b>Last Name</b></td><td>
                                                    <input type='text' size='20' maxlength='40' name='f_referral_lastname' value='<?=$f_referral_lastname;?>'>
                                                    </td></tr>
                                                    </Table>
                                            </TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
<? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_allow_matching"<?=(in_array("chk_allow_matching",$chkFields) ? " Checked":"");?>></TD>
<? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>Allow Matching</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>">
                                                <Select name='f_allow_matching'>
                                                <option value=''>&nbsp;</option>
                                                <option value='Y' <?=($f_allow_matching == "Y" ? " Selected":"");?>>Yes</option>
                                                <option value='N' <?=($f_allow_matching == "N" ? " Selected":"");?>>No</option>
                                                </select>
                                            </TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
<? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_direct_donations"<?=(in_array("chk_direct_donations",$chkFields) ? " Checked":"");?>></TD>
<? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>Direct Donations</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>">
                                                <Select name='f_direct_donations'>
                                                <option value=''>&nbsp;</option>
                                                <option value='Y' <?=($f_direct_donations == "Y" ? " Selected":"");?>>Yes</option>
                                                <option value='N' <?=($f_direct_donations == "N" ? " Selected":"");?>>No</option>
                                                </select>
                                            </TD>
					</TR>

					<TR ALIGN="left" VALIGN="middle">
<? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_newsletter"<?=(in_array("chk_newsletter",$chkFields) ? " Checked":"");?>></TD>
<? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>Newsletter</b></TD>
<? 	if (empty($f_user_id)) { ?>
                                            <TD bgcolor="<?=$color_table_col_bg;?>">
                                                <Select name='f_newsletter'>
                                                <option value=''>&nbsp;</option>
                                                <option value='Y'>Yes</option>
                                                <option value='N'>No</option>
                                                </select>
                                            </TD>
					</TR>
<?	} else { ?>
                                            <TD bgcolor="<?=$color_table_col_bg;?>"><b><?=$f_newsletter;?></b></TD>
					</TR>
<? } ?>

					<TR ALIGN="left" VALIGN="middle">
<? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_email_verified"<?=(in_array("chk_email_verified",$chkFields) ? " Checked":"");?>></TD>
<? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>Email Verified</b></TD>
<? 	if (empty($f_user_id)) { ?>
                                            <TD bgcolor="<?=$color_table_col_bg;?>">
                                                <Select name='f_email_verified'>
                                                <option value=''>&nbsp;</option>
                                                <option value='Y'>Yes</option>
                                                <option value='N'>No</option>
                                                </select>
                                            </TD>
					</TR>
<?	} else { ?>
                                            <TD bgcolor="<?=$color_table_col_bg;?>"><b><?=$f_email_verified;?></b></TD>
					</TR>
<? } ?>
					<TR ALIGN="left" VALIGN="middle">
<? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_ip_address"<?=(in_array("chk_ip_address",$chkFields) ? " Checked":"");?>></TD>
<? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>IP Address</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>"><b><?=$f_ip_address;?></b></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
<? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_opt_date"<?=(in_array("chk_opt_date",$chkFields) ? " Checked":"");?>></TD>
<? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>Date Set</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>"><b><?=($f_opt_date == ""?"":date("m/d/Y h:i:s A",strtotime($f_opt_date)));?></b></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
<? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_type"<?=(in_array("chk_type",$chkFields) ? " Checked":"");?>></TD>
<? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>*</font>&nbsp;<b>Type</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>">
                                                <table border=0 cellpadding=0 cellspacing=0>
                                                    <tr valign='bottom'>
                                                        <td valign='bottom'>
                                                            <SELECT NAME="f_type_id" SIZE=1>
<?
    echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"0\"></OPTION>\n");
    if(count($usertypes->user_type_list))
    {
		reset($usertypes->user_type_list);
		while (list($typeid, $usertype) = each($usertypes->user_type_list))
		{
				echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"$usertype->user_type_id\"".($usertype->user_type_id == $f_type_id ? " SELECTED" : "") .">$usertype->user_type_description</OPTION>\n");
		}
	}
?>
                                                            </SELECT>
                                                        </td>
                                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                                    </tr>
                                                </table>
                                            </TD>
					</TR>
<? 	if (!empty($f_user_id)) { ?>
                    <TR ALIGN="left" VALIGN="middle">
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><b>Notes</b></TD>
                                            <TD Align='left' bgcolor="<?=$color_table_col_bg;?>">
							<TEXTAREA NAME="f_usernotes" ROWS="10" COLS="65"><?=$f_usernotes;?></TEXTAREA>

                                            </TD>
					</TR>
<? } ?>
                    <TR ALIGN="left" VALIGN="middle">
<? 	if (empty($f_user_id)) { ?>
                                            <TD>&nbsp;</TD>
<? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>">&nbsp;</TD>
                                            </TD>
                                            <TD Align='left' bgcolor="<?=$color_table_col_bg;?>">
                                                <small><b>Donors</b> will not have any Affiliations. Teachers and Principles that review projects should have a
                                                type of <b>Teacher</b>. A <b>Site Administrator</b> has access to the User Maintenance. A <b>Site Support</b> has
                                                access to User Maintenance and Site Configuration.</small>
                                            </TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
<? 	if (empty($f_user_id)) { ?>
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_district"<?=(in_array("chk_district",$chkFields) ? " Checked":"");?>></TD>
<? } ?>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>District</b></TD>
                                            <TD bgcolor="<?=$color_table_col_bg;?>">
                                                <table border=0 cellpadding=0 cellspacing=0>
                                                    <tr valign='bottom'>
                                                        <td valign='bottom'>
                                                            <SELECT NAME="f_district_id" SIZE=1>
<?
    echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"-1\"></OPTION>\n");
    echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"0\">No District</OPTION>\n");
    echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"ACTIVE\"".($f_district_id == "ACTIVE" ? " SELECTED" : "") .">Active Districts</OPTION>\n");
    echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"INACTIVE\"".($f_district_id == "INACTIVE" ? " SELECTED" : "") .">Inactive Districts</OPTION>\n");
    if(count($districts->district_list))
    {
		reset($districts->district_list);
		while (list($districtid, $district) = each($districts->district_list)) {
				echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"$district->district_id\"".($district->district_id == $f_district_id ? " SELECTED" : "") .">$district->district_name</OPTION>\n");
		}
	}
?>
                                                            </SELECT>
                                                        </td>
                                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                                    </tr>
                                                </table>
                                            </TD>
					</TR>
<? 	if (empty($f_user_id)) { ?>
					<TR ALIGN="left" VALIGN="middle">
                                            <TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_last_login"<?=(in_array("chk_last_login",$chkFields) ? " Checked":"");?>></TD>
                                            <TD Align='Right' bgcolor="<?=$color_table_col_bg;?>"><font color='red'>&nbsp;</font>&nbsp;<b>Last Login</b></TD>
                                            <TD></TD>
					</TR>
<? } ?>
					<TR ALIGN="left" VALIGN="middle">
<? 	if (empty($f_user_id)) { ?>
						<TD><Input Type="Checkbox" Name="chkFields[]" Value="chk_affiliations" /></TD>
<? } ?>
						<TD Align='left' Colspan='2' bgcolor="<?=$color_table_col_bg;?>">
							<table width=100% cellspacing=1 cellpadding=1>
							  <TR ALIGN="left" VALIGN="middle">
								<td align=left width=50% bgcolor="<?=$color_table_hdg_bg;?>">

<?	# List of affilations
                echo "<table width=100% border=0>\n\t<tr>\n";
                echo "\t\t<td align='center' colspan='3' bgcolor=\"$color_table_hdg_bg\"><font size='+1' color=\"$color_table_hdg_font\">Affiliations</font></TD>\n";
                echo "\t</TR>\n";
                echo "\t<TR>\n";
                echo "\t\t<TD Colspan='3' bgcolor=\"$color_table_hdg_bg\"><font color=\"$color_table_hdg_font\">Schools that this user may submit or reviews projects for.</font></TD>\n";
                echo "\t</TR>\n";
                echo "\t<TR>\n";
                echo "\t\t<TH VAlign=\"Bottom\" bgcolor=\"$color_table_col_bg\">School</TH>\n";
                echo "\t\t<TH VAlign=\"Bottom\" bgcolor=\"$color_table_col_bg\">Admin</TH>\n";
                echo "\t\t<TH VAlign=\"Bottom\" bgcolor=\"$color_table_col_bg\">&nbsp;</TH>\n";
                echo "\t</TR>\n";
                $i = 1;
                if (count($user_affiliations->affiliation_list) > 0) {
                    while (list($affid, $affiliation) = each($user_affiliations->affiliation_list))
                    {
                        echo "\t<TR>\n";
                        echo "\t\t<TD VAlign=\"Top\" bgcolor=\"$color_table_col_bg\">\n";
                        echo "\t\t<input type='hidden' name='aff_id[$i]' value='$affiliation->affiliation_id'>\n";
                        echo "\t\t<input type='hidden' name='aff_oldschool[$i]' value='$affiliation->school_id'>\n";
                        echo "\t\t<input type='hidden' name='aff_oldadmin[$i]' value='$affiliation->admin_flag'>\n";
                        echo "\t\t<Select name='aff_school[$i]'>\n";
                        echo "\t\t\t<Option value='0'></Option>\n";
                        if(count($schools->school_list))
                        {
							reset($schools->school_list);
							while (list($schoolid, $school) = each($schools->school_list))
								echo "\t\t\t<Option value='$school->school_id'".($affiliation->school_id == $school->school_id ? " SELECTED":"").">$school->school_name</option>\n";
						}
						echo "\t\t</SELECT>\n";
						echo "\t\t</TD>\n";
						echo "\t\t<TD VAlign='middle' bgcolor=\"$color_table_col_bg\"><INPUT Type='Checkbox' Value = 'Y' Name='aff_admin[$i]'".($affiliation->admin_flag == "Y" ? " CHECKED":"")."></TD>\n";
						if ($i == 1)
							echo "\t\t<TD valign='top' rowspan='".(count($user_affiliations->affiliation_list) + 1)."' bgcolor=\"$color_table_col_bg\">Admin flag indicates this user will review projects submitted for the<br>school indicated.</TD>\n";
						echo "\t</TR>";
						$i += 1;
					}
				}
                echo "\t<TR>\n";
                echo "\t\t<TD bgcolor=\"$color_table_col_bg\">\n";
                echo "\t\t\t<input type='hidden' name='aff_id[$i]' value='0'>\n";
                echo "\t\t\t<Select name='aff_school[$i]'>\n";
                echo "\t\t\t\t<Option value='0'></Option>\n";
                if(count($schools->school_list))
                {
					reset($schools->school_list);
					while (list($schoolid, $school) = each($schools->school_list))
                        echo "\t\t\t\t<Option value='$school->school_id'>$school->school_name</option>\n";
                }
					echo "\t\t\t</select>\n";
					echo "\t\t</TD>\n";
					echo "\t\t<TD  VAlign='Top' bgcolor=\"$color_table_col_bg\"><INPUT Type='Checkbox' Value='Y' Name='aff_admin[$i]'></TD>\n";
					if ($i == 1)
					echo "\t\t<TD valign='top' bgcolor=\"$color_table_col_bg\">Admin flag indicates this user will<br>review projects submitted for the<br>school indicated.</TD>\n";
					echo "\t</TR>\n";
					echo "</TABLE>\n";
?>
                                            </td>
                                          </tr>
                                        </table>
                                        </TD>
                                    </TR>
<?	if (!empty($f_user_id)) {
                                    include "inc/box_end.htm";
?>
                                    <TR ALIGN="left" VALIGN="middle">
                                        <TD Align='left' Colspan='2' bgcolor="<?=$color_table_col_bg;?>">
                                            <A href="login_history.php?user_id=<?=$f_user_id;?>">Login History</A>&nbsp;|&nbsp;
<?	$user_projects = new projects();
	$user_projects->load_projects(0, '', '', 0, $f_user_id, '', '', '', '');
	echo "\t\t\t\t\t\t\t<A href=\"project_list.php?f_author_id=$f_user_id\">".count($user_projects->project_list)." Projects</A>&nbsp;|&nbsp;";
	echo "\t\t\t\t\t\t\t<A href=\"donation_history.php?userid=$f_user_id\">".count($f_donation_list)." Donations</A>";

?>
                                        </TD>
                                    </TR>
<?	}	?>
                                    <TR ALIGN="left" VALIGN="middle">
                                        <? 	if (empty($f_user_id)) { ?>
                                        <TD>&nbsp;</TD>
                                        <? } ?>
                                        <TD Align='left' Colspan='2' bgcolor="<?=$color_table_col_bg;?>">
<?
			if (empty($f_user_id))
				echo "<Input Type='Submit' Class='nicebtns' Name='Submit' Value='Search'>\n|&nbsp;&nbsp;";
			echo "<Input Type='Submit' Class='nicebtns' Name='Submit' Value='Save'>&nbsp;&nbsp;\n";
			if (!empty($f_user_id))
				echo "|&nbsp;&nbsp;<Input Type='Submit' Class='nicebtns' Name='Submit' Value='Delete'>&nbsp;&nbsp;\n";
?>
                                        </TD>
                                </TR>
                                </Form>
<? 	if (!empty($f_user_id)) {
                                # List of Notes
                                echo "\t<TR ALIGN=\"left\" VALIGN=\"middle\">";
                                echo "<TD Align='center' Colspan=2>\n";
                                include "inc/box_begin.htm";
                                echo "\t<table border='0' width='100%'>\n";
                                echo "\t\t<tr><td align='center' colspan='4' bgcolor=\"$color_table_hdg_bg\"><font size='+1' color=\"$color_table_hdg_font\"><b><font size='+1'>Internal Notes</font></b></td></tr>\n";
                                echo "\t\t<tr><td colspan='4' align='center'><hl></td></tr>\n";
                                echo "\t\t<tr><td valign='bottom' width='10%'><b>Entered<br>By</d></td><td align='center' valign='bottom'><b>Entry<BR>Date</b></td><td align='center' valign='bottom' width='90%'><BR><b>Note</b></td><td align='center' valign='bottom' width='10%'><BR><b>Delete</b></td></tr>\n";
                                $i = 1;
                                if(count($f_notes))
                                {
									reset($f_notes);
									while (list($noteid, $note)= each($f_notes))
									{
										echo "\t\t<tr><td width='10%'>".$note->author_name();
										echo "</td><td align='center'>".date("m/d/Y", strtotime($note->date_entered))."</td><td align='left' width='90%'>";
										echo "<textarea name='note_text[$i]' rows='3' cols='60'>".$note->note."</textarea>";
										echo "<input type='hidden' name='note_id[$i]' value='$note->user_note_id'></td><td align='center'><input type='checkbox' name='note_delete[$i]' value='Y'></td>";
										echo "</tr>\n";
										$i += 1;
									}
								}
                                echo "\t\t<tr><td width='10%'><input type='hidden' name='note_id[$i] value='0'><input type='hidden' name='note_entered_by[$i]' value='$User_ID'></td><td align='center'>New</td><td align='left'>";
                                echo "<textarea name='note_text[$i]' rows='3' cols='60'></textarea>";
                                echo "<input type='hidden' name='note_id[$i]' value='0'></td><td>&nbsp;</td>";
                                echo "<tr><td align='center' colspan='6'><Input Type='Submit' Name='Submit' class='nicebtns' Value='Update Note Changes'></td></tr>";
                                echo "\t</table>\n";
                                echo "\t</TD></TR>\n";
?>
                                <TR ALIGN="left" VALIGN="middle">
                                    <TD COLSPAN=<? echo(empty($f_user_id) ? "3" : "2"); ?>>
<?		include "inc/box_begin.htm"; ?>
                                        <TABLE>
                                        <TR>
                                            <TD>
                                            <B>Banner Upload</B>
                                            </TD>
                                        </TR>
                                        <TR ALIGN="left" VALIGN="middle">
                                            <TD>
                                                <? if (!empty($f_user_id)) { ?>
                                                <FORM ENCTYPE="multipart/form-data" ACTION="" METHOD=POST>
                                                <input type=hidden name=MAX_FILE_SIZE value=250000>
                                                <BR>Upload this file: <INPUT NAME="imgfile" TYPE="file" SIZE="60">
                                                <INPUT TYPE="Hidden" NAME="f_user_id" VALUE="<?=$f_user_id;?>">
                                                <INPUT TYPE="Hidden" NAME="f_login" VALUE="<?=$f_login;?>">
                                                <INPUT TYPE="Submit" NAME="Submit" VALUE="Upload File">
                                                </FORM>
                                                <? } ?>
                                            </TD>
                                        </TR>
                                        </TABLE>
<?		include "inc/box_end.htm"; ?>
                                    </TD>
                                </TR>
                                <? } ?>
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
