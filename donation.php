<?
#donation.php

	function url_to_link($text) {
		$reg_exUrl = "/((((http|https|ftp|ftps)\:\/\/)|www\.)[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,4}(\/\S*)?)/";
		return preg_replace( $reg_exUrl, "<a href=\"$1\" target=\"new\">Webpage</a> ", $text );
	}
    require "inc/db_inc.php";

    require_once "inc/class_cart_item.php";
    require_once "inc/class_user.php";
    require_once "inc/class_project.php";
    require_once "inc/class_donation.php";
    require_once "inc/class_donation_project.php";
    require_once "inc/class_school.php";
    require_once "inc/class_schools.php";
    require_once "inc/class_district.php";
    require_once "inc/class_districts.php";
    require_once "inc/class_grade_level.php";
    require_once "inc/class_grade_levels.php";
    require_once "inc/class_project_type.php";
    require_once "inc/class_project_types.php";
    require_once "inc/class_affiliation.php";
    require_once "inc/class_affiliations.php";
    require_once "inc/class_state.php";
    require_once "inc/class_states.php";
    require_once "inc/class_country.php";
    require_once "inc/class_countries.php";
    require_once "inc/class_payment_type.php";
    require_once "inc/class_payment_types.php";
    require_once "inc/func.php";
    require_once "inc/func.https_libcurl.php";
    require_once "inc/class_authorizenet.php";
    require_once "inc/class_matching.php";

	$message			= $_GET["message"];
	$f_project_id 		= $_GET["f_project_id"];
	$projectid 			= $_GET["projectid"];
	$review				= $_GET["review"];
	$search_arg			= $_GET["earch_arg"];
	$f_full_amount		= $_GET["f_full_amount"];
	$f_partial_amount 	= $_GET["f_partial_amount"];
	$f_payment_choice	= $_GET["f_payment_choice"];
	$f_donation_choice	= $_GET["f_donation_choice"];
	$gift_first_name	= $_GET["gift_first_name"];
	$gift_last_name		= $_GET["gift_last_name"];
	$gift_street		= $_GET["gift_street"];
	$gift_city			= $_GET["gift_city"];
	$gift_state			= $_GET["gift_state"];
	$gift_zip			= $_GET["gift_zip"];
	$gift_country		= $_GET["gift_country"];
	$paypalStatus		= $_GET["paypalStatus"];
	$dkey				= $_GET["dkey"];

$debug = false;
if ($debug) {
	echo "loading user<br>\n";
	flush();
}
	$user = new user();
	$user->load_user($User_ID);

if ($debug) {
	echo "loading schools<br>\n";
	flush();
}
	$schools = new schools();
	$schools->load_schools();

	$districts = new districts();
	$districts->load_districts();

	$district = new district();

if ($debug) {
	echo "loading grade_levels<br>\n";
	flush();
}
	$gradelevels = new grade_levels();
	$gradelevels->load_grade_levels();

if ($debug) {
	echo "loading states<br>\n";
	flush();
}
	$states = new states();
	$states->load_states();

	$countries = new countries();
	$countries->load_countries();

if ($debug) {
	echo "loading project_types<br>\n";
	flush();
}
	$projecttypes = new project_types();
	$projecttypes->load_project_types();

	$payment_types = new payment_types();
	$payment_types->load_payment_types();

	$user_affiliations = new affiliations();
	$user_affiliations->load_affiliations($User_ID);

	$authorize_net = new authorizenet();
	$matching = new matching();

