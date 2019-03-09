<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";
	require_once "inc/class_users.php";
	require_once "inc/class_school.php";
	require_once "inc/class_schools.php";
	require_once "inc/class_district.php";
	require_once "inc/class_grade_level.php";
	require_once "inc/class_grade_levels.php";
	require_once "inc/class_project_type.php";
	require_once "inc/class_project_types.php";
	require_once "inc/class_affiliation.php";
	require_once "inc/class_affiliations.php";
	require_once "inc/class_project_status.php";
	require_once "inc/class_project_statuses.php";
	require_once "inc/class_project.php";
	require_once "inc/class_project_comment.php";
	require_once "inc/class_projects.php";
	require_once "inc/class_donation.php";
	require_once "inc/class_donations.php";
	require_once "inc/class_donation_project.php";
	require_once "inc/class_disbursement.php";
	require_once "inc/class_disbursements.php";
	require_once "inc/class_payment_type.php";
	require_once "inc/class_payment_types.php";

	$user = new user();
	$user->load_user($User_ID);

	require "inc/validate_teacher.php";

if ($debug) {
	echo "loading schools<br>";
	flush();
}
	$schools = new schools();
	$schools->load_schools();

if ($debug) {
	echo "loading grade_levels<br>";
	flush();
}
	$gradelevels = new grade_levels();
	$gradelevels->load_grade_levels();

