<?
require_once "inc/class_user.php";
class project_comment
{
	var	$project_comment_id;
	var	$project_id;
	var	$date_entered;
	var $entered_by;
	var $comment;
	var $error_message;

	function __construct()
	{
		$this->project_comment_id = 0;
	}

	function load_project_comment ($commentid)
	{
		global $db_link;
		$sql = "select * from project_comments where project_comment_id = '$commentid'";
		$results = $db_link->query($sql);
		if (mysqli_num_rows($results) == 0)
		{
			$results->close();
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->project_comment_id	= $row["project_comment_id"];
			$this->project_id 			= $row["project_id"];
			$this->date_entered			= $row["date_entered"];
			$this->entered_by			= $row["entered_by"];
			$this->comment				= $row["comment"];
			$results->close();
			return true;
		}
	} // load project comment

	function save_project_comment ()
	{
		global $db_link;
		if ($this->project_comment_id == 0)
		{
			// Insert a new comment.
			$sql = "insert project_comments (project_id, date_entered, entered_by, comment) values ('$this->project_id', '".date("Y-m-d")."', '$this->entered_by', '";
			$sql .= (get_magic_quotes_gpc() ? addslashes(stripslashes($this->comment)) : addslashes($this->comment))."')";
			$db_link->query($sql);
			if (mysqli_errno == 0)
			{
				$this->project_comment_id = mysqli_insert_id($db_link);
				return true;
			} else {
				$this->error_message = mysqli_error($db_link)."<br>$sql";
				return false;
			}
		} else {
			// Update an existing comment
			$sql = "update project_comments set comment = '".(get_magic_quotes_gpc() ? addslashes(stripslashes($this->comment)) : addslashes($this->comment))."'";
			$sql .= " where project_comment_id = '$this->project_comment_id'";
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

	function delete_project_comment ()
	{
		global $db_link;
		if ($this->project_comment_id != 0)
		{
			// Delete a comment.
			$sql = "delete from project_comments where project_comment_id = '$this->project_comment_id'";
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