?>
<html>
<head>
<?
	$search_results = $_SESSION['search_results'];
	$search_idx	= 0;
	if (count($search_results) > 0) {
		if (!$projectid) $projectid = $f_project_id;
		while (list($sidx, $project) = each($search_results)) {
			$search_idx += 1;
			if ($project->project_id == $projectid) {
				if ($search_idx > 1)
					$prevlink = "<font size=\"-1\" color=\"$color_table_hdg_font\"><a href=\"donation.php?projectid=".$search_results[$search_idx-2]->project_id."\">[PREV]</a></font>";
				if ($search_idx < count($search_results))
					$nextlink = "<font size=\"-1\" color=\"$color_table_hdg_font\"><a href=\"donation.php?projectid=".$search_results[$search_idx]->project_id."\">[NEXT]</a></font>";
				break;
			}
		}
	}
	$_SESSION['search_idx'] = $search_idx;

	if ($_SERVER['REQUEST_METHOD'] == "POST")
        {
        	$review				= $_POST["review"];
			$search_arg			= $_POST["search_arg"];
			$Submit 			= $_POST["Submit"];
			$f_project_id		= $_POST["f_project_id"];
			$f_partial_amount 	= $_POST["f_partial_amount"];
			$f_full_amount		= $_POST["f_full_amount"];
			$f_fullwmatch_amount= $_POST["f_fullwmatch_amount"];
			$f_payment_choice	= $_POST["f_payment_choice"];
			$f_include_paypal_fee= $_POST["f_include_paypal_fee"];
			$f_contact_choice	= $_POST["f_contact_choice"];
			$gift_first_name	= $_POST["gift_first_name"];
			$gift_last_name		= $_POST["gift_last_name"];
			$gift_street		= $_POST["gift_street"];
			$gift_city			= $_POST["gift_city"];
			$gift_state			= $_POST["gift_state"];
			$gift_zip			= $_POST["gift_zip"];
			$gift_country		= $_POST["gift_country"];

            if ($debug) {
                echo "Posting<br>\n";
                flush();
            }
            if ($Submit == "Cancel")
                echo "<script type=\"text/javascript\">\nlocation.href='$http_location"."index.php'\n</script>\n";
            if ($Submit == "Choose a Different Request")
                echo "<script type=\"text/javascript\">\nlocation.href='$http_location"."donation_search.php".$search_arg."'\n</script>\n";
            if ($Submit == "Previous Step")
                if ($review == "Y")
                    $review = "D";
                elseif ($review != "Y")
                    echo "<script type=\"text/javascript\">\nlocation.href='$http_location"."donation_search.php".$search_arg."'\n</script>\n";
                else {
                    $params = "&projectid=$f_project_id&f_payment_choice=$f_payment_choice&f_donation_choice=$f_donation_choice";
                    if ($f_donation_choice == "Partial")
                        $params .= "&f_partial_amount=$f_partial_amount";
                    $params .= "&f_contact_choice=$f_contact_choice";
                    if ($f_contact_choice == "G")
                        $params .= "&gift_first_name=$gift_first_name&gift_last_name=$gift_last_name&gift_street=$gift_street&gift_city=$gift_city&gift_state=$gift_state&gift_zip=$gift_zip&gift_country=$gift_country";
                    echo "<script type=\"text/javascript\">\nlocation.href='donation.php?search_arg=".htmlentities(urlencode($search_arg)).$params."'\n</script>\n";
                }
            if ($Submit == "Add Donation To Cart") {
                # Add a donation to the User Cart
                if ($f_partial_amount) {
                    $f_partial_amount = strtr($f_partial_amount,"$,","");
                    if (!is_numeric($f_partial_amount)) {
                        $errors .= (empty($errors) ? "" : "<BR>")."An invalid donation amount was entered.";
                    } else {
                        if ($f_partial_amount < $config_min_donation_amount || $f_partial_amount > $f_full_amount)  {
                            $errors .= (empty($errors) ? "" : "<BR>")."Donation amount must be at least $".sprintf("%01.2f", $config_min_donation_amount)." and not more than ".sprintf("%01.2f", $f_full_amount).".";
                            $projectid = $f_project_id;
                        }
                    }
                    if (!$errors) {
                        $donation_amount = $f_partial_amount;
                    }
                } elseif ($f_fullwmatch_amount) {
                    $donation_amount = $f_fullwmatch_amount;
                } else {
                    $donation_amount = $f_full_amount;
                }
                if ($donation_amount) {
                    if ($user->add_cart_item($f_project_id, $donation_amount)) {
                        # echo "<script type=\"text/javascript\">\nlocation.href='donation_search.php?search_arg=".htmlentities(urlencode($search_arg)).$params."&message=".htmlentities("Donation added to your cart.")."'\n</script>\n";
                        echo "<script type=\"text/javascript\">\nlocation.href='cart.php?search_arg=".htmlentities(urlencode($search_arg)).$params."&message=".htmlentities("Donation added to your cart.")."'\n</script>\n";
                    } else {
                        $errors .= (empty($errors) ? "" : "<BR>").$user->error_message;
                    }
                } else {
                    if (empty($errors)) {
                        # Display Review Page
                        $review = "Y";
                        $projectid = $f_project_id;
                    } else {
                        // Display errors.
                        $message .= "<p>Please correct the following items and then resubmit:<br>$errors</p>";
                        $projectid = $f_project_id;
                    }
                }
            } elseif ($review != "Y" && $Submit == "Next Step") {
                # Review
                // Validate the fields.
                $errors = "";

                if ($user->cart_item_total() <= 0)
                    $errors .= (empty($errors) ? "" : "<BR>")."A donation must be added to your cart.";
                if (!isset($User_ID))
                    $errors .= (empty($errors) ? "" : "<BR>")."A registered user must be logged in.";
                if (empty($f_payment_choice))
                    $errors .= (empty($errors) ? "" : "<BR>")."A payment choice must be selected.";
                if ($f_payment_choice == "Credit")
                {
                    # Validate Cedit card number and Exp Date.
                    if (empty($f_credit_card_no))
                        $errors .= (empty($errors) ? "" : "<BR>")."A credit card number is required.";
                    if ($f_expdt_year < date("Y",time()) ||($f_expdt_year == date("Y",time()) && $f_expdt_month < date("m", time()) ) )
                        $errors .= (empty($errors) ? "" : "<BR>")."The credit card is expired.";
                }
                if ($f_contact_choice == "G") {
                    if (empty($gift_first_name))
                        $errors .= (empty($errors) ? "" : "<BR>")."A first name is required for Gift donations.";
                    if (empty($gift_last_name))
                        $errors .= (empty($errors) ? "" : "<BR>")."A last name is required for Gift donations.";
                    if (empty($gift_street))
                        $errors .= (empty($errors) ? "" : "<BR>")."A street is required for Gift donations.";
                    if (empty($gift_city))
                        $errors .= (empty($errors) ? "" : "<BR>")."A city is required for Gift donations.";
                    if (empty($gift_state))
                        $errors .= (empty($errors) ? "" : "<BR>")."A state is required for Gift donations.";
                    if (empty($gift_zip))
                        $errors .= (empty($errors) ? "" : "<BR>")."A zip is required for Gift donations.";
                    if (empty($gift_country))
                        $errors .= (empty($errors) ? "" : "<BR>")."A country is required for Gift donations.";
                }
                if (empty($errors)) {
                    # Display Review Page
                    $review = "Y";
                    $projectid = $f_project_id;
                } else {
                    // Display errors.
                    $message .= "<p>Please correct the following items and then resubmit:<br>$errors</p>";
                    $projectid = $f_project_id;
                }
            } elseif ($Submit == "Process Donation" || $Submit == "Prepare Payment") {
                # Confirmation
                if (empty($errors)) {
                    // Save donation
                    if ($debug)	{
                        echo "Saving donation...<br>\n";
                        flush();
                    }
                    $donation = new donation();
                    $donation->user_id		= $User_ID;
                    $donation->donation_amount  = 0;
                    $donation->project_id	= 0;

                    if ($f_donation_option == "Refund")
                        $donation->refund_flag = "Y";
                    else
                        $donation->refund_flag = "N";

                    $donation->donation_date = date("Y-m-d H:i:s");
                    $donation->contact_flag = $f_contact_choice;

                    if ($f_contact_choice == "G")
                    {
                        $donation->gift_first_name	= $gift_first_name;
                        $donation->gift_last_name	= $gift_last_name;
                        $donation->gift_street		= $gift_street;
                        $donation->gift_city		= $gift_city;
                        $donation->gift_state		= $gift_state;
                        $donation->gift_zip			= $gift_zip;
                        $donation->gift_country		= $gift_country;
                    }
                    if ($f_payment_choice == "Credit")
                    {
                        $donation->payment_type_id 	= $f_credit_card_type;
                        $donation->payment_no		= $f_credit_card_no;
                        $donation->payment_exp_date	= sprintf("%02d",$f_expdt_month)."/$f_expdt_year";
                        $donation->payment_cvv2		= $f_cvv2;
                    } elseif ($f_payment_choice == "Paypal/Credit Card") {
                        $donation->payment_type_id 	= "Paypal";
                        $donation->payment_authorized = "Y";
                    } else {
                        $donation->payment_type_id 	= $f_payment_choice;
                        $donation->payment_authorized = "Y";
                    }

                    // If credit card, process transaction
                    if ($debug)	{
                        echo "Saving Donation...<br>\n";
                        flush();
                    }
                    if ($donation->save_donation()) {
                        if ($debug)	{
                            echo "Donation ".$donation->donation_id." saved.<br>\n";
                            flush();
                        }
                        if ($f_payment_choice == "Credit")
                        {
                            $authorize_net->an_login 			= $config_authorizenet_login;
                            $authorize_net->an_transactionid	= $config_authorizenet_transaction_id;
                            $authorize_net->an_curr				= $config_authorizenet_currency;
                            $authorize_net->an_prefix			= $config_authorizenet_prefix;
                            $authorize_net->live				= $config_authorizenet_live;
                            $authorize_net->user_id				= $User_ID;
                            $authorize_net->merchant_email		= $district->email;
                            $authorize_net->donation_id			= $donation->donation_id;
                            $authorize_net->payment_number		= $donation->payment_no;
                            $authorize_net->payment_exp_date	= $donation->payment_exp_date;
                            $authorize_net->payment_cvv2		= $donation->payment_cvv2;
                            $authorize_net->payment_amount		= $user->cart_item_total();

                            if ($f_include_cc_fee == "Y") {
                                $donation->fees_paid = $f_include_cc_fee_amount;
                                $authorize_net->payment_amount = $authorize_net->payment_amount + $donation->fees_paid;
                            }

                            if ($debug)	{
                                echo "Authorizing Credit Card...<br>\n";
                                flush();
                            }
                            if ($authorize_net->authorize_card()) {
                                $donation->payment_authorized = "Y";
                                $donation->payment_received = "Y";
                                $donation->payment_received_date = date("Y-m-d");
                            } else {
                                $donation->payment_authorized = "N";
                                $donation->payment_received = "N";
                            }
                            $donation->payment_auth_message 	= $authorize_net->payment_auth_message;
                            $donation->payment_auth_avs_msg	= $authorize_net->payment_auth_avs_msg;
                            $donation->payment_auth_cvv_msg	= $authorize_net->payment_auth_cvv_msg;
                            $donation->payment_auth_code	= $authorize_net->payment_auth_code;
                            $donation->payment_auth_date	= date("Y-m-d");
                            $donation->payment_auth_id		= $authorize_net->payment_auth_id;

                            # Resave donation with credit card authorization information.
                            if ($debug)	{
                                echo "ReSaving Donation #$donation->donation_id<br>";
                                echo "Msg: $authorize_net->payment_auth_message<br>";
                                echo "AVS: $authorize_net->payment_auth_avs_msg<br>";
                                echo "CVV: $authorize_net->payment_auth_cvv_msg<br>";
                                echo "Code: $authorize_net->payment_auth_code<br>";
                                flush();
                            }
                            $donation->save_donation();
                            if ($donation->payment_authorized == "Y" && !empty($user->email))
                            {
                                # Update the projects
                                reset($user->cart_item_list);
                                while (list($cartid, $cartitem) = each($user->cart_item_list))
                                {
                                    $project = new project();
                                    $projectid = $cartitem->project_id;
                                    $donation_amount = $cartitem->donation_amount;
                                    $donation->add_project($projectid, $donation_amount);
                                }
                                $donation->save_donation();
                                reset($donation->donation_project_list);

                                $donation_request_info = preg_replace("%TAB","\t",$config_donation_request_info_hdr)."\n";
                                while (list($donation_project_id, $donation_project)= each($donation->donation_project_list)) {
                                    $projectid = $donation_project->project_id;
                                    $donation_amount = $donation_project->donation_amount;
                                    $project = new project();
                                    if ($project->load_project($projectid))
                                    {
                                        $donation_request_info .= preg_replace("%PROJECT_ID","$project->project_id",
                                        preg_replace("%DONATION_AMOUNT",substr("      ".sprintf("%01.2f", $donation_amount),-10),
                                        preg_replace("%TEACHER_NAME",$project->submitted_by_name(),
                                        preg_replace("%PROJECT_NAME",$project->project_name,
                                        preg_replace("%TAB","\t",
                                        $config_donation_request_info))))) ."\n";

                                        # Send notice to principal, district, d2e
                                        $districtid = $schools->school_district_id($project->school_id);
                                        $district = new district();
                                        $district->load_district($districtid);

                                        $body = preg_replace("%DONATION_AMOUNT",sprintf("%01.2f", $donation_amount),
                                        preg_replace("%AMOUNT_REQUESTED",sprintf("%01.2f", $project->amount_needed),
                                        preg_replace("%AMOUNT_DONATED",sprintf("%01.2f", $project->amount_donated()+$project->amount_pledged()),
                                        preg_replace("%PROJECT_NAME",$donation->project_name,
                                        preg_replace("%SCHOOL",$schools->school_name($project->school_id),
                                        preg_replace("%PROJECT_ID","$project->project_id",
                                        preg_replace("%DONATION_ID","$donation->donation_id",
                                        preg_replace("%PROJECT_NAME",$project->project_name,
                                        preg_replace("%TEACHER_NAME",$project->submitted_by_name(),
                                        preg_replace("%FIRST_NAME",($donation->contact_flag == "A" ? "" : $user->first_name),
                                        preg_replace("%LAST_NAME",($donation->contact_flag == "A" ? "" : $user->last_name),
                                        preg_replace("%COMPANY",($donation->contact_flag == "A" ? "" : $user->company),
                                        preg_replace("%STREET",($donation->contact_flag == "A" ? "" : $user->street),
                                        preg_replace("%ADDRESS",($donation->contact_flag == "A" ? "" : $user->street),
                                        preg_replace("%CITY",($donation->contact_flag == "A" ? "" : $user->city),
                                        preg_replace("%STATE",($donation->contact_flag == "A" ? "" : $user->state),
                                        preg_replace("%ZIP",($donation->contact_flag == "A" ? "" : $user->zip),
                                        preg_replace("%ANONYMOUS",($donation->contact_flag == "A" ? "Anonymous" : ($donation->contact_flag == "G" ? "Gift For\n  $donation->gift_first_name $donation->gift_last_name\n  $donation->gift_street\n  $donation->gift_city $donation->gift_state $donation->gift_zip" : "")),
                                        preg_replace("%DATE",date("m/d/Y"),
                                        preg_replace("%ADMINISTRATOR",$district->administrator,
                                        preg_replace("%TAXID",$district->tax_id,
                                        preg_replace("%PAYMENT_CONTACT",$district->payment_contact,
                                        preg_replace("%PAYMENT_STREET",$district->payment_street,
                                        preg_replace("%PAYMENT_CITY",$district->payment_city,
                                        preg_replace("%PAYMENT_STATE",$district->payment_state,
                                        preg_replace("%PAYMENT_ZIP",$district->payment_zip,
                                        preg_replace("%PAYMENT_FAX",$district->payment_fax,
                                        preg_replace("%PAYMENT_METHOD",$payment_types->payment_type_description($donation->payment_type_id),
                                        $config_donation_notice_body))))))))))))))))))))))))))));

                                        $subject = preg_replace("%DONATION_AMOUNT",sprintf("%01.2f", $donation_amount),
                                        preg_replace("%PROJECT_ID","$project->project_id",
                                        preg_replace("%PROJECT_NAME",$project->project_name,
                                        preg_replace("%FIRST_NAME",($donation->contact_flag == "A" ? "Anonymous" : $user->first_name),
                                        preg_replace("%LAST_NAME",($donation->contact_flag == "A" ? "" : $user->last_name),
                                        preg_replace("%COMPANY",($donation->contact_flag == "A" ? "" : $user->company),
                                        preg_replace("%STREET",($donation->contact_flag == "A" ? "" : $user->street),
                                        preg_replace("%ADDRESS",($donation->contact_flag == "A" ? "" : $user->street),
                                        preg_replace("%CITY",($donation->contact_flag == "A" ? "" : $user->city),
                                        preg_replace("%STATE",($donation->contact_flag == "A" ? "" : $user->state),
                                        preg_replace("%ZIP",($donation->contact_flag == "A" ? "" : $user->zip),
                                        preg_replace("%DATE",date("m/d/Y"),
                                        preg_replace("%ADMINISTRATOR",$district->administrator,
                                        preg_replace("%TAXID",$district->tax_id,
                                        $config_donation_notice_subject))))))))))))));

                                        $headers = "From: Donate2Educate <support@donate2educate.org>\r\nBCC: help@donate2educate.org\r\n";
                                        mail($project->notify_emails(), $subject, $body, $headers);
                                        mail("help@donate2educate.org", "Donate2Educate Donation", $body, $headers);
                                    }
                                }

                                $body = preg_replace("%DONATION_AMOUNT",sprintf("%01.2f", $donation->donation_total()),
                                preg_replace("%PROJECT_NAME",$donation->project_name,
                                preg_replace("%DONATION_ID","$donation->donation_id",
                                preg_replace("%FIRST_NAME",$user->first_name,
                                preg_replace("%LAST_NAME",$user->last_name,
                                preg_replace("%COMPANY",$user->company,
                                preg_replace("%STREET",$user->street,
                                preg_replace("%ADDRESS",$user->street,
                                preg_replace("%CITY",$user->city,
                                preg_replace("%STATE",$user->state,
                                preg_replace("%ZIP",$user->zip,
                                preg_replace("%DATE",date("m/d/Y"),
                                preg_replace("%ADMINISTRATOR",$district->administrator,
                                preg_replace("%TAXID",$district->tax_id,
                                preg_replace("%PAYMENT_CONTACT",$district->payment_contact,
                                preg_replace("%PAYMENT_STREET",$district->payment_street,
                                preg_replace("%PAYMENT_CITY",$district->payment_city,
                                preg_replace("%PAYMENT_STATE",$district->payment_state,
                                preg_replace("%PAYMENT_FAX",$district->payment_fax,
                                preg_replace("%PAYMENT_ZIP",$district->payment_zip,
                                preg_replace("%PAYMENT_AUTH",$authorize_net->payment_auth_code,
                                preg_replace("%PAYMENT_METHOD",$payment_types->payment_type_description($donation->payment_type_id),
                                preg_replace("%REQUEST_INFO", $donation_request_info,
                                $config_donation_cc_reply_body)))))))))))))))))))))));

                                $subject = preg_replace("%DONATION_AMOUNT",sprintf("%01.2f", $donation->donation_total()),
                                preg_replace("%FIRST_NAME",$user->first_name,
                                preg_replace("%LAST_NAME",$user->last_name,
                                preg_replace("%COMPANY",$user->company,
                                preg_replace("%STREET",$user->street,
                                preg_replace("%ADDRESS",$user->street,
                                preg_replace("%CITY",$user->city,
                                preg_replace("%STATE",$user->state,
                                preg_replace("%ZIP",$user->zip,
                                preg_replace("%DATE",date("m/d/Y"),
                                preg_replace("%ADMINISTRATOR",$district->administrator,
                                preg_replace("%TAXID",$district->tax_id,
                                $config_donation_cc_reply_subject))))))))))));

                                $headers = "From: Donate2Educate <support@donate2educate.org>\r\nBCC: help@donate2educate.org\r\n";
                                mail($user->email, $subject, $body, $headers);
                                mail("help@donate2educate.org", "Donate2Educate Donation", $body, $headers);

                                $user->delete_cart_items();
                            } else {
                                $message = "Credit Card Authorization Failed.<BR>Message: $donation->payment_auth_message";
                                # Go back to Step 2.
                                $review = "D";
                            }
                        } elseif ($f_payment_choice == "Paypal/Credit Card") {
                            # Update the projects
                            reset($user->cart_item_list);
                            while (list($cartid, $cartitem) = each($user->cart_item_list))
                            {
                                $project = new project();
                                $projectid = $cartitem->project_id;
                                $donation_amount = $cartitem->donation_amount;
                                $donation->add_project($projectid, $donation_amount);
                            }
                            if ($f_include_paypal_fee == "Y") {
                                $donation->fees_paid = $f_include_paypal_fee_amount;
                            }

                            # Need to jump off to Paypal to collect the payment, then return here.
                            $donation->donation_key = md5(uniqid(rand()));
                            $donation->save_donation();
                            reset($donation->donation_project_list);
                            if ($debug)	{
                                echo "Preparing Paypal parameters...<br>\n";
                                flush();
                            }
                            $paypal_flag = true;
                            $paypal_param = array();
                            $paypal_param["first_name"]	= $user->first_name;
                            $paypal_param["last_name"]	= $user->last_name;
                            $paypal_param["address1"]	= $user->street;
                            $paypal_param["city"]	= $user->city;
                            $paypal_param["state"]	= $user->state;
                            $paypal_param["zip"]	= $user->zip;
                            $paypal_param["country"]	= $user->country;
                            $paypal_param["image_url"]	= $https_location."images/banner_paypal.jpg";
                            $paypal_param["no_shipping"]= "1"; # Do not prompt for a shipping address
                            $paypal_param["no_note"]	= "1"; # Do not prompt for a note
                            $paypal_param["return"]	= $https_location."donation.php?paypalStatus=complete&dkey=".$donation->donation_key;
                            $paypal_param["rm"]		= "2";
                            $paypal_param["cbt"]	= "Complete Donation";
                            $paypal_param["cancel_return"] = $https_location."donation.php?paypalStatus=cancel&dkey=".$donation->donation_key;
                            $paypal_param["cmd"]	= "_cart";
                            $paypal_param["upload"]	= "1";
                            $paypal_param["business"] 	= "donate@donate2educate.org";
                            $paypal_param["invoice"]	= $donation->donation_id;

                            reset($donation->donation_project_list);
                            $item_idx = 1;
                            while (list($donation_project_id, $donation_project)= each($donation->donation_project_list))
                            {
                                $projectid = $donation_project->project_id;
                                $donation_amount = $donation_project->donation_amount;
                                $project = new project();
                                if ($project->load_project($projectid))
                                {
                                    $paypal_param["item_name_".$item_idx] = $project->project_name;
                                    $paypal_param["amount_".$item_idx] = $donation_amount;
                                    $paypal_param["item_number_".$item_idx] = $projectid;
                                }
                                $item_idx += 1;
                            }
                            if ($donation->fees_paid > 0)
                            {
                                $paypal_param["item_name_".$item_idx] = "Payment Processing Fees";
                                $paypal_param["amount_".$item_idx] = $donation->fees_paid;
                                $paypal_param["item_number_".$item_idx] = "Fee";
                            }

                            if ($debug) {
                                reset($paypal_param);
                                while (list($key, $value) = each($paypal_param)) {
                                    echo "Key=$key Value=$value<BR>";
                                }
                            }
                        } elseif ($f_payment_choice == "Check") {
                            # Update the projects
                            reset($user->cart_item_list);
                            while (list($cartid, $cartitem) = each($user->cart_item_list))
                            {
                                $project = new project();
                                $projectid = $cartitem->project_id;
                                $donation_amount = $cartitem->donation_amount;
                                $donation->add_project($projectid, $donation_amount);
                            }
                            if ($donation->save_donation()) {
                                reset($donation->donation_project_list->donation_projects_list);
                                $donation_request_info = preg_replace("%TAB","\t",$config_donation_request_info_hdr)."\n";
                                while (list($donation_project_id, $donation_project)= each($donation->donation_project_list)) {
                                    $projectid = $donation_project->project_id;
                                    $donation_amount = $donation_project->donation_amount;
                                    $project = new project();
                                    if ($project->load_project($projectid))
                                    {
                                        $donation_request_info .= preg_replace("%PROJECT_ID","$project->project_id",
                                        preg_replace("%DONATION_AMOUNT",substr("      ".sprintf("%01.2f", $donation_amount),-10),
                                        preg_replace("%TEACHER_NAME",$project->submitted_by_name(),
                                        preg_replace("%PROJECT_NAME",$project->project_name,
                                        preg_replace("%TAB","\t",
                                        $config_donation_request_info))))) ."\n";

                                        $districtid = $schools->school_district_id($project->school_id);
                                        $district = new district();
                                        $district->load_district($districtid);
                                    }
                                }

                                $body = preg_replace("%DONATION_AMOUNT",sprintf("%01.2f", $donation->donation_total()),
                                preg_replace("%DONATION_ID","$donation->donation_id",
                                preg_replace("%FIRST_NAME",$user->first_name,
                                preg_replace("%LAST_NAME",$user->last_name,
                                preg_replace("%COMPANY",$user->company,
                                preg_replace("%STREET",$user->street,
                                preg_replace("%ADDRESS",$user->street,
                                preg_replace("%CITY",$user->city,
                                preg_replace("%STATE",$user->state,
                                preg_replace("%ZIP",$user->zip,
                                preg_replace("%DATE",date("m/d/Y"),
                                preg_replace("%ADMINISTRATOR",$district->administrator,
                                preg_replace("%TAXID",$district->tax_id,
                                preg_replace("%PAYMENT_CONTACT",$district->payment_contact,
                                preg_replace("%PAYMENT_STREET",$district->payment_street,
                                preg_replace("%PAYMENT_CITY",$district->payment_city,
                                preg_replace("%PAYMENT_STATE",$district->payment_state,
                                preg_replace("%PAYMENT_ZIP",$district->payment_zip,
                                preg_replace("%PAYMENT_FAX",$district->payment_fax,
                                preg_replace("%PAYMENT_METHOD",$payment_types->payment_type_description($donation->payment_type_id),
                                preg_replace("%REQUEST_INFO", $donation_request_info,
                                $config_donation_check_reply_body)))))))))))))))))))));

                                $subject = preg_replace("%DONATION_AMOUNT",sprintf("%01.2f", $donation->donation_total()),
                                preg_replace("%FIRST_NAME",$user->first_name,
                                preg_replace("%LAST_NAME",$user->last_name,
                                preg_replace("%COMPANY",$user->company,
                                preg_replace("%STREET",$user->street,
                                preg_replace("%ADDRESS",$user->street,
                                preg_replace("%CITY",$user->city,
                                preg_replace("%STATE",$user->state,
                                preg_replace("%ZIP",$user->zip,
                                preg_replace("%DATE",date("m/d/Y"),
                                preg_replace("%ADMINISTRATOR",$district->administrator,
                                preg_replace("%TAXID",$district->tax_id,
                                $config_donation_reply_subject))))))))))));

                                $headers = "From: Donate2Educate <support@donate2educate.org>\r\nBCC: help@donate2educate.org\r\n";
                                mail($user->email, $subject, $body, $headers);
                                mail("help@donate2educate.org", "Donate2Educate Donation", $body, $headers);

                                $user->delete_cart_items();
                            } else {
                                $message = "Donation save error.<BR>Message: $donation->error_message";
                                # Go back to Step 2.
                                $review = "";
                            }
                        } elseif ($f_payment_choice == "Fax") {
                            reset($user->cart_item_list);
                            $donation_request_info = preg_replace("%TAB","\t",$config_donation_request_info_hdr)."\n";
                            while (list($cartid, $cartitem) = each($user->cart_item_list))
                            {
                                $project = new project();
                                $projectid = $cartitem->project_id;
                                $donation_amount = $cartitem->donation_amount;
                                if ($project->load_project($projectid))
                                {
                                    $donation->add_project($projectid, $donation_amount);
                                    if (!empty($user->email))
                                    {
                                        $donation_request_info .= preg_replace("%PROJECT_ID","$project->project_id",
                                        preg_replace("%DONATION_AMOUNT",substr("      ".sprintf("%01.2f", $donation_amount),-10),
                                        preg_replace("%TEACHER_NAME",$project->submitted_by_name(),
                                        preg_replace("%PROJECT_NAME",$project->project_name,
                                        preg_replace("%TAB","\t",
                                        $config_donation_request_info))))) ."\n";

                                        $districtid = $schools->school_district_id($project->school_id);
                                        $district = new district();
                                        $district->load_district($districtid);
                                    }
                                }

                                $body = preg_replace("%DONATION_AMOUNT",sprintf("%01.2f", $donation->donation_total()),
                                preg_replace("%DONATION_ID","$donation->donation_id",
                                preg_replace("%FIRST_NAME",$user->first_name,
                                preg_replace("%LAST_NAME",$user->last_name,
                                preg_replace("%COMPANY",$user->company,
                                preg_replace("%STREET",$user->street,
                                preg_replace("%ADDRESS",$user->street,
                                preg_replace("%CITY",$user->city,
                                preg_replace("%STATE",$user->state,
                                preg_replace("%ZIP",$user->zip,
                                preg_replace("%DATE",date("m/d/Y"),
                                preg_replace("%ADMINISTRATOR",$district->administrator,
                                preg_replace("%TAXID",$district->tax_id,
                                preg_replace("%PAYMENT_CONTACT",$district->payment_contact,
                                preg_replace("%PAYMENT_STREET",$district->payment_street,
                                preg_replace("%PAYMENT_CITY",$district->payment_city,
                                preg_replace("%PAYMENT_STATE",$district->payment_state,
                                preg_replace("%PAYMENT_ZIP",$district->payment_zip,
                                preg_replace("%PAYMENT_FAX",$district->payment_fax,
                                preg_replace("%PAYMENT_METHOD",$payment_types->payment_type_description($donation->payment_type_id),
                                preg_replace("%REQUEST_INFO", $donation_request_info,
                                $config_donation_fax_reply_body)))))))))))))))))))));

                                $subject = preg_replace("%DONATION_AMOUNT",sprintf("%01.2f", $donation->donation_total()),
                                preg_replace("%FIRST_NAME",$user->first_name,
                                preg_replace("%LAST_NAME",$user->last_name,
                                preg_replace("%COMPANY",$user->company,
                                preg_replace("%STREET",$user->street,
                                preg_replace("%ADDRESS",$user->street,
                                preg_replace("%CITY",$user->city,
                                preg_replace("%STATE",$user->state,
                                preg_replace("%ZIP",$user->zip,
                                preg_replace("%DATE",date("m/d/Y"),
                                preg_replace("%ADMINISTRATOR",$district->administrator,
                                preg_replace("%TAXID",$district->tax_id,
                                $config_donation_reply_subject))))))))))));

                                $headers = "From: Donate2Educate <support@donate2educate.org>\r\nBCC: help@donate2educate.org\r\n";
                                mail($user->email, $subject, $body, $headers);
                                mail("help@donate2educate.org", "Donate2Educate Donation", $body, $headers);

                                $user->delete_cart_items();
                            }
                            $donation->save_donation();
                        }
                    } else
                        $message = "<p>Errors occured saving the donation!<br>$donation->error_message</p>";
                } else
                    $message = "<p>Errors occured saving the donation!<br>$donation->error_message</p>";
            }
	}
	if ($review != 'Y') {
            $project = new project();
            if (!empty($projectid)) {
                if ($project->load_project($projectid)) {
                    $f_project_id = $project->project_id;
                    $f_project_name = stripslashes($project->project_name);
                    $f_description = stripslashes($project->project_description);
                    $f_gradelevel = $project->grade_level_id;
                    $f_projecttype = $project->project_type_id;
                    $f_schoolid = $project->school_id;
                    $f_materials = stripslashes($project->materials_needed);
                    $f_amount_needed = $project->amount_needed;
                    $f_amount_donated = $project->amount_donated();
                    $f_date_required = (empty($project->required_by_date) ? "" : date("m/d/y", strtotime($project->required_by_date)));
                    $f_status_id = $project->project_status_id;
                    $f_review_notes = $project->review_notes;
                    $matching_amount = $matching->matching_amount($projectid);
                    if ($matching_amount > ($project->amount_needed - ($project->amount_donated()+$project->amount_pledged()))/2)
                        $matching_amount = ($project->amount_needed - ($project->amount_donated()+$project->amount_pledged()))/2;
                    if ($project->error_message) {
                        if ($message) $message .= "<BR>";
                        $message .= "Matching Error: $project->error_message";
                    }
                    $district = new district();
                    $district->load_district($schools->school_district_id($project->school_id));
                } else {
                    $projectid = "";
                    $f_status_id = "0";
                    $district = new $district();
                }
            }
	}
        if ($paypalStatus)
        {
            $req = "cmd=_notify-synch";
            if(function_exists('get_magic_quotes_gpc'))
            {
                $get_magic_quotes_exits = true;
            }
            $req .= "&tx=$tx";
            $req .= "&at=$config_paypal_identity_token";
            // Post back to PayPal to validate
            $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
            $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
            $fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);
            // Process validation from PayPal
            if (!$fp)
            {
                // HTTP ERROR
            } else {
                // NO HTTP ERROR
                fputs ($fp, $header . $req);
                while (!feof($fp))
                {
                    $res = fgets ($fp, 1024);
                    if (strcmp ($res, "SUCCESS") == 0)
                    {
                        // TODO:
                        // Check the payment_status is Completed
                        // Check that txn_id has not been previously processed
                        // Check that receiver_email is your Primary PayPal email
                        // Check that payment_amount/payment_currency are correct
                        // Process payment
                        // If 'VERIFIED', send an email of IPN variables and values to the
                        // specified email address
                        $paypalStatus = "complete";
                        $paypalTransactionId = $tx;
                        $paypalAmt = $_POST["amt"];
                        $donationid = $_POST["invoice"];
                        foreach ($_POST as $key => $value)
                        {
                            $emailtext .= $key . " = " .$value ."\n\n";
                        }
                        mail("farmfaraway@gmail.com", "Live-VERIFIED Payment Response", $emailtext . "\n\n" . $req);
                    }
                    else if (strcmp ($res, "FAIL") == 0)
                    {
                        // If 'INVALID', send an email. TODO: Log for manual investigation.
                        foreach ($_POST as $key => $value)
                        {
                            $emailtext .= $key . " = " .$value ."\n\n";
                        }
                        mail("farmfaraway@gmail.com", "Live-INVALID Payment Response", $emailtext . "\n\n" . $req);
                    }
                }
                fclose ($fp);
            }
            if ($paypalStatus == "complete")
            {
                // The paypal payment was complete.  Mark the donation as received and empty shopping cart.
                $donation = new donation();
                $donation->load_donation_from_key($dkey);
                $donation->payment_received = "Y";
                $donation->payment_received_date = date("Y-m-d H:i:s");
                $donation->save_donation();
                reset($donation->donation_project_list);
                $donation_request_info = preg_replace("%TAB","\t",$config_donation_request_info_hdr)."\n";

                while (list($donation_project_id, $donation_project)= each($donation->donation_project_list))
                {
                    $projectid = $donation_project->project_id;
                    $donation_amount = $donation_project->donation_amount;
                    $project = new project();
                    if ($project->load_project($projectid))
                    {
                        $donation_request_info .= preg_replace("%PROJECT_ID","$project->project_id",
                        preg_replace("%DONATION_AMOUNT",substr("      ".sprintf("%01.2f", $donation_amount),-10),
                        preg_replace("%TEACHER_NAME",$project->submitted_by_name(),
                        preg_replace("%PROJECT_NAME",$project->project_name,
                        preg_replace("%TAB","\t",
                        $config_donation_request_info))))) ."\n";

                        $districtid = $schools->school_district_id($project->school_id);
                        $district = new district();
                        $district->load_district($districtid);

                        # Send notice to principal, district, d2e
                        $body = preg_replace("%DONATION_AMOUNT",sprintf("%01.2f", $donation_amount),
                        preg_replace("%AMOUNT_REQUESTED",sprintf("%01.2f", $project->amount_needed),
                        preg_replace("%AMOUNT_DONATED",sprintf("%01.2f", $project->amount_donated()+$project->amount_pledged()),
                        preg_replace("%PROJECT_NAME",$donation->project_name,
                        preg_replace("%SCHOOL",$schools->school_name($project->school_id),
                        preg_replace("%PROJECT_ID","$project->project_id",
                        preg_replace("%DONATION_ID","$donation->donation_id",
                        preg_replace("%PROJECT_NAME",$project->project_name,
                        preg_replace("%TEACHER_NAME",$project->submitted_by_name(),
                        preg_replace("%FIRST_NAME",($donation->contact_flag == "A" ? "" : $user->first_name),
                        preg_replace("%LAST_NAME",($donation->contact_flag == "A" ? "" : $user->last_name),
                        preg_replace("%COMPANY",($donation->contact_flag == "A" ? "" : $user->company),
                        preg_replace("%STREET",($donation->contact_flag == "A" ? "" : $user->street),
                        preg_replace("%ADDRESS",($donation->contact_flag == "A" ? "" : $user->street),
                        preg_replace("%CITY",($donation->contact_flag == "A" ? "" : $user->city),
                        preg_replace("%STATE",($donation->contact_flag == "A" ? "" : $user->state),
                        preg_replace("%ZIP",($donation->contact_flag == "A" ? "" : $user->zip),
                        preg_replace("%ANONYMOUS",($donation->contact_flag == "A" ? "Anonymous" : ($donation->contact_flag == "G" ? "Gift For\n  $donation->gift_first_name $donation->gift_last_name\n  $donation->gift_street\n  $donation->gift_city $donation->gift_state $donation->gift_zip" : "")),
                        preg_replace("%DATE",date("m/d/Y"),
                        preg_replace("%ADMINISTRATOR",$district->administrator,
                        preg_replace("%TAXID",$district->tax_id,
                        preg_replace("%PAYMENT_CONTACT",$district->payment_contact,
                        preg_replace("%PAYMENT_STREET",$district->payment_street,
                        preg_replace("%PAYMENT_CITY",$district->payment_city,
                        preg_replace("%PAYMENT_STATE",$district->payment_state,
                        preg_replace("%PAYMENT_ZIP",$district->payment_zip,
                        preg_replace("%PAYMENT_FAX",$district->payment_fax,
                        preg_replace("%PAYMENT_METHOD",$payment_types->payment_type_description($donation->payment_type_id),
                        $config_donation_notice_body))))))))))))))))))))))))))));

                        $subject = preg_replace("%DONATION_AMOUNT",sprintf("%01.2f", $donation_amount),
                        preg_replace("%PROJECT_ID","$project->project_id",
                        preg_replace("%PROJECT_NAME",$project->project_name,
                        preg_replace("%FIRST_NAME",$user->first_name,
                        preg_replace("%LAST_NAME",$user->last_name,
                        preg_replace("%COMPANY",$user->company,
                        preg_replace("%STREET",$user->street,
                        preg_replace("%ADDRESS",$user->street,
                        preg_replace("%CITY",$user->city,
                        preg_replace("%STATE",$user->state,
                        preg_replace("%ZIP",$user->zip,
                        preg_replace("%DATE",date("m/d/Y"),
                        preg_replace("%ADMINISTRATOR",$district->administrator,
                        preg_replace("%TAXID",$district->tax_id,
                        $config_donation_notice_subject))))))))))))));

                        $headers = "From: Donate2Educate <support@donate2educate.org>\r\nBCC: help@donate2educate.org\r\n";
                        mail($project->notify_emails(), $subject, $body, $headers);
                        mail("help@donate2educate.org", "Donate2Educate Donation", $body, $headers);
                    }
                }
                #$message .= "Thank you for your donation!";
                $body = preg_replace("%DONATION_AMOUNT",sprintf("%01.2f", $donation->donation_total()),
                preg_replace("%DONATION_ID","$donation->donation_id",
                preg_replace("%FIRST_NAME",$user->first_name,
                preg_replace("%LAST_NAME",$user->last_name,
                preg_replace("%COMPANY",$user->company,
                preg_replace("%STREET",$user->street,
                preg_replace("%ADDRESS",$user->street,
                preg_replace("%CITY",$user->city,
                preg_replace("%STATE",$user->state,
                preg_replace("%ZIP",$user->zip,
                preg_replace("%DATE",date("m/d/Y"),
                preg_replace("%ADMINISTRATOR",$district->administrator,
                preg_replace("%TAXID",$district->tax_id,
                preg_replace("%PAYMENT_CONTACT",$district->payment_contact,
                preg_replace("%PAYMENT_STREET",$district->payment_street,
                preg_replace("%PAYMENT_CITY",$district->payment_city,
                preg_replace("%PAYMENT_STATE",$district->payment_state,
                preg_replace("%PAYMENT_FAX",$district->payment_fax,
                preg_replace("%PAYMENT_ZIP",$district->payment_zip,
                preg_replace("%PAYMENT_AUTH",$authorize_net->payment_auth_code,
                preg_replace("%PAYMENT_METHOD",$payment_types->payment_type_description($donation->payment_type_id),
                preg_replace("%REQUEST_INFO", $donation_request_info,
                $config_donation_cc_reply_body))))))))))))))))))))));

                $subject = preg_replace("%DONATION_AMOUNT",sprintf("%01.2f", $donation->donation_total()),
                preg_replace("%FIRST_NAME",$user->first_name,
                preg_replace("%LAST_NAME",$user->last_name,
                preg_replace("%COMPANY",$user->company,
                preg_replace("%STREET",$user->street,
                preg_replace("%ADDRESS",$user->street,
                preg_replace("%CITY",$user->city,
                preg_replace("%STATE",$user->state,
                preg_replace("%ZIP",$user->zip,
                preg_replace("%DATE",date("m/d/Y"),
                preg_replace("%ADMINISTRATOR",$district->administrator,
                preg_replace("%TAXID",$district->tax_id,
                $config_donation_cc_reply_subject))))))))))));

                $headers = "From: Donate2Educate <support@donate2educate.org>\r\nBCC: help@donate2educate.org\r\n";
                mail($user->email, $subject, $body, $headers);
                mail("help@donate2educate.org", "Donate2Educate Donation", $body, $headers);

		$user->delete_cart_items();
                $message = "Your payment has been confirmed.";
                echo "<script type=\"text/javascript\">\nlocation.href='$http_location"."donation_search.php?message=".htmlentities(urlencode($message))."'\n</script>\n";
            }
            else
            {
                // The paypal payment was cancelled.  Delete the donation and leave the items in the shopping cart.
                $donation = new donation();
                if ($donation->load_donation_from_key($dkey))
                {
                    if (!$donation->delete_donation())
                    {
                        $headers = "From: Donate2Educate <support@donate2educate.org>\r\n";
                        mail("farmfaraway@gmail.com", "Donate2Educate Donation key not found", "Donate with key $dkey not found.", $headers);
                    }
                } else {
                    $headers = "From: Donate2Educate <support@donate2educate.org>\r\n";
                    mail("farmfaraway@gmail.com", "Donate2Educate Donation key not found", "Donate with key $dkey not found.", $headers);
                }
                $message = "Your payment was cancelled.";
                echo "<script type=\"text/javascript\">\nlocation.href='$http_location"."cart.php?message=".htmlentities(urlencode($message))."'\n</script>\n";
            }
        }
	include "inc/cssstyle.php";
	$pagename = "$config_donation_page_name";
	$help_msg_name = "config_donation_help";
	$help_msg = "$config_donation_help";
	$help_width = "$config_donation_help_width";
	$help_height = "$config_donation_help_height";
	require "inc/title.php";
