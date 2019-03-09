<?
require_once "inc/class_matching_project.php";
require_once "inc/class_matching_school.php";
require_once "inc/class_matching_district.php";
require_once "inc/class_matching_state.php";
require_once "inc/class_matching_comment.php";
require_once "inc/class_matching_project_type.php";
require_once "inc/class_donation.php";
require_once "inc/class_project.php";

class matching
{
	var $matching_id;
	var $user_id;
	var $donation_id;
	var $begin_date;
	var $end_date;
	var $max_amount;
	var $district_id;
	var $date_created;
	var $matching_project_list;
	var $matching_school_list;
	var $matching_district_list;
	var $matching_state_list;
	var $matching_comment_list;
	var $matching_project_type_list;
	var $error_message;

	function __construct()
	{
		$this->matching_id = 0;
		$this->donation_id = 0;
		$this->distinct_id = 0;
		$this->max_amount = 0;
		$this->matching_project_list = array();
		$this->matching_project_type_list = array();
		$this->matching_school_list = array();
		$this->matching_state_list = array();
		$this->matching_district_list = array();
		$this->matching_comment_list = array();
		$this->date_created = date("Y-m-d H:i:s");
	}

	function load_matching($matching_id)
	{
		global $db_link;
		$sql = "select * from matching where matching_id = '$matching_id'";
		$results = $db_link->query($sql);
		if (mysqli_num_rows($results) == 0)
		{
			$results->close();
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->matching_id 			= $row["matching_id"];
			$this->user_id				= $row["user_id"];
			$this->donation_id 			= $row["donation_id"];
			$this->date_created			= $row["date_created"];
			$this->begin_date			= (is_null($row["begin_date"]) ? "" : $row["begin_date"]);
			$this->end_date				= (is_null($row["end_date"]) ? "" : $row["end_date"]);
			$this->max_amount			= $row["max_amount"];
			$this->district_id			= $row["district_id"];
			$this->load_matching_projects($this->matching_id);
			$this->load_matching_schools($this->matching_id);
			$this->load_matching_districts($this->matching_id);
			$this->load_matching_states($this->matching_id);
			$this->load_matching_project_types($this->matching_id);
			$this->load_matching_comments($this->matching_id);
			return true;
		}
	}

	function add_comment($comment, $user_id)
	{
		global $db_link;
		# Insert new comment
		if ($comment) {
			$matching_comment = new matching_comment();
			$matching_comment->matching_id = $this->matching_id;
			$matching_comment->user_id = $user_id;
			$matching_comment->comment = $comment;
			if ($matching_comment->save_matching_comment()) {
				$this->matching_comment_list[] = $matching_comment;
				return true;
			} else {
				$this->error_message = $matching_comment->error_message;
				return false;
			}
		} else {
			$this->error_message = "Cannot save blank comment.";
			return false;
		}
	}

	function add_project($project_id)
	{
		$matching_project = new matching_project();
		$matching_project->matching_project_id = 0;
		$matching_project->project_id = $project_id;
		$this->matching_project_list[] = $matching_project;
		return true;
	}

	function add_matching_project($row)
	{
		$newmatching_project = new matching_project();
		$newmatching_project->matching_project_id 	= $row[matching_project_id];
		$newmatching_project->matching_id 			= $row[matching_id];
		$newmatching_project->project_id 			= $row[project_id];
		$this->matching_project_list[] = $newmatching_project;
	}

	function add_project_type($project_type_id)
	{
		$newmatching_project_type = new matching_project_type();
		$newmatching_project_type->matching_project_id 	= 0;
		$newmatching_project_type->project_type_id		= $project_type_id;
		$this->matching_project_type_list[] = $newmatching_project_type;
	}

	function add_matching_project_type($row)
	{
		$newmatching_project_type = new matching_project_type();
		$newmatching_project_type->matching_project_id 	= $row[matching_project_id];
		$newmatching_project_type->matching_id 			= $row[matching_id];
		$newmatching_project_type->project_type_id		= $row[project_type_id];
		$this->matching_project_type_list[] = $newmatching_project_type;
	}

	function add_school($school_id)
	{
		$matching_school = new matching_school();
		$matching_school->matching_school_id = 0;
		$matching_school->school_id = $school_id;
		$this->matching_school_list[] = $matching_school;
		return true;
	}

	function add_matching_school($row)
	{
		$newmatching_school = new matching_school();
		$newmatching_school->matching_school_id 	= $row[matching_school_id];
		$newmatching_school->matching_id 			= $row[matching_id];
		$newmatching_school->school_id 				= $row[school_id];
		$this->matching_school_list[] = $newmatching_school;
	}

	function add_district($district_id)
	{
		$matching_district = new matching_district();
		$matching_district->matching_district_id = 0;
		$matching_district->district_id = $district_id;
		$this->matching_district_list[] = $matching_district;
		return true;
	}

	function add_matching_district($row)
	{
		$newmatching_district = new matching_district();
		$newmatching_district->matching_district_id = $row[matching_district_id];
		$newmatching_district->matching_id 			= $row[matching_id];
		$newmatching_district->district_id 			= $row[district_id];
		$this->matching_district_list[] = $newmatching_district;
	}
	function add_state($state)
	{
		$matching_state = new matching_state();
		$matching_state->matching_state_id = 0;
		$matching_state->state = $state;
		$this->matching_state_list[] = $matching_state;
		return true;
	}

	function add_matching_state($row)
	{
		$newmatching_state = new matching_state();
		$newmatching_state->matching_state_id 	= $row[matching_state_id];
		$newmatching_state->matching_id 			= $row[matching_id];
		$newmatching_state->state 				= $row[state];
		$this->matching_state_list[] = $newmatching_state;
	}

	function remove_project($project_id)
	{
		$new_matching_project_list = array();
		while (list($idx, $matching_project)= each($this->matching_project_list)) {
			if ($matching_project->project_id == $project_id) {
				if ($matching_project->matching_project_id != '0') {
					# The matching_project record needs to be deleted.
					$this->delete_matching_project($matching_project->project_id);
				}
			} else {
				$new_matching_project_list[$matching_project->matching_project_id] = $matching_project;
			}
		}
		$this->matching_project_list = $new_matching_project_list;
		return true;
	}

	function remove_project_type($project_type_id)
	{
		$new_matching_project_type_list = array();
		while (list($idx, $matching_project_type)= each($this->matching_project_type_list)) {
			if ($matching_project_type->project_type_id == $project_type_id) {
				if ($matching_project_type->matching_project_type_id != '0') {
					# The matching_project_type record needs to be deleted.
					$this->delete_matching_project_type($matching_project_type->project_type_id);
				}
			} else {
					$new_matching_project_type_list[] = $matching_project_type;
			}
		}
		$this->matching_project_type_list = $new_matching_project_type_list;
		return true;
	}

