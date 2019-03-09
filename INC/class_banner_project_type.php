<?
class banner_project_type
{
	var	$banner_project_type_id;
	var $banner_id;
	var $project_type_id;

	function banner_school()
	{
		$this->banner_project_type_id = "0";
		$this->banner_id = "0";
		$this->project_type_id = "0";
	}

	function load_banner_school($banner_project_type_id)
	{
		global $db_link;
		$results = $db_link->query("select * from banner_project_type where banner_project_type_id = '$banner_project_type_id'");
		if (mysqli_num_rows($results) == 0) {
			mysqli_free_result($results);
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->banner_project_type_id 	= $row["banner_project_type_id"];
			$this->banner_id			= $row["banner_id"];
			$this->project_type_id			= $row["project_type_id"];
			mysqli_free_result($results);
			return true;
		}
	}

}	// end of class banner_school
?>