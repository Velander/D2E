<?	require_once "inc/db_inc.php";
	require_once "inc/class_user.php";
	if ($id) {
		$results = $db_link->query("Select * from user_email_validation where unique_id = '$id'");
        if (mysqli_num_rows($results) == 0) {
            $this->error_message = mysqli_error()."<BR>";
            return false;
        }
        $row = mysqli_fetch_array($results, MYSQL_ASSOC);
        #Check for a mathing email address to validate.
    	$user_rcd = new user();
    	$user_rcd->load_user($row["user_id"]);
        if ($row["email"] == $user_rcd->email) {
            if ($db_link->query("update user set email_verified = 'Y' where user_id = '".$row["user_id"]."'"))
            {
            	$db_link->query("delete user_email_validation where unique_id = '$id'");
	            $message = "Email address validated.";
	        }
	        else
	        {
	        	$message = "Validation update failed.<BR>".mysqli_error;
	        }
        }
        else
        {
        	$message = "Current email address does not match this validation request.";
        }
	} else {
		$message = "No project ID specified.";
	}
	echo "<script type=\"text/javascript\">\nlocation.href=\"index.php?message=".urlencode($message)."\"\n</script>";
?>