<?
class project_statuses
{
	var $project_status_list;
	var $error_mesesage;

	function add_project_status($row) {
		$newproject_status = new project_status();
		$newproject_status->project_status_id = $row["project_status_id"];
		$newproject_status->project_status_description = $row["project_status_description"];
		$newproject_status->explanation = $row["explanation"];
		$this->project_status_list[$row["project_status_id"]] = $newproject_status;
	}

	function load_project_statuses() {
		global $db_link;
		$results = $db_link->query("Select * from project_status order by project_status_id");
		if (mysqli_num_rows($results) > 0) {
			while ($row = mysqli_fetch_assoc($results)) {
				$this->add_project_status($row);
			}
		}
		mysqli_free_result($results);
	}

	function project_status_description($project_status_id) {
		$project_status = $this->project_status_list[$project_status_id];
		return $project_status->project_status_description;
	}

	function project_status_explanation($project_status_id) {
		$project_status = $this->project_status_list[$project_status_id];
		return $project_status->explanation;
	}

	function count() {
		return count($this->project_status_list);
	}
}
?>