	function remove_school($school_id)
	{
		$new_matching_school_list = array();
		while (list($idx, $matching_school)= each($this->matching_school_list)) {
			if ($matching_school->school_id == $school_id) {
				if ($matching_school->matching_school_id != '0') {
					# The matching_school record needs to be deleted.
					$this->delete_matching_school($matching_school->school_id);
				}
			} else {
				$new_matching_school_list[] = $matching_school;
			}
		}
		$this->matching_school_list = $new_matching_school_list;
		return true;
	}

	function remove_district($district_id)
	{
		$new_matching_district_list = array();
		while (list($idx, $matching_district)= each($this->matching_district_list)) {
			if ($matching_district->district_id == $district_id) {
				if ($matching_district->matching_district_id != '0') {
					# The matching_district record needs to be deleted.
					$this->delete_matching_district($matching_district->district_id);
				}
			} else {
				$new_matching_district_list[] = $matching_district;
			}
		}
		$this->matching_district_list = $new_matching_district_list;
		return true;
	}

	function remove_state($state)
	{
		$new_matching_state_list = array();
		while (list($idx, $matching_state)= each($this->matching_state_list)) {
			if ($matching_state->state == $state) {
				if ($matching_state->matching_state_id != '0') {
					# The matching_state record needs to be deleted.
					$this->delete_matching_state($matching_state->state);
				}
			} else {
				$new_matching_state_list[] = $matching_state;
			}
		}
		$this->matching_state_list = $new_matching_state_list;
		return true;
	}

	function donation_total()
	{
		if ($this->donation_id == 0) {
			return 0;
		} else {
			$donation = new donation;
			$donation->load_donation($this->donation_id);
			return $donation->donation_total();
		}
	}

	function load_matching_projects($matching_id)
	{
		global $db_link;
		$this->matching_project_list = array();
		$results = $db_link->query("Select * from matching_project where matching_id = '$matching_id' order by project_id");
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_matching_project($row);
		}
		$results->close();
	}

	function load_matching_project_types($matching_id)
	{
		global $db_link;
		$this->matching_project_type_list = array();
		$results = $db_link->query("Select * from matching_project_type where matching_id = '$matching_id' order by matching_project_type_id");
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_matching_project_type($row);
		}
		$results->close();
	}

	function load_matching_schools($matching_id)
	{
		global $db_link;
		$this->matching_school_list = array();
		$results = $db_link->query("Select * from matching_school where matching_id = '$matching_id' order by matching_school_id");
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_matching_school($row);
		}
		$results->close();
	}

	function load_matching_comments($matching_id = 0)
	{
		global $db_link;
		$this->matching_comment_list = array();
		$sql = "Select * from matching_comment where matching_id = '".($matching_id ? $this->matching_id : $matching_id)."' order by matching_comment_id";
		$results = $db_link->query($sql);
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_matching_comment($row);
		}
		$results->close();
	}

	function add_matching_comment($row)
	{
		$newmatching_comment = new matching_comment();
		$newmatching_comment->matching_comment_id 	= $row[matching_comment_id];
		$newmatching_comment->matching_id 			= $row[matching_id];
		$newmatching_comment->user_id	 			= $row[user_id];
		$newmatching_comment->datecreated			= $row[datecreated];
		$newmatching_comment->comment	 			= $row[comment];
		$this->matching_comment_list[] = $newmatching_comment;
	}

	function load_matching_districts($matching_id)
	{
		global $db_link;
		$this->matching_district_list = array();
		$results = $db_link->query("Select * from matching_district where matching_id = '$matching_id' order by matching_district_id");
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_matching_district($row);
		}
		$results->close();
	}

	function load_matching_states($matching_id)
	{
		global $db_link;
		$this->matching_state_list = array();
		$results = $db_link->query("Select * from matching_state where matching_id = '$matching_id' order by matching_state_id");
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_matching_state($row);
		}
		$results->close();
	}

	function delete_matching_project($project_id)
	{
		global $db_link;
		if ($db_link->query("delete from matching_project where project_id = '$project_id' and matching_id = '$this->matching_id'")) {
			return true;
		} else {
			return false;
		}
	}

	function delete_matching_project_type($project_type_id)
	{
		global $db_link;
		if ($db_link->query("delete from matching_project_type where project_type_id = '$project_type_id' and matching_id = '$this->matching_id'")) {
			return true;
		} else {
			return false;
		}
	}

	function delete_matching_school($school_id)
	{
		global $db_link;
		if ($db_link->query("delete from matching_school where school_id = '$school_id' and matching_id = '$this->matching_id'")) {
			return true;
		} else {
			return false;
		}
	}

	function delete_matching_district($district_id)
	{
		global $db_link;
		if ($db_link->query("delete from matching_district where district_id = '$district_id' and matching_id = '$this->matching_id'")) {
			return true;
		} else {
			return false;
		}
	}

	function delete_matching_state($state)
	{
		global $db_link;
		if ($db_link->query("delete from matching_state where state = '$state' and matching_id = '$this->matching_id'")) {
			return true;
		} else {
			return false;
		}
	}

	function delete_matching()
	{
		global $db_link;
		if ($this->matching_id)
		{
			if ($this->donation_id)
			{
				$donations = new donation;
				if ($donations->load_donation($this->donation_id))
				{
					if ($donations->donation_total() > 0)
					{
						$this->error_message = "Cannot delete.  Donations exist.";
						return false;
					}
				}
			}
			$sql = "Delete from matching_comment where matching_id = '$this->matching_id'";
			$db_link->query($sql);
			$sql = "Delete from matching_district where matching_id = '$this->matching_id'";
			$db_link->query($sql);
			$sql = "Delete from matching_project where matching_id = '$this->matching_id'";
			$db_link->query($sql);
			$sql = "Delete from matching_project_type where matching_id = '$this->matching_id'";
			$db_link->query($sql);
			$sql = "Delete from matching_school where matching_id = '$this->matching_id'";
			$db_link->query($sql);
			$sql = "Delete from matching_state where matching_id = '$this->matching_id'";
			$db_link->query($sql);
			$sql = "Delete donation_project where donation_id = '$this->donation_id'";
			$db_link->query($sql);
			$sql = "Delete donation where donation_id = '$this->donation_id'";
			$db_link->query($sql);
			$sql = "Delete from matching where matching_id = '$this->matching_id'";
			$db_link->query($sql);
			return true;
		}
	}

	function matching_amount($project_id)
	{
		global $db_link;
		global $User_ID;
		# Here are the possible combinations of matching funds.
		#
		# 1) Nothing is selected so everything matches
		# 8) 1 or more states only are selected
		# 2) A district only is selected
		# 3) A district and 1 or more project types are selected
		# 4) 1 or more schools only are selected
		# 5) 1 or more schools and 1 or more project types are selected
		# 6) 1 or more projects only are selected
		# 7) 1 or more project types only are selected

		$matching_list = array();
		$amount = 0;
		#1 - Select matching funds that don't specify anything. (#1 above)
		$sql = "select distinct matching.matching_id, matching.max_amount from matching".
		" left join matching_district on matching_district.matching_id = matching.matching_id".
		" left join matching_project on matching_project.matching_id = matching.matching_id".
		" left join matching_school on matching_school.matching_id = matching.matching_id".
		" left join matching_project_type on matching_project_type.matching_id = matching.matching_id".
		" left join matching_state on matching_state.matching_id = matching.matching_id".
		" where matching_district.district_id is null AND matching_project.matching_id is null AND matching_state.matching_id is null".
		" AND matching_school.matching_id is null AND matching_project_type.matching_id is null".
		" AND matching.begin_date <= now()".
		" and (matching.end_date is null or matching.end_date >= now())";
if ($User_ID == 2 && $debug) echo "1: $sql<BR>";
		if ($results = $db_link->query($sql)) {
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
if ($User_ID == 2 && $debug) echo " Match found: ".$row["matching_id"]."<BR>";
				if (!in_array($row["matching_id"], $matching_list)) {
					$matching = new matching();
					if ($matching->load_matching($row["matching_id"])) {
						if ($matching->max_amount - $matching->donation_total() > 0) {
							$matching_list[$row["matching_id"]] = $row["matching_id"];
							$amount += $row["max_amount"] - $matching->donation_total();
						}
					}
				}
			}
		} else {
			$this->error_message .= mysqli_error($db_link);
		}
		$results->close();

		#8 Select matching funds that select 1 or more states. (#8 above)
		$sql = "select matching.matching_id, matching.max_amount from project".
		" inner join school on project.school_id = school.school_id".
		" inner join matching_state on matching_state.state = school.state".
		" inner join matching on matching_state.matching_id = matching.matching_id".
		" where project.project_id = ".$project_id." AND matching.begin_date <= now()".
		" and (matching.end_date is null or matching.end_date >= now())";
