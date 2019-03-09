<?
require_once "inc/class_school.php";
class schools
{
	var $school_list;
	var $error_message;

	function __construct() {
		$this->school_list = array();
	}

	function add_school($row) {
		$newschool = new school();
		$newschool->school_id = $row["school_id"];
		$newschool->school_name = $row["school_name"];
		$newschool->street = $row["street"];
		$newschool->city = $row["city"];
		$newschool->state = $row["state"];
		$newschool->zip = $row["zip"];
		$newschool->phone = $row["phone"];
		$newschool->homepage = $row["homepage"];
		$newschool->contact_user_id = $row["contact_user_id"];
		$newschool->volunteer_user_id = $row["volunteer_user_id"];
		$newschool->number_of_students = $row["number_of_students"];
		$newschool->percent_free_lunch = $row["percent_free_lunch"];
		$newschool->grade_level_id = $row["grade_level_id"];
		$newschool->district_id = $row["district_id"];
		$newschool->contest_goal = $row["contest_goal"];
		$newschool->inactive = $row["inactive"];
		$newschool->expiration_date = $row["expiration_date"];
		$newschool->sponsor_user_id = $row["sponsor_user_id"];
		$newschool->sponsor_user_id2 = $row["sponsor_user_id2"];

		$this->school_list[$row["school_id"]] = $newschool;
	}