?>
<script language="javascript" type="text/javascript">
function partialDonation(partialAmount, maxAmount, donationChoice, idx)
{
    partialAmount.value = parseFloat(partialAmount.value);
    if (partialAmount.value == "NaN") {
        partialAmount.value = "";
        alert("Invalid amount.");
        donationChoice[0].checked=true;
    } else {
        if(parseFloat(partialAmount.value) > parseFloat(maxAmount.value)) {
            donationChoice[0].checked=true;
            partialAmount.value = "";
            alert("Donation amount cannot exceed funds needed.");
        } else {
            if(partialAmount.value == "" || partialAmount.value == "0")
                donationChoice[0].checked=true;
            else
                donationChoice[idx].checked=true;
        }
    }
}
</script>

<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <TD width="640" align="left" valign="top">
<?
if ($debug) {
	echo "HTTPS=$https_location<BR>";
	flush();
}
	if (!empty($config_donation_paragraph1)) {
            include "inc/box_begin.htm";
            echo "$config_donation_paragraph1\n";
            include "inc/box_end.htm";
	}
	if (!empty($message)) {
            include "inc/box_begin.htm";
            echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center>\n";
            include "inc/box_end.htm";
	}
	if ($review == "Y")
            if ($Submit == "Process Donation" || $Submit == "Prepare Payment")
                $step = 4;
            else
                $step = 3;
	else
            $step = 2;
	include "inc/progress.php";
