<?
class disbursements
{
	var $disbursement_list;
	var $error_message;

	function __construct()
	{
		$this->disbursement_list = array();
	}

	function add_disbursement($row) {
		$newdisbursement = new disbursement();
		$newdisbursement->disbursement_id 		= $row["disbursement_id"];
		$newdisbursement->project_id			= $row["project_id"];
		$newdisbursement->disbursement_amount	= $row["disbursement_amount"];
		$newdisbursement->disbursement_date		= (is_null($row["disbursement_date"]) ? "now()" : $row["disbursement_date"]);
		$newdisbursement->recipient_name		= $row["recipient_name"];
		$newdisbursement->tran_no				= $row["tran_no"];
		$this->disbursement_list[$row["disbursement_id"]] = $newdisbursement;
	}

	function load_disbursements($project_id) {
		global $db_link;
		if (!empty($project_id)) {
			$results = $db_link->query("Select * from disbursement where project_id = $project_id order by disbursement_date");
			if (mysqli_num_rows($results) > 0 ) {
				while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
					$this->add_disbursement($row);
				}
				mysqli_free_result($results);
			}
		}
	}

	function count() {
		return count($this->disbursement_list);
	}
}
?>