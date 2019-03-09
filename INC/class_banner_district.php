<?
class banner_district
{
	var	$banner_district_id;
	var $banner_id;
	var $district_id;

	function __construct()
	{
		$this->banner_district_id = "0";
		$this->banner_id = "0";
		$this->district_id = "0";
	}

	function load_banner_district($banner_district_id)
	{
		global $db_link;
		$results = $db_link->query("select * from banner_district where banner_district_id = '$banner_district_id'");
		if (mysqli_num_rows($results) == 0) {
			mysqli_free_result($results);
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->banner_district_id 	= $row["banner_district_id"];
			$this->banner_id			= $row["banner_id"];
			$this->district_id			= $row["district_id"];
			mysqli_free_result($results);
			return true;
		}
	}

}	// end of class banner_district
?>