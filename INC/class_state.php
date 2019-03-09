<?
class state
{
	var	$state_code;
	var $state_name;
	var $state_grp;

	function load_state($state_code)	{
		global $db_link;
		$results = $db_link->query("select * from states where code = '$state_code'");
		if (mysqli_num_rows($results) == 0) {
			mysqli_free_result($results);
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->state_code	= $row["code"];
			$this->state_name	= $row["state"];
			$this->state_grp	= $row["grp"];
			mysqli_free_result($results);
			return true;
		}
	}
}	// end of class state
?>