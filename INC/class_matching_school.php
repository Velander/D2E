<?
class matching_school
{
	var	$matching_school_id;
	var $matching_id;
	var $school_id;

	function __construct()
	{
		$this->matching_school_id = "0";
		$this->matching_id = "0";
		$this->school_id = "0";
	}

	function load_matching_school($matching_school_id)
	{
		global $db_link;
		$results = $db_link->query("select * from matching_school where matching_school_id = '$matching_school_id'");
		if (mysqli_num_rows($results) == 0) {
			mysqli_free_result($results);
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->matching_school_id 	= $row["matching_school_id"];
			$this->matching_id			= $row["matching_id"];
			$this->school_id			= $row["school_id"];
			mysqli_free_result($results);
			return true;
		}
	}

}	// end of class matching_school
?>