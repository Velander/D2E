<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php"; ?>

<?
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
$sql = "Insert teacher_training (name, school_district, school, phone, email, address, comments, dateadded) values ('".mysqli_escape_string($name)."', '".
mysqli_escape_string($district)."', '".mysqli_escape_string($school)."', '".mysqli_escape_string($email)."', '".mysqli_escape_string($phone)."', '".
mysqli_escape_string($address)."', '".mysqli_escape_string($comments)."',now())";

if (!$db_link->query($sql))
{
  $err = mysqli_error();
}
		$emailbody = "Name: $name\n\rDistrict: $district\n\rSchool: $school\n\rEmail: $email";
		$emailbody .= "Phone:$phone\n\rAddress: $address\n\rComments: $comments\n\r\n\r$err\n\r";
		$headers = "From: Donate2Educate <support@donate2educate.org>\r\n";
		if (!mail("donate2educate@gmail.com", "Gift Certificate Request", $emailbody, $headers)) {
			$message = "Your registration email failed to send.";
		} else {
			$message = "$config_gc_paragraph2";
		}
	} else {
		$message = "";
	}
?>
<html>
<head>
<?	require "inc/cssstyle.php"; ?>
<META NAME="Keywords" CONTENT="Oregon City, Schools, Donate, Donation, Education">
<?
	$pagename = "$config_gc_page_name";
	$help_msg_name = "config_gc_help";
	$help_msg = "$config_gc_help";
	$help_width = "$config_gc_help_width";
	$help_height = "$config_gc_help_height";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/home_body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
					<td width="655" align="left" valign="top">
					<table align="center"<? echo ($config_gc_image ? " background=\"images/$config_gc_image\"" : "");?>><tr><td>
<?	if (!empty($message)) {
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center><br>";
	} else {
		if (!empty($config_gc_paragraph1)) {
			echo "$config_gc_paragraph1";
		}
?>
	<br>
	<table>
	 <tr>
	  <td align='center'>
	   <form Name='frmGC_Request' Method='POST'>
	     <table>
	       <tr><td align='right' nowrap>Name:</td><td><input name='name' type='text' size='60'></td></tr>
	       <tr><td align='right' nowrap>School District:</td><td><input name='district' type='text' size='60'></td></tr>
	       <tr><td align='right' nowrap>School:</td><td><input name='school' type='text' size='60'></td></tr>
	       <tr><td align='right' nowrap>Phone:</td><td><input name='phone' type='text' size='60'></td></tr>
	       <tr><td align='right' nowrap>E-Mail Address:</td><td><input name='email' type='text' size='60'></td></tr>
	       <tr><td align='right' nowrap>Mailing Address:</td><td><textarea name='address' cols=60 rows=3></textarea></td></tr>
	       <tr><td align='center' colspan=2>Please make comments about OAAE training and/or art supplies you hope to purchase for your classroom with gift certificate.</td></tr>
	       <tr><td align='right' nowrap>Comments:</td><td><textarea name='comments' cols=60 rows=6></textarea></td></tr>
	       <tr><td align='center' colspan=2><input name='Submit' type='submit' value='Submit Request'></td></tr>
	   </form>
	  </td>
	 </tr>
	</table>
<?
	}
	if ($config_gc_banners == "Y") include "inc/banner_ads.php";
?>
					</td></tr></table>
					</td>
<? require "inc/body_end.inc"; ?>
</html>
&nbsp;