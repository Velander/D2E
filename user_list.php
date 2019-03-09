<?
require "inc/db_inc.php";
require_once "inc/func.php";
require_once "inc/class_user.php";
$users = $db_link->query("Select * from newuser");

while ($suser = mysqli_fetch_array($users)) {
	$doc .= "insert newuser values (";
	$doc.= "'".$suser["user_id"]."', ";
	$doc.= "'".$suser["login"]."', ";
	$doc.= "'".$suser["setup_date"]."', ";
	$doc.= "'".text_decrypt($suser["password"])."', ";
	$doc.= "'".$suser["first_name"]."', ";
	$doc.= "'".$suser["last_name"]."', ";
	$doc.= "'".$suser["company"]."', ";
	$doc.= "'".$suser["street"]."', ";
	$doc.= "'".$suser["city"]."', ";
	$doc.= "'".$suser["state"]."', ";
	$doc.= "'".$suser["zip"]."', ";
	$doc.= "'".$suser["country"]."', ";
	$doc.= "'".$suser["email"]."', ";
	$doc.= "'".$suser["phone"]."', ";
	$doc.= "'".$suser["fax"]."', ";
	$doc.= "'".$suser["url"]."', ";
	$doc.= "'".$suser["user_type_id"]."', ";
	$doc.= "'".$suser["verified"]."', ";
	$doc.= "'".$suser["newsletter"]."', ";
	$doc.= "'".$suser["ip_address"]."', ";
	$doc.= "'".$suser["opt_date"]."', ";
	$doc.= "'".$suser["referral_firstname"]."', ";
	$doc.= "'".$suser["referral_lastname"]."', ";
	$doc.= "'".$suser["referral_schoolid"]."', ";
	$doc.= "'".$suser["district_id"]."', ";
	$doc.= "'".$suser["banner_link"]."', ";
	$doc.= "'".$suser["half_banner_link"]."'";
	$doc.= ")\n";
}

mysqli_free_result($users);
header("Content-type: application/octet-stream");
echo($doc);
exit();

?>