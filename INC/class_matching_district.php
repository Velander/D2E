<?
class matching_district
{
	var	$matching_district_id;
	var $matching_id;
	var $district_id;

	function __construct()
	{
		$this->matching_district_id = "0";
		$this->matching_id = "0";
		$this->district_id = "0";
	}

	function load_matching_district($matching_district_id)
	{
		global $db_link;
		$results = $db_link->query("select * from matching_district where matching_district_id = '$matching_district_id'");
		if (mysqli_num_rows($results) == 0) {
			mysqli_free_result($results);
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->matching_district_id 	= $row["matching_district_id"];
			$this->matching_id			= $row["matching_id"];
			$this->district_id			= $row["district_id"];
			mysqli_free_result($results);
			return true;
		}
	}

}	// end of class matching_district
?>