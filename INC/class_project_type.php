<?
class project_type
{
	var	$project_type_id;
	var $project_type_description;

	function load_project_type($project_type_id)
	{
		global $db_link;
		$results = $db_link->query("select * from project_type where project_type_id = '$project_type_id'");
		if (mysqli_num_rows($results) == 0) {
			mysqli_free_result($results);
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->project_type_id 			= $row["project_type_id"];
			$this->project_type_description	= $row["project_type_description"];
			mysqli_free_result($results);
			return true;
		}
	}
}	// end of class project_type
?>