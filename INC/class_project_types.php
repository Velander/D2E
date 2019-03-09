<?
require_once "inc/class_project_type.php";
class project_types
{
	var $project_type_list;
	var $error_message;

	function add_project_type($row)
	{
		$newproject_type = new project_type();
		$newproject_type->project_type_id = $row["project_type_id"];
		$newproject_type->project_type_description = $row["project_type_description"];
		$this->project_type_list[$row["project_type_id"]] = $newproject_type;
	}

	function load_project_types()
	{
		global $db_link;
		$project_type_list = array();
		$results = $db_link->query("Select * from project_type order by project_type_description");
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_project_type($row);
		}
		$results->close();
	}

	function load_project_search_types($district_id = "ALL", $school_id = "ALL")
	{
		global $db_link;
		if (!$district_id) $district_id = "ALL";
		if (!$school_id) $school_id = "ALL";
		$project_type_list = array();
		// Search for all main and additional categories.
		if ($school_id != "ALL") {
			$sql = "select distinct * from (";
			$sql .= "Select distinct project_type.* from project_type inner join project on project_type.project_type_id = project.project_type_id where project.school_id = '$school_id' and project.project_status_id = 3";
			$sql .= " UNION Select distinct project_type.* from project_type inner join project_types on project_types.project_type_id = project_type.project_type_id inner join project on project_types.project_id = project.project_id where project.school_id = '$school_id' and project.project_status_id = 3";
			$sql .= ") a order by project_type_description";
		} else if ($district_id != "ALL") {
			$sql = "select distinct * from (";
			$sql .= "Select distinct project_type.* from project_type inner join project on project_type.project_type_id = project.project_type_id inner join school on project.school_id = school.school_id where school.district_id = '$district_id' and project.project_status_id = 3";
			$sql .= " UNION Select distinct project_type.* from project_type inner join project_types on project_types.project_type_id = project_type.project_type_id inner join project on project_types.project_id = project.project_id inner join school on project.school_id = school.school_id where school.district_id = '$district_id' and project.project_status_id = 3";
			$sql .= ") a order by project_type_description";
		} else {
			$sql = "select distinct * from (";
			$sql .= "Select distinct project_type.* from project_type inner join project on project_type.project_type_id = project.project_type_id where project.project_status_id = 3";
			$sql .= " UNION Select distinct project_type.* from project_type inner join project_types on project_types.project_type_id = project_type.project_type_id inner join project on project_types.project_id = project.project_id where project.project_status_id = 3";
			$sql .= ") a order by project_type_description";
		}

		$results = $db_link->query($sql);
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_project_type($row);
		}

		$results->close();
	}

	function project_type_description($project_type_id)
	{
		$project_type = $this->project_type_list[$project_type_id];
		return $project_type->project_type_description;
	}

	function new_project_type($type_description)
	{
		global $db_link;
		// Insert the new Project Type.
		$db_link->query("Insert project_type (project_type_description) values ('".mysqli_escape_string($db_link, $type_description)."')");
		// Get the project_type_id for the new Project Type.
		$typeid = mysqli_insert_id($db_link);
		// Now add the new Project Type to the list.
		$results = $db_link->query("Select * from project_type where project_type_id = '$typeid'");
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_project_type($row);
		}
		$results->close();
		return $typeid;
	}

	function count()
	{
		return count($this->project_type_list);
	}
}	// end of class project_types
?>