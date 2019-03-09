<?
class donation_change
{
	var $donation_change_id;
	var $donation_id;
	var $change_date;
	var $change_user_id;
	var $user_id;
	var $donation_amount;
	var $refund_flag;
	var $donation_date;
	var $payment_type_id;
	var $payment_number;
	var $payment_authorized;
	var $payment_received;
	var $date_received;
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
	var $noncash_donation;
	var $error_message;

	function load_donation_change($donation_change_id)
	{
		global $db_link;
		$sql = "select * from donation_changes where donation_change_id = '$donation_change_id'";
		$results = $db_link->query($sql);
		if (mysqli_num_rows($results) == 0)
		{
			$results->close();
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->donation_change_id	= $row["donation_change_id"];
			$this->donation_id 		= $row["donation_id"];
			$this->change_user_id		= $row["change_user_id"];
			$this->user_id			= $row["user_id"];
			$this->change_date		= (is_null($row["change_date"]) ? "now()" : $row["change_date"]);
			$this->donation_date		= (is_null($row["donation_date"]) ? "now()" : $row["donation_date"]);
			$this->refund_flag		= $row["refund_flag"];
			$this->donation_amount		= $row["donation_amount"];
			$this->payment_type_id		= $row["payment_type_id"];
			$this->payment_number		= $row["payment_number"];
			$this->payment_authorized	= $row["payment_authorized"];
			$this->payment_received		= $row["payment_received"];
			$this->date_received		= (is_null($row["date_received"]) ? "" : $row["date_received"]);
			$this->contact_flag		= $row["contact_flag"];
			$this->gift_first_name		= $row["gift_first_name"];
			$this->gift_last_name		= $row["gift_last_name"];
			$this->gift_street		= $row["gift_street"];
			$this->gift_city		= $row["gift_city"];
			$this->gift_state		= $row["gift_state"];
			$this->gift_country		= $row["gift_country"];
			$this->gift_zip			= $row["gift_zip"];
			$this->matching_donation	= $row["matching_donation"];
			$this->show_donation		= $row["show_donation"];
			$this->direct_donation		= $row["direct_donation"];
			$this->noncash_donation		= $row["noncash_donation"];
			return true;
		}
	}

	function __construct()
	{
		$this->donation_change_id = 0;
		$this->donation_id = 0;
		$this->payment_authorized = "N";
		$this->payment_received = "N";
		$this->matching_donation = "N";
		$this->show_donation = "N";
		$this->direct_donation = "N";
		$this->noncash_donation = "N";
		$this->donation_date = date("Y-m-d H:i:s");
		$this->change_date = date("Y-m-d H:i:s");
	}

	function save_donation_change()
	{
		global $db_link;
		$this->payment_no = strtr($this->payment_no, " -","");
		if ($this->donation_change_id == 0)
		{
			// Insert new donation
			$sql = "Insert donation_changes (donation_id, change_date, change_user_id, user_id, donation_amount, refund_flag, donation_date, payment_type_id";
			$sql .= ", payment_number, payment_authorized, payment_received, date_received, contact_flag";
			$sql .= ", gift_first_name, gift_last_name, gift_street, gift_city, gift_state, gift_country, gift_zip, matching_donation, show_donation, direct_donation, noncash_donation) values (";
			$sql .= "'$this->donation_id', '".date("Y-m-d H:i:s")."', '$this->change_user_id', '$this->user_id', '$this->donation_amount', '$this->refund_flag'";
			$sql .= ", ".(empty($this->donation_date) ? "NULL" : "'$this->donation_date'");
			$sql .= ", '$this->payment_type_id', '".substr($this->payment_no, -4)."', '$this->payment_authorized'";
			$sql .= ", '$this->payment_received', ".(empty($this->date_received) ? "NULL" : "'$this->date_received'");
			$sql .= ", '$this->contact_flag', '$this->gift_first_name', '$this->gift_last_name', '$this->gift_street'";
			$sql .= ", '$this->gift_city', '$this->gift_state', '$this->gift_country', '$this->gift_zip', '$this->matching_donation', '$this->show_donation', '$this->direct_donation', '$this->noncash_donation')";
			if ($db_link->query($sql))
			{
				$this->donation_change_id = mysqli_insert_id($db_link);
				return true;
			} else {
				$this->error_message = mysqli_error($db_link)."<BR>$sql";
				return false;
			}
		}
	}	# end of save_donation_changes function
}	# end of class donation changes
?>