if ($User_ID == 2 && $debug) echo "2: $sql<BR>";
		if ($results = $db_link->query($sql)) {
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
if ($User_ID == 2 && $debug) echo " Match found: ".$row["matching_id"]."<BR>";
				if (!in_array($row["matching_id"], $matching_list)) {
					$matching = new matching();
					if ($matching->load_matching($row["matching_id"])) {
						if ($matching->max_amount - $matching->donation_total() > 0) {
							$matching_list[$row["matching_id"]] = $row["matching_id"];
							$amount += $row["max_amount"] - $matching->donation_total();
						}
					}
				}
			}
		} else {
			$this->error_message .= mysqli_error($db_link);
		}
		$results->close();

		#2 & 3 Select matching funds that select a district and 0 or more project types. (#2 & #3 above)
		$sql = "select matching.matching_id, matching.max_amount from matching".
		" inner join matching_district on matching_district.matching_id = matching.matching_id".
		" inner join school on matching_district.district_id = school.district_id".
		" inner join project on project.school_id = school.school_id".
		" left join matching_project_type on matching_project_type.matching_id = matching.matching_id".
		" where project.project_id = ".$project_id." AND matching.begin_date <= now()".
		" and (matching_project_type.project_type_id is null or matching_project_type.project_type_id = project.project_type_id)".
		" and (matching.end_date is null or matching.end_date >= now())";
if ($User_ID == 2 && $debug) echo "3: $sql<BR>";
		if ($results = $db_link->query($sql)) {
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
if ($User_ID == 2 && $debug) echo " Match found: ".$row["matching_id"]."<BR>";
				if (!in_array($row["matching_id"], $matching_list)) {
					$matching = new matching();
if ($User_ID == 2 && $debug) echo " Loading matching<BR>";
					if ($matching->load_matching($row["matching_id"])) {
if ($User_ID == 2 && $debug) echo "Max=".$matching->max_amount." Total".$matching->donation_total()."<BR>";
						if ($matching->max_amount - $matching->donation_total() > 0) {
if ($User_ID == 2 && $debug) echo " Adding to list<BR>";
							$matching_list[$row["matching_id"]] = $row["matching_id"];
							$amount += $row["max_amount"] - $matching->donation_total();
						}
					}
				}
			}
		} else {
			$this->error_message .= mysqli_error($db_link);
		}
		$results->close();

		# Select matching funds that select 1 or more schools and 0 or more project types. (#4 or #5 above)
		$sql = "select matching.matching_id, matching.max_amount from matching".
		" inner join matching_school on matching_school.matching_id = matching.matching_id".
		" inner join school on matching_school.school_id = school.school_id".
		" inner join project on project.school_id = school.school_id".
		" left join matching_project_type on matching_project_type.matching_id = matching.matching_id".
		" where project.project_id = ".$project_id." AND matching.begin_date <= now()".
		" and (matching_project_type.project_type_id is null or matching_project_type.project_type_id = project.project_type_id)".
		" and (matching.end_date is null or matching.end_date >= now())";
if ($User_ID == 2 && $debug) echo "4: $sql<BR>";
		if ($results = $db_link->query($sql)) {
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
if ($User_ID == 2 && $debug) echo " Match found: ".$row["matching_id"]."<BR>";
				if (!in_array($row["matching_id"], $matching_list)) {
					$matching = new matching();
					if ($matching->load_matching($row["matching_id"])) {
						if ($matching->max_amount - $matching->donation_total() > 0) {
							$matching_list[$row["matching_id"]] = $row["matching_id"];
							$amount += $row["max_amount"] - $matching->donation_total();
						}
					}
				}
			}
		} else {
			$this->error_message .= mysqli_error($db_link);
		}
		$results->close();

		# Select matching funds that select 1 or more projects.			(#6 above)
		$sql = "select matching.matching_id, matching.max_amount from matching".
		" inner join matching_project on matching_project.matching_id = matching.matching_id".
		" where matching_project.project_id = ".$project_id." AND matching.begin_date <= now()".
		" and (matching.end_date is null or matching.end_date >= now())";
if ($User_ID == 2 && $debug) echo "5: $sql<BR>";
		if ($results = $db_link->query($sql)) {
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
if ($User_ID == 2 && $debug) echo " Match found: ".$row["matching_id"]."<BR>";
				if (!in_array($row["matching_id"], $matching_list)) {
					$matching = new matching();
					if ($matching->load_matching($row["matching_id"])) {
						if ($matching->max_amount - $matching->donation_total() > 0) {
							$matching_list[$row["matching_id"]] = $row["matching_id"];
							$amount += $row["max_amount"] - $matching->donation_total();
						}
					}
				}
			}
		} else {
				$this->error_message .= mysqli_error($db_link);
		}
		$results->close();

		# Select matching funds that select 1 or more project types.	(#7 above)
		$sql = "select matching.matching_id, matching.max_amount from matching".
		" inner join matching_project_type on matching_project_type.matching_id = matching.matching_id".
		" inner join project on project.project_type_id = matching_project_type.project_type_id".
		" where project.project_id = ".$project_id." AND matching.begin_date <= now()".
		" and (matching.end_date is null or matching.end_date >= now())";
