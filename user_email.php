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

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        // Validate the fields.
        $errors = "";
        if (empty($email_addresses))
            $errors .= (empty($errors) ? "" : "<BR>")."Users with email addresses are required.";
        if (empty($errors)) {
            if ($Submit == "Send")
            {
                // Send an email to each user using the supplied email template.
                $emails = split(";", $email_addresses);
                foreach ($emails as $key => $email)
                {
                   if ($email)
                   {
                        $user_data = new users();
                        $user_data->email_users($email);
                        reset($user_data->user_list);
                        while(list($uid, $euser) = each($user_data->user_list))
                        {
                            $body = preg_replace("%LOGINID", $euser->login, $f_body);
                            $body = preg_replace("%PASSWORD", text_decrypt($euser->password), $body);
                            if(!$euser->first_name && !$euser->last_name)
                                $body = preg_replace("%NAME", $euser->company, $body);
                            else
                                $body = preg_replace("%NAME", "$euser->first_name $euser->last_name", $body);
                            $body = preg_replace("%FIRSTNAME", $euser->first_name, $body);
                            $body = preg_replace("%LASTNAME", $euser->last_name, $body);
                            $body = preg_replace("%COMPANY", $euser->company, $body);
                            $body = preg_replace("%STREET", $euser->street, $body);
                            $body = preg_replace("%CITY", $euser->city, $body);
                            $body = preg_replace("%STATE", $euser->state, $body);
                            $body = preg_replace("%ZIP", $euser->zip, $body);
                            $body = preg_replace("%PHONE", $euser->phone, $body);
                            $body = preg_replace("%EMAIL", $euser->email, $body);
                            $body = preg_replace("%FAX", $euser->fax, $body);
                            $body = preg_replace("%URL", $euser->url, $body);
//                            $body = preg_replace("%USERTYPE", $usertypes->user_type_description($euser->$type_id), $body);

                            $subject = preg_replace("%LOGINID", $euser->login, $f_subject);
                            $subject = preg_replace("%PASSWORD", text_decrypt($euser->password), $subject);
                            if(!$euser->first_name && !$euser->last_name)
                                $subject = preg_replace("%NAME", $euser->company, $subject);
                            else
                                $subject = preg_replace("%NAME", "$euser->first_name $euser->last_name", $subject);
                            $subject = preg_replace("%FIRSTNAME", $euser->first_name, $subject);
                            $subject = preg_replace("%LASTNAME", $euser->last_name, $subject);
                            $subject = preg_replace("%COMPANY", $euser->company, $subject);
                            $subject = preg_replace("%STREET", $euser->street, $subject);
                            $subject = preg_replace("%CITY", $euser->city, $subject);
                            $subject = preg_replace("%STATE", $euser->state, $subject);
                            $subject = preg_replace("%ZIP", $euser->zip, $subject);
                            $subject = preg_replace("%PHONE", $euser->phone, $subject);
                            $subject = preg_replace("%EMAIL", $euser->email, $subject);
                            $subject = preg_replace("%FAX", $euser->fax, $subject);
                            $subject = preg_replace("%URL", $euser->url, $subject);
//                            $subject = preg_replace("%USERTYPE", $usertypes->user_type_description($euser->$type_id), $subject);

                            $headers = "From: $f_from_name <$f_from_email>\r\n";
                            mail($euser->email, $subject, $body, $headers);
                            $message .= "Email sent to $euser->first_name $euser->last_name ($euser->email)<BR>";
                            $sendcount += 1;
                        }
                   }
                }
                $message .= "$sendcount emails sent.";
            }
        } else {
            $message .= "<p>Please correct the following items and then resubmit:<br>$errors</p>";
        }
    } else {
        $message = "Select users first.";
        echo "<script type=\"text/javascript\">\nlocation.href='user_maint.php?user_id=$f_user_id&message=".htmlentities(urlencode($message))."'\n</script>";
    }
?>
<html>
<head>
<?
    include "inc/cssstyle.php";
    $pagename = "$config_useremail_page_name";
    $help_msg_name = "config_useremail_help";
    $help_msg = "$config_useremail_help";
    $help_width = "$config_useremail_help_width";
    $help_height = "$config_useremail_help_height";
    if (!$f_body)
        $f_body = $config_useremail_body;
    if (!$f_subject)
        $f_subject = $config_useremail_subject;
    if (!$f_from_name)
        $f_from_name = $config_useremail_from_name;
    if (!$f_from_email)
        $f_from_email = $config_useremail_from_email;

    require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <td width="100%" align="left" valign="top">
<?
    if ($config_useremail_banners == "Y") include "inc/banner_ads.php";
    echo "$config_useremail_paragraph1";
    if (!empty($message)) {
            include "inc/box_begin.htm";
            echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center>";
            include "inc/box_end.htm";
    }
?>
<table width=100% cellspacing=1 cellpadding=1>
    <form method="Post" name="SendEmail" action="user_email.php">
        <tr ALIGN="left" VALIGN="middle">
            <td align=right width=10% bgcolor="<?=$color_table_col_bg;?>">
            From Name:
            </td>
            <td align=left width=90% bgcolor="<?=$color_table_col_bg;?>">
            <input type="text" name="f_from_name" size="40" value="<?=$f_from_name;?>">
            </td>
         </tr>
         <tr ALIGN="left" VALIGN="middle">
            <td align=right width=10% bgcolor="<?=$color_table_col_bg;?>">
            From Email Address:
            </td>
            <td align=left width=90% bgcolor="<?=$color_table_col_bg;?>">
            <input type="text" name="f_from_email" size="40" value="<?=$f_from_email;?>">
            </td>
         </tr>
         <tr ALIGN="left" VALIGN="middle">
            <td align=right width=10% bgcolor="<?=$color_table_col_bg;?>">
            Subject:
            </td>
            <td align=left width=90% bgcolor="<?=$color_table_col_bg;?>">
            <input type="text" name="f_subject" size="40" value="<?=$f_subject;?>">
            </td>
         </tr>
         <tr ALIGN="left" VALIGN="middle">
            <td colspan="100%" align=left width=10% bgcolor="<?=$color_table_col_bg;?>">
            Email Body:
            </td>
         </tr>
         <tr>
            <td colspan="100%" align=left bgcolor="<?=$color_table_col_bg;?>">
            <textarea name="f_body" rows="6" cols="90"><?=$f_body;?></textarea>
            </td>
         </tr>
         <tr ALIGN="left" VALIGN="middle">
            <td colspan="100%" align=left width=10% bgcolor="<?=$color_table_col_bg;?>">
            Merge Tags:
            </td>
         </tr>
         <tr>
            <td align=left colspan="100%" bgcolor="<?=$color_table_col_bg;?>">
            %LOGINID %PASSWORD<BR>
            %NAME %FIRSTNAME %LASTNAME %COMPANY <BR>
            %STREET %CITY %STATE %ZIP<BR>
            %PHONE %EMAIL %FAX %URL
            </td>
         </tr>
         <TR><TD Colspan="100%">Email Addresses:</TD></TR>
         <TR><TD Colspan="100%"><textarea name="email_addresses" rows="6" cols="90"><?=$email_addresses;?></textarea></TD></TR>
         <TR><TD Colspan="100%"><input type="Submit" name="Submit" value="Send"></TD></TR>
    </form>
</table>
                  </td>
<? require "inc/body_end.inc"; ?>
</html>
