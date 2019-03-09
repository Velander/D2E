<?
class banner_state
{
	var $banner_state_id;
	var $banner_id;
	var $state;

	function __construct()
	{
		$this->banner_state_id = "0";
		$this->banner_id = "0";
	}

	function load_banner_state($banner_state_id)
	{
		global $db_link;
		$results = $db_link->query("select * from banner_state where banner_state_id = '$banner_state_id'");
		if (mysqli_num_rows($results) == 0) {
			mysqli_free_result($results);
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->banner_state_id 	= $row["banner_state_id"];
			$this->banner_id		= $row["banner_id"];
			$this->state			= $row["state"];
			mysqli_free_result($results);
			return true;
		}
	}
}	// end of class banner_state
?>