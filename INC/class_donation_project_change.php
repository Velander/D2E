<?
class donation_project_change
{
	var	$id;
	var	$donation_project_id;
	var $donation_id;
	var $user_id;
	var $change_date;
	var $project_id;
	var $donation_amount;
	var $matching_donation;

	function __construct()
	{
		$this->id = "0";
		$this->donation_project_id = "0";
		$this->donation_id = "0";
		$this->project_id = "0";
		$this->donation_amount = "0";
		$this->matching_donation = "0";
	}

	function load_donation_project_change($id)
	{
		global $db_link;
		$results = $db_link->query("select * from donation_project_changes where id = '$id'");
		if (mysqli_num_rows($results) == 0)
		{
			$results->close();
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->id 					= $row["id"];
			$this->donation_project_id 	= $row["donation_project_id"];
			$this->donation_id			= $row["donation_id"];
			$this->user_id 				= $row["user_id"];
			$this->change_date 			= $row["change_date"];
			$this->project_id			= $row["project_id"];
			$this->donation_amount		= $row["donation_amount"];
			$this->matching_donation	= $row["matching_donation"];
			$results->close();
			return true;
		}
	}

	function save_donation_project_change()
	{
		global $db_link;
		if ($this->id == "0")
		{
			// Insert record
			$sql  = "Insert donation_project_changes (donation_project_id, donation_id, user_id, change_date, project_id, matching_donation, donation_amount) values (";
			$sql .= "'$this->donation_project_id', '$this->donation_id', '$this->user_id', '".date("Y-m-d H:i:s")."', '$this->project_id', '$this->matching_donation', '$this->donation_amount')";
			if ($db_link->query($sql))
			{
				$this->id = mysqli_insert_id($db_link);
				return true;
			} else {
				$this->error_message = mysqli_error($db_link)."<BR>$sql";
				return False;
			}
		}
	}
}	// end of class donation_project_change
?>