if ($debug) {
	echo "loading project_types<br>";
	flush();
}
	$projecttypes = new project_types();
	$projecttypes->load_project_types();

	$payment_types = new payment_types();
	$payment_types->load_payment_types();

	$user_affiliations = new affiliations;
	$user_affiliations->load_affiliations($User_ID);

	$statuses = new project_statuses;
	$statuses->load_project_statuses();
	$users = new users;
	if ($REQUEST_METHOD == "POST") {
if ($debug) {
	echo "Posting<br>";
	flush();
}
			// Get form values
			$Submit 			= $_POST["Submit"];

			$projectid			= $_POST["projectid"];
			$f_new_projectid	= $_POST["f_new_projectid"];
			$f_project_name 	= $_POST["f_project_name"];
			$f_amount_needed 	= $_POST["f_amount_needed"];
			$f_newprojecttype 	= $_POST["f_newprojecttype"];
			$f_gradelevel 		= $_POST["f_gradelevel"];
			$f_schoolid 		= $_POST["f_schoolid"];
			$f_description 		= $_POST["f_description"];
			$f_project_id 		= $_POST["f_project_id"];
			$f_materials 		= $_POST["f_materials"];
			$f_review_notes 	= $_POST["f_review_notes"];
			$f_shipping_charge	= $_POST["f_shipping_charge"];
			$f_handling_charge	= $_POST["f_handling_charge"];
			$f_date_submitted	= $_POST["f_date_submitted"];
			$f_date_last_updated	= $_POST["f_date_last_updated"];
			$f_date_last_warning	= $_POST["f_date_last_warning"];
			$f_date_status_changed	= $_POST["f_date_status_changed"];
			$f_status_id		= $_POST["f_status_id"];

			$disb_amount 		= $_POST["disb_amount"];
			$disb_id 			= $_POST["disb_id"];
			$disb_tran_no		= $_POST["disb_tran_no"];

			$comment_id 		= $_POST["comment_id"];
			$comment_text 		= $_POST["comment_text"];

			$date_receipts_received	= $_POST["date_receipts_received"];
			$date_thankyous_sent	= $_POST["date_thankyous_sent"];

			$payment_no			= $_POST["payment_no"];
			$donationids		= $_POST["donationids"];
			$payment_received_date = $_POST["payment_received_date"];

            // Validate the fields.
            $errors = "";
            $message = "";
            $f_amount_needed = str_replace(",","",str_replace("$","",$f_amount_needed));
            if ($Submit == "Save for Editing" || $Submit == "Submit for Review") {
                if (empty($f_project_name))
                    $errors .= (empty($errors) ? "" : "<BR>")."Project Name is required.";
                if ($f_projecttype == 0 && empty($f_newprojecttype))
                    $errors .= (empty($errors) ? "" : "<BR>")."Category is required.";
                if ($f_gradelevel == 0)
                    $errors .= (empty($errors) ? "" : "<BR>")."Grade Level is required.";
                if ($f_schoolid == 0)
                    $errors .= (empty($errors) ? "" : "<BR>")."School is required.";
                if (empty($f_description))
                    $errors .= (empty($errors) ? "" : "<BR>")."Project Description is required.";
                if (empty($f_amount_needed) || $f_amount_needed < $config_min_project_amount)
                    $errors .= (empty($errors) ? "" : "<BR>")."Amount Needed must be > ".sprintf("%01.2f", $config_min_project_amount).".";
            }
            if (empty($errors)) {
                // Save project
                $project = new project();
                $stay_with_project = false;
                if (!empty($f_project_id)) {
                    $project->load_project($f_project_id, $User_ID);
                    $prev_project_status = $project->project_status_id;
                } else {
                    $project->submitted_user_id = $user->user_id;
                    $project->entered_date = date("Y-m-d H:i:s");
                    $prev_project_status = 0;
                }
                if ($Submit == "Save for Editing")
                    $project->project_status_id = 0;
                elseif ($Submit == "Save Changes") {
                    $project->project_name = $f_project_name;
                    $project->project_description = $f_description;
                    $project->materials_needed = $f_materials;
                    $project->review_notes = $f_review_notes;
                } elseif ($Submit == "Unsubmit for Review")
                    $project->project_status_id = 0;
                elseif ($Submit == "Modify Project")
                    $project->project_status_id = 0;
                elseif ($Submit == "Confirm Project Active")
                    $project->date_last_updated = date("Y-m-d H:i:s");
                elseif ($Submit == "Submit for Review") {
                    $project->project_status_id = 1;
                    $project->submitted_date = date("Y-m-d H:i:s");
                } elseif ($Submit == "Return for Modification") {
                    $project->project_status_id = 2;
                    $project->review_date = date("Y-m-d H:i:s");
                    $project->review_user_id = $User_ID;
                } elseif ($Submit == "Approve") {
                    $project->project_name = $f_project_name;
                    $project->project_description = $f_description;
                    $project->materials_needed = $f_materials;
                    $project->project_status_id = 3;
                    $project->review_date = date("Y-m-d H:i:s");
                    $project->review_user_id = $User_ID;
                    $project->review_notes = $f_review_notes;
                } elseif ($Submit == "Mark Project Funded" || $Submit == "Return Project to Funded Status") {
                    $project->project_status_id = 4;
                    if ($project->completed_date == "")
                        $project->completed_date  = date("Y-m-d H:i:s");
                    $stay_with_project = true;
                } elseif ($Submit == "Mark Project Unfunded") {
                    $project->project_status_id = 7;
                    if ($project->completed_date == "")
                        $project->completed_date  = date("Y-m-d H:i:s");
                } elseif ($Submit == "Update Comment Changes") {
                    $comment = new project_comment();
                    $i = 1;
                    while ($i <= count($comment_id)) {
                        if ($comment_id[$i] > 0) {
                            // Update existing comment.
                            if ($comment->load_project_comment($comment_id[$i])) {
                                if ($comment_delete[$i] == "Y") {
                                    // delete comment.
                                    if ($comment->delete_project_comment())
                                        $message .= "Comment deleted.<BR>";
                                    else
                                        $message .= "Delete failed.<BR>".$comment->error_message;
                                } else {
                                    $comment->comment = $comment_text[$i];
                                    $comment->save_project_comment();
                                }
                            } else {
                                $message .= "Unable to load comment ".$comment_id[$i]."<BR>";
                            }
                        } elseif (!empty($comment_text[$i])) {
                            // Add a new comment
                            $message .= "Adding a comment.<BR>";
                            $comment = new project_comment();
                            $comment->project_id = $project->project_id;
                            $comment->entered_by = $User_ID;
                            $comment->comment = $comment_text[$i];
                            $comment->save_project_comment();
                            $message .= $comment->error_message;
                        }
                        $i += 1;
                    }
                    $stay_with_project = true;
                    echo "<script type=\"text/javascript\">\nlocation.href='proposal.php?projectid=$projectid&message=".htmlentities(urlencode($message))."'\n</script>";
                    exit;
                } elseif ($Submit == "Update Disbursement Changes") {
                    $disbursement = new disbursement();
                    $i = 1;
                    while ($i <= count($disb_recipient_name)) {
                        if ($disb_id[$i] == '0') {
                            // Update existing disbursement
                            if ($disb_amount[$i] > 0) {
                                $disbursement = new disbursement();
                                $disbursement->project_id = $projectid;
                                $disbursement->recipient_name = $disb_recipient_name[$i];
                                $disbursement->disbursement_amount = $disb_amount[$i];
                                $disbursement->tran_no = $disb_tran_no[$i];
                                $disbursement->disbursement_date = date("Y-m-d", strtotime($disb_date[$i]="" ? date("Y-m-d H:i:s") : $disb_date[$i]));
                                if ($disbursement->save_disbursement()) {
                                    $message .= "Added Disbursement #$disbursement->disbursement_id<br>";
                                    $project->funds_dispersed_amount += $disb_amount[$i];
                                    if ($project->funds_dispersed_amount > 0 and $project->project_status_id == 4)
                                        $project->funds_dispersed = "Y";
                                    else
                                        $project->funds_dispersed = "N";
                                    $project->funds_dispersed_date = $disbursement->disbursement_date;
                                }
                            }
                        } else {
                            if ($disbursement->load_disbursement($disb_id[$i])) {
                                if (($disb_delete[$i] == "Y") || ($disb_amount[$i] == 0)) {
                                    if ($disbursement->delete_disbursement()) {
                                        $message .= "Disbursement #$disbursement->disbursement_id deleted.<br>";
                                        $project->funds_dispersed_amount = 0;
                                        $project->funds_dispersed = "N";
                                        $project->funds_dispersed_date = "";
                                    } else {
                                        $message .= "Disbursement #".$disb_id[$i]." not deleted:".$disbursement->error_message."<br>";
                                    }
                                } else {
                                    $disbursement->recipient_name = $disb_recipient_name[$i];
                                    $disbursement->disbursement_date = date("Y-m-d", strtotime($disb_date[$i]));
                                    $disbursement->tran_no = $disb_tran_no[$i];
                                    $prevdisbamount = $disbursement->disbursement_amount;
                                    $disbursement->disbursement_amount = $disb_amount[$i];
                                    if ($disbursement->save_disbursement()) {
                                        $message .= "Disbursement #$disbursement->disbursement_id updated.<br>";
                                        $project->funds_dispersed_amount += $disb_amount[$i] - $prevdisbamount;
                                        if ($project->funds_dispersed_amount > 0)
                                            $project->funds_dispersed = "Y";
                                        else
                                            $project->funds_dispersed = "N";
                                        $project->funds_dispersed_date = $disbursement->disbursement_date;
                                    } else {
                                        $message .= "Disbursement #".$disb_id[$i]." not saved:".$disbursement->error_message."<br>";
                                    }
                                }
                            } else {
                                $message .= "Disbursement #".$disb_id[$i]." not loaded:".$disbursement->error_message."<br>";
                            }
                        }
                        $i += 1;
                    }
                    $stay_with_project = true;
                    echo "<script type=\"text/javascript\">\nlocation.href='proposal.php?projectid=$projectid&message=".htmlentities(urlencode($message))."'\n</script>";
                    exit;
                } elseif ($Submit == "Update Receipts") {
                    $project->date_receipts_received = ($date_receipts_received == "" ? "" : date("Y-m-d", strtotime($date_receipts_received)));
                    $stay_with_project = true;
                } elseif ($Submit == "Update Thankyous") {
                    $project->date_thankyous_sent = ($date_thankyous_sent == "" ? "" : date("Y-m-d", strtotime($date_thankyous_sent)));
                    $stay_with_project = true;
                } elseif ($Submit == "Update Donation Changes") {
                    # Update Donation Changes.
                    $donation = new donation;
                    $i = 1;
                    while ($i <= count($payment_no)) {
                        if ($donation->load_donation($donationids[$i])) {
                            $donation->payment_no = $payment_no[$i];
                            if (!empty($payment_received_date[$i])) {
                                $donation->payment_received_date = date("Y-m-d", strtotime($payment_received_date[$i]));
                                $donation->payment_received = "Y";
                                $donation->payment_no = $payment_no[$i];
                            }
                            if ($donation->save_donation())
                                $message .= "Donation $donation->donation_id saved.<br>";
                            else
                                $message .= "Donation $donation->donation_id failed to save: $donation->error_message<br>";
                        } else
                            $message .= "Donation ".$donationids[$i]." failed to load: $payment_no[$i] $payment_received_date[$i] $donation->error_message<br>";
                        $i += 1;
                    }
                    if (empty($message))
                        $message .= "Donation changes saved.<br>";
                    $stay_with_project = true;
                } elseif ($Submit == "Deny") {
                    $project->project_status_id = 8;
                    $project->review_date = date("Y-m-d H:i:s");
                    $project->review_user_id = $User_ID;
                } elseif ($Submit == "Cancel Project")
                    $project->project_status_id = 9;
                elseif ($Submit == "Delete")
                    $project->project_status_id = 9;

                if (($Submit == "Save for Editing") || ($Submit == "Submit for Review")) {
                    $project->project_name = $f_project_name;
                    $project->project_description = $f_description;
                    $project->grade_level_id = $f_gradelevel;
                    if (!empty($f_newprojecttype))
                        $project->project_type_id = $projecttypes->new_project_type($f_newprojecttype);
                    else
                        $project->project_type_id = $f_projecttype;
                    $project->school_id = $f_schoolid;
                    $project->materials_needed = $f_materials;
                    if ($f_shipping_charge == "")
                    {
                        $f_shipping_charge = $f_amount_needed * ((substr($config_shipping_charge, 0, strlen($config_shipping_charge)-1))/100);
                        if ($f_shipping_charge > $config_shipping_charge_max)
                           $f_shipping_charge = $config_shipping_charge_max;
                    }
                    $project->amount_needed = $f_amount_needed + $f_handling_charge + $f_shipping_charge;
                    $project->handling_charge = $f_handling_charge;
                    $project->shipping_charge = $f_shipping_charge;
                } elseif (($Submit == "Return for Modification") || ($Submit == "Approve") ||($Submit == "Deny")) {
                    $project->review_notes = $f_review_notes;
                }
                if (($Submit == "Create Copy")) {
                    echo "<script type=\"text/javascript\">\nlocation.href='".(stristr($SCRIPT_NAME,"/admin/") ? "../" : "")."proposal.php?projectid=$f_project_id&createcopy=1'\n</script>";
                    exit;
                } elseif (($Submit == "Save All Fields")) {
                    $project->new_project_id = $f_new_projectid;
                    if (empty($f_submitted_user_id)) {
                        if (empty($project->submitted_user_id))
                                $project->submitted_user_id = $user->user_id;
                    } else
                        $project->submitted_user_id = $f_submitted_user_id;
                    $project->submitted_date = (empty($f_date_submitted) ? "" : date("Y-m-d", strtotime($f_date_submitted)));
                    $project->entered_date = (empty($f_date_submitted) ? "" : date("Y-m-d", strtotime($f_date_submitted)));
                    $project->date_last_updated = (empty($f_date_last_updated) ? "" : date("Y-m-d", strtotime($f_date_last_updated)));
                    $project->date_last_warning = (empty($f_date_last_warning) ? "" : date("Y-m-d", strtotime($f_date_last_warning)));
                    $project->date_status_changed = (empty($f_date_status_changed) ? "" : date("Y-m-d", strtotime($f_date_status_changed)));
                    $project->project_name = $f_project_name;
                    $project->project_description = $f_description;
                    $project->grade_level_id = $f_gradelevel;
                    if (!empty($f_newprojecttype))
                        $project->project_type_id = $projecttypes->new_project_type($f_newprojecttype);
                    else
                        $project->project_type_id = $f_projecttype;
                    $project->school_id = $f_schoolid;
                    $project->project_status_id = $f_status_id;
                    $project->materials_needed = $f_materials;
                    if ($f_shipping_charge == "")
                    {
                        $f_shipping_charge = $f_amount_needed * ((substr($config_shipping_charge, 0, strlen($config_shipping_charge)-1))/100);
                        if ($f_shipping_charge > $config_shipping_charge_max)
                           $f_shipping_charge = $config_shipping_charge_max;
                    }
                    $project->amount_needed = $f_amount_needed + $f_handling_charge + $f_shipping_charge;
                    $project->handling_charge = $f_handling_charge;
                    $project->shipping_charge = $f_shipping_charge;
                    $project->review_user_id = ($f_review_user_id == "" ? "0" : $f_review_user_id);
                    if (!empty($f_review_user_id))
                        $project->review_date = (empty($f_date_submitted) ? "" : date("Y-m-d", strtotime($f_date_submitted)));
                    $project->date_receipts_received = ($date_receipts_received == "" ? "" : date("Y-m-d", strtotime($date_receipts_received)));
                    $project->date_thankyous_sent = ($date_thankyous_sent == "" ? "" : date("Y-m-d", strtotime($date_thankyous_sent)));

                    $stay_with_project = true;
                    $suppress_email = true;
                }
                if ($project->save_project()) {
                    if (!$message) $message .= stripslashes("Project '$project->project_name' saved.");
                    $f_project_id = $project->project_id;
                    $f_project_name = $project->project_name;
                    $f_description = $project->project_description;
                    $f_gradelevel = $project->grade_level_id;
                    $f_projecttype = $project->project_type_id;
                    $f_schoolid = $project->school_id;
                    $f_materials = $project->materials_needed;
                    $f_handling_charge = $project->handling_charge;
                    $f_shipping_charge = $project->shipping_charge;
                    $f_amount_needed = $project->amount_needed;
                    $f_amount_received = $project->amount_donated();
                    $f_amount_pledged = $project->amount_pledged();
                    $f_status_id = $project->project_status_id;
                    $projectid = $project->project_id;

                    if (!$suppress_email) {
                        if ($prev_project_status != 1 && $project->project_status_id == 1) {
                            # Notify Reviewer that the project has been submitted for approval
                            $body = $config_proposal_review_email_body;
                            $subject = $config_proposal_review_email_subject;

                            $body 	 = eregi_replace("%PROJECT_ID","$project->project_id",
                            eregi_replace("%PROJECT_NAME",stripslashes($project->project_name),
                            eregi_replace("%PROJECT_DESCRIPTION",stripslashes($project->project_description),
                            eregi_replace("%SCHOOL",$schools->school_name($project->school_id),
                            eregi_replace("%CATEGORY",$projecttypes->project_type_description($project->project_type_id),
                            eregi_replace("%SUBMITTED_BY",$users->user_name($project->submitted_user_id),
                            eregi_replace("%GRADE_LEVEL",$gradelevels->grade_level_description($project->grade_level_id),
                            eregi_replace("%AMOUNT_NEEDED",sprintf("%01.2f", $project->amount_needed),
                            eregi_replace("%AMOUNT_DONATED",sprintf("%01.2f", $project->amount_donated()),
                            eregi_replace("%REVIEW_NOTES",$project->review_notes,
                            eregi_replace("%MATERIALS_NEEDED",$project->materials_needed,
                            eregi_replace("%URL",$http_location,
                            $body))))))))))));

                            $subject  	 = eregi_replace("%PROJECT_ID","$project->project_id",
                            eregi_replace("%PROJECT_NAME",stripslashes($project->project_name),
                            eregi_replace("%PROJECT_DESCRIPTION",stripslashes($project->project_description),
                            eregi_replace("%SCHOOL",$schools->school_name($project->school_id),
                            eregi_replace("%CATEGORY",$projecttypes->project_type_description($project->project_type_id),
                            eregi_replace("%SUBMITTED_BY",$users->user_name($project->submitted_user_id),
                            eregi_replace("%GRADE_LEVEL",$gradelevels->grade_level_description($project->grade_level_id),
                            eregi_replace("%AMOUNT_NEEDED",sprintf("%01.2f", $project->amount_needed),
                            eregi_replace("%AMOUNT_DONATED",sprintf("%01.2f", $project->amount_donated()),
                            eregi_replace("%REVIEW_NOTES",$project->review_notes,
                            eregi_replace("%MATERIALS_NEEDED",$project->materials_needed,
                            eregi_replace("%URL",$http_location,
                            $subject))))))))))));

                            $email_to = $project->reviewer();
                            if (empty($email_to))
                                $email_to = "support@donate2educate.org";
                            $headers = "From: Donate2Educate <support@donate2educate.org>\r\nCC: ".$users->user_email($project->submitted_user_id)."\r\nBCC: support@donate2educate.org\r\n";
                            if (!empty($email_to))
                                mail($email_to, $subject, $body, $headers);
                        } elseif ($prev_project_status == 3 && $project->project_status_id == 4) {
                            # Project is funded
                            $school = new school;
                            $school->load_school($project->school_id);
                            $district = new district;
                            $district->load_district($school->district_id);
                            # Notify Reviewer that the project has been funded
                            if ($district->funded_email_override == "Y" && $district->funded_email_body != "")
                                $body = $district->funded_email_body;
                            else
                                $body = $config_proposal_funded_email_body;
                            if ($district->funded_email_override == "Y" && $district->funded_email_subject != "")
                                $subject = $district->funded_email_subject;
                            else
                                $subject = $config_proposal_funded_email_subject;

                            $body 	 = eregi_replace("%PROJECT_ID","$project->project_id",
                            eregi_replace("%PROJECT_NAME",stripslashes($project->project_name),
                            eregi_replace("%PROJECT_DESCRIPTION",stripslashes($project->project_description),
                            eregi_replace("%SCHOOL",$schools->school_name($project->school_id),
                            eregi_replace("%CATEGORY",$projecttypes->project_type_description($project->project_type_id),
                            eregi_replace("%SUBMITTED_BY",$users->user_name($project->submitted_user_id),
                            eregi_replace("%GRADE_LEVEL",$gradelevels->grade_level_description($project->grade_level_id),
                            eregi_replace("%AMOUNT_NEEDED",sprintf("%01.2f", $project->amount_needed),
                            eregi_replace("%AMOUNT_DONATED",sprintf("%01.2f", $project->amount_donated()),
                            eregi_replace("%REVIEW_NOTES",$project->review_notes,
                            eregi_replace("%MATERIALS_NEEDED",$project->materials_needed,
                            eregi_replace("%URL",$http_location,
                            eregi_replace("%DONORS",$project->donors(),
                            $body)))))))))))));

                            $subject  	 = eregi_replace("%PROJECT_ID","$project->project_id",
                            eregi_replace("%PROJECT_NAME",stripslashes($project->project_name),
                            eregi_replace("%PROJECT_DESCRIPTION",stripslashes($project->project_description),
                            eregi_replace("%SCHOOL",$schools->school_name($project->school_id),
                            eregi_replace("%CATEGORY",$projecttypes->project_type_description($project->project_type_id),
                            eregi_replace("%SUBMITTED_BY",$users->user_name($project->submitted_user_id),
                            eregi_replace("%GRADE_LEVEL",$gradelevels->grade_level_description($project->grade_level_id),
                            eregi_replace("%AMOUNT_NEEDED",sprintf("%01.2f", $project->amount_needed),
                            eregi_replace("%AMOUNT_DONATED",sprintf("%01.2f", $project->amount_donated()),
                            eregi_replace("%REVIEW_NOTES",$project->review_notes,
                            eregi_replace("%MATERIALS_NEEDED",$project->materials_needed,
                            eregi_replace("%URL",$http_location,
                            $subject))))))))))));

                            $email_to = $users->user_email($project->submitted_user_id);
                            if (empty($email_to))
                                $email_to = "support@donate2educate.org";
                            $headers = "From: Donate2Educate <support@donate2educate.org>";
                            $headers .= "\r\nCC: ".$project->notify_emails()."\r\nBCC: support@donate2educate.org".($config_proposal_funded_email_bcc ? ", $config_proposal_funded_email_bcc" : "")."\r\n";
                            if (!empty($email_to))
                                mail($email_to, $subject, $body, $headers);
                        } elseif ($prev_project_status != 2 && $project->project_status_id == 2) {
                            # Notify user that the project has been returned
                            $body = $config_proposal_return_email_body;
                            $subject = $config_proposal_return_email_subject;

                            $body 	 = eregi_replace("%PROJECT_ID","$project->project_id",
                            eregi_replace("%PROJECT_NAME",stripslashes($project->project_name),
                            eregi_replace("%PROJECT_DESCRIPTION",stripslashes($project->project_description),
                            eregi_replace("%SCHOOL",$schools->school_name($project->school_id),
                            eregi_replace("%CATEGORY",$projecttypes->project_type_description($project->project_type_id),
                            eregi_replace("%SUBMITTED_BY",$users->user_name($project->submitted_user_id),
                            eregi_replace("%GRADE_LEVEL",$gradelevels->grade_level_description($project->grade_level_id),
                            eregi_replace("%AMOUNT_NEEDED",sprintf("%01.2f", $project->amount_needed),
                            eregi_replace("%AMOUNT_DONATED",sprintf("%01.2f", $project->amount_donated()),
                            eregi_replace("%REVIEW_NOTES",$project->review_notes,
                            eregi_replace("%MATERIALS_NEEDED",$project->materials_needed,
                            eregi_replace("%URL",$http_location,
                            $body))))))))))));

                            $subject  	 = eregi_replace("%PROJECT_ID","$project->project_id",
                            eregi_replace("%PROJECT_NAME",stripslashes($project->project_name),
                            eregi_replace("%PROJECT_DESCRIPTION",stripslashes($project->project_description),
                            eregi_replace("%SCHOOL",$schools->school_name($project->school_id),
                            eregi_replace("%CATEGORY",$projecttypes->project_type_description($project->project_type_id),
                            eregi_replace("%SUBMITTED_BY",$users->user_name($project->submitted_user_id),
                            eregi_replace("%GRADE_LEVEL",$gradelevels->grade_level_description($project->grade_level_id),
                            eregi_replace("%AMOUNT_NEEDED",sprintf("%01.2f", $project->amount_needed),
                            eregi_replace("%AMOUNT_DONATED",sprintf("%01.2f", $project->amount_donated()),
                            eregi_replace("%REVIEW_NOTES",$project->review_notes,
                            eregi_replace("%MATERIALS_NEEDED",$project->materials_needed,
                            eregi_replace("%URL",$http_location,
                            $subject))))))))))));

                            $email_to = $users->user_email($project->submitted_user_id);
                            $headers = "From: Donate2Educate <support@donate2educate.org>\r\nBCC: support@donate2educate.org, ildaco@donate2educate.org\r\n";
                            if (!empty($email_to))
                                mail($email_to, $subject, $body, $headers);

                        } elseif ($prev_project_status != 3 && $project->project_status_id == 3)	{
                            # Notify user that the project has been approved
                            $body = $config_proposal_approval_email_body;
                            $subject = $config_proposal_approval_email_subject;

                            $body 	 = eregi_replace("%PROJECT_ID","$project->project_id",
                            eregi_replace("%PROJECT_NAME",stripslashes($project->project_name),
                            eregi_replace("%PROJECT_DESCRIPTION",stripslashes($project->project_description),
                            eregi_replace("%SCHOOL",$schools->school_name($project->school_id),
                            eregi_replace("%CATEGORY",$projecttypes->project_type_description($project->project_type_id),
                            eregi_replace("%SUBMITTED_BY",$users->user_name($project->submitted_user_id),
                            eregi_replace("%GRADE_LEVEL",$gradelevels->grade_level_description($project->grade_level_id),
                            eregi_replace("%AMOUNT_NEEDED",sprintf("%01.2f", $project->amount_needed),
                            eregi_replace("%AMOUNT_DONATED",sprintf("%01.2f", $project->amount_donated()),
                            eregi_replace("%REVIEW_NOTES",$project->review_notes,
                            eregi_replace("%MATERIALS_NEEDED",$project->materials_needed,
                            eregi_replace("%URL",$http_location,
                            $body))))))))))));

                            $subject  	 = eregi_replace("%PROJECT_ID","$project->project_id",
                            eregi_replace("%PROJECT_NAME",stripslashes($project->project_name),
                            eregi_replace("%PROJECT_DESCRIPTION",stripslashes($project->project_description),
                            eregi_replace("%SCHOOL",$schools->school_name($project->school_id),
                            eregi_replace("%CATEGORY",$projecttypes->project_type_description($project->project_type_id),
                            eregi_replace("%SUBMITTED_BY",$users->user_name($project->submitted_user_id),
                            eregi_replace("%GRADE_LEVEL",$gradelevels->grade_level_description($project->grade_level_id),
                            eregi_replace("%AMOUNT_NEEDED",sprintf("%01.2f", $project->amount_needed),
                            eregi_replace("%AMOUNT_DONATED",sprintf("%01.2f", $project->amount_donated()),
                            eregi_replace("%REVIEW_NOTES",$project->review_notes,
                            eregi_replace("%MATERIALS_NEEDED",$project->materials_needed,
                            eregi_replace("%URL",$http_location,
                            $subject))))))))))));

                            $email_to = $users->user_email($project->submitted_user_id);
                            $headers = "From: Donate2Educate <support@donate2educate.org>\r\nBCC: support@donate2educate.org\r\n";
                            if (!empty($email_to))
                                mail($email_to, $subject, $body, $headers);

                        } elseif ($prev_project_status != 8 && $project->project_status_id == 8)	{
                            # Notify user that the project has been denied
                            $body = $config_proposal_deny_email_body;
                            $subject = $config_proposal_deny_email_subject;

                            $body 	 = eregi_replace("%PROJECT_ID","$project->project_id",
                            eregi_replace("%PROJECT_NAME",stripslashes($project->project_name),
                            eregi_replace("%PROJECT_DESCRIPTION",stripslashes($project->project_description),
                            eregi_replace("%SCHOOL",$schools->school_name($project->school_id),
                            eregi_replace("%CATEGORY",$projecttypes->project_type_description($project->project_type_id),
                            eregi_replace("%SUBMITTED_BY",$users->user_name($project->submitted_user_id),
                            eregi_replace("%GRADE_LEVEL",$gradelevels->grade_level_description($project->grade_level_id),
                            eregi_replace("%AMOUNT_NEEDED",sprintf("%01.2f", $project->amount_needed),
                            eregi_replace("%AMOUNT_DONATED",sprintf("%01.2f", $project->amount_donated()),
                            eregi_replace("%REVIEW_NOTES",$project->review_notes,
                            eregi_replace("%MATERIALS_NEEDED",$project->materials_needed,
                            eregi_replace("%URL",$http_location,
                            $body))))))))))));

                            $subject  	 = eregi_replace("%PROJECT_ID","$project->project_id",
                            eregi_replace("%PROJECT_NAME",stripslashes($project->project_name),
                            eregi_replace("%PROJECT_DESCRIPTION",stripslashes($project->project_description),
                            eregi_replace("%SCHOOL",$schools->school_name($project->school_id),
                            eregi_replace("%CATEGORY",$projecttypes->project_type_description($project->project_type_id),
                            eregi_replace("%GRADE_LEVEL",$gradelevels->grade_level_description($project->grade_level_id),
                            eregi_replace("%SUBMITTED_BY",$users->user_name($project->submitted_user_id),
                            eregi_replace("%AMOUNT_NEEDED",sprintf("%01.2f", $project->amount_needed),
                            eregi_replace("%AMOUNT_DONATED",sprintf("%01.2f", $project->amount_donated()),
                            eregi_replace("%REVIEW_NOTES",$project->review_notes,
                            eregi_replace("%MATERIALS_NEEDED",$project->materials_needed,
                            eregi_replace("%URL",$http_location,
                            $subject))))))))))));

                            $email_to = $users->user_email($project->submitted_user_id);
                            $headers = "From: Donate2Educate <support@donate2educate.org>\r\nBCC: support@donate2educate.org\r\n";
                            if (!empty($email_to))
                                mail($email_to, $subject, $body, $headers);
                        }
                    }
                    if ($stay_with_project)
                        echo "<script type=\"text/javascript\">\nlocation.href='proposal.php?projectid=$projectid&message=".htmlentities(urlencode($message))."'\n</script>";
                    else
                        echo "<script type=\"text/javascript\">\nlocation.href='project_list.php?message=".htmlentities(urlencode($message))."'\n</script>";
                } else
                    $message .= "<p>Errors occured saving the project!<br>$project->error_message</p>";
            } else {
                // Display errors.
                $message .= "<p>Please correct the following items and then resubmit:<br>$errors</p>";
            }
	} else {
		$projectid 			= $_GET["projectid"];
    	if (!empty($projectid))
    	{
			$project = new project();
			if ($createcopy)
			{
				if ($project->load_project($projectid)) {
					$f_project_id = $project->project_id;
					$f_project_name = $project->project_name;
					$f_description = $project->project_description;
					$f_gradelevel = $project->grade_level_id;
					$f_projecttype = $project->project_type_id;
					$f_schoolid = $project->school_id;
					$f_materials = $project->materials_needed;
					$f_handling_charge = $project->handling_charge;
					$f_shipping_charge = $project->shipping_charge;
					$f_amount_needed = $project->amount_needed - $f_handling_charge - $f_shipping_charge;
					$f_amount_received = $project->amount_donated();
					$f_amount_pledged = $project->amount_pledged();
					$f_status_id = $project->project_status_id;
					$f_review_notes = $project->review_notes;
					$f_project_id="";
					$projectid = "";
					$f_status_id = "0";
					$f_date_submitted = "";
					if ($f_handling_charge == 0)
						$f_handling_charge = $config_handling_charge;
				}
			} else {
				if ($project->load_project($projectid, $User_ID)) {
					$f_project_id = $project->project_id;
					$f_project_name = $project->project_name;
					$f_description = $project->project_description;
					$f_gradelevel = $project->grade_level_id;
					$f_projecttype = $project->project_type_id;
					$f_schoolid = $project->school_id;
					$f_materials = $project->materials_needed;
					$f_handling_charge = $project->handling_charge;
					$f_shipping_charge = $project->shipping_charge;
					$f_amount_needed = $project->amount_needed - $f_handling_charge - $f_shipping_charge;
					$f_amount_received = $project->amount_donated();
					$f_amount_pledged = $project->amount_pledged();
					$f_status_id = $project->project_status_id;
					$f_review_notes = $project->review_notes;
					$f_date_submitted = date("m/d/Y", strtotime($project->submitted_date));
					if ($f_status_id == 3 || $f_status_id == 1)
					{
						if ($f_handling_charge == 0)
							$f_handling_charge = $config_handling_charge;
					}
				} else {
					$projectid = "";
					$f_status_id = "0";
					$f_handling_charge = $config_handling_charge;
					$f_description = $config_proposal_default_text;
					$f_materials = $config_proposal_default_materials;
				}
			}
		}
		else
		{
			$projectid = "";
			$f_status_id = "0";
			$f_handling_charge = $config_handling_charge;
			$f_description = $config_proposal_default_text;
			$f_materials = $config_proposal_default_materials;
		}
	}