if ($User_ID == 2 && $debug) echo "6: $sql<BR>";
		if ($results = $db_link->query($sql)) {
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
if ($User_ID == 2 && $debug) echo " Match found: ".$row["matching_id"]."<BR>";
				if (!in_array($row["matching_id"], $matching_list)) {
					$matching = new matching();
					if ($matching->load_matching($row["matching_id"])) {
						if ($matching->max_amount - $matching->donation_total() > 0) {
							$matching_list[$row["matching_id"]] = $row["matching_id"];
							$amount += $row["max_amount"] - $matching->donation_total();
						}
					}
				}
			}
		} else {
			$this->error_message .= mysqli_error($db_link);
		}
		$results->close();

if ($User_ID == 2 && $debug) echo " Match amount: ".$amount."<BR>";
		return $amount;
	}

	function matching_sponsors($project_id)
	{
		global $db_link;
		global $User_ID;
		# Here are the possible combinations of matching funds.
		#
		# 1) Nothing is selected so everything matches
		# 2) A district only is selected
		# 3) A district and 1 or more project types are selected
		# 4) 1 or more schools only are selected
		# 5) 1 or more schools and 1 or more project types are selected
		# 6) 1 or more projects only are selected
		# 7) 1 or more project types only are selected

		$sponsors = array();
		$users = array();

		# Select matching funds that select 1 or more projects.			(#6 above)
		$sql = "select distinct user.user_id, matching.begin_date, matching.end_date, matching.matching_id from matching".
		" inner join user on matching.user_id = user.user_id".
		" inner join matching_project on matching_project.matching_id = matching.matching_id".
		" where matching_project.project_id = '".$project_id."' AND matching.begin_date <= now()".
		" and (matching.end_date is null or matching.end_date >= now())";
if ($User_ID == 2 && $debug) echo "S1: $sql<BR>";
		if ($results = $db_link->query($sql)) {
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
				if (!in_array($row["user_id"], $sponsors)) {
if ($User_ID == 2 && $debug) echo " Match found: ".$row["user_id"]."<BR>";
					$matching = new matching();
if ($User_ID == 2 && $debug) echo " Checking Matching ".$row["matching_id"]."<BR>";
					if ($matching->load_matching($row["matching_id"])) {
						if ($matching->max_amount - $matching->donation_total() > 0) {
if ($User_ID == 2 && $debug) echo " Some Available<BR>";
							$sponsors[$row["user_id"]] = $row["begin_date"];
							$user = new user();
							$user->load_user($row["user_id"]);
							$user->setup_date = $row["end_date"];
							$users[$row["user_id"]] =$user;
						}
					}
				}
			}
		} else {
			$this->error_message .= mysqli_error($db_link);
		}
		$results->close();

		# Select matching funds that select 1 or more project types.	(#7 above)
		$sql = "select distinct user.user_id, matching.begin_date, matching.end_date, matching.matching_id from matching".
		" inner join user on matching.user_id = user.user_id".
		" inner join matching_project_type on matching_project_type.matching_id = matching.matching_id".
		" inner join project on project.project_type_id = matching_project_type.project_type_id".
		" left join matching_district on matching_district.matching_id = matching.matching_id".
		" left join matching_school on matching_school.matching_id = matching.matching_id".
		" where project.project_id = ".$project_id." AND matching.begin_date <= now()".
		" and (matching.end_date is null or matching.end_date >= now())".
		" and matching_school.matching_id is null and matching_district.district_id is null";
		" order by matching.begin_date";
if ($User_ID == 2 && $debug) echo "S2: $sql<BR>";
		if ($results = $db_link->query($sql)) {
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
if ($User_ID == 2 && $debug) echo " Match found: ".$row["user_id"]."<BR>";
				if (!in_array($row["user_id"], $sponsors)) {
if ($User_ID == 2 && $debug) echo " Checking Matching ".$row["matching_id"]."<BR>";
					$matching = new matching();
					if ($matching->load_matching($row["matching_id"])) {
						if ($matching->max_amount - $matching->donation_total() > 0) {
if ($User_ID == 2 && $debug) echo " Some Available<BR>";
							$sponsors[$row["user_id"]] = $row["begin_date"];
							$user = new user();
							$user->load_user($row["user_id"]);
							$user->setup_date = $row["end_date"];
							$users[$row["user_id"]] =$user;
						}
					}
				}
			}
		} else {
			$this->error_message .= mysqli_error($db_link);
		}
		$results->close();


		# Select matching funds that select 1 or more schools and 0 or more project types. (#4 or #5 above)
		$sql = "select distinct user.user_id, matching.begin_date, matching.end_date, matching.matching_id from matching".
		" inner join user on matching.user_id = user.user_id".
		" inner join matching_school on matching_school.matching_id = matching.matching_id".
		" inner join school on matching_school.school_id = school.school_id".
		" inner join project on project.school_id = school.school_id".
		" left join matching_project_type on matching_project_type.matching_id = matching.matching_id".
		" where project.project_id = '".$project_id."' AND matching.begin_date <= now()".
		" and ((matching_project_type.project_type_id is null or matching_project_type.project_type_id = project.project_type_id)".
		" or (matching_school.school_id = project.school_id))".
		" and (matching.end_date is null or matching.end_date >= now())".
		" order by matching.begin_date";
if ($User_ID == 2 && $debug) echo "S3: $sql<BR>";
		if ($results = $db_link->query($sql)) {
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
if ($User_ID == 2 && $debug) echo " Match found: ".$row["user_id"]."<BR>";
				if (!in_array($row["user_id"], $sponsors)) {
if ($User_ID == 2 && $debug) echo " Checking Matching ".$row["matching_id"]."<BR>";
					$matching = new matching();
					if ($matching->load_matching($row["matching_id"])) {
						if ($matching->max_amount - $matching->donation_total() > 0) {
if ($User_ID == 2 && $debug) echo " Some Available<BR>";
							$sponsors[$row["user_id"]] = $row["begin_date"];
							$user = new user();
							$user->load_user($row["user_id"]);
							$user->setup_date = $row["end_date"];
							$users[$row["user_id"]] =$user;
						}
					}
				}
			}
		} else {
			$this->error_message .= mysqli_error($db_link);
		}
		$results->close();

		# Select matching funds that select a district and 0 or more project types. (#2 & #3 above)
		$sql = "select distinct user.user_id, matching.begin_date, matching.end_date, matching.matching_id from matching".
		" inner join user on matching.user_id = user.user_id".
		" inner join matching_district on matching_district.matching_id = matching.matching_id".
		" inner join school on matching_district.district_id = school.district_id".
		" inner join project on project.school_id = school.school_id".
		" left join matching_project_type on matching_project_type.matching_id = matching.matching_id".
		" where project.project_id = '".$project_id."' AND matching.begin_date <= now()".
		" and (matching_project_type.project_type_id is null or matching_project_type.project_type_id = project.project_type_id)".
		" and (matching.end_date is null or matching.end_date >= now())".
		" order by matching.begin_date";
