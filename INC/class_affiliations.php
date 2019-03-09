<?
class affiliations
{
	var $affiliation_list;
	var $error_message;

	function __construct()
	{
		$this->affiliation_list = array();
	}

	function add_affiliation($affiliation_id, $user_id, $school_id, $administration_flag) {
		$newaffiliation = new affiliation();
		$newaffiliation->affiliation_id = $affiliation_id;
		$newaffiliation->user_id = $user_id;
		$newaffiliation->school_id = $school_id;
		$newaffiliation->admin_flag = $administration_flag;
		$this->affiliation_list[$affiliation_id] = $newaffiliation;
		return true;
	}

	function load_affiliations($user_id) {
		global $db_link;
		$results = $db_link->query("Select affiliation_id, user_id, school_id, administration_flag from user_affiliation where user_id = '$user_id'");
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_affiliation($row["affiliation_id"], $row["user_id"], $row["school_id"], $row["administration_flag"]);
		}
		$results->close();
		return true;
	}

	function new_affiliation($user_id, $school_id, $administration_flag) {
		global $db_link;
		if ($db_link->query("Insert user_affiliation (user_id, school_id, administration_flag) values ('$user_id', '$school_id', '$administration_flag')")) {
			$affiliation_id = mysqli_insert_id($db_link);
			$newaffiliation = new affiliation();
			$newaffiliation->affiliation_id = $affiliation_id;
			$newaffiliation->user_id = $user_id;
			$newaffiliation->school_id = $school_id;
			$newaffiliation->admin_flag = $administration_flag;
			$this->affiliation_list[$affiliation_id] = $newaffiliation;
			return true;
		} else {
			$this->error_message = mysqli_error($db_link);
			return false;
		}
	}

	function change_affiliation($affiliation_id, $user_id, $school_id, $administration_flag) {
		global $db_link;
		if (empty($school_id)) {
			if ($db_link->query("Delete from user_affiliation where affiliation_id = '$affiliation_id'")) {
				unset ($this->affiliation_list[$affiliation_id]);
				return true;
			} else {
				$this->error_message = mysqli_error($db_link);
				return false;
			}
		} else {
			$newaffiliation = new affiliation();
			$newaffiliation->affiliation_id = $affiliation_id;
			$newaffiliation->user_id = $user_id;
			$newaffiliation->school_id = $school_id;
			$newaffiliation->admin_flag = $administration_flag;
			$this->affiliation_list[$affiliation_id] = $newaffiliation;
			$sql = "Update user_affiliation Set user_id = '$user_id', school_id = '$school_id', administration_flag = '$administration_flag' where affiliation_id = '$affiliation_id'";
			if ($db_link->query($sql))
				return true;
			else {
				$this->error_message = mysqli_error($db_link);
				return false;
			}
		}
	}

	function delete_affiliations() {
		if (count($this->affiliation_list) > 0)
			while (list($affiliation_id, $affiliation) = each($this->affiliation_list)) {
				$this->delete_affiliation($affiliation_id);
			}
	}

	function delete_affiliation($affiliation_id) {
		global $db_link;
		if ($db_link->query("Delete from user_affiliation where affiliation_id = '$affiliation_id'")) {
			unset ($this->affiliation_list[$affiliation_id]);
			return true;
		} else {
			$this->error_message = mysqli_error($db_link);
			return false;
		}
	}

	function is_admin($schoolid)	{
		// Check if this user is administrator for school id.
		reset($this->affiliation_list);
		while (list($affid, $affiliation) = each($this->affiliation_list)) {
			if (($affiliation->school_id == $schoolid) && ($affiliation->admin_flag == "Y"))
				return TRUE;
		}
		return FALSE;
	}

	function is_affiliated($schoolid)	{
		// Check if this user is affiliated to school id.
		reset($this->affiliation_list);
		while (list($affid, $affiliation) = each($this->affiliation_list)) {
			if ($affiliation->school_id == $schoolid)
				return TRUE;
		}
		return FALSE;
	}

	function count() {
		return count($this->affiliation_list);
	}
}
?>