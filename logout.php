<?	require "inc/db_inc.php";
	// Use $HTTP_SESSION_VARS with PHP 4.0.6 or less
	$loggedin = $User_ID;
	setcookie ("User_ID", "", mktime(12,0,0,1, 1, 1970), "/");  /* expire now */
	setcookie ("RememberMe", "", mktime(12,0,0,1, 1, 1970), "/");  /* expire now */
	unset($User_ID);
	unset($uniqueid);
	if ($_COOKIE["HttpLogout"] =! "Y" || $_COOKIE["HttpsLogout"] =! "Y") {
		$newunique_id = md5(uniqid(rand(),1));
		$db_link->query("update user set unique_id = '".addslashes($newunique_id)."' where user_id = '$loggedin'");
		if (empty($HTTPS)) {
			setcookie ("HttpLogout", "Y");
			echo "<script type=\"text/javascript\">\nlocation.href='$http_location"."logout.php'\n</script>";
		} else {
			setcookie ("HttpsLogout", "Y");
			echo "<script type=\"text/javascript\">\nlocation.href='$https_location"."logout.php'\n</script>";
		}
	} else
		session_destroy();
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";
	$pagename = "$config_login_page_name";
	require "inc/title.php";
	require "inc/jscript.inc";
	echo "<script type=\"text/javascript\">\nlocation.href='$http_location"."index.php'\n</script>";
?>
<html>
<head>