?>
	<table width=100% cellspacing=1 cellpadding=1>
		<TR>
			<TD align=left width=50% bgcolor="<?=$color_table_hdg_bg;?>">
<?
if ($debug) {
    echo "Paypal Flag=$paypal_flag<BR>";
}
            if ($review == "Y") {

		if ($Submit == "Process Donation" || $Submit == "Prepare Payment") {
?>
				<table width="100%" align="center" border=0 cellpadding=5 cellspacing=0>
					<TR height=235>
						<TD bgcolor="<?=$color_table_col_bg;?>">
							<TABLE ALIGN="left" BORDER=0 CELLSPACING=1 CELLPADDING=2 WIDTH="100%">
								<TR ALIGN="left" VALIGN="top">
<? if ($f_payment_choice == "Paypal/Credit Card") { ?>
									<TD Align='center' valign='Top' colspan='3'><h2>Donation Payment Transaction</h2></TD>
<? } else { ?>
									<TD Align='center' valign='Top' colspan='3'><h2>Your Donation Confirmation</h2></TD>
								</TR>
								<TR ALIGN="left" VALIGN="top">
									<TD Align='center' valign='Top' colspan='3'>
									<font size="+1"><?=$district->administrator;?></font><br>
									<? echo ($district->payment_contact ? "$district->payment_contact<br>" : ""); ?><?=$district->payment_street;?><br>
									<?=$district->payment_city;?>&nbsp;
									<?=$district->payment_state;?>,&nbsp;
									<?=$district->payment_zip;?><br>
									</TD>
								</TR>
<?	if (!empty($district->tax_id)) { ?>
								<TR ALIGN="left" VALIGN="top">
									<TD Align='center' valign='Top' colspan='3'><h3>Tax ID No:&nbsp;<?=$district->tax_id;?></h3></TD>
<? } ?>
								</TR>
<?	}	?>
								<TR ALIGN="left" VALIGN="top">
									<TD Align='Right' valign='Top' Colspan='3'>
<?		include "inc/donation_detail.php"; ?>
									</TD>
								</TR>
								<TR Align='Left' VALIGN=Bottom>
									<TD Align='Right'><b>Donor:</b></TD>
									<TD Align='Left' Colspan='2'><?=$user->first_name;?>&nbsp;<?=$user->last_name.(!empty($user->company) ? "<BR>".$user->company : "")."<BR>".$user->street."<br>".$user->city."&nbsp;".$user->state."&nbsp;".$user->zip;?></TD>
									</TD>
								</TR>
								<TR Align='Left' VALIGN=Bottom>
									<TD Align='Right' nowrap><b>Donation Amount:</b></TD>
									<TD Align='Left' Colspan='2'>
<?							echo "\t\t\t\t\t\t\t\t\t\t$".sprintf("%01.2f", $donation->donation_amount)."\n";
?>
									</TD>
								</TR>
								<TR ALIGN='Left' VALIGN=Top>
									<TD Align='Right'><B>Payment Method</B>:</TD>
									<TD Align='Left' Colspan='2'>
<?			if ($f_payment_choice == "Credit")
			{
							echo $payment_types->payment_type_description($f_credit_card_type);
							echo "&nbsp;&nbsp;<b>No</B>:&nbsp;".substr($f_credit_card_no,-4);
							echo "&nbsp;&nbsp;<b>Exp Dt</B>:&nbsp;$f_expdt_month/$f_expdt_year";
			} else {
							echo $f_payment_choice;
			}
?>
								</TR>
								<TR ALIGN='Left' VALIGN=Top>
									<TD Align='Right'><B>Contact</B>:</TD>
									<TD Align='Left' Colspan='2'>
									<input type="hidden" name="f_contact_choice" value="<?=$f_contact_choice;?>">
<?			if ($f_contact_choice == "D") { ?>
									Send correspondence directly to me.
<?			} elseif ($f_contact_choice == "G") { ?>
									This donation is a gift in the name of:<br>
									&nbsp;&nbsp;&nbsp;<?=$gift_first_name;?>&nbsp;<?=$gift_last_name;?><BR>
									&nbsp;&nbsp;&nbsp;<?=$gift_street;?><BR>
									&nbsp;&nbsp;&nbsp;<?=$gift_city;?>,&nbsp;<?=$gift_state;?>&nbsp;&nbsp;<?=$gift_zip;?><BR>
<?				if ($gift_country != $config_default_country) { ?>
									&nbsp;&nbsp;&nbsp;<?="<BR>".$countries->country_name($gift_country);?>
<?				}	?>
<?			} else { ?>
 									I prefer to remain anonymous
<?			}	?>
									</TD>
								</TR>
								<TR ALIGN="left" VALIGN="middle">
									<TD Align='center' Colspan='3'>
<?
                                                                            if ($paypal_flag) {
                                                                                    # This is a paypal submission.
                                                                                    echo "<form method=\"POST\" action=\"$config_paypal_server\" name=\"paypal_submit\">\n";
                                                                                    reset($paypal_param);
                                                                                    while (list($key, $value) = each($paypal_param)) {
                                                                                            echo "<input type=\"hidden\" name=\"$key\" value=\"$value\">\n";
                                                                                    }
                                                                                    echo "<input type=\"Submit\" onClick=\"return checksubmit(this)\" value=\"Process Payment\" class=\"nicebtns\" >";
                                                                                    echo "</form>";
                                                                            }
?>
                                                                        </TD>
								</TR>
							</TABLE>
						</TR>
					</TABLE>
<?

		} else {
?>
				<table width="100%" align="center" border=0 cellpadding=5 cellspacing=0>
					<TR height=235>
						<TD bgcolor="<?=$color_table_col_bg;?>">
							<TABLE ALIGN="left" BORDER=0 CELLSPACING=1 CELLPADDING=2 WIDTH="100%">
								<form method="POST" name="donation" ACTION="donation.php">
								<input type="hidden" name="uniqueid" value="<?=$uniqueid;?>">
								<input type="hidden" name="review" value="Y">
								<TR ALIGN="left" VALIGN="top">
									<TD Align='center' valign='Top' colspan='3'><h2>Your Donation Preview</h2></TD>
								</TR>
								<TR ALIGN="left" VALIGN="top">
									<TD Align='Left' valign='Top' Colspan='3'>
<?		include "inc/cart_detail.php";?>
									</TD>
								</TR>
<?
/*
								<TR Align='Left' VALIGN=Bottom>
									<TD Align='Right'><b><B>If never fully funded</B>:</b></TD>
									<TD Align='Left' Colspan='2'>
<?						if ($f_donation_option == "Refund")
							echo "Refund to me.";
						else
							echo "Apply to another request.";
?>
									</TD>
								</TR>
*/
?>
									<input type="hidden" name="f_donation_option" value="<?=$f_donation_option;?>">
								<TR Align='Left' VALIGN=Bottom>
									<TD Align='Right'><b>Donor Name:</b></TD>
									<TD Align='Left' Colspan='2'><?=$user->first_name;?>&nbsp;<?=$user->last_name;?>
									<? if (!empty($user->company)) echo "<BR>".$user->company; ?>
									</TD>
								</TR>
								<TR Align='Left' VALIGN=Bottom>
									<TD Align='Right'><b>Donor Zip Code:</b></TD>
									<TD Align='Left' Colspan='2'><?=$user->zip;?></TD>
								</TR>
								<TR Align='Left' VALIGN=Bottom>
									<TD Align='Right' nowrap><b>Donation Amount:</b></TD>
									<TD Align='Left' Colspan='2'>

<?
                                                                        echo "\t\t\t\t\t\t\t\t\t\t$".sprintf("%01.2f", $user->cart_item_total()+($f_include_paypal_fee == "Y" ? $f_include_paypal_fee_amount : 0)+($f_include_cc_fee == "Y" ? $f_include_cc_fee_amount : 0))."\n";
?>
									</TD>
								</TR>
								<TR ALIGN='Left' VALIGN=Top>
									<TD Align='Right'><B>Payment Method</B>:</TD>
									<TD Align='Left' Colspan='2'>
									<input type="hidden" name="f_payment_choice" value="<?=$f_payment_choice;?>">
									<input type="hidden" name="f_credit_card_type" value="<?=$f_credit_card_type;?>">
									<input type="hidden" name="f_credit_card_no" value="<?=$f_credit_card_no;?>">
									<input type="hidden" name="f_expdt_month" value="<?=$f_expdt_month;?>">
									<input type="hidden" name="f_expdt_year" value="<?=$f_expdt_year;?>">
									<input type="hidden" name="f_cvv2" value="<?=$f_cvv2;?>">
                                                                        <input type="hidden" name="f_include_paypal_fee" value="<?=$f_include_paypal_fee;?>">
                                                                        <input type="hidden" name="f_include_paypal_fee_amount" value="<?=$f_include_paypal_fee_amount;?>">
                                                                        <input type="hidden" name="f_include_cc_fee" value="<?=$f_include_cc_fee;?>">
                                                                        <input type="hidden" name="f_include_cc_fee_amount" value="<?=$f_include_cc_fee_amount;?>">
<?			if ($f_payment_choice == "Credit")
			{
							echo $payment_types->payment_type_description($f_credit_card_type);
							echo "&nbsp;&nbsp;<b>No</B>:&nbsp;".substr($f_credit_card_no,-4);
							echo "&nbsp;&nbsp;<b>Exp Dt</B>:&nbsp;$f_expdt_month/$f_expdt_year";
			} else {
							echo $f_payment_choice;
			}
?>
								</TR>
								<TR ALIGN='Left' VALIGN=Top>
									<TD Align='Right'><B>Contact</B>:</TD>
									<TD Align='Left' Colspan='2'>
									<input type="hidden" name="f_contact_choice" value="<?=$f_contact_choice;?>">
<?			if ($f_contact_choice == "D") { ?>
									Send correspondence directly to me.
<?			} elseif ($f_contact_choice == "G") { ?>
									<input type="hidden" name="gift_first_name" value="<?=$gift_first_name;?>">
									<input type="hidden" name="gift_last_name" value="<?=$gift_last_name;?>">
									<input type="hidden" name="gift_street" value="<?=$gift_street;?>">
									<input type="hidden" name="gift_city" value="<?=$gift_city;?>">
									<input type="hidden" name="gift_state" value="<?=$gift_state;?>">
									<input type="hidden" name="gift_zip" value="<?=$gift_zip;?>">
									<input type="hidden" name="gift_country" value="<?=$gift_country;?>">
									 This donation is a gift in someone else's name.<br>
									 &nbsp;&nbsp;&nbsp;<?="$gift_first_name&nbsp;$gift_last_name";?><BR>
									 &nbsp;&nbsp;&nbsp;<?="$gift_street";?><BR>
									 &nbsp;&nbsp;&nbsp;<?="$gift_city&nbsp;$gift_state&nbsp;$gift_zip";?>
<?				if ($gift_country != $config_default_country) { ?>
									 &nbsp;&nbsp;&nbsp;<?="<BR>".$countries->country_name($gift_country);?>
<?				}	?>
<?			} else { ?>
 									I prefer to remain anonymous
<?			}	?>
									</TD>
								</TR>
								<TR ALIGN="left" VALIGN="middle">
									<TD Align='Left' Colspan='3'>
										<hr>
									</TD>
								</TR>
								<TR ALIGN="left" VALIGN="middle">
									<input type='hidden' name='f_project_id' value='<?=$f_project_id;?>'>
									<input type='hidden' name='search_arg' value='<?=$search_arg;?>'>
									<input type="hidden" name="review" value="Y">
									<TD Align='center' Colspan='3'>
<? if($f_payment_choice == "Paypal/Credit Card") { ?>
		<Input Type="Submit" Name="Submit" class="nicebtns" Value="Prepare Payment">
<? } else { ?>
		<Input Type="Submit" Name="Submit" class="nicebtns" Value="Process Donation">
<? } ?>
		<Input Type="Submit" Name="Submit" class="nicebtns" Value="Previous Step">
		<Input Type="Submit" Name="Submit" class="nicebtns" Value="Cancel">
									</TD>
								</TR>
								</Form>
							</TABLE>
						</TR>
					</TABLE>
<?
		}
	} elseif ($review == "D" && $user->cart_item_total() == 0) {
?>
				<table width="100%" align="center" border=0 cellpadding=5 cellspacing=0>
					<TR>
						<TD bgcolor="<?=$color_table_hdg_bg;?>" align="center">
							<Font Size="+1" color="<?=$color_table_hdg_font;?>">You Shopping Cart is Empty</font>
						</TD>
					</TR>
				</table>
<?
	} elseif ($review == "D") {
?>
		<table width="100%" align="center" border=0 cellpadding=5 cellspacing=0>
			<TR>
				<TD bgcolor="<?=$color_table_hdg_bg;?>" align="left">
					<Font Size="+1" color="<?=$color_table_hdg_font;?>">Donation Payment Info</font>
				</TD>
			</TR>
		</table>
		<table width="100%" align="center" border=0 cellpadding=5 cellspacing=0>
			<TR height=235>
				<TD bgcolor="<?=$color_table_col_bg;?>">
					<form method="POST" ACTION="donation.php">
					<input type="hidden" name="uniqueid" value="<?=$uniqueid;?>">
					<TABLE ALIGN="left" BORDER=0 CELLSPACING=1 CELLPADDING=0 WIDTH="100%">
						<TR ALIGN="left" VALIGN="middle"><TD colspan=3>
<?
		if ($f_include_cc_fee == "Y") $config_cc_fee_default = "Y";
		$f_include_cc_fee = "N";
		include "inc/cart_detail.php";
?>
						</TD></TR>
						<TR Align='Left' VALIGN=Bottom>
							<TD Align='Left' Colspan='3'><b>Donor Name:&nbsp;</b><?=$user->first_name;?>&nbsp;<?=$user->last_name;?>
							<? if (!empty($user->company)) echo ",&nbsp;".$user->company; ?>
							</TD>
						</TR>
<?
/*
						<TR ALIGN='Left' VALIGN=Top>
							<TD Align='Left' Colspan='3'><B>If the request is never fully funded, my donation should be:</B></TD>
						</TR>
						<TR ALIGN='Left' VALIGN=Top>
							<TD Width='5'>&nbsp;</TD>
							<TD Align='Left' Colspan='2'><input type='radio' name='f_donation_option' value='Choose'<?=($d_donation_option != "Refund" ? " Checked" : "");?>>
							&nbsp;Automatically applied to another request.</TD>
						</TR>
						<TR ALIGN='Left' VALIGN=Top>
							<TD Width='5'>&nbsp;</TD>
							<TD Align='Left' Colspan='2'><input type='radio' name='f_donation_option' value='Refund'<?=($d_donation_option == "Refund" ? " Checked" : "");?>>
							&nbsp;Applied to another request after contacting me.</TD>
						</TR>
*/
?>
<input type='hidden' name='f_donation_option' value='Choose'>
						<TR ALIGN='Left' VALIGN=Top>
							<TD Align='Left' Colspan='3'><B>Payment Method</B></TD>
						</TR>
<? if ($config_paypal_enable == "Y") {
	if (empty($f_payment_choice)) {
		$f_payment_choice = "Paypal/Credit Card";
	}
?>
						<TR ALIGN='Left' VALIGN=Top>
							<TD Width='5'>&nbsp;</TD>
							<TD Align='Left' Colspan='2'><input type='radio' name='f_payment_choice' value='Paypal/Credit Card'<?=($f_payment_choice == "Paypal/Credit Card" ? " Checked" : "");?>>
							<b>Paypal or Credit Card</b>
							</TD>
						</TR>
<? if ($config_paypal_fee_enabled == "Y") { ?>
						<TR ALIGN='Left' VALIGN=Top>
							<TD Width='5'>&nbsp;</TD>
							<TD Align='Left' Colspan='2'>
								<TABLE>
									<TR ALIGN='Left' VALIGN=Top>
										<TD Width='25'>&nbsp;</TD>
										<TD Align='left' colspan='2'><input type='checkbox' name='f_include_paypal_fee' value='Y'<?=($config_paypal_fee_default == "Y"?" CHECKED":"");?>>&nbsp;&nbsp;<?=$config_paypal_fee_message;?><BR>Payment Fee Amount: $<?=sprintf("%01.2f", $total * ($config_paypal_fee_rate/100) + $config_paypal_tran_fee);?><input type='hidden' name='f_include_paypal_fee_amount' value='<?=sprintf("%01.2f", $total * ($config_paypal_fee_rate/100) + $config_paypal_tran_fee);?>'></TD>
									</TR>
								</TABLE>
							</TD>
						</TR>
<? } else { ?>
	  <input type='hidden' name='f_include_paypal_fee_amount' value='0'>
<?   }
}
if ($config_authorizenet_accepting_cc == "Y")	{
	if (empty($f_payment_choice)) {
		$f_payment_choice = "Credit";
	}
?>
						<TR ALIGN='Left' VALIGN=Top>
							<TD Width='5'>&nbsp;</TD>
							<TD Align='Left' Colspan='2'><input type='radio' name='f_payment_choice' value='Credit'<?=($f_payment_choice == "Credit" ? " Checked" : "");?>>
							&nbsp;<b>Credit Card</b>
							</TD>
						</TR>
						<TR ALIGN='Left' VALIGN=Top>
							<TD Width='5'>&nbsp;</TD>
							<TD Align='Left' Colspan='2'>
								<TABLE>
<? if ($config_cc_fee_enabled == "Y") { ?>
									<TR ALIGN='Left' VALIGN=Top>
										<TD Width='25'>&nbsp;</TD>
										<TD Align='left' colspan='2'><input type='checkbox' name='f_include_cc_fee' value='Y'<?=($config_cc_fee_default == "Y"?" CHECKED":"");?>>&nbsp;&nbsp;<?=$config_cc_fee_message;?><BR>Credit Card Fee Amount: $<?=sprintf("%01.2f", $total * ($config_cc_fee_rate/100) + $config_cc_tran_fee);?><input type='hidden' name='f_include_cc_fee_amount' value='<?=sprintf("%01.2f", $total * ($config_cc_fee_rate/100) + $config_cc_tran_fee);?>'></TD>
									</TR>
<? } else { ?>
		<input type='hidden' name='f_include_cc_fee_amount' value='0'>
<? } ?>
									<TR ALIGN='Left' VALIGN=Top>
										<TD Width='25'>&nbsp;</TD>
										<TD Align='Right'>Credit Card Type:</TD>
										<TD Align='Left'>
											<select name="f_credit_card_type">
<?
$prev_grp = "";
if (empty($f_credit_card_type))
$f_credit_card_type = $config_default_credit_card_type;
while (list($payment_code, $payment_type) = each($payment_types->payment_type_list)) {
if ($payment_type->credit_card_flag == "Y" && $payment_type->inactive == "N")
echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"$payment_code\"".($payment_code == $f_credit_card_type ? " SELECTED" : "").">$payment_type->payment_type_description</OPTION>\n");
}
?>
											</select>
										</TD>
									</TR>
									<TR ALIGN='Left' VALIGN=Top>
										<TD Width='25'>&nbsp;</TD>
										<TD Align='Right'>Card Number:</TD>
										<TD Align='Left'><input type='text' name='f_credit_card_no' size='20'></TD>
									</TR>
									<TR ALIGN='Left' VALIGN=Top>
										<TD Width='25'>&nbsp;</TD>
										<TD Align='Right'>Expiration Date:</TD>
										<TD Align='Left'><select name='f_expdt_month'>
<?		$mon = 1;
while ($mon <= 12) {
?>
									<option value="<?=$mon;?>"<?=(($f_expdt_month == $mon)?" SELECTED":"");?>><?=sprintf("%02d",$mon);?></option>
<?			$mon += 1;
}
?>
								</select>
								<select name="f_expdt_year">
<?		$yr = date("Y");
while ($yr <= date("Y")+6) {
?>
									<option value="<?=$yr;?>"<?=(($f_expdt_year == $yr)?" SELECTED":"");?>><?=$yr;?></option>
<?			$yr += 1;
}
?>
								</select>
										</TD>
									</TR>
									<TR ALIGN='Left' VALIGN=Top>
										<TD Width='25'>&nbsp;</TD>
										<TD Align='Right'>Card Verification #:</TD>
										<TD Align='Left'><input type='text' name='f_cvv2' size='4'>&nbsp;
											<font size='-2'><a href="javascript:display_msg('<?=$http_location;?>/purchase_cv.htm','395','430')">What's this?</a></font>
										</TD>
									</TR>

								</TABLE>
							</TD>
						</TR>
<?
}	elseif (empty($f_payment_choice)) {
	$f_payment_choice = "Check";
}
?>
						<TR ALIGN='Left' VALIGN=Top>
							<TD Width='5'>&nbsp;</TD>
							<TD Align='Left' Colspan='2'><input type='radio' name='f_payment_choice' value='Check'<?=($f_payment_choice == "Check" ? " Checked" : "");?>>&nbsp;<b>Check</b></TD>
						</TR>
						<TR ALIGN='Left' VALIGN=Top>
							<TD Width='5'>&nbsp;</TD>
							<TD Align='Left' Colspan='2'>
								<TABLE>
									<TR ALIGN='Left' VALIGN=Top>
										<TD Width='25'>&nbsp;</TD>
										<TD Align='Left'>Make payable to <b>Donate2Educate</b>.</TD>
									</TR>
								</TABLE>
							</TD>
						</TR>