?>
<html>
<head>
<?
	include "inc/cssstyle.php";
	$pagename = "$config_proposal_page_name";
	$help_msg_name = "config_proposal_help";
	$help_msg = "$config_proposal_help";
	$help_width = "$config_proposal_help_width";
	$help_height = "$config_proposal_help_height";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <td <? if ($user->type_id < 50) echo "width=\"655\" ";?>align="left" valign="top">
<?
	if (!empty($config_proposal_paragraph1)) {
#		if ($user->type_id < 40) {
			include "inc/box_begin.htm";
			if ($f_status_id == 1)
				echo "$config_proposal_reviewer";
			else
				echo "$config_proposal_paragraph1";
			include "inc/box_end.htm";
#		}
	}
	$editable = ($createcopy || $user->type_id >= 40 || (($User_ID == $project->submitted_user_id || empty($project->submitted_user_id) || ($user->type_id == 25 && $user_affiliations->is_affiliated($project->school_id))) && ($f_status_id == 0 || $f_status_id == 2)));
	if (!empty($message))
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center><br>";
		include "inc/box_begin.htm";
?>
					<TABLE ALIGN="left" BORDER=0 CELLSPACING=10 CELLPADDING=0 WIDTH="100%">
					<Form Name='Proposal' Method='POST'>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>Project ID</TD>
						<TD>
							<input type='hidden' name='f_project_id' value='<?=$f_project_id;?>'>
							<input type='hidden' name='f_status_id' value='<?=$f_status_id;?>'>
							<? 	if (empty($f_project_id))
									echo "<B>New</B>";
								else {
									echo "<B>$f_project_id</B>";
									$school = new school();
									$school->load_school($f_schoolid);
								}
							?>
						</TD>
					</TR>
