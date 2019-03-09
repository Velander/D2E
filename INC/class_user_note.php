<?
require_once "inc/class_user.php";
class user_note
{
	var	$user_note_id;
	var	$user_id;
	var	$date_entered;
	var $entered_by;
	var $note;
	var $error_message;

	function __construct()
	{
		$this->user_note_id = 0;
	}

	function load_user_note ($noteid)
	{
		global $db_link;
		$sql = "select * from user_notes where user_note_id = '$noteid'";
		$results = $db_link->query($sql);
		if (mysqli_num_rows($results) == 0)
		{
			$results->close();
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->user_note_id			= $row["user_note_id"];
			$this->user_id	 			= $row["user_id"];
			$this->date_entered			= $row["date_entered"];
			$this->entered_by			= $row["entered_by"];
			$this->note				= $row["note"];
			$results->close();
			return true;
		}
	} // load user note

	function save_user_note ()
	{
		global $db_link;
		if ($this->user_note_id == 0)
		{
			// Insert a new note.
			$sql = "insert user_notes (user_id, date_entered, entered_by, note) values ('$this->user_id', '".date("Y-m-d")."', '$this->entered_by', '";
			$sql .= (get_magic_quotes_gpc() ? addslashes(stripslashes($this->note)) : addslashes($this->note))."')";
			$db_link->query($sql);
			if (mysqli_errno == 0)
			{
				$this->user_note_id = mysqli_insert_id($db_link);
				return true;
			} else {
				$this->error_message = mysqli_error($db_link)."<br>$sql";
				return false;
			}
		} else {
			// Update an existing note
			$sql = "update user_notes set note = '".(get_magic_quotes_gpc() ? addslashes(stripslashes($this->note)) : addslashes($this->note))."'";
			$sql .= " where user_note_id = '$this->user_note_id'";
			$db_link->query($sql);
			if (mysqli_errno == 0)
			{
				return true;
			} else {
				$this->error_message = mysqli_error($db_link)."<br>$sql";
				return false;
			}
		}
	}

	function delete_user_note ()
	{
		global $db_link;
		if ($this->user_note_id != 0)
		{
			// Delete a note.
			$sql = "delete from user_notes where user_note_id = '$this->user_note_id'";
			$db_link->query($sql);
			if (mysqli_errno == 0)
			{
				return true;
			} else {
				$this->error_message .= mysqli_error($db_link)."<br>$sql";
				return false;
			}
 		} else {
			return false;
		}
	}

	function author_name()
	{
		$user = new user();
		$user->load_user($this->entered_by);
		return $user->first_name." ".$user->last_name;
	}
}