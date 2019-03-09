<?
class project_status
{
	var	$project_status_id;
	var $project_status_description;
	var $explanation;

	function load_project_status($project_status_id)
	{
		global $db_link;
		$results = $db_link->query("select * from project_status where project_status_id = '$project_status_id'");
		if (mysqli_num_rows($results) == 0) {
			mysqli_free_result($results);
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->project_status_id 			= $row["project_status_id"];
			$this->project_status_description	= $row["project_status_description"];
			$this->explanation					= $row["explanation"];
			mysqli_free_result($results);
			return true;
		}
	}
}	// end of class project_status
?>