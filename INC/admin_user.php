<?
	  	if (isset($User_ID)) {
		  	$user = new user;
		  	if ($user->load_user($User_ID)) {
				if ($user->type_id < 40) {
					echo "<script type=\"text/javascript\">\nlocation.href='index.php?message=".htmlentities(urlencode("Administrators Only"))."$user->type_id'\n</script>";
				}
			} else
				echo "<script type=\"text/javascript\">\nlocation.href='index.php?message=".htmlentities(urlencode("Administrator Login Required"))."'\n</script>";
		} elseif (isset($uniqueid)) {
		  	$user = new user;
			if ($user->load_unique_id($uniqueid)) {
				if ($user->type_id < 40) {
					echo "<script type=\"text/javascript\">\nlocation.href='index.php?message=".htmlentities(urlencode("Administrators Only"))."$user->type_id'\n</script>";
				}
			}
		} else
			echo "<script type=\"text/javascript\">\nlocation.href='index.php?message=".htmlentities(urlencode("Administrator Login Required"))."'\n</script>";
?>