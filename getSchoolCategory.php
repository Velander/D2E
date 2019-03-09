<?	require "inc/db_inc.php";
	require_once "inc/class_project_type.php";
	require_once "inc/class_project_types.php";

	$project_types = new project_types;

	if (isset($_GET['district_id']) && isset($_GET['school_id']) )
	{
		$project_types->load_project_search_types($_GET['district_id'],$_GET['school_id']);

		$returnvalue = "All Categories-ALL";
		while (list($project_type_id, $project_type) = each($project_types->project_type_list))
		{
			$project_type = new project_type();
			$project_type->load_project_type($project_type_id);
			if ($returnvalue) $returnvalue .= ";";
			$returnvalue .= "$project_type->project_type_description-$project_type_id";
		}
		echo $returnvalue;
	}
?>
