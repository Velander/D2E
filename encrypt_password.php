<?	require "include/db_inc.php";
	require_once "include/func.php";
	require_once "include/class_user.php";
	require_once "include/class_users.php";
?>
<html>
<body>
<?

	$usersearch = new users;
	$usersearch->find_users("", $f_login, $f_first_name, $f_last_name, $f_type_id, false);

	if ($usersearch->count() == 0)
		echo "No matching users found.";
	else {
		reset($usersearch->user_list);
		while (list($userid, $suser) = each($usersearch->user_list)) {
			echo "Encrypting $suser->first_name $suser->lastname<br>";
			$user->save_user();
		}
	}
?>
</body>
</html>
