<?
error_reporting(E_ALL);
	$mysqli_host = "localhost";
	$mysqli_user = "donatet6_d2euser";
	$mysqli_db = "donatet6_d2e";
	$mysqli_password = "d2e-Colton";

echo "connecting<BR>";


$dbh=mysqli_connect ($mysqli_host, $mysqli_user, $mysqli_password)
	or die ('I cannot connect to the database.');

if ($result = $dbh->query("SELECT DATABASE()")) {
    $row = $result->fetch_row();
    printf("Default database is %s.\n", $row[0]);
    $result->close();
}

$dbh->select_db ($mysqli_db);
echo "connected<BR>";

if ($result = $dbh->query("SELECT DATABASE()")) {
    $row = $result->fetch_row();
    printf("Default database is %s.\n", $row[0]);
    $result->close();
}

if($c_result = $dbh->query("select name, value from config")) {
	printf("Select returned %d rows.\n", $c_result->num_rows);
	while ($row = $c_result->fetch_row()) {
		${$row[0]} = $row[1];
		echo "1 $row[0], $row[1]<br>";
	}
	$c_result->close();
}
$dbh->close();
?>