<?	if ($user->type_id >= 40) { ?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>&nbsp;&nbsp;New Project ID</TD>
						<TD>
							<input type='text' size='10' maxlength='150' name='f_new_projectid' value=''>
						</TD>
					</TR>
<? }	?>
<?	if ($user->type_id >= 25) { ?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>&nbsp;&nbsp;Submitted By</TD>
						<TD>
							<select name='f_submitted_user_id'>
							<OPTION VALUE="0">
<?				$teachers = new users;
				$teachers->find_users('', '', '', '', array('20','25'), '', '', '', '', '', '', '', $user_affiliations->affiliation_list, '', '',$school->district_id,'','');
				while (list($teacherid, $teacher) = each($teachers->user_list)) {
					echo ("<OPTION VALUE=\"$teacherid\"".($teacherid == $project->submitted_user_id ? " SELECTED" : "").">$teacher->last_name, $teacher->first_name ($teacherid)</OPTION>\n");
				}
	}
?>
							</SELECT>&nbsp;
<?
	if ($user->type_id >= 50 && !empty($project->submitted_user_id)) {
?>
	<A href='user_maint.php?user_id=<?=$project->submitted_user_id;?>'>User</a>
<?  }	?>
						</TD>
					</TR>
<?
	if ($user->type_id >= 30) {
?>

					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>&nbsp;&nbsp;Date Submitted</TD>
						<TD>
							<input type='text' size='12' name='f_date_submitted' value='<?=$f_date_submitted; ?>'>
						</TD>
					</TR>


<? }	?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;Project Name</TD>
						<TD>
