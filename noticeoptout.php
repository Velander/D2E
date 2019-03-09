<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

    require_once "inc/db_inc.php";
    require_once "inc/class_email_notice.php";


    if ($_SERVER['REQUEST_METHOD'] == "POST" && $Submit == "Block future email notices")
    {
        if(!empty($key))
        {
            $emn = new email_notice();
            if ($emn->suppress_email($key))
                $message = "Email notices blocked for email address $emn->email";
            else
                $message = "No email with the specified key was found.";

            echo "<script type=\"text/javascript\">\nlocation.href='$http_location"."index.php?message=".htmlentities($message)."'\n</script>\n";
        }
        elseif (!empty($optout_email))
        {
            $emn = new email_notice($optout_email);
            $emn->save_email_notice();
            $subject = $config_optout_email_subject;
            $body = $config_optout_email_body;
            $body = preg_replace("%EMAIL",$emn->func_email_only($optout_email),
                preg_replace("%UNSUBSCRIBE_KEY",$emn->internal_key,
                $body));
            mail($optout_email, $subject, $body, $header);
            $message = "A confirmation email has been sent to you. Click on the link in the email to complete the opt out process.";
            echo "<script type=\"text/javascript\">\nlocation.href='$http_location"."donation_search.php?message=".htmlentities($message)."'\n</script>\n";
        }
    }
?>
<html>
<head>
<?
	include "inc/cssstyle.php";
	$pagename = "$config_optout_page_name";
	$help_msg_name = "config_optout_help";
	$help_msg = "$config_optout_help";
	$help_width = "$config_optout_help_width";
	$help_height = "$config_optout_help_height";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
    <td width="655" align="left" valign="top">
<?
    if ($config_optout_banners == "Y") include "inc/banner_ads.php";
    if (!empty($config_optout_paragraph1)) {
            include "inc/box_begin.htm";
            echo "$config_optout_paragraph1";
            include "inc/box_end.htm";
    }
    if (!empty($message)) {
            include "inc/box_begin.htm";
            echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center>";
            include "inc/box_end.htm";
    }
    include "inc/dark_box_begin.htm";
    echo "<font size='+1' color=\"$color_table_hdg_font\">$config_optout_page_name</font>";
    include "inc/box_middle.htm";
    if(!empty($key))
    {
        $email_notice = new email_notice();
        if($email_notice->load_email_notice_from_key($key))
        {
            $email = $email_notice->email;
        }
    }
?>
        <form name="optout_form" method="POST" action="noticeoptout.php">
            <p>Enter Opt out Key:<input type="text" name="internal_key" value="<?=$key;?>" size="40" /></p>
            <p>Enter email address:<input type="text" name="optout_email" value="<?=$email;?>" size="40" /></p>
            <p><input type="submit" value="Block future email notices" name="Submit" />
        </form>
<?
        include "inc/box_end.htm";

?>
      </td>
<? require "inc/body_end.inc"; ?>
</html>