	function load_district_schools($district_id) {
		global $db_link;

		$this->school_list = array();
		if($district_id=="ALL")
			$sql = "Select * from school order by school_name";
		else
			$sql = "Select * from school where district_id = '$district_id' order by school_name";
		$results = $db_link->query($sql);
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_school($row);
		}
		mysqli_free_result($results);
	}

	function load_schools() {
		global $db_link;
		$this->school_list = array();
		$results = $db_link->query("Select * from school order by school_name");
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_school($row);
		}
		mysqli_free_result($results);
	}

	function load_donation_schools($district_id = "ALL") {
		global $db_link;
		if (!$district_id) $district_id = "ALL";
		$this->school_list = array();
		$sql = "Select distinct school.* from school inner join project on school.school_id = project.school_id where ".($district_id != "ALL" ? "school.district_id = $district_id and " : "")."project.project_status_id = 3 order by school_name";
		$results = $db_link->query($sql);
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_school($row);
		}
		mysqli_free_result($results);
	}

	function school_name($schoolid) {
		$school = $this->school_list[$schoolid];
		return $school->school_name;
	}

	function school_homepage($schoolid) {
		$school = $this->school_list[$schoolid];
		return $school->homepage;
	}

	function sponsor_banner($schoolid) {
		$school = $this->school_list[$schoolid];
		$user = new user();
		$user->load_user($school->sponsor_user_id);
		return $user->banner_link;
	}

	function sponsor2_banner($schoolid) {
		$school = $this->school_list[$schoolid];
		$user = new user();
		$user->load_user($school->sponsor_user_id2);
		return $user->banner_link;
	}

	function sponsor_half_banner($schoolid) {
		$school = $this->school_list[$schoolid];
		$user = new user();
		$user->load_user($school->sponsor_user_id);
		return $user->half_banner_link;
	}

	function sponsor2_half_banner($schoolid) {
		$school = $this->school_list[$schoolid];
		$user = new user();
		$user->load_user($school->sponsor_user_id2);
		return $user->half_banner_link;
	}

	function sponsor_url($schoolid) {
		$school = $this->school_list[$schoolid];
		$user = new user();
		$user->load_user($school->sponsor_user_id);
		return $user->url;
	}

	function sponsor2_url($schoolid) {
		$school = $this->school_list[$schoolid];
		$user = new user();
		$user->load_user($school->sponsor_user_id2);
		return $user->url;
	}

	function sponsor_name($schoolid) {
		$school = $this->school_list[$schoolid];
		$user = new user();
		$user->load_user($school->sponsor_user_id);
		if ($user->company)
			$sponsor_name = trim($user->company);
		else
			$sponsor_name = trim("$user->first_name $user->last_name");
		return $sponsor_name;
	}

	function sponsor2_name($schoolid) {
		$school = $this->school_list[$schoolid];
		$user = new user();
		$user->load_user($school->sponsor_user_id2);
		if ($user->company)
			$sponsor_name = trim($user->company);
		else
			$sponsor_name = trim("$user->first_name $user->last_name");
		return $sponsor_name;
	}

	function contest_goal($schoolid) {
		$school = $this->school_list[$schoolid];
		return $school->contest_goal;
	}

	function school_district_id($schoolid) {
		$school = $this->school_list[$schoolid];
		return $school->district_id;
	}

	function count() {
		return count($this->school_list);
	}

	function find_schools($school_id_, $school_name_, $street_, $city_, $state_, $zip_, $phone_, $contact_user_id_, $number_of_students_min_, $number_of_students_max_, $percent_free_lunch_min_, $percent_free_lunch_max_, $grade_level_id_, $district_id_, $contest_goal_min_, $contest_goal_max_, $expiration_date_min_, $expiration_date_max_, $inactive_, $sponsor_id_) {
		global $db_link;
		$sql = "Select school.* from school";
		if (!empty($school_id_)) $where = " school.school_id = '$school_id_'";
		if (!empty($school_name_))
			if (empty($where))
				$where = " school.school_name like '%$school_name_%'";
			else
				$where .= " and school.school_name like '%$school_name_%'";
		if (!empty($street_))
			if (empty($where))
				$where = " school.street like '%$street_%'";
			else
				$where .= " and school.street like '%$street_%'";
		if (!empty($city_))
			if (empty($where))
				$where = " school.city like '$%city_%'";
			else
				$where .= " and school.city like '$%city_%'";
		if (!empty($state_))
			if (empty($where))
				$where = " school.state = '$state_'";
			else
				$where .= " and school.state = '$state_'";
		if (!empty($zip_))
			if (empty($where))
				$where = " school.zip like '$zip_%'";
			else
				$where .= " and school.zip like '$zip_%'";
		if (!empty($contact_user_id_))
			if (empty($where))
				$where = " school.contact_user_id = '$contact_user_id_'";
			else
				$where .= " and school.contact_user_id = '$contact_user_id_'";
		if (!empty($number_of_students_min_))
			if (empty($where))
				$where = " school.number_of_students >= '$number_of_students_min_'";
			else
				$where .= " and school.number_of_students >= '$number_of_students_min_'";
		if (!empty($number_of_students_max_))
			if (empty($where))
				$where = " school.number_of_students <= '$number_of_students_max_'";
			else
				$where .= " and school.number_of_students <= '$number_of_students_max_'";
		if (!empty($percent_free_lunch_min_))
			if (empty($where))
				$where = " school.percent_free_lunch >= '$percent_free_lunch_min_'";
			else
				$where .= " and school.percent_free_lunch >= '$percent_free_lunch_min_'";
		if (!empty($percent_free_lunch_max_))
			if (empty($where))
				$where = " school.percent_free_lunch <= '$percent_free_lunch_max_'";
			else
				$where .= " and school.percent_free_lunch <= '$percent_free_lunch_max_'";
		if (!empty($grade_level_id_))
			if (empty($where))
				$where = " school.grade_level_id = '$grade_level_id_'";
			else
				$where .= " and school.grade_level_id = '$grade_level_id_'";
		if (!empty($district_id_))
			if (empty($where))
				$where = " school.district_id = '$district_id_'";
			else
				$where .= " and school.district_id = '$district_id_'";
		if (!empty($contest_goal_min_))
			if (empty($where))
				$where = " school.contest_goal >= '$contest_goal_min_'";
			else
				$where .= " and school.contest_goal >= '$contest_goal_min_'";
		if (!empty($contest_goal_max_))
			if (empty($where))
				$where = " school.contest_goal <= '$contest_goal_max_'";
			else
				$where .= " and school.contest_goal <= '$contest_goal_max_'";
		if (!empty($expiration_date_min_))
			if (empty($where))
				$where = " school.expiration_date >= '$expiration_date_min_'";
			else
				$where .= " and school.expiration_date >= '$expiration_date_min_'";
		if (!empty($expiration_date_max_))
			if (empty($where))
				$where = " school.expiration_date <= '$expiration_date_max_'";
			else
				$where .= " and school.expiration_date <= '$expiration_date_max_'";
		if (!empty($inactive_))
			if (empty($where))
				$where = " school.inactive = '$inactive_'";
			else
				$where .= " and school.inactive = '$inactive_'";

		if (!empty($sponsor_id_))
			if (empty($where))
				$where = " school.sponsor_user_id = '$sponsor_id_'";
			else
				$where .= " and school.sponsor_user_id = '$sponsor_id_'";

		if (!empty($where))
			$where = " where ".$where;
		$sql .= $where." order by school.school_name, school.state";
		if ($results = $db_link->query($sql)) {
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
				$this->add_school($row);
			}
			mysqli_free_result($results);
		}
	}
}
?>