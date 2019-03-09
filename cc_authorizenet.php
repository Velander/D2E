<?
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

#$an_login = $module_params["param01"];
#$an_password = $module_params["param02"];
#$an_prefix = $module_params["param04"];
#$an_curr = $module_params["param05"];

$an_login 		= $district->cc_login;
$an_password 	= $district->cc_password;
$an_prefix 		= $district->cc_prefix;
$an_curr 		= $district->cc_currency;

$post[] = "x_Login=".$an_login;
$post[] = "x_Tran_Key=".$an_password;
$post[] = "x_Version=3.1";
$post[] = "x_Test_Request=".($district->cc_live == "N" ? "FALSE" : "TRUE");
$post[] = "x_Delim_Data=True";
$post[] = "x_Delim_Char=,";
$post[] = "x_Encap_Char=|";
$post[] = "x_ADC_URL=False";
$post[] = "x_First_Name=".$user->first_name;
$post[] = "x_Last_Name=".$user->last_name;
$post[] = "x_Address=".$user->street;
$post[] = "x_City=".$user->city;
$post[] = "x_State=".((!empty($user->state))? $user->state : "Non US");
$post[] = "x_Zip=".$user->zip;
$post[] = "x_Country=".$user->country;
$post[] = "x_Phone=".$user->phone;
$post[] = "x_Cust_ID=".$user->user_id;
$post[] = "x_Customer_IP=".$REMOTE_ADDR;
$post[] = "x_Email=".$user->email;
$post[] = "x_Merchant_Email=".$district->email;
$post[] = "x_Invoice_Num=".$an_prefix.join("-",$secure_oid);
$post[] = "x_Amount=".$donation->donation_amount;
$post[] = "x_Currency_Code=".$an_curr;
$post[] = "x_Method=CC";
$post[] = "x_Type=auth_capture";
$post[] = "x_Card_Num=".$donation->payment_no;
$post[] = "x_Exp_Date=".$donation->payment_exp_date;
$post[] = "x_Card_Code=".$donation->payment_cvv2"];

print "calling authorize.net<BR>";

list($a,$return) = func_https_request("POST","https://secure.authorize.net:443/gateway/transact.dll",$post);
$mass = split("\|,\|","|,".$return);

#|3|,|1|,|39|,|The supplied currency code is either invalid, not supported, not allowed for this merchant or doesn't have an exchange rate.|,|000000|,|P|,|0|,||,||,|26.56|,||,|auth_capture|,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,|723CAF563B19FDC52ACDB6999AB876B7|,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||


print $return;
print_r($mass);

if($mass[1]==1)
{
	#$bill_output[code] = 1;
	#$bill_output[billmes] = " Approval Code: ".$mass[7];
	$donation->payment_auth_status = 1;
	$donation->payment_auth_code = $mass[7];
	$donation->payment_auth_message = " Approval Code: ".$mass[7];
}
else
{
	#$bill_output[code] = 2;
	#$bill_output[billmes] = ($mass[1]==2 ? "Declined" : "Error").": ";
	#$bill_output[billmes].= $mass[4]." (N ".$mass[3]." / Sub ".$mass[2].")";
	$donation->payment_auth_status = 2;
	$donation->payment_auth_message = ($mass[1]==2 ? "Declined" : "Error").": ";
	$donation->payment_auth_message.= $mass[4]." (N ".$mass[3]." / Sub ".$mass[2].")";
}


if(!empty($mass[6]))
	#$bill_output[avsmes] = (empty($avserr[$mass[6]]) ? "Code: ".$mass[6] : $avserr[$mass[6]]);
	$donation->payment_auth_avs_msg = (empty($avserr[$mass[6]]) ? "Code: ".$mass[6] : $avserr[$mass[6]]);

if(!empty($mass[39]))
	#$bill_output[cvvmes] = (empty($cvverr[$mass[39]]) ? "Code: ".$mass[39] : $cvverr[$mass[39]]);
	$donation->payment_auth_cvv_msg = (empty($cvverr[$mass[39]]) ? "Code: ".$mass[39] : $cvverr[$mass[39]]);

#print_r($bill_output);
#exit;

?>
