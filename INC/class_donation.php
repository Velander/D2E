<?
require_once "inc/class_donation_project.php";
require_once "inc/class_donation_projects.php";
require_once "inc/class_matching.php";
require_once "inc/class_project.php";

class donation
{
	var	$donation_id;
	var $project_id;
	var $user_id;
	var $donation_amount;
	var $refund_flag;
	var $donation_date;
	var $payment_type_id;
	var $payment_no;
	var $payment_cvv2;
	var $payment_exp_date;
	var $payment_authorized;
	var $payment_auth_message;
	var $payment_auth_avs_msg;
	var $payment_auth_cvv_msg;
	var $payment_auth_code;
	var $payment_auth_date;
	var $payment_auth_id;
	var $payment_received;
	var $payment_received_date;
	var $contact_flag;
	var $gift_first_name;
	var $gift_last_name;
	var $gift_street;
	var $gift_city;
	var $gift_state;
	var $gift_country;
	var $gift_zip;
	var $matching_donation;
	var $show_donation;
	var $direct_donation;
	var $nocash_donation;
	var $donation_project_list;
	var $donation_key;
	var $error_message;

	function load_donation_from_key($dkey)
	{

	}

	function load_donation($donation_id)
	{
		global $db_link;
		$sql = "select * from donation where donation_id = '$donation_id'";
		$results = $db_link->query($sql);
		if (mysqli_num_rows($results) == 0)
		{
			$results->close();
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->donation_id 			= $row["donation_id"];
			$this->project_id			= $row["project_id"];
			$this->user_id				= $row["user_id"];
			$this->donation_amount		= $row["donation_amount"];
			$this->refund_flag			= $row["refund_flag"];
			$this->donation_date		= (is_null($row["donation_date"]) ? "now()" : $row["donation_date"]);
			$this->payment_type_id		= $row["payment_type_id"];
			$this->payment_no			= $row["payment_number"];
			$this->payment_authorized	= $row["payment_authorized"];
			$this->payment_auth_message	= $row["payment_auth_message"];
			$this->payment_auth_code	= $row["payment_auth_code"];
			$this->payment_auth_date	= $row["payment_auth_date"];
			$this->payment_auth_id		= $row["payment_auth_id"];
			$this->payment_received		= $row["payment_received"];
			$this->payment_received_date= (is_null($row["payment_received_date"]) ? "" : $row["payment_received_date"]);
			$this->contact_flag			= $row["contact_flag"];
			$this->gift_first_name		= $row["gift_first_name"];
			$this->gift_last_name		= $row["gift_last_name"];
			$this->gift_street			= $row["gift_street"];
			$this->gift_city			= $row["gift_city"];
			$this->gift_state			= $row["gift_state"];
			$this->gift_country			= $row["gift_country"];
			$this->gift_zip				= $row["gift_zip"];
			$this->matching_donation	= $row["matching_donation"];
			$this->show_donation		= $row["show_donation"];
			$this->direct_donation		= $row["direct_donation"];
			$this->noncash_donation		= $row["noncash_donation"];
			$this->donation_key			= $row["donation_key"];
			$this->load_donation_projects($this->donation_id);
			return true;
		}
	}

	function add_project($project_id, $donation_amount, $matching_donation_id = "0")
	{
		$donation_project = new donation_project();
		$donation_project->donation_id = $this->donation_id;
		$donation_project->project_id = $project_id;
		$donation_project->donation_amount = $donation_amount;
		$donation_project->original_amount = $donation_amount;
		$donation_project->matching_donation_id = $matching_donation_id;
		$this->donation_project_list[] = $donation_project;
		$this->donation_amount += $donation_amount;
		return true;
	}

	function remove_project($project_id)
	{
		$new_project_donation_list = array();
		$donation_projects = new donation_projects();
		while (list($donation_project_id, $donation_project)= each($this->donation_project_list)) {
			if ($donation_project->project_id == $project_id) {
				$this->donation_amount -= $donation_project->donation_amount;
				if ($donation_project_id != '0') {
					# The donation_project record needs to be delete.
					$donation_projects->delete_donation_project($donation_project_id);
					$project = new project();
					if (!$project->add_donation($donation_project->donation_amount))
						return false;
				}
			} else {
				$new_project_donation_list[] = $donation_project;
			}
		}
		$this->donation_project_list = $new_project_donation_list;
		return true;
	}

	function __construct()
	{
		$this->donation_id 			= 0;
		$this->payment_authorized 	= "N";
		$this->payment_received 	= "N";
		$this->donation_project_list = array();
		$this->matching_donation 	= "N";
		$this->show_donation 		= "N";
		$this->direct_donation 		= "N";
		$this->noncash_donation 		= "N";
		$this->donation_date = date("Y-m-d H:i:s");;
	}

