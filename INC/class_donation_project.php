<?
class donation_project
{
	var	$donation_project_id;
	var $donation_id;
	var $project_id;
	var $donation_amount;
	var $original_amount;
	var $matching_donation_id;
	var $error_message;

	function __construct()
	{
		$this->donation_project_id = "0";
		$this->donation_id = "0";
		$this->project_id = "0";
		$this->donation_amount = "0";
		$this->matching_donation_id = "0";
	}

	function load_donation_project($donation_project_id)
	{
		global $db_link;
		$results = $db_link->query("select * from donation_project where donation_project_id = '$donation_project_id'");
		if (mysqli_num_rows($results) == 0) {
			$results->close();
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->donation_project_id 	= $row["donation_project_id"];
			$this->donation_id			= $row["donation_id"];
			$this->project_id			= $row["project_id"];
			$this->donation_amount		= $row["amount"];
			$this->original_amount		= $row["original_amount"];
			$this->matching_donation_id = $row["matching_donation_id"];
			$results->close();
			return true;
		}
	}

	function delete_donation_project($donation_project_id)
	{
		global $db_link;
		if (empty($donation_project_id))
			$donation_project_id = $this->donation_project_id;
		$db_link->query("delete from donation_project where donation_project_id = '$donation_project_id'");
		if (mysqli_errno()) {
			$this->error_message = mysqli_error($db_link);
			return false;
		} else
			return true;
	}

	function save_donation_project()
	{
		global $db_link;
		if ($this->donation_project_id)
		{
			# Update Donation Project
			$sql = "Update donation_project set project_id = '$this->project_id', amount='$this->donation_amount', matching_donation_id = '$this->matching_donation_id' where donation_project_id = '$this->donation_project_id'";
			$db_link->query($sql);
		} else {
			# Insert a new Donation Project
			$sql = "Insert donation_project (donation_id,project_id,amount,original_amount,matching_donation_id) values (";
			$sql .= "'$this->donation_id','$this->project_id','$this->donation_amount','$this->donation_amount','$this->matching_donation_id')";
			$db_link->query($sql);
			$this->donation_project_id = mysqli_insert_id($db_link);
		}
		if (mysqli_errno()) {
			$this->error_message = mysqli_error($db_link);
			return false;
		} else
			return true;
	}

}	// end of class donation_project
?>