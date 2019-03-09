<?
class countries
{
	var $country_list;
	var $error_message;

	function add_country($row) {
		$newcountry = new country();
		$newcountry->country_code 	= $row["code"];
		$newcountry->country_name	= $row["name"];
		$this->country_list[$row["code"]] = $newcountry;
	}

	function load_countries() {
		global $db_link;
		$results = $db_link->query("Select * from country order by sortorder, name");
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_country($row);
		}
		mysqli_free_result($results);
	}

	function country_name($country_code) {
		$country = $this->country_list[$country_code];
		return $country->country_name;
	}

	function count() {
		return count($this->country_list);
	}
}
?>