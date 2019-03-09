<?	require "inc/db_inc.php";
	require_once "inc/class_user.php";
	require_once "inc/class_users.php";
	require_once "inc/class_school.php";
	require_once "inc/class_schools.php";
	require_once "inc/class_grade_level.php";
	require_once "inc/class_grade_levels.php";
	require_once "inc/class_project_type.php";
	require_once "inc/class_project_types.php";
	require_once "inc/class_affiliation.php";
	require_once "inc/class_affiliations.php";
	require_once "inc/class_project_status.php";
	require_once "inc/class_project_statuses.php";
	require_once "inc/class_project.php";

	$user = new user();
	$user->load_user($User_ID);

	require "inc/validate_admin.php";

	$schools = new schools();
	$schools->load_schools();

	$gradelevels = new grade_levels();
	$gradelevels->load_grade_levels();

	$projecttypes = new project_types();
	$projecttypes->load_project_types();

	$user_affiliations = new affiliations;
	$user_affiliations->load_affiliations($User_ID);

	$statuses = new project_statuses;
	$statuses->load_project_statuses();

	$users = new users;
	$project = new project;

	if (!empty($f_project_id)) {
		$project->load_project($f_project_id, $User_ID);

		if ($project->project_status_id == 1) {
			# Notify Reviewer that the project has been submitted for approval
			$body = $config_proposal_review_email_body;
			$subject = $config_proposal_review_email_subject;

			$body 	 = preg_replace("%PROJECT_ID","$project->project_id",
			preg_replace("%PROJECT_NAME","$project->project_name",
			preg_replace("%PROJECT_DESCRIPTION","$project->project_description",
			preg_replace("%SCHOOL","$schools->school_name($project->school_id)",
			preg_replace("%AMOUNT_NEEDED","$project->amount_needed",
			preg_replace("%AMOUNT_DONATED","$project->amount_donated()",
			preg_replace("%REVIEW_NOTES","$project->review_notes",
			preg_replace("%MATERIALS_NEEDED","$project->materials_needed",
			preg_replace("%URL","$http_location",
			$body)))))))));

			$subject  	 = preg_replace("%PROJECT_ID","$project->project_id",
			preg_replace("%PROJECT_NAME","$project->project_name",
			preg_replace("%PROJECT_DESCRIPTION","$project->project_description",
			preg_replace("%SCHOOL","$schools->school_name($project->school_id)",
			preg_replace("%AMOUNT_NEEDED","$project->amount_needed",
			preg_replace("%AMOUNT_DONATED","$project->amount_donated()",
			preg_replace("%REVIEW_NOTES","$project->review_notes",
			preg_replace("%MATERIALS_NEEDED","$project->materials_needed",
			preg_replace("%URL","$http_location",
			$subject)))))))));

			$email_to = $project->reviewer();

			$headers = "From: Donate2Educate <support@donate2educate.org>\r\nBCC: support@donate2educate.org\r\n";
			if (!empty($email_to))
				mail($email_to, $subject, $body, $headers);
			$message = "Email sent to $email_to for submitted project $project->project_id";

		} elseif ($project->project_status_id == 2) {
			# Notify user that the project has been returned
			$body = $config_proposal_return_email_body;
			$subject = $config_proposal_return_email_subject;

			$body 	 = preg_replace("%PROJECT_ID","$project->project_id",
			preg_replace("%PROJECT_NAME","$project->project_name",
			preg_replace("%PROJECT_DESCRIPTION","$project->project_description",
			preg_replace("%SCHOOL","$schools->school_name($project->school_id)",
			preg_replace("%AMOUNT_NEEDED","$project->amount_needed",
			preg_replace("%AMOUNT_DONATED","$project->amount_donated()",
			preg_replace("%REVIEW_NOTES","$project->review_notes",
			preg_replace("%MATERIALS_NEEDED","$project->materials_needed",
			preg_replace("%URL","$http_location",
			$body)))))))));

			$subject  	 = preg_replace("%PROJECT_ID","$project->project_id",
			preg_replace("%PROJECT_NAME","$project->project_name",
			preg_replace("%PROJECT_DESCRIPTION","$project->project_description",
			preg_replace("%SCHOOL","$schools->school_name($project->school_id)",
			preg_replace("%AMOUNT_NEEDED","$project->amount_needed",
			preg_replace("%AMOUNT_DONATED","$project->amount_donated()",
			preg_replace("%REVIEW_NOTES","$project->review_notes",
			preg_replace("%MATERIALS_NEEDED","$project->materials_needed",
			preg_replace("%URL","$http_location",
			$subject)))))))));

			$email_to = $users->user_email($project->submitted_user_id);

			$headers = "From: Donate2Educate <support@donate2educate.org>\r\nBCC: support@donate2educate.org\r\n";
			mail($email_to, $subject, $body, $headers);
			$message = "Email sent to $email_to for returned project $project->project_id";

		} elseif ($project->project_status_id == 3)	{
			# Notify user that the project has been approved
			$body = $config_proposal_approval_email_body;
			$subject = $config_proposal_approval_email_subject;

			$body 	 = preg_replace("%PROJECT_ID","$project->project_id",
			preg_replace("%PROJECT_NAME","$project->project_name",
			preg_replace("%PROJECT_DESCRIPTION","$project->project_description",
			preg_replace("%SCHOOL","$schools->school_name($project->school_id)",
			preg_replace("%AMOUNT_NEEDED","$project->amount_needed",
			preg_replace("%AMOUNT_DONATED","$project->amount_donated()",
			preg_replace("%REVIEW_NOTES","$project->review_notes",
			preg_replace("%MATERIALS_NEEDED","$project->materials_needed",
			preg_replace("%URL","$http_location",
			$body)))))))));

			$subject  	 = preg_replace("%PROJECT_ID","$project->project_id",
			preg_replace("%PROJECT_NAME","$project->project_name",
			preg_replace("%PROJECT_DESCRIPTION","$project->project_description",
			preg_replace("%SCHOOL","$schools->school_name($project->school_id)",
			preg_replace("%AMOUNT_NEEDED","$project->amount_needed",
			preg_replace("%AMOUNT_DONATED","$project->amount_donated()",
			preg_replace("%REVIEW_NOTES","$project->review_notes",
			preg_replace("%MATERIALS_NEEDED","$project->materials_needed",
			preg_replace("%URL","$http_location",
			$subject)))))))));

			$email_to = $users->user_email($project->submitted_user_id);

			$headers = "From: Donate2Educate <support@donate2educate.org>\r\nBCC: support@donate2educate.org\r\n";
			mail($email_to, $subject, $body, $headers);
			$message = "Email sent to $email_to for approved project $project->project_id";

		} elseif ($project->project_status_id == 8)	{
			# Notify user that the project has been denied
			$body = $config_proposal_deny_email_body;
			$subject = $config_proposal_deny_email_subject;

			$body 	 = preg_replace("%PROJECT_ID","$project->project_id",
			preg_replace("%PROJECT_NAME","$project->project_name",
			preg_replace("%PROJECT_DESCRIPTION","$project->project_description",
			preg_replace("%SCHOOL","$schools->school_name($project->school_id)",
			preg_replace("%AMOUNT_NEEDED","$project->amount_needed",
			preg_replace("%AMOUNT_DONATED","$project->amount_donated()",
			preg_replace("%REVIEW_NOTES","$project->review_notes",
			preg_replace("%MATERIALS_NEEDED","$project->materials_needed",
			preg_replace("%URL","$http_location",
			$body)))))))));

			$subject  	 = preg_replace("%PROJECT_ID","$project->project_id",
			preg_replace("%PROJECT_NAME","$project->project_name",
			preg_replace("%PROJECT_DESCRIPTION","$project->project_description",
			preg_replace("%SCHOOL","$schools->school_name($project->school_id)",
			preg_replace("%AMOUNT_NEEDED","$project->amount_needed",
			preg_replace("%AMOUNT_DONATED","$project->amount_donated()",
			preg_replace("%REVIEW_NOTES","$project->review_notes",
			preg_replace("%MATERIALS_NEEDED","$project->materials_needed",
			preg_replace("%URL","$http_location",
			$subject)))))))));

			$email_to = $users->user_email($project->submitted_user_id);

			$headers = "From: Donate2Educate <support@donate2educate.org>\r\nBCC: support@donate2educate.org\r\n";
			mail($email_to, $subject, $body, $headers);
			$message = "Email sent to $email_to for denied project $project->project_id";
		}
	}
	if ($f_new_registration == "Y") {
		$users->unverified_users();

		while (list($user_id, $user_rcd) = each($users->user_list)) {
			if (!empty($user_rcd->email)) {
				$body = 	preg_replace("%LOGIN","$user_rcd->login",
				preg_replace("%FIRST_NAME","$user_rcd->first_name",
				preg_replace("%LAST_NAME","$user_rcd->last_name",
				preg_replace("%PASSWORD","$user_rcd->password",
				$config_registration_message))));
				$headers = "From: Donate2Educate <support@donate2educate.org>\r\nBCC: Donate2Educate <support@donate2educate.org>\r\n";
				if (!mail($user_rcd->email, $config_registration_subject, $body, $headers)) {
					$message .= "Your registration email failed to send.";
				} else {
					echo "Email sent to $user_rcd->first_name $user_rcd->last_name ($user_rcd->email) Login: $user_rcd->login.<BR>";
				}
			}
		}
	} elseif (!empty($f_login)) {
		$user_rcd = new user();
		$userids = $user_rcd->lookup_login($f_login);
		while (list($idx, $user_id) = each($userids)) {
			$user_rcd->load_user($user_id);
			if (!empty($user_rcd->email)) {
				$body = 	preg_replace("%LOGIN","$user_rcd->login",
				preg_replace("%FIRST_NAME","$user_rcd->first_name",
				preg_replace("%LAST_NAME","$user_rcd->last_name",
				preg_replace("%PASSWORD","$user_rcd->password",
				$config_registration_message))));
				$headers = "From: Donate2Educate <support@donate2educate.org>\r\nBCC: Donate2Educate <support@donate2educate.org>\r\n";
				if (!mail($user_rcd->email, $config_registration_subject, $body, $headers)) {
					$message .= "Your registration email failed to send.";
				} else {
					echo "Email sent to $user_rcd->first_name $user_rcd->last_name ($user_rcd->email) Login: $user_rcd->login.<BR>";
				}
			}
		}
	}
	echo "<html><body><P><B>$message</p><p>This script is used to resend emails for projects.</P>";
?>
		<FORM Name="Resend Email" Method="POST">
		Project ID:&nbsp;<input type='text' name='f_project_id'><br>
		All Un-Verified Users:&nbsp;<input type='checkbox' name='f_new_registration' value='Y'><br>
		Single Un-Verified User.&nbsp;&nbsp;Login:&nbsp;<input type='textbox' name='f_login' value=''><br>
		<input type='submit' name='Submit' Value='Submit'>
		</body>
	</HTML>