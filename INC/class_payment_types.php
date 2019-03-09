<?
class payment_types
{
	var $payment_type_list;
	var $error_message;

	function add_payment_type($row) {
		$newpayment_type = new payment_type();
		$newpayment_type->payment_type_id 			= $row["payment_type_id"];
		$newpayment_type->payment_type_description 	= $row["payment_description"];
		$newpayment_type->credit_card_flag			= $row["credit_card_flag"];
		$newpayment_type->inactive					= $row["Inactive"];
		$this->payment_type_list[$row["payment_type_id"]] = $newpayment_type;
	}

	function load_payment_types() {
		global $db_link;
		$results = $db_link->query("Select * from payment_type order by payment_description");
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_payment_type($row);
		}
		mysqli_free_result($results);
	}

	function payment_type_description($payment_type_id) {
		$payment_type = $this->payment_type_list[$payment_type_id];
		return $payment_type->payment_type_description;
	}

	function count() {
		return count($this->payment_type_list);
	}
}	// end of class payment_types
?>