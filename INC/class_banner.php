<?
require_once "inc/class_banner_school.php";
require_once "inc/class_banner_district.php";
require_once "inc/class_banner_state.php";
require_once "inc/class_banner_comment.php";
require_once "inc/class_banner_project_type.php";
require_once "inc/class_project.php";

class banner
{
	var $banner_id;
	var $user_id;
	var $begin_date;
	var $end_date;
	var $date_created;
	var $date_last_displayed;
	var $banner_school_list;
	var $banner_district_list;
	var $banner_state_list;
	var $banner_project_type_list;
	var $banner_comment_list;
	var $error_message;

	function __construct()
	{
		$this->banner_id = 0;
		$this->banner_project_type_list = array();
		$this->banner_school_list = array();
		$this->banner_state_list = array();
		$this->banner_district_list = array();
		$this->banner_comment_list = array();
		$this->date_created = date("Y-m-d H:i:s");
	}

	function load_banner($banner_id)
	{
		global $db_link;
		$sql = "select * from banner where banner_id = '$banner_id'";
		$results = $db_link->query($sql);
		if (mysqli_num_rows($results) == 0)
		{
			$results->close();
			return false;
		} else {
			$row = mysqli_fetch_assoc($results);
			$this->banner_id 			= $row["banner_id"];
			$this->user_id				= $row["user_id"];
			$this->date_created			= $row["date_created"];
			$this->date_last_displayed	= $row["date_last_displayed"];
			$this->begin_date			= (is_null($row["begin_date"]) ? "" : $row["begin_date"]);
			$this->end_date				= (is_null($row["end_date"]) ? "" : $row["end_date"]);
			$this->load_banner_schools($this->banner_id);
			$this->load_banner_districts($this->banner_id);
			$this->load_banner_states($this->banner_id);
			$this->load_banner_project_types($this->banner_id);
			$this->load_banner_comments($this->banner_id);
			return true;
		}
	}

	function update_last_displayed($banner_id, $userid="0", $page="", $project_id="0", $click="N")
	{
		global $db_link;
		if (!$banner_id) $banner_id = $this->banner_id;
		$db_link->query("Update banner set date_last_displayed = CURRENT_TIMESTAMP() where banner_id = '$banner_id'");
		# Now update log.
		$db_link->query("insert banner_log (banner_id, datecreated, user_id, page, project_id, clicked) values ('$banner_id',CURRENT_TIMESTAMP(), '$userid','$page','$project_id', '$click')");
		return true;
	}

	function add_comment($comment)
	{
		# Insert new comment
		if ($comment) {
			$banner_comment = new banner_comment();
			$banner_comment->banner_id = $this->banner_id;
			if ($banner_comment->save_banner_comment()) {
				$this->banner_comment_list[] = $banner_comment;
				return true;
			} else {
				$this->error_message = $banner_comment->error_message;
				return false;
			}
		} else {
			$this->error_message = "Cannot save blank comment.";
			return false;
		}
	}

	function add_project_type($project_type_id)
	{
		$newbanner_project_type = new banner_project_type();
		$newbanner_project_type->banner_project_id 	= 0;
		$newbanner_project_type->project_type_id		= $project_type_id;
		$this->banner_project_type_list[] = $newbanner_project_type;
	}

	function add_banner_project_type($row)
	{
		$newbanner_project_type = new banner_project_type();
		$newbanner_project_type->banner_project_id 	= $row[banner_project_id];
		$newbanner_project_type->banner_id 			= $row[banner_id];
		$newbanner_project_type->project_type_id		= $row[project_type_id];
		$this->banner_project_type_list[] = $newbanner_project_type;
	}

	function add_school($school_id)
	{
		$banner_school = new banner_school();
		$banner_school->banner_school_id = 0;
		$banner_school->school_id = $school_id;
		$this->banner_school_list[] = $banner_school;
		return true;
	}

	function add_banner_school($row)
	{
		$newbanner_school = new banner_school();
		$newbanner_school->banner_school_id 	= $row[banner_school_id];
		$newbanner_school->banner_id 			= $row[banner_id];
		$newbanner_school->school_id 				= $row[school_id];
		$this->banner_school_list[] = $newbanner_school;
	}

	function add_district($district_id)
	{
		$banner_district = new banner_district();
		$banner_district->banner_district_id = 0;
		$banner_district->district_id = $district_id;
		$this->banner_district_list[] = $banner_district;
		return true;
	}