<?	if (!empty($district->payment_faxno) && 0) {	?>
						<TR ALIGN='Left' VALIGN=Top>
							<TD Width='5'>&nbsp;</TD>
							<TD Align='Left' Colspan='2'><input type='radio' name='f_payment_choice' value='Fax'<?=($f_payment_choice == "Fax" ? " Checked" : "");?>>&nbsp;<b>Fax</b></TD>
						</TR>
						<TR ALIGN='Left' VALIGN=Top>
							<TD Width='5'>&nbsp;</TD>
							<TD Align='Left' Colspan='2'>
								<TABLE>
									<TR ALIGN='Left' VALIGN=Top>
										<TD Width='25'>&nbsp;</TD>
										<TD Align='Left'>Credit Card information will be faxed to <b><?=$distict_fax_number;?></b>.</TD>
									</TR>
								</TABLE>
							</TD>
						</TR>
<?	}	?>
						<TR ALIGN='Left' VALIGN=Top>
							<TD Align='Left' Colspan='3'><B>Verify Contact Information</B></TD>
						</TR>
<?
if (empty($f_contact_choice))
$f_contact_choice = "D";
if (empty($gift_state))
	$gift_state = $config_default_state;
?>
						<TR ALIGN='Left' VALIGN=Top>
							<TD Width='5'>&nbsp;</TD>
							<TD Align='Left' Colspan='2'><input type='radio' name='f_contact_choice' value='D'<?=($f_contact_choice == "D" ? " Checked" : "");?>>
							&nbsp;Please send all correspondence directly to me.</TD>
						</TR>
						<TR ALIGN='Left' VALIGN=Top>
							<TD Width='5'>&nbsp;</TD>
							<TD Align='Left' Colspan='2'><input type='radio' name='f_contact_choice' value='G'<?=($f_contact_choice == "G" ? " Checked" : "");?>>
							&nbsp;This donation is a gift in someone else's name.</TD>
						</TR>
						<TR ALIGN='Left' VALIGN=Top>
							<TD Width='5'>&nbsp;</TD>
							<TD Align='Left' Colspan='2'>
								<Table>
									<TR ALIGN='Left' VALIGN=Top>
										<TD Width='25'>&nbsp;</TD>
										<TD ALIGN='Right'>First Name</TD>
										<TD ALIGN='Left'><Input Type='Text' Size='30' Maxlength='40' name='gift_first_name' Value='<?=$gift_first_name;?>'></TD>
									</TR>
									<TR ALIGN='Left' VALIGN=Top>
										<TD Width='25'>&nbsp;</TD>
										<TD ALIGN='Right'>Last Name</TD>
										<TD ALIGN='Left'><Input Type='Text' Size='30' Maxlength='40' name='gift_last_name' Value='<?=$gift_last_name;?>'></TD>
									</TR>
									<TR ALIGN='Left' VALIGN=Top>
										<TD Width='25'>&nbsp;</TD>
										<TD ALIGN='Right'>Street</TD>
										<TD ALIGN='Left'><Input Type='Text' Size='40' Maxlength='50' name='gift_street' Value='<?=$gift_street;?>'></TD>
									</TR>
									<TR ALIGN='Left' VALIGN=Top>
										<TD Width='25'>&nbsp;</TD>
										<TD ALIGN='Right'>City</TD>
										<TD ALIGN='Left'><Input Type='Text' Size='30' Maxlength='30' name='gift_city' Value='<?=$gift_city;?>'></TD>
									</TR>
									<TR ALIGN='Left' VALIGN=Top>
										<TD Width='25'>&nbsp;</TD>
										<TD ALIGN='Right'>State</TD>
										<TD ALIGN='Left'>
											<Select name='gift_state'>
