<?
class banner_school
{
	var	$banner_school_id;
	var $banner_id;
	var $school_id;

	function __construct()
	{
		$this->banner_school_id = "0";
		$this->banner_id = "0";
		$this->school_id = "0";
	}

	function load_banner_school($banner_school_id)
	{
		global $db_link;
		$results = $db_link->query("select * from banner_school where banner_school_id = '$banner_school_id'");
		if (mysqli_num_rows($results) == 0) {
			mysqli_free_result($results);
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->banner_school_id 	= $row["banner_school_id"];
			$this->banner_id			= $row["banner_id"];
			$this->school_id			= $row["school_id"];
			mysqli_free_result($results);
			return true;
		}
	}

}	// end of class banner_school
?>