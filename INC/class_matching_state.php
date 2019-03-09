<?
class matching_state
{
	var $matching_state_id;
	var $matching_id;
	var $state;

	function __construct()
	{
		$this->matching_state_id = "0";
		$this->matching_id = "0";
	}

	function load_matching_state($matching_state_id)
	{
		global $db_link;
		$results = $db_link->query("select * from matching_state where matching_state_id = '$matching_state_id'");
		if (mysqli_num_rows($results) == 0) {
			mysqli_free_result($results);
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->matching_state_id 	= $row["matching_state_id"];
			$this->matching_id		= $row["matching_id"];
			$this->state			= $row["state"];
			mysqli_free_result($results);
			return true;
		}
	}

}	// end of class matching_state
?>