	function donation_total()
	{
		$donation_amount = 0;
		while (list($donation_project_id, $donation_project) = each($this->donation_project_list)) {
			$donation_amount += $donation_project->donation_amount;
		}
		return $donation_amount;

	}

	function load_donation_projects($donation_id)
	{
		global $db_link;
		$this->donation_project_list = array();
		$results = $db_link->query("Select * from donation_project where donation_id = '$donation_id' order by donation_project_id");
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_donation_project($row);
		}
		$results->close();
	}

	function delete_donation_project($donation_project_id)
	{
		global $db_link;
		if ($db_link->query("delete from donation_project where donation_project_id = '$donation_project_id'")) {
			return true;
		} else {
			return false;
		}
	}

	function add_donation_project($row)
	{
		$newdonation_project = new donation_project();
		$newdonation_project->donation_project_id 	= $row[donation_project_id];
		$newdonation_project->donation_id 			= $row[donation_id];
		$newdonation_project->project_id 			= $row[project_id];
		$newdonation_project->donation_amount 		= $row[amount];
		$newdonation_project->original_amount 		= $row[original_amount];
		$newdonation_project->matching_donation_id  = $row[matching_donation_id];
		$this->donation_project_list[$row["donation_project_id"]] = $newdonation_project;
	}

	function donation_amount($project_id)
	{
		while (list($donation_project_id, $donation_project) = each($this->donation_project_list)) {
			if ($donation_project->project_id == $project_id)
				 return $donation_project->donation_amount;
		}
		return -1;
	}

	function save_donation()
	{
		global $db_link;
		$this->payment_no = strtr($this->payment_no, " -","");
		if ($this->donation_id == 0)
		{
			// Insert new donation
			$sql = "Insert donation (project_id, user_id, donation_amount, refund_flag, donation_date, payment_type_id";
			$sql .= ", payment_number, payment_authorized, payment_auth_message, payment_auth_code, payment_auth_avs_msg, payment_auth_cvv_msg, payment_auth_date";
			$sql .= ", payment_auth_id, payment_received, payment_received_date, contact_flag";
			$sql .= ", gift_first_name, gift_last_name, gift_street, gift_city, gift_state, gift_country, gift_zip, matching_donation, show_donation, direct_donation, noncash_donation) values (";
			$sql .= "'$this->project_id', '$this->user_id', '$this->donation_amount', '$this->refund_flag'";
			$sql .= ", ".(empty($this->donation_date) ? "now()" : "'$this->donation_date'");
			$sql .= ", '$this->payment_type_id', '".substr($this->payment_no, -4)."', '$this->payment_authorized', '$this->payment_auth_message', '$this->payment_auth_code', '$this->payment_auth_avs_msg', '$this->payment_auth_cvv_msg'";
			$sql .= ", '$this->payment_auth_date'";
			$sql .= ", '$this->payment_auth_id', '$this->payment_received', ".((empty($this->payment_received_date)) ? "NULL" : "'$this->payment_received_date'");
			$sql .= ", '$this->contact_flag', '$this->gift_first_name', '$this->gift_last_name', '$this->gift_street'";
			$sql .= ", '$this->gift_city', '$this->gift_state', '$this->gift_country', '$this->gift_zip', '$this->matching_donation', '$this->show_donation', '$this->direct_donation', '$this->noncash_donation')";
			if ($db_link->query($sql))
			{
				$this->donation_id = mysqli_insert_id($db_link);
				reset($this->donation_project_list);
				while (list($donation_project_id, $donation_project)= each($this->donation_project_list))
				{
					# Now save the new donation_project record.
					$sql = "Insert donation_project (donation_id, project_id, amount, original_amount, matching_donation_id) values ('".$this->donation_id."','".$donation_project->project_id."','".$donation_project->donation_amount."','".$donation_project->original_amount."', '".$donation_project->matching_donation_id."')";
					if ($db_link->query($sql)) {
						$donation_project->donation_project_id = mysqli_insert_id($db_link);
						if ($this->matching_donation == "N" && $this->direct_donation == "N" && $this->noncash_donation == "N") {
							# Check for matching donations.
							$project = new project();
							$matching = new matching();
							if ($project->load_project($donation_project->project_id)) {
								if ($matching->matching_amount($donation_project->project_id) > 0) {
									$matches = $matching->matching_list($donation_project->project_id);
									reset($matches);
									$match_amount = $donation_project->donation_amount;
									$amount = $match_amount;
									while (list($matchingid, $begin_date) = each($matches) && ($match_amount > 0)) {
										$matching = new matching();
										if ($matching->load_matching($matchingid)) {
											if ($matching->user_id != $this->user_id) {
												if (($matching->max_amount - $matching->donation_total()) < $amount)
													$amount = $matching->max_amount - $matching->donation_total();
												if (($project->amount_needed - $project->amount_donated() - $project->amount_pledged()) < $amount)
													$amount = ($project->amount_needed - $project->amount_donated() - $project->amount_pledged());
												if ($matching->match_donation($this->donation_id, $donation_project->project_id, $amount)) {
													$match_amount -= $amount;
												} else {
												}
											}
										}
									}
								}
							}
						}
					} else {
						$this->error_message = mysqli_error($db_link)."<BR>$sql";
						return False;
					}
				}
				$this->load_donation_projects($this->donation_id);
				return true;
			} else {
				$this->error_message = mysqli_error($db_link)."<BR>$sql";
				return False;
			}
		} else {
			// Update existing donation
			$existing_rcd = new donation();
			if ($existing_rcd->load_donation($this->donation_id))
			{
				$sql = "update donation Set project_id = '$this->project_id', user_id = '$this->user_id'";
				$sql .= ", donation_amount = '$this->donation_amount', refund_flag = '$this->refund_flag'";
				$sql .= ", donation_date = ".(empty($this->donation_date) ? "now()" : "'$this->donation_date'").", payment_type_id = '$this->payment_type_id'";
				$sql .= ", payment_number = '".substr($this->payment_no, -4)."', payment_authorized = '$this->payment_authorized', payment_auth_message = '$this->payment_auth_message'";
				$sql .= ", payment_auth_avs_msg = '$this->payment_auth_avs_msg', payment_auth_cvv_msg = '$this->payment_auth_cvv_msg', payment_auth_code = '$this->payment_auth_code'";
				$sql .= ", payment_auth_date = '$this->payment_auth_date', payment_auth_id = '$this->payment_auth_id'";
				$sql .= ", payment_received = '$this->payment_received', payment_received_date = ".(empty($this->payment_received_date) ? "NULL" : "'$this->payment_received_date'");
				$sql .= ", contact_flag = '$this->contact_flag', gift_first_name = '$this->gift_first_name'";
				$sql .= ", gift_last_name = '$this->gift_last_name', gift_street = '$this->gift_street'";
				$sql .= ", gift_city = '$this->gift_city', gift_state = '$this->gift_state', gift_country = '$this->gift_country', gift_zip = '$this->gift_zip', matching_donation = '$this->matching_donation', show_donation = '$this->show_donation', direct_donation = '$this->direct_donation', noncash_donation = '$this->noncash_donation'";
				$sql .= " where donation_id = '$this->donation_id'";
				if ($db_link->query($sql))
				{
					# Only record the donation if payment JUST received
					reset($this->donation_project_list);
					while (list($donation_project_id, $donation_project)= each($this->donation_project_list))
					{
						if (empty($donation_project->donation_project_id))
						{
							# Now save the new donation_project record.
							$sql = "Insert donation_project (donation_id, project_id, amount, original_amount, matching_donation_id) values ('$this->donation_id','$donation_project->project_id','$donation_project->donation_amount','$donation_project->original_amount', '$donation_project->matching_donation_id')";
							if ($db_link->query($sql)) {
								$donation_project->donation_project_id = mysqli_insert_id($db_link);
								if ($this->matching_donation == "N" && $this->noncash_donation == "N" && $this->direct_donation == "N") {
									# Check for matching donations.
									$project = new project();
									$matching = new matching;
									if ($project->load_project($donation_project->project_id)) {
										if ($matching->matching_amount($donation_project->project_id) > 0) {
											$match_list = $matching->matching_list($donation_project->project_id);
											reset($match_list);
											$match_amount = $donation_project->donation_amount;
											while ((list($matchingid, $begin_date) = each($match_list)) && ($match_amount > 0)) {
												$amount = $match_amount;
												$matching = new matching();
												if ($matching->load_matching($matchingid)) {
													if (($matching->max_amount - $matching->donation_total()) < $amount)
														$amount = $matching->max_amount - $matching->donation_total();
													if (($project->amount_needed - $project->amount_donated() - $project->amount_pledged()) < $amount)
														$amount = ($project->amount_needed - $project->amount_donated() - $project->amount_pledged());
													if ($matching->match_donation($this->donation_id, $donation_project->project_id, $amount)) {
														$match_amount -= $amount;
													} else {
													}
												}
											}
										}
									}
								}
							} else {
								$this->error_message = mysqli_error($db_link)."<BR>$sql";
								return False;
							}
						}
					}
					$this->load_donation_projects($this->donation_id);
					return true;
				} else {
					$this->error_message = mysqli_error($db_link)."<BR>$sql";
					return False;
				}
			} else {
				$this->error_message = "Can't read existing donation record.<br>$existing_rcd->error_message";
				return False;
			}
		}
	}	# end of save_donation function
}	# end of class donation
?>