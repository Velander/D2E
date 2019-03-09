<?
    require_once "inc/db_inc.php";
    require_once "inc/class_user.php";
    require_once "inc/class_users.php";
    require_once "inc/class_project.php";
    require_once "inc/class_grade_level.php";
    require_once "inc/class_grade_levels.php";
    require_once "inc/class_school.php";
    require_once "inc/class_schools.php";
    require_once "inc/class_district.php";
    require_once "inc/class_districts.php";
    require_once "inc/class_project_type.php";
    require_once "inc/class_project_types.php";
    require_once "inc/class_email_notice.php";
    require_once "inc/class_matching.php";
    require_once "inc/func.php";
    require_once "PHPMailer/class.phpmailer.php";
$debug = false;
if ($debug) {
    echo "loading user<br>\n";
    flush();
    error_reporting(E_ALL);
}
    $user = new user();
    $user->load_user($User_ID);

    $users = new users;

if ($debug) {
    echo "loading schools<br>\n";
    flush();
}
    $schools = new schools();
    $schools->load_schools();

    $districts = new districts();
    $districts->load_districts();

    $district = new district();

    $grade_levels = new grade_levels;
    $grade_levels->load_grade_levels();

    $projecttypes = new project_types;
    $projecttypes->load_project_search_types();

    $matching = new matching();

