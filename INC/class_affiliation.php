<?
class affiliation
{
	var	$affiliation_id;
	var	$user_id;
	var	$school_id;
	var	$admin_flag;

	function load_affiliation($affiliation_id)	{
		global $db_link;
		$results = $db_link->query("select * from user_affiliation where affiliation_id = '$affiliation_id'");
		if (mysqli_num_rows($results) == 0) {
			mysqli_free_result($results);
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->affiliation_id 	= $row["affiliation_id"];
			$this->user_id			= $row["user_id"];
			$this->school_id		= $row["school_id"];
			$this->admin_flag		= $row["administration_flag"];
			mysqli_free_result($results);
			return true;
		}
	}
}	// end of class affiliation
?>