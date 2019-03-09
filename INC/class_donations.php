<?
class donations
{
	var $donation_list;
	var $error_message;

	function add_donation($row) {
		$newdonation = new donation();
		if ($newdonation->load_donation($row[donation_id]))
			$this->donation_list[$row[donation_id]] = $newdonation;
	}

	function load_donations($project_id) {
		global $db_link;
		if (!empty($project_id)) {
			$sql = "Select donation.donation_id from donation inner join donation_project on donation.donation_id = donation_project.donation_id where donation_project.project_id = $project_id and donation.Payment_Authorized = 'Y' order by donation.donation_date";
			$results = $db_link->query($sql);
			if (mysqli_num_rows($results) > 0 ) {
				while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
					$this->add_donation($row);
				}
				mysqli_free_result($results);
			}
		}
	}

	function count() {
		return count($this->donation_list);
	}
}
?>