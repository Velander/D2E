<?
class grade_level
{
	var	$grade_level_id;
	var $grade_level_description;

	function load_grade_level($grade_level_id)	{
		global $db_link;
		$results = $db_link->query("select * from grade_level where grade_level_id = '$grade_level_id'");
		if (mysqli_num_rows($results) == 0) {
			mysqli_free_result($results);
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->grade_level_id 			= $row["grade_level_id"];
			$this->grade_level_description	= $row["grade_level_description"];
			mysqli_free_result($results);
			return true;
		}
	}
}	// end of class grade_level
?>