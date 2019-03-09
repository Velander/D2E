<?
class user_types
{
	var $user_type_list;
	var $error_message;

	function add_user_type($row) {
		$newuser_type = new user_type();
		$newuser_type->user_type_id = $row["user_type_id"];
		$newuser_type->user_type_description = $row["user_type"];
		$this->user_type_list[$row["user_type_id"]] = $newuser_type;
	}

	function load_user_types() {
		global $db_link;
		$results = $db_link->query("Select * from user_type order by user_type_id");
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_user_type($row);
		}
		$results->close();
	}

	function user_type_description($user_type_id) {
		$user_type = $this->user_type_list[$user_type_id];
		return $user_type->user_type_description;
	}

	function new_user_type($type_description) {
		// Insert the new user Type.
		global $db_link;
		$db_link->query("Insert user_type (user_type) values ('".mysqli_escape_string($db_link, $type_description)."')");
		// Get the user_type_id for the new user Type.
		$typeid = mysqli_insert_id($db_link);
		// Now add the new user Type to the list.
		$results = $db_link->query("Select * from user_type where user_type_id = '$typeid'");
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_user_type($row);
		}
		$results->close();
		return $typeid;
	}

	function count() {
		return count($this->user_type_list);
	}
}	// end of class user_types
?>