<?
$mysqli_host = "localhost";
$mysqli_user = "donatet6_d2euser";
$mysqli_db = "donatet6_d2e";
$mysqli_password = "d2e-Colton";

$db_link = mysqli_connect($mysqli_host, $mysqli_user, $mysqli_password);
$db_link->select_db($mysqli_db) || die("Could not connect to SQL db");
$c_result = $db_link->query("select name, value from config");
while ($row = mysqli_fetch_row($c_result)) {
	${$row[0]} = $row[1];
}
$c_result->close();

#
# Initialize global variables
#
if(isset($_SERVER['PHP_SELF'])) $PHP_SELF = $_SERVER['PHP_SELF'];
$http_location = "http://www.donate2education.org/";
$https_location = "http://www.donate2education.org/";

if (isset($_COOKIE["User_ID"]))
{
	$User_ID = $_COOKIE["User_ID"];
	if ($_COOKIE["RememberMe"] == "Y")
	{
		setcookie("RememberMe", "Y", time()+60*60*24*30);  /* expire in 30 days */
		setcookie("User_ID", $_COOKIE["User_ID"], time()+60*60*24*30 );  /* expire in 30 days */
	}
	else
	{
		setcookie("RememberMe", "N", 0);  /* doesn't expire */
		setcookie ("User_ID", $_COOKIE["User_ID"], time()+3600);  /* expire in 1 hour */
	}
}
#
# SALT & CODE for user password & credit card encryption
#
$CRYPT_SALT = 185; # any number ranging 1-255
$START_CHAR_CODE = 100; # 'd' letter

if (strstr($_SERVER["SCRIPT_NAME"],"richtext"))
	include_once "../inc/func.php";
else
	include_once "inc/func.php";

$debug = false;
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
error_reporting(E_ALL & ~E_NOTICE);

if (strstr($_SERVER["SCRIPT_NAME"],"richtext"))
	include_once "../inc/class_user.php";
else
	include_once "inc/class_user.php";
if(isset($_COOKIE["uniqueid"]))
    $uniqueid = $_COOKIE["uniqueid"];
if (!empty($uniqueid)) {
	$user = new user;
	if ($user->load_unique_id($uniqueid)) {
		$User_ID = $user->user_id;
		setcookie ("User_ID", $User_ID, time()+3600);  /* expire in 1 hour */
	}
	unset($uniqueid);
}

	if ($config_maintenance_flag == "Y" && substr($PHP_SELF,-9)!="login.php" && substr($PHP_SELF,-10)!="closed.php" && $User_ID != "2")
		echo "<script type=\"text/javascript\">\nlocation.href='$http_location"."closed.php'\n</script>\n";

?>