<? if ($editable || ($user_affiliations->is_admin($project->school_id))) {	?>
						<input type="text" size="70" maxlength='150' name="f_project_name" value="<?=$f_project_name;?>">
<? }	else	{	?>
						<b><?=$f_project_name;?></b>
						<input type="hidden" name="f_project_name" value="<?=$f_project_name;?>">
<? }	?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;Category</TD>
						<TD>
<? if ($editable) {	?>
							<table border=0 cellpadding=0 cellspacing=0>
							<tr valign='bottom'><td valign='bottom'>
								<SELECT NAME="f_projecttype" SIZE=1>
									<OPTION value='0'></OPTION>
<?	reset($projecttypes->project_type_list);
	while (list($projecttypeid, $projecttype) = each($projecttypes->project_type_list)) {
		echo ("<OPTION VALUE=\"$projecttype->project_type_id\"".($projecttype->project_type_id == $f_projecttype ? " SELECTED" : "") .">$projecttype->project_type_description</OPTION>\n");
	}
?>
							</SELECT>
                                                            </td>
							</tr></table>
<? }	else	{	?>
							<b><?=$projecttypes->project_type_description($f_projecttype);?></b>
							<input type='hidden' name='f_projecttype' value='<?=$f_projecttype;?>'>
<? }	?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;Grade Level</TD>
						<TD>
<? if ($editable) {	?>
						<SELECT NAME="f_gradelevel" SIZE=1>
							<OPTION value='0'></OPTION>
<?	reset($gradelevels->grade_level_list);
	while (list($gradelevelid, $gradelevel) = each($gradelevels->grade_level_list)) {
		echo ("<OPTION VALUE=\"$gradelevel->grade_level_id\"".($gradelevel->grade_level_id == $f_gradelevel ? " SELECTED" : "") .">$gradelevel->grade_level_description</OPTION>\n");
	}
?>
						</SELECT>
<? }	else	{	?>
							<b><?=$gradelevels->grade_level_description($f_gradelevel);?></b>
							<input type='hidden' name='f_gradelevel' value='<?=$f_gradelevel;?>'>
<? }	?>
						&nbsp;&nbsp;<font color='red'>*</font>&nbsp;School&nbsp;
<? if ($editable) {	?>
						<SELECT NAME="f_schoolid" SIZE=1>
							<OPTION value='0'></OPTION>
<?
	#reset($schools->school_list);
	#while (list($schoolid, $school) = each($schools->school_list)) {
	#	echo ("<OPTION VALUE=\"$school->school_id\"".($school->school_id == $f_schoolid ? " SELECTED" : "") .">$school->school_name</OPTION>\n");
	#}
	reset($user_affiliations->affiliation_list);
	while (list($aff_id, $aff) = each($user_affiliations->affiliation_list)) {
		echo ("<OPTION VALUE=\"$aff->school_id\"".($aff->school_id == $f_schoolid ? " SELECTED" : "") .">".$schools->school_name($aff->school_id)."</OPTION>\n");
	}
?>
						</SELECT>
<? }	else	{	?>
							<b><?=$schools->school_name($f_schoolid);?></b>
							<input type='hidden' name='f_schoolid' value='<?=$f_schoolid;?>'>
<? }	?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="top">
						<TD Align='Right' valign='Top'><font color='red'>*</font>&nbsp;Description
<? if ($editable || ($user_affiliations->is_admin($project->school_id))) {	?>
	<br><small>Only use a Carriage<br>Return at the end of<br>paragraphs. The text will<br>wrap on it's own.</small></TD>
<? } ?>
						<TD>