if ($User_ID == 2 && $debug) echo "S4: $sql<BR>";
		if ($results = $db_link->query($sql)) {
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
if ($User_ID == 2 && $debug) echo " Match found: ".$row["user_id"]."<BR>";
				if (!in_array($row["user_id"], $sponsors)) {
if ($User_ID == 2 && $debug) echo " Checking Matching ".$row["matching_id"]."<BR>";
					$matching = new matching();
					if ($matching->load_matching($row["matching_id"])) {
						if ($matching->max_amount - $matching->donation_total() > 0) {
if ($User_ID == 2 && $debug) echo " Some Available<BR>";
							$sponsors[$row["user_id"]] = $row["begin_date"];
							$user = new user();
							$user->load_user($row["user_id"]);
							$users[$row["user_id"]] =$user;
							$user->setup_date = $row["end_date"];
						}
					}
				}
			}
		} else {
				$this->error_message .= mysqli_error($db_link);
		}
		$results->close();

		# Select matching funds that don't specify anything. (#1 above)
		$sql = "select distinct user.user_id, matching.begin_date, matching.end_date, matching.matching_id from matching".
		" inner join user on matching.user_id = user.user_id".
		" left join matching_district on matching_district.matching_id = matching.matching_id".
		" left join matching_project on matching_project.matching_id = matching.matching_id".
		" left join matching_school on matching_school.matching_id = matching.matching_id".
		" left join matching_project_type on matching_project_type.matching_id = matching.matching_id".
		" where matching_district.district_id is null AND matching_project.matching_id is null".
		" AND matching_school.matching_id is null AND matching_project_type.matching_id is null".
		" AND matching.begin_date <= now()".
		" and (matching.end_date is null or matching.end_date >= now())".
		" order by matching.begin_date";
if ($User_ID == 2 && $debug) echo "S5: $sql<BR>";
		if ($results = $db_link->query($sql)) {
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
if ($User_ID == 2 && $debug) echo " Match found: ".$row["user_id"]."<BR>";
				if (!in_array($row["user_id"], $sponsors)) {
if ($User_ID == 2 && $debug) echo " Checking Matching ".$row["matching_id"]."<BR>";
					$matching = new matching();
					if ($matching->load_matching($row["matching_id"])) {
						if ($matching->max_amount - $matching->donation_total() > 0) {
if ($User_ID == 2 && $debug) echo " Some Available<BR>";
							$sponsors[$row["user_id"]] = $row["begin_date"];
							$user = new user();
							$user->load_user($row["user_id"]);
							$user->setup_date = $row["end_date"];
							$users[$row["user_id"]] =$user;
						}
					}
				}
			}
		} else {
			$this->error_message .= mysqli_error($db_link);
		}
		$results->close();

		#Sort the array by date. - Leave sort in order found: Most specific to most general
#		arsort($users);
		return $users;
	}


	function matching_list($project_id)
	{
		global $db_link;
		global $User_ID;
		# Here are the possible combinations of matching funds.
		#
		# 1) Nothing is selected so everything matches
		# 2) A district only is selected
		# 3) A district and 1 or more project types are selected
		# 4) 1 or more schools only are selected
		# 5) 1 or more schools and 1 or more project types are selected
		# 6) 1 or more projects only are selected
		# 7) 1 or more project types only are selected

		$matching_list = array();
		$amount = 0;

		# Select matching funds that select 1 or more projects.			(#6 above)
		$sql = "select distinct matching.matching_id, matching.max_amount, matching.begin_date from matching".
		" inner join matching_project on matching_project.matching_id = matching.matching_id".
		" where matching_project.project_id = ".$project_id." AND matching.begin_date <= now()".
		" and (matching.end_date is null or matching.end_date >= now())";
if ($User_ID == 2 && $debug) echo "L1: $sql<BR>";
		if ($results = $db_link->query($sql)) {
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
if ($User_ID == 2 && $debug) echo " Match found: ".$row["matching_id"]."<BR>";
				$matching = new matching();
if ($User_ID == 2 && $debug) echo " Checking Matching ".$row["matching_id"]."<BR>";
				if ($matching->load_matching($row["matching_id"])) {
					$amount_avail = $row["max_amount"] - $matching->donation_total();
					if (!in_array($row["matching_id"], $matching_list) && ($amount_avail > 0)) {
if ($User_ID == 2 && $debug) echo " Some Available<BR>";
						$matching_list[$row["matching_id"]] = $row["begin_date"];
					}
				}
			}
		} else {
				$this->error_message .= mysqli_error($db_link);
		}
		$results->close();

		# Select matching funds that select 1 or more project types.	(#7 above)
		$sql = "select distinct matching.matching_id, matching.max_amount, matching.begin_date from matching".
		" inner join matching_project_type on matching_project_type.matching_id = matching.matching_id".
		" inner join project on project.project_type_id = matching_project_type.project_type_id".
		" left join matching_school on matching_school.matching_id = matching.matching_id".
		" where project.project_id = ".$project_id." AND matching.begin_date <= now()".
		" and (matching.end_date is null or matching.end_date >= now())".
		" and matching_school.matching_id is null and matching.district_id is null";
if ($User_ID == 2 && $debug) echo "L2: $sql<BR>";
		if ($results = $db_link->query($sql)) {
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
if ($User_ID == 2 && $debug) echo " Match found: ".$row["matching_id"]."<BR>";
				$matching = new matching();
if ($User_ID == 2 && $debug) echo " Checking Matching ".$row["matching_id"]."<BR>";
				if ($matching->load_matching($row["matching_id"])) {
					$amount_avail = $row["max_amount"] - $matching->donation_total();
					if (!in_array($row["matching_id"], $matching_list) && ($amount_avail > 0)) {
if ($User_ID == 2 && $debug) echo " Some Available<BR>";
						$matching_list[$row["matching_id"]] = $row["begin_date"];
					}
				}
			}
		} else {
			$this->error_message .= mysqli_error($db_link);
		}
		$results->close();

		# Select matching funds that select 1 or more schools and 0 or more project types. (#4 or #5 above)
		$sql = "select distinct matching.matching_id, matching.max_amount, matching.begin_date from matching".
		" inner join matching_school on matching_school.matching_id = matching.matching_id".
		" inner join school on matching_school.school_id = school.school_id".
		" inner join project on project.school_id = school.school_id".
		" left join matching_project_type on matching_project_type.matching_id = matching.matching_id".
		" where project.project_id = '".$project_id."' AND matching.begin_date <= now()".
		" and (matching_project_type.project_type_id is null or matching_project_type.project_type_id = project.project_type_id)".
		" and (matching.end_date is null or matching.end_date >= now())";
if ($User_ID == 2 && $debug) echo "L3: $sql<BR>";
		if ($results = $db_link->query($sql)) {
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
if ($User_ID == 2 && $debug) echo " Match found: ".$row["matching_id"]."<BR>";
				$matching = new matching();
if ($User_ID == 2 && $debug) echo " Checking Matching ".$row["matching_id"]."<BR>";
				if ($matching->load_matching($row["matching_id"])) {
					$amount_avail = $row["max_amount"] - $matching->donation_total();
					if (!in_array($row["matching_id"], $matching_list) && ($amount_avail > 0)) {
if ($User_ID == 2 && $debug) echo " Some Available<BR>";
						$matching_list[$row["matching_id"]] = $row["begin_date"];
					}
				}
			}
		} else {
			$this->error_message .= mysqli_error($db_link);
		}
		$results->close();

		# Select matching funds that select a district and 0 or more project types. (#2 & #3 above)
		$sql = "select distinct matching.matching_id, matching.max_amount, matching.begin_date from matching".
		" inner join matching_district on matching_district.matching_id = matching.matching_id".
		" inner join school on matching_district.district_id = school.district_id".
		" inner join project on project.school_id = school.school_id".
		" left join matching_project_type on matching_project_type.matching_id = matching.matching_id".
		" where project.project_id = '".$project_id."' AND matching.begin_date <= now()".
		" and (matching_project_type.project_type_id is null or matching_project_type.project_type_id = project.project_type_id)".
		" and (matching.end_date is null or matching.end_date >= now())".
		" order by matching.begin_date";
