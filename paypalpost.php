<?
	error_reporting(E_ALL);
	$paypal_param = array();
	$paypal_param["first_name"]	= "Eric";
	$paypal_param["last_name"]	= "Vaterlaus";
	$paypal_param["address1"]	= "PO Box 14";
	$paypal_param["city"]		= "Colton";
	$paypal_param["state"]		= "OR";
	$paypal_param["zip"]		= "97017";
	$paypal_param["country"]	= "US";
	$paypal_param["notify_url"] = "http://www.donate2educate.org/paypal_complete.php";
	$paypal_param["image_url"]	= "http://www.donate2educate.org/images/banner_paypal.jpg";
	$paypal_param["no_shipping"]= "1"; # Do not prompt for a shipping address
	$paypal_param["no_note"]	= "1"; # Do not prompt for a note
	$paypal_param["return"]		= "http://www.donate2educate.org/paypal_complete.php";
	$paypal_param["rm"]		= "2";
	$paypal_param["cbt"]		= "Complete Donation";
	$paypal_param["cancel_return"]	= "http://www.donate2educate.org/paypal_complete.php";
	$paypal_param["cmd"]		= "_cart";
	$paypal_param["upload"]		= "1";
	$paypal_param["business"] 	= "donate@donate2educate.org";
	$paypal_param["invoice"]	= "TESTDONATION";

	reset($donation->donation_project_list);
	for($item_idx = 1; $item_idx<=3; $item_idx++) {
		$paypal_param["item_name_".$item_idx] = "Request $item_idx";
		$paypal_param["amount_".$item_idx] = (3.45*$item_idx);
	}
	$paypal_param["item_name_".$item_idx] = "Payment Processing Fees";
	$paypal_param["amount_".$item_idx] = ($item_idx*.25);
	#http_redirect("https://www.paypal.com/cgi-bin/webscr", $paypal_param, true, HTTP_REDIRECT_POST);
	phpinfo();
	echo "Paypal";
?>