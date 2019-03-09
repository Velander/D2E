<?
class user_report
{
	var $report_id;
	var $user_id;
	var $report_name;
	var $create_date;
	var $error_message;

	function __construct()
	{
		$this->create_date = date("Y-m-d");
	}

	function load_report($userid, $reportid)	{

		global $db_link;
		$results = $db_link->query("Select * from user_reports where user_id = '$userid' and report_id = '$reportid'");
		if (mysqli_num_rows($results) == 0) {
			$this->error_message = mysqli_error($db_link)."<BR>";
			return FALSE;
		}
		$row = mysqli_fetch_array($results, MYSQLI_ASSOC);
		$this->report_id = $reportid;
		$this->user_id = $userid;
		$this->report_name = $row["report_name"];
		$this->create_date = $row["create_date"];

		$results->close();
		return TRUE;
	}

	function save_report()	{
		global $db_link;
		if (empty($this->report_id)) {
			$sql = "Insert user_reports (user_id, create_date, report_name) values";
			$sql .= "('".mysqli_escape_string($db_link, $this->user_id)."'";
			$sql .= ", now(), '".mysqli_escape_string($db_link, $this->report_name)."')";
			$results = $db_link->query($sql);
			if (mysqli_errno()  == 0) {
				$this->report_id = mysqli_insert_id($db_link);
				$this->error_message = mysqli_error($db_link);
				return true;
			} else {
				$this->error_message = mysqli_error($db_link)."<BR>".$sql;
				return false;
			}
		} else {
			$sql = "Update user_reports Set report_name = '".mysqli_escape_string($db_link, $this->report_name)."'";
			$sql .= " where report_id = '$this->report_id'";
			$results = $db_link->query($sql);
			if (mysqli_errno()  == 0) {
				return true;
			} else {
				$this->error_message = mysqli_error($db_link)."<BR>".$sql;
				return false;
			}
		}
	}

	function delete_report()	{
		if (!empty($this->report_id)) {
			$sql = "Delete from user_reports where user_id = '$this->user_id' and report_id = '$this->report_id'";
			if ($db_link->query($sql) == 0) {
				$this->error_message = mysqli_error($db_link)."<BR>$sql";
				return FALSE;
			} else {
				unset ($this->report_id);
				return True;
			}
		} else {
			$this->error_message = "No User Loaded.";
			return FALSE;
		}
	}

}	// end of class district
?>