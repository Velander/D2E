<?
class payment_type
{
	var	$payment_type_id;
	var $payment_type_description;
	var $credit_card_flag;
	var $inactive;

	function load_payment_type($payment_type_id)	{
		global $db_link;
		$results = $db_link->query("select * from payment_type where payment_type_id = '$payment_type_id'");
		if (mysqli_num_rows($results) == 0) {
			mysqli_free_result($results);
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->payment_type_id 			= $row["payment_type_id"];
			$this->payment_type_description	= $row["payment_description"];
			$this->credit_card_flag			= $row["credit_card_flag"];
			$this->inactive					= $row["Inactive"];
			mysqli_free_result($results);
			return true;
		}
	}
}	// end of class payment_type
?>