<?
if (empty($f_state))
$f_state = $config_default_state;
reset($states->state_list);
$prev_grp = "";
while (list($statecode, $state) = each($states->state_list)) {
if ($state->state_group != $prev_grp) {
if (!empty($prev_grp))
	echo "</optgroup>";
$prev_grp = $state->state_group;
echo ("\t\t\t\t\t\t\t\t\t\t<optgroup label=\"$state->state_group\">\n");
}
echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"$statecode\"".($statecode == $gift_state ? " SELECTED" : "").">$state->state_name</OPTION>\n");
}
if (!empty($prev_grp))
echo "</optgroup>";
?>
											</select>
										</TD>
									</TR>
									<TR ALIGN='Left' VALIGN=Top>
										<TD Width='25'>&nbsp;</TD>
										<TD ALIGN='Right'>Zip Code</TD>
										<TD ALIGN='Left'><Input Type='Text' Size='10' Maxlength='10' name='gift_zip' Value='<?=$gift_zip;?>'></TD>
									</TR>
									<TR ALIGN="left" VALIGN="middle">
										<TD Width='25'>&nbsp;</TD>
										<TD Align='Right' bgcolor="<?=$color_table_col_bg;?>">Country</TD>
										<TD Align='Left'>
											<Select name='gift_country'>
