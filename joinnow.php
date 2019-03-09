<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";

?>
<html>
<head>
<META NAME="Keywords" CONTENT="Oregon City, Oregon, Alaska, Washington, Schools, Donate, Donation, Education">
<?
	$pagename = "$config_joinnow_page_name";
	$help_msg_name = "config_joinnow_help";
	$help_msg = "$config_joinnow_help";
	$help_width = "$config_joinnow_help_width";
	$help_height = "$config_joinnow_help_height";
	require "inc/cssstyle.php";
	require "inc/title.php";
	if ($_SERVER['REQUEST_METHOD'] == "POST")
	{
		if ($Submit == "Submit")
		{
			if (empty($f_name))
				$message .= "Your name is required.<BR>";
			if (empty($f_email))
				$message .= "Your email is required.<BR>";
			elseif (!strstr($f_email,"@") || !strstr($f_email,"."))
				$message .= "Invalid email address.<BR>";
			if (empty($f_school_name))
				$message .= "School's name is required.<BR>";
			if (empty($message))
			{
				# Update database and send email here.
				$subject = "Donate2Educate Join Now Request";
				$body  = "      Name: $f_name\n";
				$body .= "     Title: $f_title\n";
				$body .= "    E-Mail: $f_email\n";
				$body .= "    School: $f_school_name\n";
				$body .= "   Message: $f_message\n";
				$headers = "To: Donate2Educate Support <support@donate2educate.org>\n";
				$headers .= "From: $f_name of $f_school_name <support@donate2educate.org>\n";
				$headers .= "Reply-To: $f_name <$f_email>\n";
				if (mail("support@donate2educate.org", $subject, $body, $headers)) {
					$message = "$config_joinnow_thankyou";
					echo "<script type=\"text/javascript\">\nlocation.href='$http_location"."index.php?message=".htmlentities(urlencode($message))."'\n</script>\n";
				} else {
					$message = "Message failed to send.";
				}
			}
		}
	}
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/home_body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
					<td width="640" align="left" valign="top">
<?
	if ($config_joinnow_banners == "Y") include "inc/banner_ads.php";

	if (!empty($message))
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center><br>";
	if (!empty($config_joinnow_paragraph1)) {
		echo "$config_joinnow_paragraph1";
	}
	if (empty($f_state)) $f_state = "OR";
?>
		<TABLE ALIGN="left" COLS=2 BORDER=0 CELLSPACING=10 CELLPADDING=0 WIDTH="100%">
			<Form Name='frmJoinNow' Method='POST'>
				<TR ALIGN="left" VALIGN="middle">
					<TD Width='100' Align='Right'><b>Your Name</b></TD>
					<TD><input type='text' size='40' maxlength='100' name='f_name' value='<?=$f_name;?>'></TD>
				</TR>
				<TR ALIGN="left" VALIGN="middle">
					<TD Width='100' Align='Right'><b>Your Title</b></TD>
					<TD><input type='text' size='40' maxlength='100' name='f_title' value='<?=$f_title;?>'></TD>
				</TR>
				<TR ALIGN="left" VALIGN="middle">
					<TD Width='100' Align='Right'><b>Your E-Mail</b></TD>
					<TD><input type='text' size='40' maxlength='100' name='f_email' value='<?=$f_email;?>'></TD>
				</TR>
				<TR ALIGN="left" VALIGN="middle">
					<TD Width='100' Align='Right'><b>Your School Name</b></TD>
					<TD><input type='text' size='40' maxlength='60' name='f_school_name' value='<?=$f_school_name;?>'></TD>
				</TR>
				<TR ALIGN="left" VALIGN="middle">
					<TD Width='100' Align='Right'><b>State</b></TD>
					<TD><select name="f_state">
					<option value="AK"<?=($f_state == "AK"?" SELECTED":"");?>>Alaska</option>
					<option value="CA"<?=($f_state == "CA"?" SELECTED":"");?>>California</option>
					<option value="HI"<?=($f_state == "HI"?" SELECTED":"");?>>Hawaii</option>
					<option value="ID"<?=($f_state == "ID"?" SELECTED":"");?>>Idaho</option>
					<option value="MT"<?=($f_state == "MT"?" SELECTED":"");?>>Montana</option>
					<option value="OR"<?=($f_state == "OR"?" SELECTED":"");?>>Oregon</option>
					<option value="WA"<?=($f_state == "WA"?" SELECTED":"");?>>Washington</option>
					</select>
					</TD>
				</TR>
				<TR>
					<TD colspan='2' align='left'><B>Comments or Questions</B></TD>
				</TR>
				<TR>
					<TD colspan='2' align='left'>
					<textarea name="f_message" cols=63 rows=5><?=stripslashes($f_message);?></textarea>
					</TD>
				</TR>
				<TR ALIGN="left" VALIGN="middle">
					<TD Align='center' Colspan=2>
					<input type="submit" class="nicebtns" name="Submit" value="Submit">
					</TD>
				</TR>
				</TR ALIGN="left" VALIGN="middle">
					<TD colspan='2'>
<?
	if (!empty($config_joinnow_paragraph2)) {
		echo "$config_joinnow_paragraph2";
	}
?>
					</TD>
				</TR>
			</Form>
		</TABLE>
					</td>
<? require "inc/body_end.inc"; ?>
</html>