	function add_banner_district($row)
	{
		$newbanner_district = new banner_district();
		$newbanner_district->banner_district_id = $row[banner_district_id];
		$newbanner_district->banner_id 			= $row[banner_id];
		$newbanner_district->district_id 			= $row[district_id];
		$this->banner_district_list[] = $newbanner_district;
	}
	function add_state($state)
	{
		$banner_state = new banner_state();
		$banner_state->banner_state_id = 0;
		$banner_state->state = $state;
		$this->banner_state_list[] = $banner_state;
		return true;
	}

	function add_banner_state($row)
	{
		$newbanner_state = new banner_state();
		$newbanner_state->banner_state_id 	= $row[banner_state_id];
		$newbanner_state->banner_id 			= $row[banner_id];
		$newbanner_state->state 				= $row[state];
		$this->banner_state_list[] = $newbanner_state;
	}

	function remove_project_type($project_type_id)
	{
		$new_banner_project_type_list = array();
		while (list($idx, $banner_project_type)= each($this->banner_project_type_list)) {
			if ($banner_project_type->project_type_id == $project_type_id) {
				if ($banner_project_type->banner_project_type_id != '0') {
					# The banner_project_type record needs to be deleted.
					$this->delete_banner_project_type($banner_project_type->project_type_id);
				}
			} else {
					$new_banner_project_type_list[] = $banner_project_type;
			}
		}
		$this->banner_project_type_list = $new_banner_project_type_list;
		return true;
	}

	function remove_school($school_id)
	{
		$new_banner_school_list = array();
		while (list($idx, $banner_school)= each($this->banner_school_list)) {
			if ($banner_school->school_id == $school_id) {
				if ($banner_school->banner_school_id != '0') {
					# The banner_school record needs to be deleted.
					$this->delete_banner_school($banner_school->school_id);
				}
			} else {
				$new_banner_school_list[] = $banner_school;
			}
		}
		$this->banner_school_list = $new_banner_school_list;
		return true;
	}

	function remove_district($district_id)
	{
		$new_banner_district_list = array();
		while (list($idx, $banner_district)= each($this->banner_district_list)) {
			if ($banner_district->district_id == $district_id) {
				if ($banner_district->banner_district_id != '0') {
					# The banner_district record needs to be deleted.
					$this->delete_banner_district($banner_district->district_id);
				}
			} else {
				$new_banner_district_list[] = $banner_district;
			}
		}
		$this->banner_district_list = $new_banner_district_list;
		return true;
	}

	function remove_state($state)
	{
		$new_banner_state_list = array();
		while (list($idx, $banner_state)= each($this->banner_state_list)) {
			if ($banner_state->state == $state) {
				if ($banner_state->banner_state_id != '0') {
					# The banner_state record needs to be deleted.
					$this->delete_banner_state($banner_state->state);
				}
			} else {
				$new_banner_state_list[] = $banner_state;
			}
		}
		$this->banner_state_list = $new_banner_state_list;
		return true;
	}

