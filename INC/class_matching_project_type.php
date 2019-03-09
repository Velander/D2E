<?
class matching_project_type
{
	var	$matching_project_type_id;
	var $matching_id;
	var $project_type_id;

	function matching_school()
	{
		$this->matching_project_type_id = "0";
		$this->matching_id = "0";
		$this->project_type_id = "0";
	}

	function load_matching_school($matching_project_type_id)
	{
		global $db_link;
		$results = $db_link->query("select * from matching_project_type where matching_project_type_id = '$matching_project_type_id'");
		if (mysqli_num_rows($results) == 0) {
			mysqli_free_result($results);
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->matching_project_type_id 	= $row["matching_project_type_id"];
			$this->matching_id			= $row["matching_id"];
			$this->project_type_id			= $row["project_type_id"];
			mysqli_free_result($results);
			return true;
		}
	}

}	// end of class matching_school
?>