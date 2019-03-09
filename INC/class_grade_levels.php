<?
class grade_levels
{
	var $grade_level_list;
	var $error_message;

	function add_grade_level($row) {
		$newgrade_level = new grade_level();
		$newgrade_level->grade_level_id = $row["grade_level_id"];
		$newgrade_level->grade_level_description = $row["grade_level_description"];
		$this->grade_level_list[$row["grade_level_id"]] = $newgrade_level;
	}

	function load_grade_levels() {
		global $db_link;
		$results = $db_link->query("Select * from grade_level order by sort_order");
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_grade_level($row);
		}
		mysqli_free_result($results);
	}

	function grade_level_description($grade_level_id) {
		$grade_level = $this->grade_level_list[$grade_level_id];
		return $grade_level->grade_level_description;
	}

	function count() {
		return count($this->grade_level_list);
	}
}
?>