<?	require_once "inc/db_inc.php";
	require_once "inc/class_project.php";
	require_once "inc/class_school.php";
	require_once "inc/class_schools.php";
	require_once "inc/class_district.php";
	require_once "inc/class_districts.php";
	$schools = new schools();
	$schools->load_schools();
	$districts = new districts();
	$districts->load_districts();

	// prepare HTML text for use as UTF-8 character data in XML
	function cleanText($intext)
	{
		return utf8_encode(htmlspecialchars(stripslashes($intext)));
	}

header("Content-type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
// Set RSS version.
echo "\n<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">";
// Start the XML.
echo "\n<channel>\n";
echo "<atom:link href=\"http://www.donate2educate.org/rss.php\" rel=\"self\" type=\"application/rss+xml\" />";
echo "<title>Donate2Educate";
if ($schoolid) echo (" - ".$schools->school_name($schoolid));
else if ($schoolname) echo (" - ".$schoolname);
else if ($districtid) echo (" - ".$districts->district_name($districtid));
echo "</title>\n<description>Communication and Funding Bridge bewteen classrooms and community</description>\n<link>http://www.donate2educate.org/</link>";
// Query database and select the last 20 entries.
$sql = "SELECT project_id FROM project inner join school on school.school_id = project.school_id inner join district on school.district_id = district.district_id where project_status_id = 3 and district.inactive <> 'Y'";
$all = true;
if ($schoolid) {
	$sql .= " and school.school_id = '$schoolid'";
	$all = false;
}
else if($schoolname) {
	$sql .= " and school.school_name = '$schoolname'";
	$all = false;
}
else if($districtid) {
	$sql .= " and school.district_id = '$districtid'";
}
$sql .= " ORDER BY project_id DESC LIMIT 30";
$data = $db_link->query($sql);
$article_count = 0;
$prev_project_name = "";
while(($row = mysqli_fetch_array($data)) && ($article_count < 20))
{
	// Convert database images data into actual image link.
	$project = new project();
	$project->load_project($row[project_id]);
//	$desc_replace_with = array(”", “”, “”);
//	$project_description = str_replace($desc_replace, $desc_replace_with, $project->project_description);
	$project_description = str_replace("images/", "http://www.donate2educate.org/images/", $project->project_description);

//	$project_description = strtr(str_replace("&","&amp;",str_replace(chr(153),"(TM)",str_replace(chr(133),"...",str_replace("images/", "http://www.donate2educate.org/images/", $project->project_description)))),chr(183).chr(161).chr(146).chr(150).chr(151).chr(147).chr(148).chr(146).chr(149),"-?'--\"\"'-");
	if (strlen($project_description) > 550)
		$project_description = substr($project_description, 0, strrpos(substr($project_description, 0, 550), ' ')) . '...';
	$project_description = cleanText($project_description);
	$project_description .= "<BR>Amount Needed: $".sprintf("%01.2f",$project->amount_needed - $project->amount_donated() - $project->amount_pledged());

//	$project_name = str_replace($desc_replace, $desc_replace_with, $project->project_name);
	$project_name = cleanText($project->project_name);
//	$project_name = strtr(str_replace("&","&amp;",str_replace(chr(133),"...",$project->project_name)),chr(183).chr(161).chr(146).chr(150).chr(151).chr(147).chr(148),"-?'--\"\"");

	// Continue with the 10 items to be included in the <item> section of the XML.
	if ($project_name != $prev_project_name)
	{
		echo "\n<item>\n<link>http://www.donate2educate.org/donation.php?projectid=".$project->project_id."</link>\n<guid isPermaLink=\"true\">http://www.donate2educate.org/donation.php?projectid=".$project->project_id."</guid>\n<category>".$schools->school_name($project->school_id)."</category>\n<pubDate>".gmdate("D, j M Y G:i:s T",strtotime($project->submitted_date))."</pubDate>\n<title>".($all ? $schools->school_name($project->school_id).": ":"").$project_name."</title>\n<description><![CDATA[".$project_description."]]></description>\n";
		echo "</item>\n";
		$article_count += 1;
	}
	$prev_project_name = $project_name;
}
echo "\n</channel>\n</rss>";
?>
