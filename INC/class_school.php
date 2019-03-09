<?
class school
{
	var	$school_id;
	var $school_name;
	var $street;
	var $city;
	var $state;
	var $zip;
	var $phone;
	var $homepage;
	var $contact_user_id;
	var $volunteer_user_id;
	var $number_of_students;
	var $percent_free_lunch;
	var $grade_level_id;
	var $district_id;
	var $contest_goal;
	var $sponsor_user_id;
	var $sponsor_user_id2;
	var $expiration_date;
	var $inactive;
	var $error_message;

	function __construct()
	{
		$this->contact_user_id = 0;
		$this->volunteer_user_id = 0;
		$this->number_of_students = 0;
		$this->percent_free_lunch = 0;
		$this->grade_level_id = 0;
		$this->contest_goal = 0;
		$this->sponsor_user_id = 0;
		$this->sponsor_user_id2 = 0;
		$this->inactive = "N";
	}

	function load_school($schoolid)
	{
		global $db_link;
		$results = $db_link->query("select * from school where school_id = '$schoolid'");
		if (mysqli_num_rows($results) == 0) {
			$results->close();
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->school_id 			= $row["school_id"];
			$this->school_name			= $row["school_name"];
			$this->street				= $row["street"];
			$this->city					= $row["city"];
			$this->state				= $row["state"];
			$this->zip					= $row["zip"];
			$this->phone				= $row["phone"];
			$this->homepage				= $row["homepage"];
			$this->contact_user_id		= $row["contact_user_id"];
			$this->volunteer_user_id	= $row["volunteer_user_id"];
			$this->sponsor_user_id		= $row["sponsor_user_id"];
			$this->sponsor_user_id2		= $row["sponsor_user_id2"];
			$this->number_of_students	= $row["number_of_students"];
			$this->percent_free_lunch	= $row["percent_free_lunch"];
			$this->grade_level_id		= $row["grade_level_id"];
			$this->district_id			= $row["district_id"];
			$this->contest_goal			= $row["contest_goal"];
			$this->expiration_date		= (is_null($row["expiration_date"]) ? "" : $row["expiration_date"]);
			$this->inactive				= $row["inactive"];
			$results->close();
			return true;
		}
	}	// end load_school

	function save_school()
	{
		global $db_link;
		if (isset($this->school_id))	{
			// Update an existing record.
			$sql = "update school Set school_name = '$this->school_name'".
					", street 				= '$this->street'".
					", city 				= '$this->city'".
					", state 				= '$this->state'".
					", zip 					= '$this->zip'".
					", phone 				= '$this->phone'".
					", homepage				= '$this->homepage'".
					", contact_user_id 		= '".(empty($this->contact_user_id) ? "0" : $this->contact_user_id)."'".
					", volunteer_user_id 	= '".(empty($this->volunteer_user_id) ? "0" : $this->volunteer_user_id)."'".
					", sponsor_user_id 		= '".(empty($this->sponsor_user_id) ? "0" : $this->sponsor_user_id)."'".
					", sponsor_user_id2 	= '".(empty($this->sponsor_user_id2) ? "0" : $this->sponsor_user_id2)."'".
					", number_of_students 	= '".(empty($this->number_of_students) ? "0" : $this->number_of_students)."'".
					", percent_free_lunch 	= '".(empty($this->percent_free_lunch) ? "0" : $this->percent_free_lunch)."'".
					", grade_level_id 		= '".(empty($this->grade_level_id) ? "0" : $this->grade_level_id)."'".
					", district_id 			= '$this->district_id'".
					", contest_goal 		= '".(empty($this->contest_goal) ? "0" : $this->contest_goal)."'".
					", expiration_date 		= ".(empty($this->expiration_date) ? "null" : "'$this->expiration_date'").
					", inactive 			= '$this->inactive'".
					" where school_id = '$this->school_id'";
			if ($db_link->query($sql)) {
				return true;
			} else {
				$this->error_message = mysqli_error($db_link)."<BR>$sql";
				return false;
			}
		} else {
			// Insert a new school.
			$sql = "insert school (school_name, street".
				", city, state, zip, phone, homepage".
				", contact_user_id, volunteer_user_id, sponsor_user_id, sponsor_user_id2, number_of_students".
				", percent_free_lunch, grade_level_id, district_id, contest_goal, expiration_date) values (".
				"'$this->school_name', '$this->street'".
				", '$this->city', '$this->state'".
				", '$this->zip', '$this->phone', '$this->homepage'".
				", '".(empty($this->contact_user_id) ? "0" : $this->contact_user_id)."'".
				", '".(empty($this->volunteer_user_id) ? "0" : $this->volunteer_user_id)."'".
				", '".(empty($this->sponsor_user_id) ? "0" : $this->sponsor_user_id)."'".
				", '".(empty($this->sponsor_user_id2) ? "0" : $this->sponsor_user_id2)."'".
				", '".(empty($this->number_of_students) ? "0" : $this->number_of_students)."'".
				", '".(empty($this->percent_free_lunch) ? "0" : $this->percent_free_lunch)."','".(empty($this->grade_level_id) ? "0" : $this->grade_level_id)."','$this->district_id','".(empty($this->contest_goal) ? "0" : $this->contest_goal)."',".(empty($this->expiration_date) ? "null" : "'$this->expiration_date'").")";
			if ($db_link->query($sql)) {
				$this->school_id = mysqli_insert_id($db_link);
				return true;
			} else {
				$this->error_message = mysqli_error($db_link)."<BR>$sql";
				return false;
			}
		}
	}	// end save_school


	function delete_school()
	{
		global $db_link;
		if (empty($this->school_id)) {
			$this->error_message = "No school loaded to delete.";
			return false;
		} else {
			$projectsel = $db_link->query("Select count(projectid) from project where school_id = '$this->school_id'");
			if ($projectsel) {
				list($projectcount) = mysqli_fetch_row($projectsel);
				$this->error_message = "There are $projectcount projects assign to this school.";
				return false;
			} else {
				$sql = "Delete from school ";
				$sql .= " where school_id = '$this->school_id'";
				$results = $db_link->query($sql);
				if (mysqli_errno()  == 0) {
					$this->school_id = "";
					return true;
				} else {
					$this->error_message = mysqli_error($db_link)."<BR>".$sql;
					return false;
				}
			}
		}
	}

}	// end of class school
?>