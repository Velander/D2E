<?
class banner_comment
{
	var $banner_comment_id;
	var $banner_id;
	var $user_id;
	var $datecreated;
	var $comment;
	var $error_message;

	function __construct()
	{
		$this->banner_comment_id = "0";
		$this->banner_id = "0";
		$this->datecrated = date("Y-m-d H:i:s");
	}

	function load_banner_comment($banner_comment_id)
	{
		global $db_link;
		$results = $db_link->query("select * from banner_comment where banner_comment_id = '$banner_comment_id'");
		if (mysqli_num_rows($results) == 0) {
			$results->close();
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->banner_comment_id 	= $row["banner_comment_id"];
			$this->banner_id		= $row["banner_id"];
			$this->user_id			= $row["user_id"];
			$this->datecreated		= $row["datecreated"];
			$this->comment			= $row["comment"];
			$results->close();
			return true;
		}
	}

	function save_banner_comment()
	{
		global $db_link;
		if ($this->banner_comment_id) {
			# Update existing comment.
			$sql = "Update banner_comment set user_id = '$User_ID', comment = '$this-comment' where banner_comment_id = '$this->banner_comment_id'";
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
				$sql = "Insert banner_comment (banner_id, user_id, datecreated, comment) values ('$this->banner_id', '$User_ID', $this->date_created', '$this->comment')";
				if ($db_link->query($sql)) {
					$this->banner_comment_id = mysqli_insert_id($db_link);
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
}	// end of class banner_comment
?>