if ($User_ID == 2 && $debug) echo "L4: $sql<BR>";
		if ($results = $db_link->query($sql)) {
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
if ($User_ID == 2 && $debug) echo " Match found: ".$row["matching_id"]."<BR>";
				$matching = new matching();
if ($User_ID == 2 && $debug) echo " Checking Matching ".$row["matching_id"]."<BR>";
				if ($matching->load_matching($row["matching_id"])) {
					$amount_avail = $row["max_amount"] - $matching->donation_total();
					if (!in_array($row["matching_id"], $matching_list) && ($amount_avail > 0)) {
if ($User_ID == 2 && $debug) echo " Some Available<BR>";
						$matching_list[$row["matching_id"]] = $row["begin_date"];
					}
				}
			}
		} else {
			$this->error_message .= mysqli_error($db_link);
		}
		$results->close();

		# Select matching funds that don't specify anything. (#1 above)
		$sql = "select distinct matching.matching_id, matching.max_amount, matching.begin_date from matching".
		" left join matching_project on matching_project.matching_id = matching.matching_id".
		" left join matching_school on matching_school.matching_id = matching.matching_id".
		" left join matching_project_type on matching_project_type.matching_id = matching.matching_id".
		" where matching.district_id is null AND matching_project.matching_id is null".
		" AND matching_school.matching_id is null AND matching_project_type.matching_id is null".
		" AND matching.begin_date <= now()".
		" and (matching.end_date is null or matching.end_date >= now())";
if ($User_ID == 2 && $debug) echo "L5: $sql<BR>";
		if ($results = $db_link->query($sql)) {
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
if ($User_ID == 2 && $debug) echo " Match found: ".$row["matching_id"]."<BR>";
				$matching = new matching();
if ($User_ID == 2 && $debug) echo " Checking Matching ".$row["matching_id"]."<BR>";
				if ($matching->load_matching($row["matching_id"])) {
					$amount_avail = $row["max_amount"] - $matching->donation_total();
					if (!in_array($row["matching_id"], $matching_list) && ($amount_avail > 0)) {
if ($User_ID == 2 && $debug) echo " Some Available<BR>";
						$matching_list[$row["matching_id"]] = $row["begin_date"];
					}
				}
			}
		} else {
			$this->error_message .= mysqli_error($db_link);
		}
		$results->close();