<?
if (empty($f_country))
$f_country = $config_default_country;
reset($country->country_list);
$prev_grp = "";
while (list($country_code, $country) = each($countries->country_list)) {
echo ("\t\t\t\t\t\t\t\t\t\t<OPTION VALUE=\"$country_code\"".($country_code == $gift_country ? " SELECTED" : "").">$country->country_name</OPTION>\n");
}
?>
											</select>
										</TD>
									</TR>
								</table>
							</TD>
						</TR>

						<TR ALIGN='Left' VALIGN=Top>
							<TD Width='5'>&nbsp;</TD>
							<TD Align='Left' Colspan='2'><input type='radio' name='f_contact_choice' value='A'<?=($f_contact_choice == "A" ? " Checked" : "");?>>
							&nbsp;I prefer to remain anonymous.  Do not forward my name along with the funds.  I do not wish to receive thank yous.  Any questions about my donation,
							however, can be addressed per my contact information.
							</TD>
						</TR>
						<TR ALIGN="left" VALIGN="middle">
							<input type='hidden' name='f_project_id' value='<?=$f_project_id;?>'>
							<input type='hidden' name='search_arg' value='<?=$search_arg;?>'>
							<input type="hidden" name="review" value="C">
							<TD Align='Center' Colspan='3'>
			<Input Type="Submit" Name="Submit" class="nicebtns" Value="Next Step">
			<Input Type="Submit" Name="Submit" class="nicebtns" Value="Previous Step">
			<Input Type="Submit" Name="Submit" class="nicebtns" Value="Cancel">
							</TD>
							</Form>
						</TR>
					</TABLE>
				</TD>
			</TR>
		</TABLE>
