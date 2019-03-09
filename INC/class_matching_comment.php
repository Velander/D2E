<?
class matching_comment
{
	var $matching_comment_id;
	var $matching_id;
	var $user_id;
	var $datecreated;
	var $comment;
	var $error_message;

	function __construct()
	{
		$this->matching_comment_id = "0";
		$this->matching_id = "0";
		$this->datecreated = date("Y-m-d H:i:s");
	}

	function load_matching_comment($matching_comment_id)
	{
		global $db_link;
		$results = $db_link->query("select * from matching_comment where matching_comment_id = '$matching_comment_id'");
		if (mysqli_num_rows($results) == 0) {
			$results->close();
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->matching_comment_id 	= $row["matching_comment_id"];
			$this->matching_id		= $row["matching_id"];
			$this->user_id			= $row["user_id"];
			$this->datecreated		= $row["datecreated"];
			$this->comment			= $row["comment"];
			$results->close();
			return true;
		}
	}

	function delete_matching_comment()
	{
		global $db_link;
		if ($this->matching_comment_id) {
			$sql = "Delete from matching_comment where matching_comment_id = '$this->matching_comment_id'";
			if ($db_link->query($sql)) {
				return true;
			} else {
				$this->error_message = mysqli_error($db_link)."<BR>$sql";
				return false;
			}
		} else {
			return false;
		}
	}
	function save_matching_comment()
	{
		global $db_link;
		if ($this->matching_comment_id) {
			# Update existing comment.
			$sql = "Update matching_comment set user_id = '$this->user_id', comment = '$this-comment' where matching_comment_id = '$this->matching_comment_id'";
			if ($db_link->query($sql)) {
				return true;
			} else {
				$this->error_message = "Save error: ".mysqli_error($db_link)."<BR>$sql";
				return false;
			}
		} else {
			# Insert new comment
			if ($this->comment)
			{
				$sql = "Insert matching_comment (matching_id, user_id, datecreated, comment) values ('$this->matching_id', '$this->user_id', '$this->datecreated', '$this->comment')";
				if ($db_link->query($sql)) {
					$this->matching_comment_id = mysqli_insert_id($db_link);
					return true;
				} else {
					$this->error_message = "Save error: ".mysqli_error($db_link)."<BR>$sql";
					return false;
				}
			} else {
				$this->error_message = "Cannot save blank comment.";
				return false;
			}
		}
	}
}	// end of class matching_comment
?>