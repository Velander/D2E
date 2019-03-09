<?
class projects
{
    var $project_list;
    var $error_message;

    function add_project($row) {
        $newproject = new project();
        $newproject->project_id             = $row["project_id"];
        $newproject->project_name           = $row["project_name"];
        $newproject->project_description    = $row["project_description"];
        $newproject->entered_date           = empty($row["entered_date"]) ? "" : $row["entered_date"];
        $newproject->materials_needed       = $row["materials_needed"];
        $newproject->amount_needed          = $row["amount_needed"];
        $newproject->grade_level_id         = $row["grade_level_id"];
        $newproject->school_id              = $row["school_id"];
        $newproject->project_status_id      = $row["project_status_id"];
        $newproject->project_type_id        = $row["project_type_id"];
        $newproject->submitted_user_id      = $row["submitted_user_id"];
        $newproject->submitted_date         = empty($row["submitted_date"]) ? "" : $row["submitted_date"];
        $newproject->required_by_date       = empty($row["required_by_date"]) ? "" : $row["required_by_date"];
        $newproject->expiration_date        = empty($row["expiration_date"]) ? "" : $row["expiration_date"];
        $newproject->review_user_id         = $row["review_user_id"];
        $newproject->review_date            = empty($row["review_date"]) ? "" : $row["review_date"];
        $newproject->review_notes           = $row["review_notes"];
        $newproject->funds_dispersed        = $row["funds_dispersed"];
        $newproject->funds_dispersed_date   = empty($row["funds_dispersed_date"]) ? "" : $row["funds_dispersed_date"];
        $newproject->completed_date         = empty($row["completed_date"]) ? "" : $row["completed_date"];
        $newproject->date_receipts_received = empty($row["date_receipts_received"]) ? "" : $row["date_receipts_received"];
        $newproject->date_thankyous_sent    = empty($row["date_thankyous_sent"]) ? "" : $row["date_thankyous_sent"];
        $newproject->date_last_updated      = empty($row["date_last_updated"]) ? "" : $row["date_last_updated"];
        $newproject->date_last_warning      = empty($row["date_last_warning"]) ? "" : $row["date_last_warning"];
        $newproject->date_status_changed    = empty($row["date_status_changed"]) ? "" : $row["date_status_changed"];
        $newproject->warning_key            = empty($row["warning_key"]) ? "" : $row["warning_key"];
        $newproject->handling_charge        = $row["handling_charge"];
        $newproject->shipping_charge        = $row["shipping_charge"];
        $newproject->view_count             = $row["view_count"];
        $newproject->search_count           = $row["search_count"];

        $this->project_list[$row["project_id"]] = $newproject;
    }