<? if ($editable || ($user_affiliations->is_admin($project->school_id))) {	?>
							<TEXTAREA NAME="f_description" ROWS="10" COLS="65"><?=$f_description;?></TEXTAREA>
<? }	else	{	?>
							<b><?=eregi_replace("\n","<BR>",urldecode($f_description));?></b>
							<input type='hidden' name='f_description' value='<?=$f_description;?>'>
<? }	?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="top">
						<TD Align='Right' valign='Top'>Materials Needed</TD>
						<TD>
<? if ($editable || ($user_affiliations->is_admin($project->school_id))) {	?>
							<TEXTAREA NAME="f_materials" ROWS="5" COLS="65"><?=$f_materials;?></TEXTAREA>
<? }	else	{	?>
							<b><?=eregi_replace("\n","<BR>",urldecode($f_materials));?></b>
							<input type='hidden' name='f_materials' value='<?=$f_materials;?>'>
<? }	?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;Funding Needed</TD>
						<TD>
<? if ($editable) {	?>
							<input type='text' size='7' name='f_amount_needed' value='<? echo(sprintf("%01.2f", $f_amount_needed)); ?>'>
<? if (!empty($config_proposal_amount_needed_help)) {
		echo "<a href=\"javascript:display_help('config_proposal_amount_needed_help','Funding Needed','".(empty($help_width) ? $default_help_width : $help_width)."','".(empty($help_height) ? $default_help_height : $help_height)."')\">";
		echo "<img src=\"$path_root"."images/helpicon.png\" width=\"22\"></a>";
	}
   } else	{	?>
							<b><? echo(sprintf("%01.2f", $f_amount_needed));?></b>
							<input type='hidden' name='f_amount_needed' value='<?=$f_amount_needed;?>'>
<? }	?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;Handling Charge</TD>
						<TD>
							<b><? echo(sprintf("%01.2f", $f_handling_charge));?></b>
							<input type='hidden' name='f_handling_charge' value='<?=$f_handling_charge;?>'>
<? if (!empty($config_proposal_handling_charge_help)) {
		echo "<a href=\"javascript:display_help('config_proposal_handling_charge_help','Handling Charge','".(empty($help_width) ? $default_help_width : $help_width)."','".(empty($help_height) ? $default_help_height : $help_height)."')\">";
		echo "<img src=\"$path_root"."images/helpicon.png\" width=\"22\" border=0></a>";
	} ?>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'><font color='red'>*</font>&nbsp;Shipping Charge</TD>
						<TD>
<? if ($editable) {	?>
							<input type='text' size='7' name='f_shipping_charge' value='<? echo($f_shipping_charge == "" ? "" : sprintf("%01.2f", $f_shipping_charge)); ?>'>
<? if (!empty($config_proposal_shipping_charge_help)) {
		echo "<a href=\"javascript:display_help('config_proposal_shipping_charge_help','Shipping Charge','".(empty($help_width) ? $default_help_width : $help_width)."','".(empty($help_height) ? $default_help_height : $help_height)."')\">";
		echo "<img src=\"$path_root"."images/helpicon.png\" width=\"22\" border=0></a>";
	}
  } else {	?>
							<b><? echo(sprintf("%01.2f", $f_shipping_charge));?></b>
							<input type='hidden' name='f_shipping_charge' value='<?=$f_shipping_charge;?>'>
<? }	?>
						</TD>
					</TR>

<? if (!empty($f_project_id)) { ?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>Funds Donated</TD>
						<TD>
							<b><? echo(sprintf("%01.2f", $f_amount_received));?></b>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>Funds Pledged</TD>
						<TD>
							<b><? echo(sprintf("%01.2f", $f_amount_pledged));?></b>
						</TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>&nbsp;Funds Dispersed</TD>
						<TD>
							<b><?=sprintf("%01.2f", $project->funds_dispersed_amount);?></b>
							<? if(!(empty($project->funds_dispersed_date)))
								echo ("</b>&nbsp;on&nbsp;<b>".date("m/d/Y", strtotime($project->funds_dispersed_date))."</b>");
							?>
						</TD>
					</TR>
<? }	?>
<?	if ($user->type_id >= 40) { ?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>&nbsp;&nbsp;Status</TD>
						<TD>
							<select name='f_status_id'>
<?				reset($statuses->project_status_list);
				while (list($statusid, $status) = each($statuses->project_status_list)) {
                                    echo ("<OPTION VALUE=\"$statusid\"".($statusid == $f_status_id  ? " SELECTED" : "") .">$status->project_status_description</OPTION>\n");
				}
?>
                                            </TD>
					</TR>
<? } else { ?>
					<TR ALIGN="left" VALIGN="middle">
                                            <TD Align='Right'>Status</TD>
                                            <TD>
                                                <b><?=$statuses->project_status_description($f_status_id);?></b>
                                            </TD>
					</TR>
<? } ?>
<? if ($f_status_id == 4) { ?>
					<TR ALIGN="left" VALIGN="middle">
                                            <TD Align='Right'>Expiration Date</TD>
                                            <TD>
<?	if ($user->type_id >= 40) {
                                            echo "<input type='text' size='12' name='f_expiration_date' value='".(empty($project->expiration_date) ? "" : date("m/d/Y", strtotime($project->expiration_date)))."'>";
?>
<? } else { ?>
                                                <b><?=date("m/d/Y", strtotime($project->funds_dispersed_date));?></b>
<? } ?>
                                            </TD>
					</TR>
<? } ?>
<? 	if ($user_affiliations->is_admin($project->school_id) && $f_status_id == 1) { ?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>Submitted By</TD>
						<TD><B><?=$users->user_name($project->submitted_user_id);?></B></TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>Review Notes</TD>
						<TD>
							<TEXTAREA NAME="f_review_notes" ROWS="5" COLS="40" WRAP="physical"><?=$f_review_notes;?></TEXTAREA>
						</TD>
					</TR>
<?
	} elseif (!empty($f_review_notes)) {
?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>Review Notes</TD>
						<TD>
							<input type='hidden' name='f_review_notes' value='<?=$f_review_notes;?>'><b><?=$f_review_notes;?></b>
						</TD>
					</TR>
<? } else {	?>
					<input type='hidden' name='f_review_notes' value='<?=$f_review_notes;?>'>
<? } ?>

<?	if ($user->type_id >= 40) { ?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Right'>&nbsp;&nbsp;Approved By</TD>
						<TD>
							<SELECT name='f_review_user_id'>
							<OPTION VALUE=''>
<?				$teachers = new users();
				$teachers->find_users('' ,'', '', '', '20', '', '', '', '', '', '', '', $user_affiliations->affiliation_list, '', '', $school->district_id, '','');
				while (list($teacherid, $teacher) = each($teachers->user_list)) {
                                    $teacher_affiliations = new affiliations();
                                    $teacher_affiliations->load_affiliations($teacherid);
                                    while (list($affid, $teacher_aff) = each($teacher_affiliations->affiliation_list)) {
                                        if ($teacher_aff->admin_flag == "Y") {
                                            echo ("<OPTION VALUE=\"$teacherid\"".($project->review_user_id == $teacherid ? " SELECTED" : "").">$teacher->last_name, $teacher->first_name (".$schools->school_name($teacher_aff->school_id).")</OPTION>\n");
                                        }
                                    }
				}
				$teachers->find_users('', '', '', '', '40', '', '', '', '', '', '', '', $user_affiliations->affiliation_list, '', '', '', '','');
				while (list($teacherid, $teacher) = each($teachers->user_list)) {
                                    $teacher_affiliations = new affiliations();
                                    $teacher_affiliations->load_affiliations($teacherid);
                                    while (list($affid, $teacher_aff) = each($teacher_affiliations->affiliation_list)) {
                                        if ($teacher_aff->admin_flag == "Y") {
                                            echo ("<OPTION VALUE=\"$teacherid\"".($project->review_user_id == $teacherid ? " SELECTED" : "").">$teacher->last_name, $teacher->first_name (".$schools->school_name($teacher_aff->school_id).")</OPTION>\n");
                                        }
                                    }
				}
				$teachers->find_users('', '', '', '', '50', '', '', '', '', '', '', '', '', '', '', '', '','');
				while (list($teacherid, $teacher) = each($teachers->user_list)) {
                                    $teacher_affiliations = new affiliations();
                                    $teacher_affiliations->load_affiliations($teacherid);
                                    while (list($affid, $teacher_aff) = each($teacher_affiliations->affiliation_list)) {
                                        if ($teacher_aff->admin_flag == "Y") {
                                            echo ("<OPTION VALUE=\"$teacherid\"".($project->review_user_id == $teacherid ? " SELECTED" : "").">$teacher->last_name, $teacher->first_name (".$schools->school_name($teacher_aff->school_id).")</OPTION>\n");
                                        }
                                    }
				}
?>
						</TD>
					</TR>
<? }

	if ($user->type_id >= 30) {
?>

					<TR ALIGN="left" VALIGN="middle">
                                            <TD Align='Right'>&nbsp;&nbsp;Date Updated</TD>
                                            <TD>
                                                <input type='text' size='12' name='f_date_last_updated' value='<?=($project->date_last_updated ? date("m/d/Y", strtotime($project->date_last_updated)) : ""); ?>'>
                                            </TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
                                            <TD Align='Right'>&nbsp;&nbsp;Date Last Warning</TD>
                                            <TD>
                                                <input type='text' size='12' name='f_date_last_warning' value='<?=($project->date_last_warning ? date("m/d/Y", strtotime($project->date_last_warning)) : ""); ?>'>
                                            </TD>
					</TR>
					<TR ALIGN="left" VALIGN="middle">
                                            <TD Align='Right'>&nbsp;&nbsp;Date Status Changed</TD>
                                            <TD>
                                                <input type='text' size='12' name='f_date_status_changed' value='<?=($project->date_status_changed ? date("m/d/Y", strtotime($project->date_status_changed)) : ""); ?>'>
                                            </TD>
					</TR>
<? }	?>


					<TR ALIGN="left" VALIGN="middle">
                                            <TD Align='center' Colspan=2><font color='red' size=2>* Indicates a required field.</font></TD>
					</TR>