#		asort($matching_list); - Leave sort in order found: Most specific to most general

		return $matching_list;
	}

	function match_donation($matching_donation_id, $project_id, $donation_amount)
	{
		global $db_link;
		global $user;
		if ($this->donation_id == 0) {
			$this->donation = new donation();
			$this->donation->user_id = $this->user_id;
			$this->donation->payment_received = "Y";
			$this->donation->payment_authorized = "Y";
			$this->donation->matching_donation = "Y";
			$this->donation->donation_date = date("Y-m-d H:i:s");
			if ($this->donation->save_donation()) {
				$this->donation_id = $this->donation->donation_id;
				if (!$db_link->query("update matching set donation_id = ".$this->donation_id." where matching_id = ".$this->matching_id)) {
					$this->error_message = "Unable to update matching record with donation_id: ".$this->donation_id;
					return false;
				}
			} else {
				$this->error_message = "Unable to create donation record: ".$this->donation->error_message;
				return false;
			}
		} else {
			$this->donation = new donation();
			$this->donation->load_donation($this->donation_id);
			$this->donation->matching_donation = "Y";
		}
		if ($this->donation->add_project($project_id, $donation_amount, $matching_donation_id)) {
			if ($this->donation->save_donation()) {
				return true;
			} else {
				$this->error_message = "Unable to save donation record: ".$this->donation->error_message;
				return false;
			}
		} else {
			$this->error_message = "Unable to add project to donation record: ".$this->donation->error_message;
			return false;
		}
	}

	function save_matching()
	{
		global $db_link;
		if ($this->matching_id == 0)
		{
			// Create a new empty donation.
			$donation = new donation();
			$donation->matching_donation = 'Y';
			$donation->user_id = $this->user_id;
			$donation->payment_authorized = "Y";
			$donation->payment_received = "Y";
			$donation->payment_received_date = date("Y-m-d");
			$donation->contact_flag = "D";
			$donation->save_donation();
			$this->donation_id = $donation->donation_id;

			// Insert new matching
			$sql = "Insert matching (user_id, max_amount, date_created, begin_date, end_date, district_id, donation_id) values (";
			$sql .= "'$this->user_id', '$this->max_amount'";
			$sql .= ", ".(empty($this->date_created) ? "NULL" : "'$this->date_created'");
			$sql .= ", ".(empty($this->begin_date) ? "NULL" : "'$this->begin_date'");
			$sql .= ", ".(empty($this->end_date) ? "NULL" : "'$this->end_date'");
			$sql .= ", '$this->distinct_id', '$this->donation_id')";
			if ($db_link->query($sql))
			{
				$this->matching_id = mysqli_insert_id($db_link);
				reset($this->matching_project_list);
				while (list($matching_project_id, $matching_project)= each($this->matching_project_list))
				{
					# Save the new matching_project record.
					$sql = "Insert matching_project (matching_id, project_id) values ('".$this->matching_id."','".$matching_project->project_id."')";
					if ($db_link->query($sql)) {
						$matching_project->matching_project_id = mysqli_insert_id($db_link);
					} else {
						$this->error_message = mysqli_error($db_link)."<BR>$sql";
						return False;
					}
				}
				$this->load_matching_projects($this->matching_id);

				reset($this->matching_district_list);
				while (list($matching_district_id, $matching_district)= each($this->matching_district_list))
				{
					# Save the new matching_district record.
					$sql = "Insert matching_district (matching_id, district_id) values ('".$this->matching_id."','".$matching_district->district_id."')";
					if ($db_link->query($sql)) {
						$matching_district->matching_district_id = mysqli_insert_id($db_link);
					} else {
						$this->error_message = mysqli_error($db_link)."<BR>$sql";
						return False;
					}
				}
				$this->load_matching_districts($this->matching_id);

				reset($this->matching_school_list);
				while (list($matching_school_id, $matching_school)= each($this->matching_school_list))
				{
					# Save the new matching_school record.
					$sql = "Insert matching_school (matching_id, school_id) values ('".$this->matching_id."','".$matching_school->school_id."')";
					if ($db_link->query($sql)) {
						$matching_school->matching_school_id = mysqli_insert_id($db_link);
					} else {
						$this->error_message = mysqli_error($db_link)."<BR>$sql";
						return False;
					}
				}
				$this->load_matching_schools($this->matching_id);

				reset($this->matching_state_list);
				while (list($matching_state_id, $matching_state)= each($this->matching_state_list))
				{
					# Save the new matching_state record.
					$sql = "Insert matching_state (matching_id, state) values ('".$this->matching_id."','".$matching_state->state."')";
					if ($db_link->query($sql)) {
						$matching_state->matching_state_id = mysqli_insert_id($db_link);
					} else {
						$this->error_message = mysqli_error($db_link)."<BR>$sql";
						return False;
					}
				}
				$this->load_matching_states($this->matching_id);

				reset($this->matching_project_type_list);
				while (list($matching_project_type_id, $matching_project_type)= each($this->matching_project_type_list))
				{
					# Save the new matching_project_type record.
					$sql = "Insert matching_project_type (matching_id, project_type_id) values ('".$this->matching_id."','".$matching_project_type->project_type_id."')";
					if ($db_link->query($sql)) {
						$matching_project_type->matching_project_type_id = mysqli_insert_id($db_link);
					} else {
						$this->error_message = mysqli_error($db_link)."<BR>$sql";
						return False;
					}
				}
				$this->load_matching_schools($this->matching_id);
				return true;
			} else {
				$this->error_message = mysqli_error($db_link)."<BR>$sql";
				return False;
			}
		} else {
			// Update existing matching
			$existing_rcd = new matching();
			if ($existing_rcd->load_matching($this->matching_id))
			{
				$sql = "update matching Set donation_id = '$this->donation_id', user_id = '$this->user_id'";
				$sql .= ", max_amount = '$this->max_amount'";
				$sql .= ", begin_date = ".(empty($this->begin_date) ? "NULL" : "'$this->begin_date'");
				$sql .= ", end_date = ".(empty($this->end_date) ? "NULL" : "'$this->end_date'");
				$sql .= ", district_id = '$this->district_id'";
				$sql .= " where matching_id = '$this->matching_id'";
				if ($db_link->query($sql))
				{
					reset($this->matching_project_list);
					while (list($matching_project_id, $matching_project)= each($this->matching_project_list))
					{
						if (empty($matching_project->matching_project_id))
						{
							# Check to see if the project_id is already on file.
							reset($existing_rcd->matching_project_list);
							$found = false;
							while (list($id, $existingproject) = each($existing_rcd->matching_project_list))
							{
								if ($existingproject->project_id == $matching_project->project_id)
								{
									$found = true;
									break;
								}
							}
							if (!$found)
							{
								# Save the new matching_project record.
								$sql = "Insert matching_project (matching_id, project_id) values ('$this->matching_id','$matching_project->project_id')";
								if ($db_link->query($sql))
								{
									$matching_project->matching_project_id = mysqli_insert_id($db_link);
								} else {
									$this->error_message = mysqli_error($db_link)."<BR>$sql";
									return False;
								}
							}
						}
					}
					# Now check for existing projects that aren't selected anymore.
					reset($existing_rcd->matching_project_list);
					while (list($id, $existingproject) = each($existing_rcd->matching_project_list)) {
						$found = false;
						reset($this->matching_project_list);
						while (list($matching_project_id, $matching_project)= each($this->matching_project_list))
						{
							if ($existingproject->project_id == $matching_project->project_id)
							{
								$found = true;
								break;
							}
						}
						if (!$found)
							$this->delete_matching_project($existingproject->project_id);
					}
					$this->load_matching_projects($this->matching_id);

					reset($this->matching_district_list);
					while (list($matching_district_id, $matching_district)= each($this->matching_district_list))
					{
						if (empty($matching_district->matching_district_id))
						{
							# Check to see if the district is already on file.
							reset($existing_rcd->matching_district_list);
							$found = false;
							while (list($id, $existingdistrict) = each($existing_rcd->matching_district_list))
							{
								if ($existingdistrict->district_id == $matching_district->district_id)
								{
									$found = true;
									break;
								}
							}
							if (!$found)
							{
								# Save the new matching_district record.
								$sql = "Insert matching_district (matching_id, district_id) values ('$this->matching_id','$matching_district->district_id')";
								if ($db_link->query($sql)) {
									$matching_district->matching_district_id = mysqli_insert_id($db_link);
								} else {
									$this->error_message = mysqli_error($db_link)."<BR>$sql";
									return False;
								}
							}
						}
					}
					# Now check for existing districts that aren't selected anymore.
					reset($existing_rcd->matching_district_list);
					while (list($id, $existingdistrict) = each($existing_rcd->matching_district_list)) {
						$found = false;
						reset($this->matching_district_list);
						while (list($matching_district_id, $matching_district)= each($this->matching_district_list))
						{
							if ($existingdistrict->district_id == $matching_district->district_id)
							{
								$found = true;
								break;
							}
						}
						if (!$found) {
							$this->delete_matching_district($existingdistrict->district_id);
						}
					}
					$this->load_matching_districts($this->matching_id);

					reset($this->matching_school_list);
					while (list($matching_school_id, $matching_school)= each($this->matching_school_list))
					{
						if (empty($matching_school->matching_school_id))
						{
							# Check to see if the school is already on file.
							reset($existing_rcd->matching_school_list);
							$found = false;
							while (list($id, $existingschool) = each($existing_rcd->matching_school_list))
							{
								if ($existingschool->school_id == $matching_school->school_id)
								{
									$found = true;
									break;
								}
							}
							if (!$found)
							{
								# Save the new matching_school record.
								$sql = "Insert matching_school (matching_id, school_id) values ('$this->matching_id','$matching_school->school_id')";
								if ($db_link->query($sql))
								{
									$matching_school->matching_school_id = mysqli_insert_id($db_link);
								} else {
									$this->error_message .= mysqli_error($db_link)."<BR>$sql";
									return False;
								}
							}
						}
					}
					# Now check for existing schools that aren't selected anymore.
					reset($existing_rcd->matching_school_list);
					while (list($id, $existingschool) = each($existing_rcd->matching_school_list)) {
						$found = false;
						reset($this->matching_school_list);
						while (list($matching_school_id, $matching_school)= each($this->matching_school_list))
						{
							if ($existingschool->school_id == $matching_school->school_id)
							{
								$found = true;
								break;
							}
						}
						if (!$found)
							$this->delete_matching_school($existingschool->school_id);
					}
					$this->load_matching_schools($this->matching_id);

					reset($this->matching_state_list);
					while (list($matching_state_id, $matching_state)= each($this->matching_state_list))
					{
						if (empty($matching_state->matching_state_id))
						{
							# Check to see if the state is already on file.
							reset($existing_rcd->matching_state_list);
							$found = false;
							while (list($id, $existingstate) = each($existing_rcd->matching_state_list))
							{
								if ($existingstate->state == $matching_state->state)
								{
									$found = true;
									break;
								}
							}
							if (!$found)
							{
								# Save the new matching_state record.
								$sql = "Insert matching_state (matching_id, state) values ('$this->matching_id','$matching_state->state')";
								if ($db_link->query($sql)) {
									$matching_state->matching_state_id = mysqli_insert_id($db_link);
								} else {
									$this->error_message .= mysqli_error($db_link)."<BR>$sql";
									return False;
								}
							}
						}
					}
					# Now check for existing states that aren't selected anymore.
					reset($existing_rcd->matching_state_list);
					while (list($id, $existingstate) = each($existing_rcd->matching_state_list)) {
						$found = false;
						reset($this->matching_state_list);
						while (list($matching_state_id, $matching_state)= each($this->matching_state_list))
						{
							if ($existingstate->state == $matching_state->state)
							{
								$found = true;
								break;
							}
						}
						if (!$found)
							$this->delete_matching_state($matching_state->state);
					}
					$this->load_matching_states($this->matching_id);

					reset($this->matching_project_type_list);
					while (list($matching_project_type_id, $matching_project_type)= each($this->matching_project_type_list))
					{
						if (empty($matching_project_type->matching_project_type_id))
						{
							# Check to see if the project_id is already on file.
							reset($existing_rcd->matching_project_type_list);
							$found = false;
							while (list($id, $existingproject_type) = each($existing_rcd->matching_project_type_list))
							{
								if ($existingproject_type->project_type_id == $matching_project_type->project_type_id)
								{
									$found = true;
									break;
								}
							}
							if (!$found)
							{
								# Save the new matching_project_type record.
								$sql = "Insert matching_project_type (matching_id, project_type_id) values ('$this->matching_id','$matching_project_type->project_type_id')";
								if ($db_link->query($sql)) {
									$matching_project_type->matching_project_type_id = mysqli_insert_id($db_link);
								} else {
									$this->error_message .= mysqli_error($db_link)."<BR>$sql";
									return False;
								}
							}
						}
					}
					# Now check for existing project_types that aren't selected anymore.
					reset($existing_rcd->matching_project_type_list);
					while (list($id, $existingproject_type) = each($existing_rcd->matching_project_type_list))
					{
						$found = false;
						reset($this->matching_project_type_list);
						while (list($matching_project_type_id, $matching_project_type)= each($this->matching_project_type_list))
						{
							if ($existingproject_type->project_type_id == $matching_project_type->project_type_id)
							{
								$found = true;
								break;
							}
						}
						if (!$found) {
							$this->delete_matching_project_type($existingproject_type->project_type_id);
						}
					}
					$this->load_matching_project_types($this->matching_id);

					return true;
				} else {
					$this->error_message .= mysqli_error($db_link)."<BR>$sql";
					return False;
				}
			} else {
				$this->error_message .= "Can't read existing donation record.<br>$existing_rcd->error_message";
				return False;
			}
		}
	}	# end of save_matching function

	function search($userid = 0, $match_id = 0, $begin_date, $begin_date_to, $end_date, $end_date_to, $districts = array(), $schools = array(), $states = array(), $types = array(), $projects = array())
	{
		global $db_link;
		$sql = "Select distinct matching.matching_id from matching\n";
		if ($match_id) $where = "matching_id = $match_id";
		if ($begin_date)
		{
			if ($where) $where .= " and ";
			$where .= "(begin_date >= '".date("Y-m-d",strtotime($begin_date))."' or begin_date is null)";
		}
		if ($begin_date_to)
		{
			if ($where) $where .= " and ";
			$where .= "(begin_date <= '".date("Y-m-d",strtotime($begin_date_to))."' or begin_date is null)";
		}
		if ($end_date)
		{
			if ($where) $where .= " and ";
			$where .= "(end_date >= '".date("Y-m-d",strtotime($end_date))."' or end_date is null)";
		}
		if ($end_date_to)
		{
			if ($where) $where .= " and ";
			$where .= "(end_date <= '".date("Y-m-d",strtotime($end_date_to))."' or end_date is null)";
		}
		if ($userid)
		{
			if ($where) $where .= " and ";
			$where .= "(user_id = '$userid')";
		}
		if (count($districts) > 0)
		{
			if ($where) $where .= " and ";
			$first = true;
			while (list($idx, $matching_district) = each($districts))
			{
				if ($first)
					$where .= "(";
				else
					$where .= " or ";
				$where .= "matching_district.district_id = ".$matching_district->district_id;
				$first = false;
			}
			if (!$first)
				$where .= ")";
			$sql .= " left join matching_district on matching_district.matching_id = matching.matching_id\n";
		}
		if (count($schools) > 0)
		{
			if ($where) $where .= " and ";
			$first = true;
			while (list($idx, $matching_school) = each($schools))
			{
				if ($first)
					$where .= "(";
				else
					$where .= " or ";
				$where .= "matching_school.school_id = ".$matching_school->school_id;
				$first = false;
			}
			if (!$first)
				$where .= ")";
			$sql .= " left join matching_school on matching_school.matching_id = matching.matching_id\n";
		}
		if (count($types) > 0)
		{
			if ($where) $where .= " and ";
			$first = true;
			while (list($idx, $matching_type) = each($types))
			{
				if ($first)
					$where .= "(";
				else
					$where .= " or ";
				$where .= "matching_project_type.project_type_id = ".$matching_type->project_type_id;
				$first = false;
			}
			if (!$first)
				$where .= ")";
			$sql .= " left join matching_project_type on matching_project_type.matching_id = matching.matching_id\n";
		}
		if (count($states) > 0)
		{
			if ($where) $where .= " and ";
			$first = true;
			while (list($idx, $matching_state) = each($states))
			{
				if ($first)
					$where .= "(";
				else
					$where .= " or ";
				$where .= "matching_state.state = '".$matching_state->state."'";
				$first = false;
			}
			if (!$first)
				$where .= ")";
			$sql .= " left join matching_state on matching_state.matching_id = matching.matching_id\n";
		}
		if (count($projects) > 0)
		{
			if ($where) $where .= " and ";
			$first = true;
			while (list($idx, $matching_project) = each($projects))
			{
				if ($first)
					$where .= "(";
				else
					$where .= " or ";
				$where .= "matching_project.project_id = ".$matching_project->project_id;
				$first = false;
			}
			if (!$first)
				$where .= ")";
			$sql .= " left join matching_project on matching_project.matching_id = matching.matching_id\n";
		}


		if ($where)
			$sql .= " where $where";
		$matching_list = array();
		if ($results = $db_link->query($sql))
		{
			while(list($matching_id) = mysqli_fetch_row($results)) {
				$newmatch = new matching();
				$newmatch->load_matching($matching_id);
				$matching_list[] = $newmatch;
			}
			$this->error_message = "";
			return $matching_list;
		} else {
			if (mysqli_errno()) {
				$this->error_message .= "Search failed.<br>".mysqli_error($db_link)."<br>$sql";
				return False;
			} else {
				return array();
			}
		}
	}
}	# end of class matching
?>