    function load_catalog($sortorder = 'project_name', $requestid, $pagesize, $pageno, $project_type_id)
    {
        global $debug, $user, $projectstatuses, $db_link;
        $sortfields = array("status" => "project_status.project_status_description",
            "name"			=> "project.project_name",
            "id"			=> "project.project_id",
            "entereddate"	=> "project.entered_date",
            "amount"		=> "project.amount_needed",
            "searchs"		=> "project.search_count",
            "views"			=> "project.view_count",
            "updatedate"	=> "project.date_last_updated",
            "warningdate"	=> "project.date_last_warning",
            "date"			=> "project.submitted_date",
            "category"			=> "project.project_type_id",
            "status desc"		=> "project_status.project_status_description desc",
            "name desc"			=> "project.project_name desc",
            "id desc"			=> "project.project_id desc",
            "entereddate desc"	=> "project.entered_date desc",
            "amount desc"		=> "project.amount_needed desc",
            "searchs desc"		=> "project.search_count desc",
            "views desc"		=> "project.view_count desc",
            "updatedate desc"	=> "project.date_last_updated desc",
            "warningdate desc"	=> "project.date_last_warning desc",
            "category desc"			=> "project.project_type_id desc",
            "date desc"			=> "project.submitted_date desc");
        if ($sortfields[$sortorder])
            $sortorder = $sortfields[$sortorder];

        if (substr($sortorder,0,7) == "teacher" || substr($sortorder,0,9) == "donations" || substr($sortorder,0,7) == "pledged" || substr($sortorder,0,6) == "funded") {
            $specialsort = $sortorder;
            $sortorder = "";
        }

        if (empty($sortorder)) $sortorder = "project.project_name";

        $sql = "Select distinct project.* from project";
        if($requestid) {
            $where = " (project.project_id = '$requestid')";
        }
        if($project_type_id)
            $where .= (!empty($where) ? " and " : "")." (project.project_type_id = '$project_type_id'))";
        if ($min_activity_date)
            $where .= (!empty($where) ? " and " : "")." (project.review_date < '$min_activity_date' and (project.date_last_updated is null or project.date_last_updated < '$min_activity_date'))";

        $statuswhere = (!empty($where) ? " and ":"")." project.project_status_id = '11'";

        $sql .= " where $where $statuswhere order by project.project_type_id, $sortorder";

        if ($results = $db_link->query($sql)) {
            while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
                $this->add_project($row);
            }
            $results->close();
            if($specialsort == "donations" || $specialsort == "donations desc"){
                # Sort list of project by amount donated.
                if(count($this->project_list))
                {
	                reset($this->project_list);
					while (list($sprojectkey, $sproject) = each($this->project_list)) {
						$donations[$sproject->project_id]  = $sproject->amount_donated();
						$projectids[$sproject->project_id]  = $sproject->project_id;
					}
				}
                // Sort the data with volume descending, edition ascending
                if($specialsort == "donations")
                    array_multisort($donations, SORT_ASC, $projectids, SORT_ASC);
                else
                    array_multisort($donations, SORT_DESC, $projectids, SORT_ASC);

                // Now rebuild the project list in the order of the keys
                foreach($donations as $key => $donation) {
                    $new_projectlist[$key] = $this->project_list[$projectids[$key]];
                }
                $this->project_list = $new_projectlist;
            } elseif($specialsort == "pledged" || $specialsort == "pledged desc"){
                # Sort list of project by amount donated.
                if(count($this->project_list))
                {
	                reset($this->project_list);
					while (list($sprojectkey, $sproject) = each($this->project_list)) {
						$donations[$sproject->project_id]  = $sproject->amount_donated();
						$projectids[$sproject->project_id]  = $sproject->project_id;
					}
				}
                // Sort the data with volume descending, edition ascending
                if($specialsort == "pledged")
                    array_multisort($donations, SORT_ASC, $projectids, SORT_ASC);
                else
                    array_multisort($donations, SORT_DESC, $projectids, SORT_ASC);

                // Now rebuild the project list in the order of the keys
                foreach($donations as $key => $donation) {
                    $new_projectlist[$key] = $this->project_list[$projectids[$key]];
                }
                $this->project_list = $new_projectlist;
            } elseif($specialsort == "funded" || $specialsort == "funded desc"){
                # Sort list of project by amount donated.
                if(count($this->project_list))
                {
	                reset($this->project_list);
					while (list($sprojectkey, $sproject) = each($this->project_list)) {
						$donations[$sproject->project_id]  = $sproject->amount_donated()/$sproject->amount_needed;
						$projectids[$sproject->project_id]  = $sproject->project_id;
					}
				}
                // Sort the data with volume descending, edition ascending
                if($specialsort == "funded")
                    array_multisort($donations, SORT_ASC, $projectids, SORT_ASC);
                else
                    array_multisort($donations, SORT_DESC, $projectids, SORT_ASC);

                // Now rebuild the project list in the order of the keys
                foreach($donations as $key => $donation) {
                    $new_projectlist[$key] = $this->project_list[$projectids[$key]];
                }
                $this->project_list = $new_projectlist;
            } elseif($specialsort == "teacher" || $specialsort == "teacher desc"){
                # Sort list of project by teacher name.
                if(count($this->project_list))
                {
	                reset($this->project_list);
					while (list($sprojectkey, $sproject) = each($this->project_list)) {
						$donations[$sproject->project_id]  = $sproject->submitted_by_lastfirstname();
						$projectids[$sproject->project_id]  = $sproject->project_id;
					}
				}
                // Sort the data with volume descending, edition ascending
                if($specialsort == "teacher")
                    array_multisort($donations, SORT_ASC, $projectids, SORT_ASC);
                else
                    array_multisort($donations, SORT_DESC, $projectids, SORT_ASC);

                // Now rebuild the project list in the order of the keys
                foreach($donations as $key => $donation) {
                    $new_projectlist[$key] = $this->project_list[$projectids[$key]];
                }
                $this->project_list = $new_projectlist;
            }
            return True;
        } else {
            $this->error_message = "Database Error Occured<BR>$sql<BR>".mysqli_error($db_link)."<BR>";
            return False;
        }
    }

    function load_projects($user_id, $fstatus_id = array(""), $sortorder = 'project_name', $donor_id = 0, $author_id=0, $schoolid=array(), $districtid, $min_activity_date, $requestid)
    {
		global $db_link;
        global $debug, $user, $projectstatuses, $User_ID;
        $sortfields = array("status" => "project_status.project_status_description",
            "name"		=> "project.project_name",
            "id"		=> "project.project_id",
            "entereddate"	=> "project.entered_date",
            "amount"		=> "project.amount_needed",
            "searchs"		=> "project.search_count",
            "views"		=> "project.view_count",
            "updatedate"	=> "project.date_last_updated",
            "warningdate"	=> "project.date_last_warning",
            "date"		=> "project.submitted_date",
            "status desc"	=> "project_status.project_status_description desc",
            "name desc"		=> "project.project_name desc",
            "id desc"		=> "project.project_id desc",
            "entereddate desc"	=> "project.entered_date desc",
            "amount desc"	=> "project.amount_needed desc",
            "searchs desc"	=> "project.search_count desc",
            "views desc"	=> "project.view_count desc",
            "updatedate desc"	=> "project.date_last_updated desc",
            "warningdate desc"	=> "project.date_last_warning desc",
            "date desc"		=> "project.submitted_date desc");
        if ($sortfields[$sortorder])
            $sortorder = $sortfields[$sortorder];

        if (substr($sortorder,0,7) == "teacher" || substr($sortorder,0,9) == "donations" || substr($sortorder,0,7) == "pledged" || substr($sortorder,0,6) == "funded") {
            $specialsort = $sortorder;
            $sortorder = "";
        }

        if (empty($sortorder)) $sortorder = "project.project_name";

        $sql = "Select distinct project.* from project inner join project_status on project.project_status_id = project_status.project_status_id";
        if($requestid) {
            $where = " (project.project_id = '$requestid')";
        }
        if($author_id == 0) {
            if ($user_id == 0) {
                $sql .= " left join donation d on project.project_id = d.project_id";
                $where .= (!empty($where) ? " and" : "")." (d.user_id = '$donor_id')";
            } else {
                $sql .= " inner join user_affiliation ua on ua.user_id = project.submitted_user_id";
                $sql .= " left join user_affiliation ua2 on ua2.school_id = project.school_id";
                if ($user->type_id < 25)
                    $sql .= " and ua2.administration_flag = 'Y'";
                $where .= (!empty($where) ? " and" : "")." (project.submitted_user_id = '$user_id' or (ua2.user_id = '$user_id' and (project.project_status_id = '1' || ($user->type_id = 25 && project.project_status_id <= 2) || $user->type_id >= 30)))";
            }
        } else {
            $where = " (project.submitted_user_id = '$author_id')";
        }
        if (!empty($districtid)) {
            $sql .= " inner join school on project.school_id = school.school_id";
            $where .= (!empty($where) ? " and" : "")." school.district_id = '$districtid'";
        }
        if (!empty($schoolid)) {
            $where .= (!empty($where) ? " and" : "")." project.school_id = '$schoolid'";
        }

		if(count($projectstatuses->project_status_list))
		{
	        reset($projectstatuses->project_status_list);
			while (list($statusid, $project_status) = each($projectstatuses->project_status_list)) {
				if (!empty($fstatus_id[$statusid]))
					$statuswhere .= (empty($statuswhere) ? " and (" : " or ")." project.project_status_id = '$statusid'";
			}
		}
        if (!empty($statuswhere))
            $statuswhere .= ")";

        if ($min_activity_date)
            $where .= (!empty($where) ? " and" : "")." (project.review_date < '$min_activity_date' and (project.date_last_updated is null or project.date_last_updated < '$min_activity_date'))";
        $sql .= (!empty($where) ? " where $where $statuswhere" : "")." order by $sortorder";

        if ($results = $db_link->query($sql)) {
            while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
                $this->add_project($row);
            }
            $results->close();
            if($specialsort == "donations" || $specialsort == "donations desc"){
                # Sort list of project by amount donated.
                if(count($this->project_list))
                {
	                reset($this->project_list);
					while (list($sprojectkey, $sproject) = each($this->project_list)) {
						$donations[$sproject->project_id]  = $sproject->amount_donated();
						$projectids[$sproject->project_id]  = $sproject->project_id;
					}
				}
                // Sort the data with volume descending, edition ascending
                if($specialsort == "donations")
                    array_multisort($donations, SORT_ASC, $projectids, SORT_ASC);
                else
                    array_multisort($donations, SORT_DESC, $projectids, SORT_ASC);

                // Now rebuild the project list in the order of the keys
                foreach($donations as $key => $donation) {
                    $new_projectlist[$key] = $this->project_list[$projectids[$key]];
                }
                $this->project_list = $new_projectlist;
            } elseif($specialsort == "pledged" || $specialsort == "pledged desc"){
                # Sort list of project by amount pledged.
                if(count($this->project_list))
                {
	                reset($this->project_list);
					while (list($sprojectkey, $sproject) = each($this->project_list)) {
						$donations[$sproject->project_id]  = $sproject->amount_pledged();
						$projectids[$sproject->project_id]  = $sproject->project_id;
					}
				}
                // Sort the data with volume descending, edition ascending
                if($specialsort == "pledged")
                    array_multisort($donations, SORT_ASC, $projectids, SORT_ASC);
                else
                    array_multisort($donations, SORT_DESC, $projectids, SORT_ASC);

                // Now rebuild the project list in the order of the keys
                foreach($donations as $key => $donation) {
                    $new_projectlist[$key] = $this->project_list[$projectids[$key]];
                }
                $this->project_list = $new_projectlist;
            } elseif($specialsort == "funded" || $specialsort == "funded desc"){
                # Sort list of project by amount donated.
                if(count($this->project_list))
                {
	                reset($this->project_list);
					while (list($sprojectkey, $sproject) = each($this->project_list)) {
						$donations[$sproject->project_id]  = $sproject->amount_donated()/$sproject->amount_needed;
						$projectids[$sproject->project_id]  = $sproject->project_id;
					}
				}
                // Sort the data with volume descending, edition ascending
                if($specialsort == "funded")
                    array_multisort($donations, SORT_ASC, $projectids, SORT_ASC);
                else
                    array_multisort($donations, SORT_DESC, $projectids, SORT_ASC);

                // Now rebuild the project list in the order of the keys
                foreach($donations as $key => $donation) {
                    $new_projectlist[$key] = $this->project_list[$projectids[$key]];
                }
                $this->project_list = $new_projectlist;
            } elseif($specialsort == "teacher" || $specialsort == "teacher desc"){
                # Sort list of project by teacher name.
                if(count($this->project_list))
                {
	                reset($this->project_list);
					while (list($sprojectkey, $sproject) = each($this->project_list)) {
						$donations[$sproject->project_id]  = $sproject->submitted_by_lastfirstname();
						$projectids[$sproject->project_id]  = $sproject->project_id;
					}
				}
                // Sort the data with volume descending, edition ascending
                if($specialsort == "teacher")
                    array_multisort($donations, SORT_ASC, $projectids, SORT_ASC);
                else
                    array_multisort($donations, SORT_DESC, $projectids, SORT_ASC);

                // Now rebuild the project list in the order of the keys
                foreach($donations as $key => $donation) {
                    $new_projectlist[$key] = $this->project_list[$projectids[$key]];
                }
                $this->project_list = $new_projectlist;
            }
            return True;
        } else {
            $this->error_message = "Database Error Occured<BR>$sql<BR>".mysqli_error($db_link)."<BR>";
            return False;
        }
    }

    function search_projects($keywords, $district_id = 0, $school_id = 0, $teacher_id = 0, $grade_level_id = 0, $project_type_id = 0, $funds_required, $endangered = 'N', $sortorder = 'project_name', $status_id = 0, $submitted_date) {
		global $db_link;
        global $config_search_funds_increment, $config_search_endangered_days;
        $sortorder = str_replace("status", "project_status.project_status_description", $sortorder);
        $sortorder = str_replace("name", "project.project_name", $sortorder);
        $sortorder = str_replace("id", "project.project_id", $sortorder);
        $sortorder = str_replace("date", "project.required_by_date", $sortorder);
        $sortorder = str_replace("updatedate", "project.required_by_date", $sortorder);
        $sortorder = str_replace("warningdate", "project.date_last_warning", $sortorder);
        $sortorder = str_replace("type", "pt.project_type_description", $sortorder);
        $sortorder = str_replace("amount", "project.amount_needed", $sortorder);
        $sortorder = str_replace("school", "school.school_name", $sortorder);
        if (substr($sortorder,0,9) == "donations" || substr($sortorder,0,7) == "pledged" || substr($sortorder,0,6) == "funded") {
            $specialsort = $sortorder;
            $sortorder = "";
        }
        if (empty($sortorder)) $sortorder = "project.project_name";
        $sql = "Select distinct project.* from project inner join project_status on project.project_status_id = project_status.project_status_id";
        $sql .= " inner join user_affiliation ua on ua.user_id = project.submitted_user_id";
        $sql .= " inner join school on school.school_id = project.school_id";
        $sql .= " inner join user teacher on teacher.user_id = project.submitted_user_id";
        $sql .= " left join project_type pt on project.project_type_id = pt.project_type_id";
        $sql .= " left join project_types pts on project.project_id = pts.project_id";

        # Add Keywords to the select
        $title_search = "";
        $description_search = "";
        $teacher_search = "";
        if (!empty($keywords)) {
            $words = split("[ ,;:]", $keywords);
            while (list($idx, $word) = each($words)) {
                $title_search .= (!empty($title_search) ? " and " : "")." project.project_name like '%$word%'";
                $description_search .= (!empty($description_search) ? " and " : "")." project.project_description like '%$word%'";
                $teacher_search .= (!empty($teacher_search) ? " and " : "")." (teacher.first_name like '%$word%' or teacher.last_name like '%$word%')";
            }
            $where = "($title_search or $description_search or $teacher_search)";
        }
        # Add Submitted Date to select
        if ($submitted_date) {
            $where .= (!empty($where) ? " and" : "")." project.review_date >= '".date("Y-m-d",strtotime($submitted_date))."'";
        }
        # Add school_id to the select
        if ($school_id != 0) {
            $where .= (!empty($where) ? " and" : "")." project.school_id = '$school_id'";
        }
        # Add teacher_id to the select
        if ($teacher_id != 0) {
            $where .= (!empty($where) ? " and" : "")." project.submitted_user_id = '$teacher_id'";
        }
        # Add grade_level_id to select
        if ($grade_level_id != 0) {
            $where .= (!empty($where) ? " and" : "")." project.grade_level_id = '$grade_level_id'";
        }
        # Add project_type_id to select
        if ($project_type_id != 0) {
                $where .= (!empty($where) ? " and" : "")." (project.project_type_id = '$project_type_id' or pts.project_type_id = '$project_type_id')";
        }
        # Add district_id to select
        if ($district_id != 0) {
            $where .= (!empty($where) ? " and" : "")." school.district_id = '$district_id'";
            $sortorder = "school.school_name".($sortorder == "" ? "" : ", ".$sortorder);
        }
        if ($status_id != 0)
            $where .= (!empty($where) ? " and" : "")."  (project.project_status_id = '$status_id'";
        if ($status_id == 4)
            $where .= " or project.project_status_id = '5')";
        else
            $where .= ")";
        # Add funds required to select
        if ($funds_required |= 0	) {
            if (substr($funds_required, -1) == 1) {
                #$where .= (!empty($where) ? " and" : "")." (project.amount_needed - project.amount_donated)  >= '$funds_required'";
            } else {
                #$where .= (!empty($where) ? " and" : "")." (project.amount_needed - project.amount_donated)  > '".($funds_required - $config_search_funds_increment)."'";
                #$where .= " and (project.amount_needed - project.amount_donated)  <= '$funds_required'";
                $where .= (!empty($where) ? " and" : "")." (project.amount_needed - project.amount_donated)  <= '$funds_required'";
            }
        }
        # Add Endangered to select
        if ($endangered == "Y") {
            $where .= (!empty($where) ? " and" : "")." project.required_by_date != '' and (to_days(project.required_by_date) - to_days(now()) <= $config_search_endangered_days)";
        }
        $sql .= (!empty($where) ? " where $where" : "")." order by $sortorder";
        if ($results = $db_link->query($sql)) {
            while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
                $this->add_project($row);
            }
            $results->close();
            return True;
        } else {
            $this->error_message = "Database Error Occured<BR>$sql<BR>".mysqli_error($db_link)."<BR>";
            return False;
        }
    }
    function project_name($projectid) {
        $project = $this->project_list[$projectid];
        return $project->project_name;
    }

    function project_viewed($projectid)	{
		global $db_link;
        $db_link->query("update project set view_count = view_count + 1 where project_id = '$projectid'");
    }

    function project_searched($projectid)	{
		global $db_link;
        $db_link->query("update project set search_count = search_count + 1 where project_id = '$projectid'");
    }

    function approved_project_count() {
		global $db_link;
        $approved = $db_link->query("select count(project_id) from project where project.project_status_id = '3'");
        list($approved_count) = mysqli_fetch_row($approved);
        return $approved_count;
    }

    function count() {
        return count($this->project_list);
    }
}
?>