if ($debug) {
    echo "Submit = $Submit<BR>Review=$review<BR>";
    flush();
}
    if ($_SERVER['REQUEST_METHOD'] == "POST")
    {

if ($debug) {
    echo "Posting<br>\n";
    flush();
}
        if ($Submit == "Send Email")
        {
if ($debug) {
echo "Checking Fields<br>\n";
flush();
}
            if (empty($send_to_address_book))
            {
if ($debug) {
echo "Not send to address book<br>\n";
flush();
}
                if (empty($f_username))
                    $message .= "User name is required.<BR>";
                if (empty($f_useremail))
                    $message .= "User email is required.<BR>";
                elseif (!strstr($f_useremail,"@") || !strstr($f_useremail,"."))
                    $message .= "Invalid User email address.<BR>";
                if (empty($f_friendname))
                    $message .= "Friend's name is required.<BR>";
                if (empty($f_friendemail))
                    $message .= "Friend's email is required.<BR>";
                elseif (!strstr($f_friendemail,"@") || !strstr($f_friendemail,"."))
                    $message .= "Invalid Friend's email address.<BR>";
            }
            $emails = array();
            if (!empty($send_to_address_book))
            {
if ($debug) {
echo "Send to address book<br>\n";
flush();
}
                reset($user->friend_list);
                $frienditem = new user_friend();
                while (list($friendid, $frienditem) = each($user->friend_list))
                {
                    if ($frienditem->include == "Y")
                    {
                        $emails[] = "$frienditem->name <".$frienditem->email.">";
if ($debug) {
echo htmlspecialchars("Add $frienditem->name <".$frienditem->email.">"),"<br>\n";
flush();
}
                    }
                }
            } else {
                // Put the single email address in an array.
                $emails[] = "$f_friendname <$f_friendemail>";
if ($debug) {
echo htmlspecialchars("Add $f_friendname <".$f_friendemail.">"),"<br>\n";
flush();
}
            }

if ($debug) {
echo "Message=$message<br>\n";
flush();
}
            if (!empty($message))
            {
                echo "<script type=\"text/javascript\">\nlocation.href='$http_location"."donation.php?projectid=".$projectid."&search_arg=".htmlentities(urlencode($search_arg))."&message=".htmlentities(urlencode($message))."&f_friendname=".htmlentities(urlencode($f_friendname))."&f_friendemail=".htmlentities(urlencode($f_friendemail))."&f_message=".htmlentities(urlencode(stripslashes($f_message)))."'\n</script>\n";
            } else {
if ($debug) {
echo "Reading Project<br>\n";
flush();
}
                $project = new project();
                if ($project->load_project($projectid))
                {
if ($debug) {
echo "Building Email Bodys<br>\n";
flush();
}
                    $htmlbody = "<html><body>".$config_request_notice_html."</body></html>";
                    $textbody = $config_request_notice_text;
                    $subject = $config_request_notice_subject;

if ($debug) {
echo "Building HTML Email Body<br>\n";
flush();
}
            If ($matching->matching_amount($project->project_id) > ($project->amount_needed - $project->amount_donated())/2)
                $matching_amount = ($project->amount_needed - $project->amount_donated())/2;
            else
                $matching_amount = $matching->matching_amount($project->project_id);
            $htmlbody = preg_replace("%PROJECT_ID","$project->project_id",
            preg_replace("%PROJECT_NAME",stripslashes($project->project_name),
            preg_replace("%PROJECT_DESCRIPTION",stripslashes($project->project_description),
            preg_replace("%SCHOOL",$schools->school_name($project->school_id),
            preg_replace("%CATEGORY",$projecttypes->project_type_description($project->project_type_id),
            preg_replace("%SUBMITTED_BY",$users->user_name($project->submitted_user_id),
            preg_replace("%GRADE_LEVEL",$grade_levels->grade_level_description($project->grade_level_id),
            preg_replace("%MATERIALS_NEEDED",$project->materials_needed,
            preg_replace("%AMOUNT_REQUESTED",sprintf("%01.2f",$project->amount_needed),
            preg_replace("%AMOUNT_DONATED",sprintf("%01.2f", $project->amount_donated()),
            preg_replace("%AMOUNT_NEEDED",sprintf("%01.2f", $project->amount_needed - $project->amount_donated()),
            preg_replace("%USER_MESSAGE",stripslashes("$f_message<BR><BR>$f_username"),
            preg_replace("%URL",$http_location,
            $htmlbody)))))))))))));
            if ($matching_amount > 0)
            {
                $htmlbody = preg_replace("%MATCHINGFUNDHEADER","Matching Funds<BR>Available",
                preg_replace("%MATCHFUNDSAMOUNT",sprintf("%01.2f", $matching_amount),
                $htmlbody));
            }
            else
            {
                $htmlbody = preg_replace("%MATCHINGFUNDHEADER","",
                preg_replace("%MATCHFUNDSAMOUNT","",
                $htmlbody));
            }
if ($debug) {
echo "Building Text Email Body<br>\n";
flush();
}

            $description = preg_replace("<BR>","/n",preg_replace("</P>","/n/n",stripslashes($project->project_description)));
            $description = strip_tags($description);
            $textbody = preg_replace("%PROJECT_ID","$project->project_id",
            preg_replace("%PROJECT_NAME",stripslashes($project->project_name),
            preg_replace("%PROJECT_DESCRIPTION",$description,
            preg_replace("%SCHOOL",$schools->school_name($project->school_id),
            preg_replace("%CATEGORY",$projecttypes->project_type_description($project->project_type_id),
            preg_replace("%SUBMITTED_BY",$users->user_name($project->submitted_user_id),
            preg_replace("%GRADE_LEVEL",$grade_levels->grade_level_description($project->grade_level_id),
            preg_replace("%MATERIALS_NEEDED",$project->materials_needed,
            preg_replace("%AMOUNT_REQUESTED",sprintf("%01.2f",$project->amount_needed),
            preg_replace("%AMOUNT_DONATED",sprintf("%01.2f", $project->amount_donated()),
            preg_replace("%AMOUNT_NEEDED",sprintf("%01.2f", $project->amount_needed - $project->amount_donated()),
            preg_replace("%USER_MESSAGE",stripslashes("$f_message\n\n$f_username"),
            preg_replace("%URL",$http_location,
            $textbody)))))))))))));
            if ($matching_amount > 0)
            {
                $textbody = preg_replace("%MATCHINGFUNDHEADER","Matching Funds Available: ",
                preg_replace("%MATCHFUNDSAMOUNT",sprintf("%01.2f", $matching_amount),
                $textbody));
            }
            else
            {
                $textbody = preg_replace("%MATCHINGFUNDHEADER","",
                preg_replace("%MATCHFUNDSAMOUNT","",
                $textbody));
            }

if ($debug) {
echo "Building Email Subject<br>\n";
flush();
}
                    $subject = preg_replace("%PROJECT_ID","$project->project_id",
                    preg_replace("%PROJECT_NAME",stripslashes($project->project_name),
                    preg_replace("%PROJECT_DESCRIPTION",stripslashes($project->project_description),
                    preg_replace("%SCHOOL",$schools->school_name($project->school_id),
                    preg_replace("%CATEGORY",$projecttypes->project_type_description($project->project_type_id),
                    preg_replace("%SUBMITTED_BY",$users->user_name($project->submitted_user_id),
                    preg_replace("%GRADE_LEVEL",$grade_levels->grade_level_description($project->grade_level_id),
                    preg_replace("%AMOUNT_NEEDED",sprintf("%01.2f",$project->amount_needed - $project->amount_donated()),
                    preg_replace("%AMOUNT_REQUESTED",sprintf("%01.2f",$project->amount_needed),
                    preg_replace("%AMOUNT_DONATED",sprintf("%01.2f", $project->amount_donated()),
                    preg_replace("%REVIEW_NOTES",$project->review_notes,
                    preg_replace("%MATERIALS_NEEDED",$project->materials_needed,
                    preg_replace("%URL",$http_location,
                    $subject)))))))))))));

                    $phpMail = new phpmailer();
                    $phpMail->IsHTML(True);
                    $phpMail->From      = "support@donate2educate.org";
                    $phpMail->FromName  = "$f_username c/o Donate2Educate";
                    $phpMail->Encoding  = "base64";
                    $phpMail->Mailer    = "mail";
                    $phpMail->Subject   = $subject;
                    $phpMail->Sender    = "emailnotice@donate2educate.org";
                    $phpMail->AddReplyTo($f_useremail, $f_username);
                    $phpMail->AddBCC("support@donate2educate.org","Donate2Educate Support");

                    $random_hash = md5(date("r", time()));

                    // To send HTML mail, the Content-type header must be set
                    $headers = "To: ##TOEMAIL##\n";
                    $headers .= "From: $f_username c/o Donate2Educate <support@donate2educate.org>\r\n";
                    $headers .= "Reply-To: $f_username <$f_useremail>\r\n";
                    $headers .= "BCC: support@donate2educate.org\r\n";
                    $textheaders = $headers;

                    $no_emails = count($emails);
                    $success_count = 0;
                    $failure_count = 0;
                    for($idx = 0; $idx<$no_emails; $idx++)
                    {
                        $email = $emails[$idx];
if ($debug) {
echo "Checking Email ".htmlspecialchars($email)."<br>\n";
flush();
}
                        $email_notice = new email_notice($email);
                        $email_notice->load_email_notice($email);
                        if($email_notice->suppress_date == "")
                        {

                            $phpMail->ClearAddresses();
if ($debug) {
echo "Sending Email to ".htmlspecialchars($email_notice->email_complete)."<br>\n";
flush();
}
                            if($phpMail->ValidateAddress($email_notice->email))
                            {
                                $phpMail->AddAddress($email_notice->email, $email_notice->func_name_only());
                                $phpMail->AltBody = preg_replace("%UNSUBSCRIBE_KEY",$email_notice->internal_key,$textbody);
                                $phpMail->Body = preg_replace("%UNSUBSCRIBE_KEY",$email_notice->internal_key,$htmlbody);

                                $email_notice->last_date_sent   = date("Y-m-d");
                                $email_notice->ip_address       = $_SERVER["REMOTE_ADDR"];
                                $email_notice->save_email_notice();
if ($debug) {
echo "Saving Email notice ".htmlspecialchars($email_notice->email_complete)."<br>\n";
flush();
}
                                if ($phpMail->Send())
                                    $success_count++;
                                else
                                    $failure_count++;
                            }
                            else
                            {
                                $failure_count++;
                            }
                        }
                    }
                    $message = (($success_count > 0) ? "$success_count email sent. " : "").(($failure_count > 0) ? "$failure_count email failed. " : "");
                    if(!$debug) echo "<script type=\"text/javascript\">\nlocation.href='$http_location"."donation.php?projectid=".$projectid."&search_arg=".htmlentities(urlencode($search_arg))."&message=".htmlentities(urlencode($message))."&f_friendname=".htmlentities(urlencode($f_friendname))."&f_friendemail=".htmlentities(urlencode($f_friendemail))."&f_message=".htmlentities(urlencode(stripslashes($f_message)))."'\n</script>\n";
                } else {
                    $message = "Project failed to load.";
                    if(!$debug) echo "<script type=\"text/javascript\">\nlocation.href='$http_location"."donation.php?projectid=".$projectid."&search_arg=".htmlentities(urlencode($search_arg))."&message=".htmlentities(urlencode($message))."&f_friendname=".htmlentities(urlencode($f_friendname))."&f_friendemail=".htmlentities(urlencode($f_friendemail))."&f_message=".htmlentities(urlencode(stripslashes($f_message)))."'\n</script>\n";
                }
            }
        } else {
            if(!$debug) echo "<script type=\"text/javascript\">\nlocation.href='$http_location"."donation_search.php?message=".htmlentities("Not Send Email")."'\n</script>\n";
        }
    } else {
        if(!$debug) echo "<script type=\"text/javascript\">\nlocation.href='$http_location"."donation_search.php?message=".htmlentities("Not Post")."'\n</script>\n";
    }

?>
