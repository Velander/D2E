<?
$mysqli_host = "localhost";
$mysqli_user = "d2esite";
$mysqli_db = "d2e_test";
$mysqli_password = "ruby1465";

$dbh=mysqli_connect ($mysqli_host, $mysqli_user, $mysqli_password) or die ('I cannot connect to the database because: ' . mysqli_error());
mysqli_select_db ($mysqli_db);
echo "Connected!";
?>