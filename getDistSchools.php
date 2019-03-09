<?	require "inc/db_inc.php";
	require_once "inc/class_school.php";
	require_once "inc/class_schools.php";

	if (isset($_GET['district_id']))
	{
		$schools = new schools();
		$schools->load_donation_schools($_GET['district_id']);

		$returnvalue = "All Schools-ALL";
		while (list($schoolid, $school) = each($schools->school_list))
		{
			$school = new school();
			$school->load_school($schoolid);
			if ($returnvalue) $returnvalue .= ";";
			$returnvalue .= "$school->school_name-$schoolid";
		}
		echo $returnvalue;
	}
?>
