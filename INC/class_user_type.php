<?
class user_type
{
	var	$user_type_id;
	var $user_type_description;

	function load_user_type($user_type_id)	{
		global $db_link;
		$results = $db_link->query("select * from user_type where user_type_id = '$user_type_id'");
		if (mysqli_num_rows($results) == 0) {
			mysqli_free_result($results);
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->user_type_id 			= $row["user_type_id"];
			$this->user_type_description	= $row["user_type"];
			mysqli_free_result($results);
			return true;
		}
	}
}	// end of class user_type
?>