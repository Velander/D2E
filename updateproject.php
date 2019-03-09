<?	require_once "inc/db_inc.php";
	require_once "inc/class_project.php";
?>
<html>
<head>
<?
	$pagename = "$config_updateproject_page_name";
	$help_msg_name = "config_updateproject_help";
	$help_msg = "$config_updateproject_help";
	$help_width = "$config_updateproject_help_width";
	$help_height = "$config_updateproject_help_height";
	require "inc/title.php";

	$message	= $_GET["message"];
	$id			= $_GET["id"];
	$key		= $_GET["key"];

?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require_once "inc/jscript.inc"; ?>
<? require_once "inc/cssstyle.php"; ?>
</head>
<? require_once "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <td width="655" align="left" valign="top">
<?	if (!empty($message))
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center><br>";
	if (!empty($config_updateproject_paragraph1)) {
		include "inc/box_begin.htm";
		echo "$config_updateproject_paragraph1";
		include "inc/box_end.htm";
	}
	if ($id) {
		if ($key) {
			$project = new project;
			if ($project->load_project($id)) {
				if ($project->warning_key == $key) {
					$project->date_last_updated = date("Y-m-d");
					$project->save_project();
					$warning = "Project $project->project_id \"$project->project_name\" has been updated.";
				} else {
					$warning = "Incorrect update key specified for project $id.";
				}
			}
		} else {
			$warning = "No update key specified.";
		}
	} else {
		$warning = "No project ID specified.";
	}
	echo "<B>$warning</B>";
?>
				  </td>
<? require "inc/body_end.inc"; ?>
</html>