<?	$donation_fields = false;
	if ($user->type_id >= 30) {
            $donations = new donations();
            $donations->load_donations($projectid);
//		if (count($donations->donation_list) > 0) {
                    echo "\t<TR ALIGN=\"left\" VALIGN=\"middle\">";
                    echo "<TD Align='center' Colspan=2>\n";
                    include "inc/box_begin.htm";
                    echo "\t<table border='0' width='100%'>\n";
                    echo "\t\t<tr><td colspan='100%' align='center'><b><font size='+1'>Donations</font></b></td></tr>\n";
                    echo "\t\t<tr><td colspan='100%' align='center'><hl></td></tr>\n";
                    echo "\t\t<tr><td valign='bottom'><b><br>ID</d></td><td valign='bottom'><b><br>Donor</d></td><td align='center' valign='bottom'><b>Dontation<BR>Date</b></td><td align='center' valign='bottom'><b>Donation<BR>Amount</b></td><td align='center' valign='bottom'><b>Payment<BR>Type</b></td><td align='center' valign='bottom'><b>Payment<BR>Number</b></td><td align='center' valign='bottom'><b>Date<BR>Received</b></td><td align='center' valign='bottom'><b>Ref<BR>Flag</b></td><td align='center' valign='bottom'><b>Match<BR>Flag</b></td><td align='center' valign='bottom'><b>Direct<BR>Flag</b></td><td align='center' valign='bottom'><b>Non<BR>Cash</b></td></tr>\n";
                    $i = 1;
                    while (list($donationid, $donation)= each($donations->donation_list)) {
                        if ($donation->contact_flag == "A") {
                            $donorname = "Anonymous";
                            $donoraddress = "";
                        } elseif ($donation->contact_flag == "G") {
                            $donorname = "$donation->gift_first_name $donation->gift_last_name";
                            $donoraddress = "$donation->gift_street<BR>$donation->gift_city $donation->gift_state $donation->gift_zip";
                        } else {
                            $donor = new user();
                            $donor->load_user($donation->user_id);
                            if (empty($donor->company))
                                $donorname = "$donor->first_name $donor->last_name";
                            else
                                $donorname = "$donor->company";
                            $donoraddress = "$donor->street<BR>$donor->city $donor->state $donor->zip";
                        }
                        while (list($donation_project_id, $donation_project) = each($donation->donation_project_list)) {
                            if ($donation_project->project_id == $projectid) {
                                $donation_amount = $donation_project->donation_amount;
                                echo "\t\t<tr><td>";
                                echo ($donation->donation_id == $prev_id ? '&nbsp;' : "<A HREF='donation_edit.php?donationid=$donation->donation_id'>$donation->donation_id</a>");
                                echo "</td><td>".($donation->donation_id == $prev_id ? "&nbsp;" : "<a href=\"user_maint.php?user_id=".$donation->user_id."\">$donorname</A><BR>".$donoraddress)."</td>";
                                echo "<td align='center'>";
                                if ($donation_project->matching_donation_id == 0) {
                                    echo date("m/d/Y", strtotime($donation->donation_date));
                                } else {
                                    $donation_match = new donation();
                                    $donation_match->load_donation($donation_project->matching_donation_id);
                                    echo date("m/d/Y", strtotime($donation_match->donation_date));
                                }
                                echo "</td><td align='right'>".sprintf("%01.2f", $donation_amount)."</td>";
                                echo "<td align='center'>".($donation->donation_id == $prev_id ? '&nbsp;' : $payment_types->payment_type_description($donation->payment_type_id))."</td>";
                                echo "<td align='center'>";
                                if ($donation->donation_id != $prev_id) {
                                    if (empty($donation->payment_no)) {
                                        echo "<input type='text' size='5' maxlength='20' name='payment_no[$i]'>";
                                        $donation_fields = true;
                                    } else {
                                        echo "<input type='hidden' name='payment_no[$i]' value='$donation->payment_no'>";
                                        echo $donation->payment_no;
                                    }
                                } else {
                                    echo ('&nbsp;');
                                }
                                echo "</td>";
                                echo "<td align='center'>";
                                if ($donation->donation_id != $prev_id) {
                                    echo "<input type='hidden' name='donationids[$i]' value='$donation->donation_id'>";
                                    if (empty($donation->payment_received_date)) {
                                        echo "<input type='text' size='10' name='payment_received_date[$i]'>";
                                        $donation_fields = true;
                                    } else {
                                        echo "<input type='hidden' name='payment_received_date[$i]' value='".date("m/d/Y", strtotime($donation->payment_received_date))."'>";
                                        echo date("m/d/Y", strtotime($donation->payment_received_date));
                                    }
                                } else {
                                    echo ('&nbsp;');
                                }
                                echo "</td>";
                                echo "<td align='center'>".($donation->donation_id == $prev_id ? '&nbsp;' : $donation->refund_flag)."</td>";
                                echo "<td align='center'>".(($donation_project->matching_donation_id == 0 && $donation->matching_donation != "Y") ? "N" : "Y")."</td>";
                                echo "<td align='center'>$donation->direct_donation</td>";
                                echo "<td align='center'>$donation->noncash_donation</td>";
                                echo "</tr>\n";
                                $prev_id = $donation->donation_id;
                            }
                        }
                        $i += 1;
                    }
                    echo "\t\t<tr><td colspan='100%' align='left'><a href=\"donation_edit.php?dontion_id=0\">Add</a></td></tr>\n";
                    if ($donation_fields)
                        echo "<tr><td align='center' colspan='100%'><Input Type='Submit' Name='Submit' class='nicebtns' Value='Update Donation Changes'></td></tr>";
                    echo "\t</table>\n";
                    echo "\t</TD></TR>\n";
                    include "inc/box_end.htm";
//		}
            $disbursements = new disbursements();
            $disbursements->load_disbursements($projectid);
            if (count($disbursements->disbursement_list) > 0 || $project->project_status_id == 3 || $project->project_status_id == 4 || $project->project_status_id == 5) {
                # List of Disbursements
                echo "\t<TR ALIGN=\"left\" VALIGN=\"middle\">";
                echo "<TD Align='center' Colspan=2>\n";
                include "inc/box_begin.htm";
                echo "\t<table border='0' width='100%'>\n";
                echo "\t\t<tr><td colspan='4' align='center'><b><font size='+1'>Disbursements</font></b></td></tr>\n";
                echo "\t\t<tr><td colspan='4' align='center'><hl></td></tr>\n";
                echo "\t\t<tr><td valign='bottom' width='90%'><b><br>Recipient</d></td><td align='center' valign='bottom'><b>Disbursement<BR>Date</b></td><td align='center' valign='bottom'><br><b>Tran No</b></td><td align='center' valign='bottom' width='10%'><b>Amount<BR>Disbursed</b></td><td align='center' valign='bottom' width='10%'><b>Delete</b></td></tr>\n";
                $i = 1;
                while (list($disbursementid, $disbursement)= each($disbursements->disbursement_list)) {
                    echo "\t\t<tr><td width='90%'><input type='text' name='disb_recipient_name[$i]' value='$disbursement->recipient_name'></td><td align='center'><input type='text' name='disb_date[$i]' value='".date("m/d/Y", strtotime($disbursement->disbursement_date))."'></td><td><input type='text' name='disb_tran_no[$i]' value='$disbursement->tran_no'></td><td align='right' width='10%'><input type='text' name='disb_amount[$i]' value='".sprintf("%01.2f", $disbursement->disbursement_amount)."'><input type='hidden' name='disb_id[$i]' value='$disbursement->disbursement_id'></td><td align='center'><input type='checkbox' name='disb_delete[$i]' value='Y'></td>";
                    echo "</tr>\n";
                    $i += 1;
                }
                echo "\t\t<tr><td width='90%'><input type='text' name='disb_recipient_name[$i]' value=''></td><td align='center'><input type='text' name='disb_date[$i]' value=''></td><td><input type='text' name='disb_tran_no[$i]' value=''></td><td align='right' width='10%'><input type='text' name='disb_amount[$i]' value=''><input type='hidden' name='disb_id[$i]' value='0'></td><td>&nbsp;</td>";
                echo "<tr><td align='center' colspan='6'><Input Type='Submit' Name='Submit' class='nicebtns' Value='Update Disbursement Changes'></td></tr>";
                echo "\t</table>\n";
                echo "\t</TD></TR>\n";
                include "inc/box_end.htm";
            }
            # List of Comments
            echo "\t<TR ALIGN=\"left\" VALIGN=\"middle\">";
            echo "<TD Align='center' Colspan=2>\n";
            include "inc/box_begin.htm";
            echo "\t<table border='0' width='100%'>\n";
            echo "\t\t<tr><td colspan='4' align='center'><b><font size='+1'>Internal Comments</font></b></td></tr>\n";
            echo "\t\t<tr><td colspan='4' align='center'><hl></td></tr>\n";
            echo "\t\t<tr><td valign='bottom' width='10%'><b>Entered<br>By</d></td><td align='center' valign='bottom'><b>Entry<BR>Date</b></td><td align='center' valign='bottom' width='90%'><BR><b>Comment</b></td><td align='center' valign='bottom' width='10%'><BR><b>Delete</b></td></tr>\n";
            $i = 1;
            reset($project->comment_list);
            while (list($commentid, $comment)= each($project->comment_list)) {
                echo "\t\t<tr><td width='10%'>".$comment->author_name();
                echo "</td><td align='center'>".date("m/d/Y", strtotime($comment->date_entered))."</td><td align='left' width='90%'>";
                echo "<textarea name='comment_text[$i]' rows='3' cols='60'>".$comment->comment."</textarea>";
                echo "<input type='hidden' name='comment_id[$i]' value='$comment->project_comment_id'></td><td align='center'><input type='checkbox' name='comment_delete[$i]' value='Y'></td>";
                echo "</tr>\n";
                $i += 1;
            }
            echo "\t\t<tr><td width='10%'><input type='hidden' name='comment_id[$i] value='0'><input type='hidden' name='comment_entered_by[$i]' value='$User_ID'></td><td align='center'>New</td><td align='left'>";
            echo "<textarea name='comment_text[$i]' rows='3' cols='60'></textarea>";
            echo "<input type='hidden' name='comment_id[$i]' value='0'></td><td>&nbsp;</td>";
            echo "<tr><td align='center' colspan='6'><Input Type='Submit' Name='Submit' class='nicebtns' Value='Update Comment Changes'></td></tr>";
            echo "\t</table>\n";
            echo "\t</TD></TR>\n";
            include "inc/box_end.htm";
	}
