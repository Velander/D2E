<?	require "inc/db_inc.php";
	require_once "inc/class_user.php";
	require_once "inc/class_banner.php";

$debug = false;
if ($debug) {
	echo "loading user<br>\n";
	flush();
}
	$user = new user();
	$user->load_user($User_ID);

	$banner = new banner();
	if ($bannerid)
	{
		$banner->load_banner($bannerid);
		# Record the click.
		$banner->update_last_displayed($bannerid, $User_ID, $pagename, $projectid, "Y");
		$banner_user = new user();
		if ($banner_user->load_user($banner->user_id))
		{
			if ($banner_user->url)
			{
				# Redirect user to the users home page.
				echo "<script type=\"text/javascript\">\nlocation.href='".$banner_user->url."'\n</script>\n";
			}
		}
	}
	echo "<script type=\"text/javascript\">\nlocation.href='$http_location"."index.php"."'\n</script>\n";
?>