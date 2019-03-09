<?
class disbursement
{
	var	$disbursement_id;
	var $project_id;
	var $disbursement_date;
	var $tran_no;
	var $disbursement_amount;
	var $recipient_name;
	var $error_message;

	function __construct()
	{
		$this->disbursement_id = 0;
	}

	function load_disbursement($disbursement_id)
	{
		global $db_link;
		$sql = "select * from disbursement where disbursement_id = '$disbursement_id'";
		$results = $db_link->query($sql);
		if (mysqli_num_rows($results) == 0)
		{
			$results->close();
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->disbursement_id		= $row["disbursement_id"];
			$this->project_id			= $row["project_id"];
			$this->disbursement_amount	= $row["disbursement_amount"];
			$this->disbursement_date	= (is_null($row["disbursement_date"]) ? "now()" : $row["disbursement_date"]);
			$this->tran_no				= $row["tran_no"];
			$this->recipient_name		= $row["recipient_name"];
			return true;
		}
	}

	function save_disbursement()
	{
		global $db_link;
		$project = new project;
		if ($project->load_project($this->project_id))
		{
			if ($this->disbursement_id == 0)
			{
				// Insert new disbursement
				$sql = "Insert disbursement (project_id, disbursement_amount, disbursement_date, recipient_name, tran_no";
				$sql .= ") values (";
				$sql .= "'$this->project_id', '$this->disbursement_amount'";
				$sql .= ", ".(empty($this->disbursement_date) ? "NULL" : "'$this->disbursement_date'");
				$sql .= ", '$this->recipient_name', '$this->tran_no')";
				if ($db_link->query($sql))
				{
					$this->disbursement_id = mysqli_insert_id($db_link);
					if ($project->add_disbursement($this->disbursement_amount, $this->disbursement_date))
					{
						return True;
					} else {
						$this->error_message = mysqli_error($db_link)."<BR>$sql";
						return False;
					}
				} else {
					$this->error_message = mysqli_error($db_link)."<BR>$sql";
					return False;
				}
			} else {
				// Update existing disbursement
				$existing_rcd = new disbursement();
				if ($existing_rcd->load_disbursement($this->disbursement_id))
				{
					$sql = "Update disbursement Set project_id = '$this->project_id'";
					$sql .= ", disbursement_amount = '$this->disbursement_amount'";
					$sql .= ", disbursement_date = ".(empty($this->disbursement_date) ? "NULL" : "'$this->disbursement_date'").", tran_no = '$this->tran_no'";
					$sql .= ", recipient_name = '$this->recipient_name'";
					$sql .= " where disbursement_id = '$this->disbursement_id'";
					if ($db_link->query($sql))
					{
						# Correct the Disbursement amount
						$amount_change = $this->disbursement_amount - $existing_rcd->disbursement_amount;
						if ($project->add_disbursement($amount_change, $this->disbursement_date))
						{
							return True;
						} else {
							$this->error_message = $project->error_message;
							return False;
						}
					} else {
						$this->error_message = mysqli_error($db_link)."<BR>$sql";
						return False;
					}
				} else {
					$this->error_message = "Can't read existing disbursement record.<br>$existing_rcd->error_message";
					return False;
				}
			}
		}
	}	# end of save_disbursement function

	function delete_disbursement()
	{
		global $db_link;
		$project = new project;
		if ($project->load_project($this->project_id))
		{
			if ($this->disbursement_id != 0)
			{
				// Update existing disbursement
				$existing_rcd = new disbursement();
				if ($existing_rcd->load_disbursement($this->disbursement_id))
				{
					$sql = "Delete from disbursement";
					$sql .= " where disbursement_id = '$this->disbursement_id'";
					if ($db_link->query($sql))
					{
						# Correct the Disbursement amount
						if ($project->add_disbursement($existing_rcd->disbursement_amount * -1, $this->disbursement_date))
						{
							return True;
						} else {
							$this->error_message = $project->error_message;
							return False;
						}
					} else {
						$this->error_message = mysqli_error($db_link)."<BR>$sql";
						return False;
					}
				}
				else
				{
					$this->error_message = "Can't read existing disbursement record.<br>$existing_rcd->error_message";
					return False;
				}
			}
		}
	}	# end of delete_disbursement function
}	# end of class disbursement
?>