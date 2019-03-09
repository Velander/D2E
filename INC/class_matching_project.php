<?
class matching_project
{
	var	$matching_project_id;
	var $matching_id;
	var $project_id;

	function __construct()
	{
		$this->matching_project_id = "0";
		$this->matching_id = "0";
		$this->project_id = "0";
	}

	function load_matching_project($matching_project_id)
	{
		global $db_link;
		$results = $db_link->query("select * from matching_project where matching_project_id = '$matching_project_id'");
		if (mysqli_num_rows($results) == 0) {
			mysqli_free_result($results);
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->matching_project_id 	= $row["matching_project_id"];
			$this->matching_id			= $row["matching_id"];
			$this->project_id			= $row["project_id"];
			mysqli_free_result($results);
			return true;
		}
	}

}	// end of class matching_project
?>