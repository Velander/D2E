<?
class referral_email
{
	var $user_id;
	var $date_created;
	var $email;
	var $first_name;
	var $last_name;
	var $unique_id;
	var $date_confirmed;
	var $referral_firstname;
	var $referral_lastname;
	var $referral_schoolid;
	var $error_message;

	function __construct()
	{
		$this->date_created = date("Y-m-d");
		$this->unique_id = md5(uniqid(rand(),1));
	}

	function duplicate_email($email, $user_id)	{
		// Check email
		global $db_link;
		$email = ereg_replace("--", "", $email);
		$sql = "Select unique_id from referral_id where email = '$email' and user_id = '$user_id'");
		if ($results = $db_link->query($sql)) {
			if (mysqli_num_rows($results) == 0) {
				$results->close();
				return FALSE;
			} else {
				$results->close();
				return TRUE;
			}
		} else {
			$this->error_message = mysqli_error($db_link)."<BR>";
			return FALSE;
		}
	}

	function assign_user($row)	{
		$this->user_id 		= $row["user_id"];
		$this->date_created	= (is_null($row["date_created"]) ? "" : $row["date_created"]);
		$this->email		= $row["email"];
		$this->first_name 	= $row["first_name"];
		$this->last_name 	= $row["last_name"];
		$this->unique_id	= $row["unique_id"];
		$this->date_confirmed		= (is_null($row["date_confirmed"]) ? "" : $row["date_confirmed"]);
		$this->referral_firstname = $row["referral_firstname"];
		$this->referral_lastname = $row["referral_lastname"];
		$this->referral_schoolid = $row["referral_schoolid"];
	}

	function load_referral($unique_id)
	{
		global $db_link;
		$results = $db_link->query("Select * from referral_email where unique_id = '$unique_id'");
		if (mysqli_num_rows($results) == 0) {
			$this->error_message = mysqli_error($db_link)."<BR>";
			return FALSE;
		}
		$row = mysqli_fetch_array($results, MYSQLI_ASSOC);
		$this->assign_user($row);
		$results->close();
		return TRUE;
	}

	function save_referral()
	{
		global $db_link;
		if (empty($this->unique_id))
			$this->unique_id = md5(uniqid(rand(),1));
			$sql = "Insert referral_email (user_id, date_created, first_name, last_name, unique_id, referral_firstname, referral_lastname, referral_schoolid) values";
			$sql .= "('".mysqli_escape_string($db_link, $this->user_id)."'";
			$sql .= ", now()";
			$sql .= ", '".mysqli_escape_string($db_link, $this->first_name)."', '".mysqli_escape_string($db_link, $this->last_name)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->unique_id)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->referral_firstname)."', '".mysqli_escape_string($db_link, $this->referral_lastname)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->referral_schoolid)."')";
			$results = $db_link->query($sql);
			if (mysqli_errno()  == 0) {
				$this->user_id = mysqli_insert_id($db_link);
				$this->error_message = mysqli_error($db_link);
				return true;
			} else {
				$this->error_message = mysqli_error($db_link)."<BR>".$sql;
				return false;
			}
		} else {
			$sql = "Update referral_email Set user_id = '".mysqli_escape_string($db_link, $this->user_id)."', ";
			$sql .= ", date_created = '".$this->date_created."', first_name = '".mysqli_escape_string($db_link, $this->first_name)."', last_name = '".mysqli_escape_string($db_link, $this->last_name)."'";
			$sql .= ", date_confirmed = '".empty($this->date_confirmed) ? "Null" : $this->date_confirmed."', referral_firstname = '$this->referral_firstname', referral_lastname = '$this->referral_lastname', referral_schoolid = '$this->referral_schoolid'";
			$sql .= " where unique_id = '".mysqli_escape_string($db_link, $this->unique_id)."'");
			$results = $db_link->query($sql);
			if (mysqli_errno()  == 0) {
				return true;
			} else {
				$this->error_message = mysqli_error($db_link)."<BR>".$sql;
				return false;
			}
		}
	}

	function confirm_referral($unique_id)
	{
		global $db_link;
		if !empty($unique_id) {
			$sql = "Update referral_email Set date_confirmed = now() where unique_id = '$unique_id'";
			if ($db_link->query($sql) == 0) {
				$this->error_message = mysqli_error($db_link)."<BR>$sql";
				return FALSE;
			} else {
				load_referral($unique_id);
				return True;
			}
		} else {
			if (!empty($this->unique_id)) {
				$sql = "Update referral_email Set date_confirmed = now() where unique_id = '$this->unique_id'";
				if ($db_link->query($sql) == 0) {
					$this->error_message = mysqli_error($db_link)."<BR>$sql";
					return FALSE;
				} else {
					$this->date_confirmed = date("Y-m-d");
					return True;
				}
			} else {
				$this->error_message = "No Referral Loaded.";
				return FALSE;
			}
		}
	}
	function delete_referral()
	{
		global $db_link;
		if (!empty($this->unique_id)) {
			$sql = "Delete from referral_email where unique_id = '$this->unique_id'";
			if ($db_link->query($sql) == 0) {
				$this->error_message = mysqli_error($db_link)."<BR>$sql";
				return FALSE;
			} else {
				unset ($this->unique_id);
				return True;
			}
		} else {
			$this->error_message = "No Referral Loaded.";
			return FALSE;
		}
	}

}	// end of class district
?>