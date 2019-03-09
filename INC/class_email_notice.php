<?
class email_notice
{
	var $email_notice_id;
	var $email;
	var $email_complete;
	var $internal_key;
	var $last_date_sent;
	var $suppress_date;
	var $ip_address;
	var $bounce_back_msg;
	var $bounce_back_date;
	var $error_message;

	function __construct($email)
	{
		$this->email_notice_id  = "0";
		$this->last_date_sent   = date("Y-m-d h:n:s");
		$this->internal_key     = md5(uniqid(rand()));
		$this->suppress_date    = "";
		$this->bounce_back_date = "";
		$this->email_complete   = $email;
		$this->email            = $this->func_email_only($email);
		if (!empty($email))
		{
			$this->load_email_notice($email);
		}
	}

	private function populate_properties($row)
	{
		$this->email_notice_id  = $row["email_notice_id"];
		$this->email            = $row["email"];
		$this->email_complete   = $row["email_complete"];
		$this->internal_key     = $row["internal_key"];
		$this->last_date_sent   = $row["last_date_sent"];
		$this->suppress_date    = (is_null($row["suppress_date"]) ? "" : $row["suppress_date"]);
		$this->bounce_back_msg  = $row["bounce_back_msg"];
		$this->bounce_back_date = (is_null($row["bounce_back_date"]) ? "" : $row["bounce_back_date"]);
		$this->ip_address       = $row["ip_address"];
	}

	function load_email_notice($email)
	{
		global $db_link;
		$results = $db_link->query("select * from email_notice where email = '".$this->func_email_only($email)."'");
		if (mysqli_num_rows($results) == 0) {
			$results->close();
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->populate_properties($row);
			$results->close();
			return true;
		}
	}

	function load_email_notice_from_key($key)
	{
		global $db_link;
		$results = $db_link->query("select * from email_notice where internal_key = '$key'");
		if (mysqli_num_rows($results) == 0) {
			$results->close();
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->populate_properties($row);
			$results->close();
			return true;
		}
	}

	function save_email_notice()
	{
		global $db_link;
		if($this->email_notice_id == 0)
		{
			if($this->email != $this->func_email_only($this->email))
			{
				$this->email_complete = $this->email;
				$this->email = $this->func_email_only($this->email);
			}
			$sql = "insert into email_notice (email, email_complete, internal_key, last_date_sent, suppress_date, ";
			$sql .= "ip_address, bounce_back_msg, bounce_back_date) values ('$this->email','$this->email_complete',";
			$sql .= "'$this->internal_key',".($this->last_date_sent == "" ? "null" : "'$this->last_date_sent'").",";
			$sql .= ($this->suppress_date == "" ? "null" : "'$this->suppress_date'").",'".$_SERVER["REMOTE_ADDR"]."'";
			$sql .= ",'$this->bounce_back_msg',".($this->bounce_back_date == "" ? "null" : "'$this->bounce_back_date'").")";
			if ($db_link->query($sql))
			{
				$this->email_notice_id = mysqli_insert_id($db_link);
				return true;
			}
			else
			{
				$this->error_message = mysqli_error($db_link)."$sql";
				return false;
			}
		}
		else
		{
			$sql = "update email_notice set internal_key = '$this->internal_key'";
			$sql .= ", last_date_sent = ".($this->last_date_sent == "" ? "null" : "'$this->last_date_sent'");
			$sql .= ", suppress_date = ".($this->suppress_date == "" ? "null" : "'$this->suppress_date'");
			$sql .= ", ip_address = '$this->ip_address', bounce_back_msg = '$this->bounce_back_msg'";
			$sql .= ", bounce_back_date = ".($this->bounce_back_date == "" ? "null" : "'$this->bounce_back_date'");
			$sql .= " where email_notice_id = '$this->email_notice_id'";
			if ($db_link->query($sql))
			{
				return true;
			}
			else
			{
				$this->error_message = mysqli_error($db_link)."$sql";
				return false;
			}
		}
	}

	function suppress_email($internal_key)
	{
		if($this->load_email_notice_from_key($internal_key))
		{
			$this->suppress_date = date("Y-m-d h:n:s");
			$this->ip_address = $_SERVER["REMOTE_ADDR"];
			$this->save_email_notice();
			return true;
		}
		else
		{
			return false;
		}
	}

	function bounce_back($bounce_back_msg)
	{
		global $db_link;
		if(!$this->email_notice_id == 0)
		{
			$sql = "update email_notice set bounce_back_date = '".date("Y-m-d h:n:s")."'";
			$sql .= ", bounce_back_msg = '$bounce_back_msg' where email_notice_id = '$this->email_notice_id'";

			if($db_link->query($sql))
			{
				$this->load_email_notice($this->email);
				return true;
			}
			else
			{
				$this->error_message = mysqli_error($db_link)."<BR>$sql";
				return false;
			}
		}
		else
		{
			$this->error_message = "No email notice loaded.";
			return false;
		}
	}

	function func_name_only($email)
	{
		if(empty($email))
			$email = $this->email_complete;
		$email = preg_replace("\"","",$email);
		if(strpos($email,"<"))
		{
			return substr($email,0,strpos($email,"<")-1);
		}
		elseif(strpos($email,"("))
		{
			return substr($email,0,strpos($email,"(")-1);
		}
		elseif(strpos($email,"@"))
		{
			return $email;
		}
		else
			return "";
	}

	function func_email_only($email)
	{
		if(empty($email))
			$email = $this->email;
		if(strpos($email,"<"))
		{
			return substr($email,strpos($email,"<")+1,strrpos($email,">")-strpos($email,"<")-1);
		}
		elseif(strpos($email,"("))
		{
			return substr($email,strpos($email,"(")+1,strrpos($email,")")-strpos($email,"(")-1);
		}
		elseif(strpos($email,"@"))
		{
			return $email;
		}
		else
			return "";
	}
}	// end of class email_notice
?>