?>
					<TR ALIGN="left" VALIGN="middle">
						<TD Align='Center' Colspan='2'>
<?
    if ($createcopy == "1" || $User_ID == $project->submitted_user_id || empty($project->submitted_user_id) || ($user->type_id == 25 && $user_affiliations->is_affiliated($project->school_id))) {
        if ($f_status_id == "0" || $createcopy == "1" || $f_status_id == "2"  || empty($project->submitted_user_id))
            echo "<Input Type='Submit' Name='Submit' class='nicebtns' Value='Save for Editing'>&nbsp;";
        if ($f_status_id == "0" || $createcopy == "1" || $f_status_id == "2" || empty($project->submitted_user_id))
            echo "<Input Type='Submit' Name='Submit' class='nicebtns' Value='Submit for Review'>&nbsp;";
        if ($f_status_id == "1" and ($project->submitted_user_id == $User_ID || ($user->type_id == 25 && $user_affiliations->is_affiliated($project->school_id))))
            echo "<Input Type='Submit' Name='Submit' class='nicebtns' Value='Unsubmit for Review'>&nbsp;";
        if (($f_status_id == "0" || $f_status_id == "1") and ($project->submitted_user_id == $User_ID || ($user->type_id == 25 && $user_affiliations->is_affiliated($project->school_id))))
            echo "<Input Type='Submit' Name='Submit' class='nicebtns' Value='Cancel Project'>&nbsp;";
        if ($f_status_id == "3" and ($project->submitted_user_id == $User_ID || ($user->type_id == 25 && $user_affiliations->is_affiliated($project->school_id)))) {
            echo "<Input Type='Submit' Name='Submit' class='nicebtns' Value='Modify Project'>&nbsp;";
            if ((!$project->date_last_updated && $project->review_date < strtotime("last month")) || ($project->date_last_updated < strtotime("last month")))
                echo "<Input Type='Submit' Name='Submit' class='nicebtns' Value='Confirm Project Active'>&nbsp;";
        }
    }
    if (($f_status_id == "1") && ($user_affiliations->is_admin($project->school_id)))
    {
        echo "<Input Type='Submit' Name='Submit' class='nicebtns' Value='Save Changes'>&nbsp;";
        echo "<BR><Input Type='Submit' Name='Submit' class='nicebtns' Value='Approve'>&nbsp;";
        echo "<Input Type='Submit' Name='Submit' class='nicebtns' Value='Return for Modification'>&nbsp;";
        echo "<Input Type='Submit' Name='Submit' class='nicebtns' Value='Deny'>";
    }
    if (($user->type_id >= 30) && ($user_affiliations->is_affiliated($project->school_id)) && ($f_status_id == 3))
    {
        echo "<BR>Expiration Date: <input type='text' size='12' name='f_expiration_date' value='".(empty($project->expiration_date) ? "" : date("m/d/Y", strtotime($project->expiration_date)))."'>&nbsp;<Input Type='Submit' Name='Submit' class='nicebtns' Value='Mark Project Funded'><BR><Input Type='Submit' Name='Submit' class='nicebtns' Value='Mark Project Unfunded'>";
    }
    if (($user->type_id >= 30) && ($user_affiliations->is_affiliated($project->school_id)) && ($f_status_id >=4 && $f_status_id <= 5))
    {
        echo "<br>";
        echo "Date Receipts Received:<input Type='Text' Name='date_receipts_received' Value='".(empty($project->date_receipts_received) ? "" : date("m-d-Y", strtotime($project->date_receipts_received)))."' Size='9'>&nbsp;<Input Type='Submit' Name='Submit' class='nicebtns' Value='Update Receipts'><br>";
        echo "Date Thank Yous Sent:<input Type='Text' Name='date_thankyous_sent' Value='".(empty($project->date_thankyous_sent) ? "" : date("m-d-Y", strtotime($project->date_thankyous_sent)))."' Size='9'>&nbsp;<Input Type='Submit' Name='Submit' class='nicebtns' Value='Update Thankyous'><BR>";
        if ($project->project_status_id == 5)
            echo "<Input Type='Submit' Name='Submit' class='nicebtns' Value='Return Project to Funded Status'>";
    }
    if ($user->type_id >= 40) {
        echo "<BR><Input Type='Submit' Name='Submit' class='nicebtns' Value='Save All Fields'>&nbsp;";
        echo "<Input Type='Submit' Name='Submit' class='nicebtns' Value='Create Copy'>&nbsp;";
    }
    if ($projectid) {
        echo "<BR><BR><B><a href=\"donation.php?projectid=$projectid\" target=\"Preview\">Preview Listing</a></B><BR><font size='-1'>Changes must first be saved to be included in preview.</font>";
    } else {
        echo "<BR><BR><B>To preview your project listing, first save it for editing,<BR>then click the link at this location.</B>";
    }

?>
                        </TD>
                    </TR>
                    </Form>
                    </TABLE>
<?
	include "inc/box_end.htm";
	if ($config_proposal_banners == "Y") include "inc/banner_ads.php";
?>
        </td>
<? require "inc/body_end.inc"; ?>
</html>
