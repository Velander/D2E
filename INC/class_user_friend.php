<?
class user_friend
{
	var $friend_id;
	var $user_id;
	var $email;
	var $name;
	var $include;
	var $error_message;

	function lookup_friend($name, $email)
	{
		// Check login
		global $db_link;
		$friendids = array();
		$name = ereg_replace("--", "", $name);
		$email = ereg_replace("--", "", $email);
		$sql = "Select friend_id from user_friend where ".((!empty($name)) ? "name like '%$name%'":"").(!empty($email) ? (!empty($name) ? " and ":""."email = '$email'") : "");
		if ($results = $db_link->query($sql)) {
			if (mysqli_num_rows($results) == 0) {
				$this->error_message = mysqli_error($db_link)."<BR>";
				return false;
			} else {
				while (list($friendid) = mysqli_fetch_row($results))
					$friendids[] = $friendid;
			}
			$results->close();
			return $friendids;
		} else {
			$this->error_message = mysqli_error($db_link)."<BR>";
			return false;
		}
	}

	function name_exists($name)
	{
		// Check for existing friend
		global $db_link;
		$name = ereg_replace("--", "", $name);
		$results = $db_link->query("Select friend_id from user_friend where name = '$name'");
		if (mysqli_num_rows($results) == 0) {
			$results->close();
			return false;
		} else {
			$results->close();
			return true;
		}
	}

	function email_exists($email)
	{
		// Check login
		global $db_link;
		$email = ereg_replace("--", "", $email);
		$results = $db_link->query("Select friend_id from user_friend where email = '$email'");
		if (mysqli_num_rows($results) == 0) {
			$results->close();
			return false;
		} else {
			$results->close();
			return true;
		}
	}

	function assign_friend($row)
	{
		$this->friend_id 	= $row["friend_id"];
		$this->user_id 	= $row["user_id"];
		$this->name	 	= $row["name"];
		$this->email 	= $row["email"];
		$this->include 	= $row["include"];
	}

	function load_friend($friendid)
	{
		global $db_link;
		$results = $db_link->query("Select * from user_friend where friend_id = '$friendid'");
		if (mysqli_num_rows($results) == 0) {
			$this->error_message = mysqli_error($db_link)."<BR>";
			return false;
		}
		$row = mysqli_fetch_array($results, MYSQLI_ASSOC);
		$this->assign_friend($row);
		$results->close();
		return true;
	}

	function save_friend()
	{
		global $db_link;
		if (empty($this->friend_id)) {
			$sql = "insert user_friend (user_id, name, email, verified) values";
			$sql .= "('".mysqli_escape_string($db_link, $this->user_id)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->name)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->email)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->include)."')";
			$results = $db_link->query($sql);
			if (mysqli_errno()  == 0) {
				$this->friend_id = mysqli_insert_id($db_link);
				return true;
			} else {
				$this->error_message = mysqli_error($db_link)."<BR>".$sql;
				return false;
			}
		} else {
			$sql = "update user_friend Set name = '".mysqli_escape_string($db_link, $this->name)."', email = '".mysqli_escape_string($db_link, $this->email)."', include = '$this->include'";
			$sql .= " where friend_id = '$this->friend_id'";
			$results = $db_link->query($sql);
			if (mysqli_errno()  == 0) {
				return true;
			} else {
				$this->error_message = mysqli_error($db_link)."<BR>".$sql;
				return false;
			}
		}
	}

	function delete_friend()
	{
		global $db_link;
		if (!empty($this->friend_id)) {
			$sql = "Delete from user_friend where friend_id = '$this->friend_id'";
			if ($db_link->query($sql) == 0) {
				$this->error_message = mysqli_error($db_link)."<BR>$sql";
				return false;
			} else {
				unset ($this->friend_id);
				return true;
			}
		} else {
			$this->error_message = "No Friend Loaded.";
			return false;
		}
	}

}	// end of class friend
?>