	function load_banner_project_types($banner_id)
	{
		global $db_link;
		$this->banner_project_type_list = array();
		$results = $db_link->query("Select * from banner_project_type where banner_id = '$banner_id' order by banner_project_type_id");
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_banner_project_type($row);
		}
		$results->close();
	}

	function load_banner_schools($banner_id)
	{
		global $db_link;
		$this->donation_school_list = array();
		$results = $db_link->query("Select * from banner_school where banner_id = '$banner_id' order by banner_school_id");
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_banner_school($row);
		}
		$results->close();
	}

	function load_banner_comments($banner_id = 0)
	{
		global $db_link;
		$this->donation_comment_list = array();
		if($results = mysqli_query($db_link, "Select * from banner_comment where banner_id = '".($banner_id ? $this->banner_id : $banner_id)."' order by banner_comment_id"))
		{
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
				$this->add_banner_comment($row);
			}
			mysqli_free_result($results);
		}
	}

	function add_banner_comment($row)
	{
		$newbanner_comment = new banner_comment();
		$newbanner_comment->banner_comment_id 	= $row[banner_comment_id];
		$newbanner_comment->banner_id 			= $row[banner_id];
		$newbanner_comment->user_id	 			= $row[user_id];
		$newbanner_comment->datecreated			= $row[datecreated];
		$newbanner_comment->comment	 			= $row[comment];
		$this->banner_comment_list[] = $newbanner_comment;
	}

	function load_banner_districts($banner_id)
	{
		global $db_link;
		$this->donation_district_list = array();
		if($results = $db_link->query("Select * from banner_district where banner_id = '$banner_id' order by banner_district_id"))
		{
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
				$this->add_banner_district($row);
			}
			$results->close();
		}
	}

	function load_banner_states($banner_id)
	{
		global $db_link;
		$this->donation_state_list = array();
		$results = $db_link->query("Select * from banner_state where banner_id = '$banner_id' order by banner_state_id");
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_banner_state($row);
		}
		$results->close();
	}

	function delete_banner_project_type($project_type_id)
	{
		global $db_link;
		if ($db_link->query("delete from banner_project_type where project_type_id = '$project_type_id' and banner_id = '$this->banner_id'")) {
			return true;
		} else {
			return false;
		}
	}

	function delete_banner_school($school_id)
	{
		global $db_link;
		if ($db_link->query("delete from banner_school where school_id = '$school_id' and banner_id = '$this->banner_id'")) {
			return true;
		} else {
			return false;
		}
	}

	function delete_banner_district($district_id)
	{
		global $db_link;
		if ($db_link->query("delete from banner_district where district_id = '$district_id' and banner_id = '$this->banner_id'")) {
			return true;
		} else {
			return false;
		}
	}

	function delete_banner_state($state)
	{
		global $db_link;
		if ($db_link->query("delete from banner_state where state = '$state' and banner_id = '$this->banner_id'")) {
			return true;
		} else {
			return false;
		}
	}

	function all_banners()
	{
		global $db_link;
		$banner_list = array();
		$amount = 0;
		# Select banner that don't specify anything. (#1 above)
		$sql = "select distinct banner.banner_id, banner.date_last_displayed from banner".
		" where banner.begin_date < now()".
		" and (banner.end_date is null or banner.end_date > now())";
		if ($results = $db_link->query($sql)) {
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
				$banner = new banner();
				if ($banner->load_banner($row["banner_id"])) {
					if (!in_array($row["banner_id"], $banner_list)) {
						$banner_list[$row["banner_id"]] = $row["date_last_displayed"];
					}
				}
			}
		} else {
			$this->error_message .= mysqli_error($db_link);
		}
		$results->close();
		asort($banner_list);

		return $banner_list;
	}

	function banner_list($project_id, $school_id, $district_id)
	{
		global $db_link;
		global $User_ID;
		# Here are the possible combinations of banners.
		#
		# 1) Nothing is selected so everything matches
		# 2) A district only is selected
		# 3) A district and 1 or more project types are selected
		# 4) 1 or more schools only are selected
		# 5) 1 or more schools and 1 or more project types are selected
		# 7) 1 or more project types only are selected
		# 8) 1 or more states are selected
		# 9) 1 or more districts as selected

		$banner_list = array();
		$amount = 0;
		# Select banner that don't specify anything. (#1 above)
		$sql = "select distinct banner.banner_id, banner.date_last_displayed from banner".
		" left join banner_school on banner_school.banner_id = banner.banner_id".
		" left join banner_project_type on banner_project_type.banner_id = banner.banner_id".
		" left join banner_state on banner_state.banner_id = banner.banner_id".
		" left join banner_district on banner_district.banner_id = banner.banner_id".
		" where banner_school.banner_id is null AND banner_project_type.banner_id is null".
		" AND banner_state.banner_id is null AND banner_district.banner_id is null".
		" AND banner.begin_date < now()".
		" and (banner.end_date is null or banner.end_date > now())";
		if ($results = $db_link->query($sql)) {
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
				$banner = new banner();
				if ($banner->load_banner($row["banner_id"])) {
					if (!in_array($row["banner_id"], $banner_list)) {
						$banner_list[$row["banner_id"]] = $row["date_last_displayed"];
					}
				}
			}
		} else {
			$this->error_message .= mysqli_error($db_link);
		}
		$results->close();

		if ($project_id) {
			$school = new school;
			$school_id = $project->school_id;
			$school->load_school($school_id);
			$state = $school->state;

			# Select banner that select a district and 0 or more project types. (#2 & #3 above)
			$sql = "select distinct banner.banner_id, banner.date_last_displayed from banner".
			" inner join banner_district on banner_district.banner_id = banner.banner_id".
			" inner join school on banner_district.district_id = school.district_id".
			" inner join project on project.school_id = school.school_id".
			" left join banner_project_type on banner_project_type.banner_id = banner.banner_id".
			" where project.project_id = '".$project_id."' AND banner.begin_date < now()".
			" and (banner_project_type.project_type_id is null or banner_project_type.project_type_id = project.project_type_id)".
			" and (banner.end_date is null or banner.end_date > now())";
			if ($results = $db_link->query($sql)) {
				while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
					$banner = new banner();
					if ($banner->load_banner($row["banner_id"])) {
						if (!in_array($row["banner_id"], $banner_list)) {
							$banner_list[$row["banner_id"]] = $row["date_last_displayed"];
						}
					}
				}
			} else {
				$this->error_message .= mysqli_error($db_link);
			}
			$results->close();

			# Select banner that select 1 or more schools and 0 or more project types. (#4 or #5 above)
			$sql = "select distinct banner.banner_id, banner.date_last_displayed from banner".
			" inner join banner_school on banner_school.banner_id = banner.banner_id".
			" inner join school on banner_school.school_id = school.school_id".
			" inner join project on project.school_id = school.school_id".
			" left join banner_project_type on banner_project_type.banner_id = banner.banner_id".
			" where project.project_id = '".$project_id."' AND banner.begin_date < now()".
			" and (banner_project_type.project_type_id is null or banner_project_type.project_type_id = project.project_type_id)".
			" and (banner.end_date is null or banner.end_date > now())";
			if ($results = $db_link->query($sql)) {
				while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
					$banner = new banner();
					if ($banner->load_banner($row["banner_id"])) {
						if (!in_array($row["banner_id"], $banner_list)) {
							$banner_list[$row["banner_id"]] = $row["date_last_displayed"];
						}
					}
				}
			} else {
				$this->error_message .= mysqli_error($db_link);
			}
			$results->close();

			# Select banner that select 1 or more project types.	(#7 above)
			$sql = "select distinct banner.banner_id, banner.date_last_displayed from banner".
			" inner join banner_district on banner_district.banner_id = banner.banner_id".
			" inner join banner_project_type on banner_project_type.banner_id = banner.banner_id".
			" inner join project on project.project_type_id = banner_project_type.project_type_id".
			" left join banner_school on banner_school.banner_id = banner.banner_id".
			" where project.project_id = ".$project_id." AND banner.begin_date < now()".
			" and (banner.end_date is null or banner.end_date > now())".
			" and banner_school.banner_id is null and banner_district.district_id is null";
			if ($results = $db_link->query($sql)) {
				while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
					$banner = new banner();
					if ($banner->load_banner($row["banner_id"])) {
						if (!in_array($row["banner_id"], $banner_list)) {
							$banner_list[$row["banner_id"]] = $row["date_last_displayed"];
						}
					}
				}
			} else {
				$this->error_message .= mysqli_error($db_link);
			}
			$results->close();
		}

		# Select banner with 1 or more states that match (# 8)
		if ($school_id) {
			$school = new school;
			$school->load_school($school_id);
			$state = $school->state;
			$district_id = $school->district_id;
			# Select banner that select 1 or more schools and 0 or more project types. (#4A)
			$sql = "select distinct banner.banner_id, banner.date_last_displayed from banner".
			" left join banner_school on banner_school.banner_id = banner.banner_id".
			" left join banner_state on banner_state.banner_id = banner.banner_id".
			" where (banner_school.school_id = '".$school_id."' or banner_state.state = '$state')".
			" AND banner.begin_date < now()".
			" and (banner.end_date is null or banner.end_date > now())";
			if ($results = $db_link->query($sql)) {
				while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
					$banner = new banner();
					if ($banner->load_banner($row["banner_id"])) {
						if (!in_array($row["banner_id"], $banner_list)) {
							$banner_list[$row["banner_id"]] = $row["date_last_displayed"];
						}
					}
				}
			} else {
				$this->error_message .= mysqli_error($db_link);
			}
			$results->close();
		}

		# Select banner with 1 or more districts that match (9 above)
		if ($district_id) {
			$district = new district;
			$district->load_district($district_id);
			$state = $district->state;
			# Select banner that select 1 or more schools and 0 or more project types. (#4A)
			$sql = "select distinct banner.banner_id, banner.date_last_displayed from banner".
			" left join banner_state on banner_state.banner_id = banner.banner_id".
			" left join banner_district on banner_district.banner_id = banner.banner_id".
			" where (banner_district.district_id = '".$district_id."' or banner_state.state = '$state')".
			" AND banner.begin_date < now()".
			" and (banner.end_date is null or banner.end_date > now())";
			if ($results = $db_link->query($sql)) {
				while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
					$banner = new banner();
					if ($banner->load_banner($row["banner_id"])) {
						if (!in_array($row["banner_id"], $banner_list)) {
							$banner_list[$row["banner_id"]] = $row["date_last_displayed"];
						}
					}
				}
			} else {
				$this->error_message .= mysqli_error($db_link);
			}
			$results->close();
		}


		asort($banner_list);

		return $banner_list;
	}

	function save_banner()
	{
		global $db_link;
		if ($this->banner_id == 0)
		{
			// Insert new banner
			$sql = "Insert banner (user_id, date_created, date_last_displayed, begin_date, end_date) values (";
			$sql .= "'$this->user_id'";
			$sql .= ", ".(empty($this->date_created) ? "NULL" : "'$this->date_created'");
			$sql .= ", ".(empty($this->date_created) ? "NULL" : "'$this->date_created'");
			$sql .= ", ".(empty($this->begin_date) ? "NULL" : "'$this->begin_date'");
			$sql .= ", ".(empty($this->end_date) ? "NULL" : "'$this->end_date'");
			$sql .= ")";
			if ($db_link->query($sql))
			{
				$this->banner_id = mysqli_insert_id($db_link);
				reset($this->banner_district_list);
				while (list($banner_district_id, $banner_district)= each($this->banner_district_list))
				{
					# Save the new banner_district record.
					$sql = "Insert banner_district (banner_id, district_id) values ('".$this->banner_id."','".$banner_district->district_id."')";
					if ($db_link->query($sql)) {
						$banner_district->banner_district_id = mysqli_insert_id($db_link);
					} else {
						$this->error_message = mysqli_error($db_link)."<BR>$sql";
						return False;
					}
				}
				$this->load_banner_districts($this->banner_id);

				reset($this->banner_school_list);
				while (list($banner_school_id, $banner_school)= each($this->banner_school_list))
				{
					# Save the new banner_school record.
					$sql = "Insert banner_school (banner_id, school_id) values ('".$this->banner_id."','".$banner_school->school_id."')";
					if ($db_link->query($sql)) {
						$banner_school->banner_school_id = mysqli_insert_id($db_link);
					} else {
						$this->error_message = mysqli_error($db_link)."<BR>$sql";
						return False;
					}
				}
				$this->load_banner_schools($this->banner_id);

				reset($this->banner_state_list);
				while (list($banner_state_id, $banner_state)= each($this->banner_state_list))
				{
					# Save the new banner_state record.
					$sql = "Insert banner_state (banner_id, state) values ('".$this->banner_id."','".$banner_state->state."')";
					if ($db_link->query($sql)) {
						$banner_state->banner_state_id = mysqli_insert_id($db_link);
					} else {
						$this->error_message = mysqli_error($db_link)."<BR>$sql";
						return False;
					}
				}
				$this->load_banner_states($this->banner_id);

				reset($this->banner_project_type_list);
				while (list($banner_project_type_id, $banner_project_type)= each($this->banner_project_type_list))
				{
					# Save the new banner_project_type record.
					$sql = "Insert banner_project_type (banner_id, project_type_id) values ('".$this->banner_id."','".$banner_project_type->project_type_id."')";
					if ($db_link->query($sql)) {
						$banner_project_type->banner_project_type_id = mysqli_insert_id($db_link);
					} else {
						$this->error_message = mysqli_error($db_link)."<BR>$sql";
						return False;
					}
				}
				$this->load_banner_schools($this->banner_id);
				return true;
			} else {
				$this->error_message = mysqli_error($db_link)."<BR>$sql";
				return False;
			}
		} else {
			// Update existing banner
			$existing_rcd = new banner();
			if ($existing_rcd->load_banner($this->banner_id))
			{
				$sql = "update banner Set user_id = '$this->user_id'";
				$sql .= ", begin_date = ".(empty($this->begin_date) ? "NULL" : "'$this->begin_date'");
				$sql .= ", end_date = ".(empty($this->end_date) ? "NULL" : "'$this->end_date'");
				$sql .= " where banner_id = '$this->banner_id'";
				if ($db_link->query($sql))
				{
					reset($this->banner_district_list);
					while (list($banner_district_id, $banner_district)= each($this->banner_district_list))
					{
						if (empty($banner_district->banner_district_id))
						{
							# Check to see if the district is already on file.
							reset($existing_rcd->banner_district_list);
							$found = false;
							while (list($id, $existingdistrict) = each($existing_rcd->banner_district_list))
							{
								if ($existingdistrict->district_id == $banner_district->district_id)
								{
									$found = true;
									break;
								}
							}
							if (!$found)
							{
								# Save the new banner_district record.
								$sql = "Insert banner_district (banner_id, district_id) values ('$this->banner_id','$banner_district->district_id')";
								if ($db_link->query($sql)) {
									$banner_district->banner_district_id = mysqli_insert_id($db_link);
								} else {
									$this->error_message = mysqli_error($db_link)."<BR>$sql";
									return False;
								}
							}
						}
					}
					# Now check for existing districts that aren't selected anymore.
					reset($existing_rcd->banner_district_list);
					while (list($id, $existingdistrict) = each($existing_rcd->banner_district_list)) {
						$found = false;
						reset($this->banner_district_list);
						while (list($banner_district_id, $banner_district)= each($this->banner_district_list))
						{
							if ($existingdistrict->district_id == $banner_district->district_id)
							{
								$found = true;
								break;
							}
						}
						if (!$found) {
							$this->delete_banner_district($existingdistrict->district_id);
						}
					}
					$this->load_banner_districts($this->banner_id);

					reset($this->banner_school_list);
					while (list($banner_school_id, $banner_school)= each($this->banner_school_list))
					{
						if (empty($banner_school->banner_school_id))
						{
							# Check to see if the school is already on file.
							reset($existing_rcd->banner_school_list);
							$found = false;
							while (list($id, $existingschool) = each($existing_rcd->banner_school_list))
							{
								if ($existingschool->school_id == $banner_school->school_id)
								{
									$found = true;
									break;
								}
							}
							if (!$found)
							{
								# Save the new banner_school record.
								$sql = "Insert banner_school (banner_id, school_id) values ('$this->banner_id','$banner_school->school_id')";
								if ($db_link->query($sql))
								{
									$banner_school->banner_school_id = mysqli_insert_id($db_link);
								} else {
									$this->error_message .= mysqli_error($db_link)."<BR>$sql";
									return False;
								}
							}
						}
					}

					# Now check for existing schools that aren't selected anymore.
					reset($existing_rcd->banner_school_list);
					while (list($id, $existingschool) = each($existing_rcd->banner_school_list)) {
						$found = false;
						reset($this->banner_school_list);
						while (list($banner_school_id, $banner_school)= each($this->banner_school_list))
						{
							if ($existingschool->school_id == $banner_school->school_id)
							{
								$found = true;
								break;
							}
						}
						if (!$found)
							$this->delete_banner_school($existingschool->school_id);
					}
					$this->load_banner_schools($this->banner_id);

					reset($this->banner_state_list);
					while (list($banner_state_id, $banner_state)= each($this->banner_state_list))
					{
						if (empty($banner_state->banner_state_id))
						{
							# Check to see if the state is already on file.
							reset($existing_rcd->banner_state_list);
							$found = false;
							while (list($id, $existingstate) = each($existing_rcd->banner_state_list))
							{
								if ($existingstate->state == $banner_state->state)
								{
									$found = true;
									break;
								}
							}
							if (!$found)
							{
								# Save the new banner_state record.
								$sql = "Insert banner_state (banner_id, state) values ('$this->banner_id','$banner_state->state')";
								if ($db_link->query($sql)) {
									$banner_state->banner_state_id = mysqli_insert_id($db_link);
								} else {
									$this->error_message .= mysqli_error($db_link)."<BR>$sql";
									return False;
								}
							}
						}
					}
					# Now check for existing states that aren't selected anymore.
					reset($existing_rcd->banner_state_list);
					while (list($id, $existingstate) = each($existing_rcd->banner_state_list)) {
						$found = false;
						reset($this->banner_state_list);
						while (list($banner_state_id, $banner_state)= each($this->banner_state_list))
						{
							if ($existingstate->state == $banner_state->state)
							{
								$found = true;
								break;
							}
						}
						if (!$found)
							$this->delete_banner_state($banner_state->state);
					}
					$this->load_banner_states($this->banner_id);

					reset($this->banner_project_type_list);
					while (list($banner_project_type_id, $banner_project_type)= each($this->banner_project_type_list))
					{
						if (empty($banner_project_type->banner_project_type_id))
						{
							# Check to see if the project_id is already on file.
							reset($existing_rcd->banner_project_type_list);
							$found = false;
							while (list($id, $existingproject_type) = each($existing_rcd->banner_project_type_list))
							{
								if ($existingproject_type->project_type_id == $banner_project_type->project_type_id)
								{
									$found = true;
									break;
								}
							}
							if (!$found)
							{
								# Save the new banner_project_type record.
								$sql = "Insert banner_project_type (banner_id, project_type_id) values ('$this->banner_id','$banner_project_type->project_type_id')";
								if ($db_link->query($sql)) {
									$banner_project_type->banner_project_type_id = mysqli_insert_id($db_link);
								} else {
									$this->error_message .= mysqli_error($db_link)."<BR>$sql";
									return False;
								}
							}
						}
					}
					# Now check for existing project_types that aren't selected anymore.
					reset($existing_rcd->banner_project_type_list);
					while (list($id, $existingproject_type) = each($existing_rcd->banner_project_type_list))
					{
						$found = false;
						reset($this->banner_project_type_list);
						while (list($banner_project_type_id, $banner_project_type)= each($this->banner_project_type_list))
						{
							if ($existingproject_type->project_type_id == $banner_project_type->project_type_id)
							{
								$found = true;
								break;
							}
						}
						if (!$found) {
							$this->delete_banner_project_type($existingproject_type->project_type_id);
						}
					}
					$this->load_banner_project_types($this->banner_id);

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
	}	# end of save_banner function

	function search($match_id = 0, $begin_date, $begin_date_to, $end_date, $end_date_to, $districts = array(), $schools = array(), $states = array(), $types = array())
	{
		global $db_link;
		$sql = "Select distinct banner.banner_id from banner\n";
		if ($match_id) $where = "banner_id = $match_id";
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
		if (count($districts) > 0)
		{
			if ($where) $where .= " and ";
			$first = true;
			while (list($idx, $banner_district) = each($districts))
			{
				if ($first)
					$where .= "(";
				else
					$where .= " or ";
				$where .= "banner_district.district_id = ".$banner_district->district_id;
				$first = false;
			}
			if (!$first)
				$where .= ")";
			$sql .= " left join banner_district on banner_district.banner_id = banner.banner_id\n";
		}
		if (count($schools) > 0)
		{
			if ($where) $where .= " and ";
			$first = true;
			while (list($idx, $banner_school) = each($schools))
			{
				if ($first)
					$where .= "(";
				else
					$where .= " or ";
				$where .= "banner_school.school_id = ".$banner_school->school_id;
				$first = false;
			}
			if (!$first)
				$where .= ")";
			$sql .= " left join banner_school on banner_school.banner_id = banner.banner_id\n";
		}
		if (count($types) > 0)
		{
			if ($where) $where .= " and ";
			$first = true;
			while (list($idx, $banner_type) = each($types))
			{
				if ($first)
					$where .= "(";
				else
					$where .= " or ";
				$where .= "banner_project_type.project_type_id = ".$banner_type->project_type_id;
				$first = false;
			}
			if (!$first)
				$where .= ")";
			$sql .= " left join banner_project_type on banner_project_type.banner_id = banner.banner_id\n";
		}
		if (count($states) > 0)
		{
			if ($where) $where .= " and ";
			$first = true;
			while (list($idx, $banner_state) = each($states))
			{
				if ($first)
					$where .= "(";
				else
					$where .= " or ";
				$where .= "banner_state.state = '".$banner_state->state."'";
				$first = false;
			}
			if (!$first)
				$where .= ")";
			$sql .= " left join banner_state on banner_state.banner_id = banner.banner_id\n";
		}

		if ($where)
			$sql .= " where $where";
		$banner_list = array();
		if ($results = $db_link->query($sql))
		{
			while(list($banner_id) = mysqli_fetch_row($results)) {
				$newmatch = new banner();
				$newmatch->load_banner($banner_id);
				$banner_list[] = $newmatch;
			}
			$this->error_message = "";
			return $banner_list;
		} else {
			if (mysqli_errno()) {
				$this->error_message .= "Search failed.<br>".mysqli_error($db_link)."<br>$sql";
				return False;
			} else {
				return array();
			}
		}
	}
}	# end of class banner
?>