<?
	} else {
?>
				<table width="100%" align="center" border=0 cellpadding=5 cellspacing=0>
					<TR>
						<TD bgcolor="<?=$color_table_hdg_bg;?>" align="left">
							<Font Size="+1" color="<?=$color_table_hdg_font;?>"><?=$f_project_name;?></font>
						</TD>
<? if ($search_idx) { ?>
						<TD bgcolor="<?=$color_table_hdg_bg;?>" align="right"><?="$prevlink&nbsp;".($nextlink ? "$nextlink&nbsp;" : "");?>
						</TD>
<? } ?>
					</TR>
				</table>
				<table width="100%" align="center" border=0 cellpadding=5 cellspacing=0>
					<TR height=235>
						<TD bgcolor="<?=$color_table_col_bg;?>">
							<TABLE ALIGN="left" BORDER=0 CELLSPACING=1 CELLPADDING=0 WIDTH="100%">
								<TR ALIGN="left" VALIGN="middle">
									<TD Align='Left'><b>Request ID</b></TD><TD Align='Left'><b>Category</b></TD><TD Align='Left'><b>School</b></TD><TD Align='Left'><b>Grade Level</b></TD>
								</TR>
								<TR ALIGN="left" VALIGN="middle">
									<TD Align='Left'><?=$f_project_id;?></TD>
									<TD Align='Left'><?=$projecttypes->project_type_description($f_projecttype);?></TD>
									<TD Align='Left'><?=($schools->school_homepage($f_schoolid) ? "<a target=\"school\" href=\"".$schools->school_homepage($f_schoolid)."\">" : "");?><?=$schools->school_name($f_schoolid);?><?=($schools->school_homepage($f_schoolid) ? "</a>":"");?>
									</TD>
									<TD Align='Left'><?=$gradelevels->grade_level_description($f_gradelevel);?></TD>
								</TR>
								<TR><TD Colspan='4'>&nbsp;</TD></TR>
								<TR ALIGN="left" VALIGN="top">
									<TD Align='Left' valign='Top' Colspan='4'><b>Description</b></TD>
								</TR>
								<TR ALIGN="left" VALIGN="top">
									<TD Align='Left' valign='Top' Colspan='4'><?=preg_replace("/\n/","<BR>",url_to_link($f_description));?></TD>
								</TR>
								<TR><TD Colspan='4'>&nbsp;</TD></TR>
<? 	if (!empty($f_materials)) { ?>
								<TR ALIGN="left" VALIGN="top">
									<TD Align='Left' valign='Top' Colspan='4'><b>Materials Needed</b></TD>
								</TR>
								<TR ALIGN="left" VALIGN="top">
									<TD Align='Left' valign='Top' Colspan='4'><?=preg_replace("/\n/","<BR>",url_to_link($f_materials));?></TD>
								</TR>
								<TR><TD Colspan='4'>&nbsp;</TD></TR>
<?	}	?>
								<TR ALIGN="left" VALIGN="middle">
									<TD Colspan='4'>
										<TABLE ALIGN="left" BORDER=0 CELLSPACING=1 CELLPADDING=0 WIDTH="100%">
											<TR><TD Align='Center' Valign='Bottom'><b>Request Cost</b></TD>
											<TD Align='Center' Valign='Bottom'><b>
<? if (($f_amount_needed-$f_amount_donated-$project->amount_pledged() <= 0) || ($project->project_status_id == 4) || ($project->project_status_id == 5)) { ?>
											Fully Funded
<? } else { ?>
											Funds Still Needed
<?	}	?>
											</b></TD>
<?      if (($project->project_status_id == 3) && ($matching_amount > 0) && ($f_amount_needed-$f_amount_donated-$project->amount_pledged() > 0)) echo "<TD Align='Center' Valign='Bottom'><font color='Red'><b>Matching Funds Available</b></font></TD>"; ?>
											</TR>
											<TR>
												<TD Align='Center' Valign='Top'>$<? echo(sprintf("%01.2f", $f_amount_needed)); ?></TD>
												<TD Align='Center' Valign='Top'><? echo($f_amount_needed-$f_amount_donated-$project->amount_pledged() <= 0 || ($project->project_status_id == 4) || ($project->project_status_id == 5) ? "&nbsp;" : "$".sprintf("%01.2f", $f_amount_needed-$f_amount_donated)); ?></TD>
<?	if (($project->project_status_id == 3) && ($matching_amount > 0)) { ?>
												<TD Align='Center' Valign='Top'><font color='Red'>$<? echo(sprintf("%01.2f", $matching_amount)); ?></font></TD>
<?  } ?>
											</TR>
<?	if (($project->project_status_id == 3) && ($matching_amount > 0) && ($f_amount_needed-$f_amount_donated-$project->amount_pledged() > 0)) { ?>
											<TR>
												<TD Align='Center' Valign='Top'>&nbsp;</TD>
												<TD Align='Center' Valign='Top'>&nbsp;</TD>
												<TD Align='Center' Valign='Top'><font color='Red'><B>Offered By</B><BR></font>
<?
	$sponsors = $matching->matching_sponsors($projectid);
	reset($sponsors);
	$offered_by = "";
	while (list($sponsorid, $sponsor) = each($sponsors)) {
		if (!empty($offered_by))
			$offered_by .= " <font color='Red'>and<BR></font>";
		if (!empty($sponsor->url)) $offered_by .= "<a href=\"$sponsor->url\" target=\"sponsor\"><font color='Red'>";
		if (empty($sponsor->company))
			$offered_by .= ("$sponsor->first_name $sponsor->last_name");
		else
			$offered_by .= ("$sponsor->company");
		# The user.setup_date was set to when the matching funds ends.  If the setup_date is less than today then there is no end.
		if (strtotime($sponsor->setup_date) > time())
			$offered_by .= "<br>until ".date("m/d/y", strtotime($sponsor->setup_date));
		if (!empty($sponsor->url)) $offered_by .=  "</font></a>";
	}
	echo "<font color='Red'>$offered_by</font>";
?>
												</font></TD>
											</TR>
<?  } ?>
										</TABLE>
									</TD>
								</TR>
								<TR><TD Colspan='4'>&nbsp;</TD></TR>
<? if (!empty($f_date_required) && false) { ?>
								<TR ALIGN="left" VALIGN="middle">
									<TD Align='Left' Colspan='4'><b>Deadline for Funds</b></TD>
								</TR>
								<TR ALIGN="left" VALIGN="middle">
									<TD Align='Left' Colspan='4'><?=date("m/d/y", strtotime($f_date_required));?></TD>
								</TR>
								<TR><TD Colspan='4'>&nbsp;</TD></TR>
<?	}
	if (($project->project_status_id == 3) && ($f_amount_needed-$f_amount_donated > 0)) {
?>
								<TR ALIGN="left" VALIGN="middle">
									<TD Align='center' Colspan='4'><HR></TD>
								</TR>
								<TR ALIGN="left" VALIGN="middle">
									<TD Align='center' Colspan='4'><font size='+1'>Donate To This Request</font></TD>
								</TR>
<?
	if (!isset($User_ID) && $REQUEST_METHOD != "POST") {
		$project->project_viewed();
?>
								<TR ALIGN="Left" VALIGN="middle">
									<TD Align="Center" Colspan="4">
										<table width=100% cellspacing=1 cellpadding=1>
											<TR>
												<TD align=left width=50% bgcolor="<?=$color_table_hdg_bg;?>">
													<table width="100%" align="center" border=0 cellpadding=5 cellspacing=0>
														<TR>
															<TD bgcolor="<?=$color_table_hdg_bg;?>" align="left">
																<font color="<?=$color_table_hdg_font;?>" face="Arial" size=2><b>Existing Users</b></font>
															</TD>
														</TR>
													</table>
													<table width="100%" align="center" border=0 cellpadding=5 cellspacing=0>
														<TR height=285>
															<TD bgcolor="<?=$color_table_col_bg;?>">
																<?=$config_login_existing_msg;?>
																<table width="100%" border=0 align=left>
																	<TR>
																	<Form Name='Login' Method='POST' Action="<?=$http_location;?>login.php">
																		<TD Align='Right'><b>ID / E-MAIL</b></TD>
																		<TD><input type='text' size='11' maxlength='100' name='login_username' value='<?=$login_username;?>'></TD>
																	</TR>
																	<TR ALIGN="left" VALIGN="middle">
																		<TD Align='Right'><b>PASSWORD</b></TD>
																		<TD><input type='password' size='11' maxlength='100' name='login_password' value='<?=$login_password;?>'></TD>
																	</TR>
																	<TR ALIGN="left" VALIGN="middle">
																		<TD Align='Center' Colspan='2'><Input Type='Submit' class="nicebtns" Name='Login' Value='Log In'></TD>
																		<input type='hidden' name='target' value='<?=urlencode($https_location."donation.php?projectid=$projectid");?>'>
																		</Form>
																	</TR>
																	<TR ALIGN="left" VALIGN="bottom">
																		<TD Align='Center' Colspan='2'><a href="login_lookup.php?target=<?=urlencode($https_location."donation.php?projectid=$projectid");?>"><font size="-1">Forgot your password?<br>Click Here!</font></a></TD>
																	</TR>
																</table>
															</TD>
														</TR>
													</table>
												</TD>
												<TD width=5>&nbsp;</TD>
												<TD align=right width=50% valign=top bgcolor="<?=$color_table_hdg_bg;?>">
													<table width="100%" align="center" border=0 cellpadding=5 cellspacing=0>
														<TR>
															<TD bgcolor="<?=$color_table_hdg_bg;?>" align="left">
																<font color="<?=$color_table_hdg_font;?>" face="Arial" size=2><b>New Users</b></font>
															</TD>
														</TR>
													</table>
													<table width="100%" align="center" border=0 cellpadding=5 cellspacing=0>
														<TR height=285>
															<TD bgcolor="<?=$color_table_col_bg;?>" align=center>
																<p><?=$config_login_new_msg;?></p>
																<font size=2>
																<a href="<?=$https_location;?>registration.php?f_type_id=10&target=<?=urlencode("donation.php?projectid=$projectid");?>"><b>Click Here for Donor Registration</b></a>
																</font>
															</TD>
														</TR>
													</table>
												</TD>
											</TR>
<?
	} else {
		if (!($user->type_id > 10))
			$project->project_viewed();
?>
								<TR ALIGN='Left' VALIGN=Bottom>
									<TD Align='Left' Colspan='4'>
										<TABLE Width="100%">
											<form method="POST" ACTION="donation.php" name="Donation">
											<input type="hidden" name="uniqueid" value="<?=$uniqueid;?>">
											<TR Align='Left' VALIGN=Bottom>
												<TD Align='Left' Colspan='3'><b>Donor Name:</b></TD>
											</TR>
											<TR Align='Left' VALIGN=Bottom>
												<TD Width='5'>&nbsp;</TD>
												<TD Align='Left' Colspan='2'><?=$user->first_name;?>&nbsp;<?=$user->last_name;?>
												<? if (!empty($user->company)) echo "<BR>".$user->company; ?>
												</TD>
											</TR>
											<TR Align='Left' VALIGN=Bottom>
												<TD Align='Left' Colspan='3'><b>Donation Amount</b></TD>
											</TR>
<?	if (empty($f_donation_choice))
		$f_donation_choice = ($matching_amount>0 ? "FullwMatch" : "Full");
?>
<?	if (($matching_amount > 0) && ($f_amount_needed-$f_amount_donated > 0)) { ?>
											<TR ALIGN='Left' VALIGN=Top>
												<TD Width='5'>&nbsp;</TD>
												<TD Align='Left' Colspan='2'><input type='radio' name='f_donation_choice' value='FullwMatch'<?=($f_donation_choice=="FullwMatch"?" Checked":"");?>>
												&nbsp;<input type='hidden' name='f_fullwmatch_amount' value='<?=sprintf("%01.2f", ($f_amount_needed-$f_amount_donated-$matching_amount));?>'><b><?=sprintf("%01.2f", ($f_amount_needed-$f_amount_donated-$matching_amount));?></b>
												&nbsp;I wish to fully fund this request <b>using</b> matching funds.</TD>
											</TR>
<? } ?>
											<TR ALIGN='Left' VALIGN=Top>
												<TD Width='5'>&nbsp;</TD>
												<TD Align='Left' Colspan='2'><input type='radio' name='f_donation_choice' value='Full'<?=($f_donation_choice=="Full"?" Checked":"");?>>
												&nbsp;<input type='hidden' name='f_full_amount' value='<?=sprintf("%01.2f", ($f_amount_needed-$f_amount_donated));?>'><b><?=sprintf("%01.2f", ($f_amount_needed-$f_amount_donated));?></b>
												&nbsp;I wish to fully fund this request
<?	if ($matching_amount > 0) echo " <b>without</b> using matching funds"; ?>
												.</TD>
											</TR>
											<TR ALIGN='Left' VALIGN=Top>
												<TD Width='5'>&nbsp;</TD>
												<TD Align='Left' Colspan='2'><input type='radio' name='f_donation_choice' value='Partial'<?=($f_donation_choice=="Partial"?" Checked":"");?>>
												&nbsp;<input size='6' type='input' name='f_partial_amount' value='<?=$f_partial_amount;?>' onchange="partialDonation(document.Donation.f_partial_amount, document.Donation.f_full_amount, document.Donation.f_donation_choice, <?=(($matching_amount > 0) && ($f_amount_needed-$f_amount_donated > 0) ? "2" : "1");?>);">
												&nbsp;I wish to partially fund this request, as I have identified here.</TD>
											</TR>
											<TR ALIGN="left" VALIGN="middle">
												<input type='hidden' name='f_project_id' value='<?=$f_project_id;?>'>
												<input type='hidden' name='search_arg' value='<?=$search_arg;?>'>
												<TD Align='Center' Colspan='3'>
							<input type="hidden" name="review" value="M">
		<Input Type="Submit" Name="Submit" class="nicebtns" Value="Add Donation To Cart">
		<Input Type="Submit" Name="Submit" class="nicebtns" Value="Choose a Different Request">
		<Input Type="Submit" Name="Submit" class="nicebtns" Value="Cancel">
<?
		}
?>
												</TD>
												</Form>
											</TR>
<? 	if ($User_ID) {
		include "inc/send_to_friend.php";
	} ?>
										</TABLE>
									</TD>
								</TR>
<? } # project->projectstatusid ?>
							</table>
						</TD>
					</TR>
				</table>
			</TD>
<?
	}
?>
		</TR>
	</table>
<?
	if ($config_donation_banners == 'Y') include "inc/banner_ads.php";
?>
</TD>
<? require "inc/body_end.inc"; ?>
</html>
