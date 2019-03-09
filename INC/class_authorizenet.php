<?
class authorizenet
{
	var $an_login;
	var $an_transactionid;
	var $an_curr;
	var $an_prefix;
	var $live;
	var $user_id;
	var $merchant_email;
	var $donation_id;
	var $payment_number;
	var $payment_exp_date;
	var $payment_cvv2;
	var $payment_amount;
	var $payment_auth_status;
	var $payment_auth_code;
	var $payment_auth_message;
	var $payment_auth_avs_msg;
	var $payment_auth_cvv_msg;
	var $payment_auth_id;

	function authorize_card() {
		$avserr = array(
			"A" => "Address (Street) matches, ZIP does not",
			"E" => "AVS error",
			"N" => "No Match on Address (Street) or ZIP",
			"P" => "AVS not applicable for this transaction",
			"R" => "Retry. System unavailable or timed out",
			"S" => "Service not supported by issuer",
			"U" => "Address information is unavailable",
			"W" => "9 digit ZIP matches, Address (Street) does not",
			"X" => "Exact AVS Match",
			"Y" => "Address (Street) and 5 digit ZIP match",
			"Z" => "5 digit ZIP matches, Address (Street) does not"
		);

		$cvverr = array(
			"M" => "Match",
			"N" => "No Match",
			"P" => "Not Processed",
			"S" => "Should have been present",
			"U" => "Issuer unable to process request"
		);

		$user_rcd = new user();
		$user_rcd->load_user($this->user_id);

		$post[] = "x_Login=".$this->an_login;
		$post[] = "x_Tran_Key=".$this->an_transactionid;
		$post[] = "x_Version=3.1";
		$post[] = "x_Test_Request=".($this->live == "N" ? "TRUE" : "FALSE");
		$post[] = "x_Delim_Data=True";
		$post[] = "x_Delim_Char=,";
		$post[] = "x_Encap_Char=|";
		$post[] = "x_ADC_URL=False";
		$post[] = "x_First_Name=".$user_rcd->first_name;
		$post[] = "x_Last_Name=".$user_rcd->last_name;
		$post[] = "x_Address=".$user_rcd->street;
		$post[] = "x_City=".$user_rcd->city;
		$post[] = "x_State=".((!empty($user_rcd->state))? $user_rcd->state : "Non US");
		$post[] = "x_Zip=".$user_rcd->zip;
		$post[] = "x_Country=".$user_rcd->country;
		$post[] = "x_Phone=".$user_rcd->phone;
		$post[] = "x_Cust_ID=".$user_rcd->user_id;
		$post[] = "x_Customer_IP=".$_SERVER["REMOTE_ADDR"];
		$post[] = "x_Email=".$user_rcd->email;
		$post[] = "x_Merchant_Email=".$this->merchant_email;
		$post[] = "x_Invoice_Num=".$this->an_prefix.$this->donation_id;
		$post[] = "x_Amount=".$this->payment_amount;
		$post[] = "x_Currency_Code=".$this->an_curr;
		$post[] = "x_Method=CC";
		$post[] = "x_Type=auth_capture";
		$post[] = "x_Card_Num=".$this->payment_number;
		$post[] = "x_Exp_Date=".substr($this->payment_exp_date,0,2).substr($this->payment_exp_date,-2);
		$post[] = "x_Card_Code=".$this->payment_cvv2;

		#print "<pre>";
		list($a,$return) = func_https_request("POST","https://secure.authorize.net:443/gateway/transact.dll",$post);
		$mass = split("\|,\|","|,".$return);

		#|3|,|1|,|39|,|The supplied currency code is either invalid, not supported, not allowed for this merchant or doesn't have an exchange rate.|,|000000|,|P|,|0|,||,||,|26.56|,||,|auth_capture|,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,|723CAF563B19FDC52ACDB6999AB876B7|,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||

#print $return;
#print_r($post);

		$this->payment_auth_id = $mass[7];
		if($mass[1]==1)
		{
			$this->payment_auth_status = 1;
			$this->payment_auth_code = $mass[5];
			$this->payment_auth_message = "Approval Code: ".$mass[5];
		} elseif($mass[1] == 4) {
			$this->payment_auth_status = 3;
			$this->payment_auth_message = $mass[4]." (Reason Code ".$mass[3]." / Sub ".$mass[2].")";
		} else {
			$this->payment_auth_status = 2;
			$this->payment_auth_message = ($mass[1]==2 ? "Declined" : "Error").": ";
			$this->payment_auth_message.= $mass[4]." (Reason Code ".$mass[3]." / Sub ".$mass[2].")";
		}

		if(!empty($mass[6]))
			$this->payment_auth_avs_msg = (empty($avserr[$mass[6]]) ? "Code: ".$mass[6] : $avserr[$mass[6]]);

		if(!empty($mass[39]))
			$this->payment_auth_cvv_msg = (empty($cvverr[$mass[39]]) ? "Code: ".$mass[39] : $cvverr[$mass[39]]);

//if(!empty($mass[6]))
//	$bill_output['avsmes'] = (empty($avserr[$mass[6]]) ? "Code: ".$mass[6] : $avserr[$mass[6]]);
//
//if(!empty($mass[39]))
//	$bill_output['cvvmes'] = (empty($cvverr[$mass[39]]) ? "Code: ".$mass[39] : $cvverr[$mass[39]]);
//
//if(!empty($mass[40]))
//    $bill_output['cavvmes'] = (empty($cavverr[$mass[40]]) ? "Code: ".$mass[40] : $cavverr[$mass[40]]);
//

		if ($this->payment_auth_status == 1)
			return true;
		else
			return false;
	}
}	// end of class authorizenet.
?>