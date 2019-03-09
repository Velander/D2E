<?
if (strstr($_SERVER["SCRIPT_NAME"],"richtext")) {
	require_once "../inc/class_donation_project.php";
} else {
	require_once "inc/class_donation_project.php";
}

class donation_projects
{
	var $donation_projects_list;
	var $error_message;

	function __construct()
	{
		$this->donation_projects_list = array();
	}

	function add_donation_project($row)
	{
		$newdonation_project = new donation_project();
		$newdonation_project->donation_project_id 	= $row[donation_project_id];
		$newdonation_project->donation_id 			= $row[donation_id];
		$newdonation_project->project_id 			= $row[project_id];
		$newdonation_project->donation_amount 		= $row[amount];
		$newdonation_project->original_amount 		= $row[original_amount];
		$newdonation_project->matching_donation_id 	= $row[matching_donation_id];
		$this->donation_projects_list[$row["donation_project_id"]] = $newdonation_project;
	}

	function load_donation_projects($donation_id)
	{
		global $db_link;
		$this->$donation_projects_list = array();
		$results = $db_link->query("Select * from donation_project where donation_id = '$donation_id' order by donation_project_id");
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_donation_project($row);
		}
		mysqli_free_result($results);
	}

	function delete_donation_project($donation_project_id)
	{
		global $db_link;
		if ($db_link->query("delete from donation_project where donation_project_id = '$donation_project_id'")) {
			return true;
		} else {
			return false;
		}
	}

	function donation_project_description($donation_project_id)
	{
		$donation_project = $this->donation_projects_list[$donation_project_id];
		return $donation_project->donation_project_description;
	}

	function count() {
		return count($this->donation_projects_list);
	}
}
?>