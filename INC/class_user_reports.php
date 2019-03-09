<?
class user_reports
{
	var $report_list;
	var $error_message;

	function add_report($row) {
		$newreport = new user_report();
		$newreport->user_id = $row["user_id"];
		$newreport->report_name = $row["report_name"];
		$newreport->create_date = $row["create_date"];

		$this->report_list[$row["report_id"]] = $newreport;
	}

	function load_reports($userid) {
		global $db_link;
		$results = $db_link->query("Select * from user_reports where user_id = '$userid' order by report_name");
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_report($row);
		}
		mysqli_free_result($results);
	}

	function report_name($reportid) {
		$report = $this->report_list[$reportid];
		return $report->report_name;
	}

	function count() {
		return count($this->report_list);
	}
}
?>