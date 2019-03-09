<?
class country
{
	var $country_code;
	var $country_name;

	function load_country($country_code)	{
		global $db_link;
		$results = $db_link->query("select * from country where code = '$country_code'");
		if (mysqli_num_rows($results) == 0) {
			mysqli_free_result($results);
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->country_code	= $row["code"];
			$this->country_name	= $row["name"];
			mysqli_free_result($results);
			return true;
		}
	}
}	// end of class country
?>