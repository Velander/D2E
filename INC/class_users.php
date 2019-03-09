<?
class users
{
    var $user_list;
    var $error_message;

    function __construct()
    {
        $this->user_list = array();
    }

    function add_user($row)
    {
        $newuser = new user();
        $newuser->user_id 	= $row["user_id"];
        $newuser->login 	= $row["login"];
        $newuser->setup_date	= (is_null($row["setup_date"]) ? "" : $row["setup_date"]);
        $newuser->password	= $row["password"];
        #$newuser->password	= stripslashes(text_decrypt($row["password"]));
        $newuser->first_name	= $row["first_name"];
        $newuser->last_name	= $row["last_name"];
        $newuser->company	= $row["company"];
        $newuser->street	= $row["street"];
        $newuser->city		= $row["city"];
        $newuser->state		= $row["state"];
        $newuser->country	= $row["country"];
        $newuser->zip		= $row["zip"];
        $newuser->email		= $row["email"];
        $newuser->bad_email	= $row["bad_email"];
        $newuser->phone 	= $row["phone"];
        $newuser->fax		= $row["fax"];
        $newuser->url		= $row["url"];
        $newuser->banner_link	= $row["banner_link"];
        $newuser->half_banner_link	= $row["half_banner_link"];
        $newuser->type_id	= $row["user_type_id"];
        $newuser->verified	= $row["verified"];
        $newuser->newsletter	= $row["newsletter"];
        $newuser->email_verified = $row["email_verified"];
        $newuser->ip_address	= $row["ip_address"];
        $newuser->district_id	= $row["district_id"];
        $newuser->opt_date	= $row["opt_date"];
        $newuser->referral_firstname = $row["referral_firstname"];
        $newuser->referral_lastname = $row["referral_lastname"];
        $newuser->referral_schoolid = $row["referral_schoolid"];
        $this->user_list[$row["user_id"]] = $newuser;
    }

    function user_email($user_id)
    {
        $sql = "Select email from user where user_id = '$user_id'";
        global $db_link;
		$results = $db_link->query($sql);
        list($email) = mysqli_fetch_row($results);
        $results->close();
        return $email;
    }

    function find_users($userid_, $login_, $first_name_, $last_name_, $type_id_, $street_, $city_, $state_, $zip_, $phone_, $referral_firstname_, $referral_lastname_, $aff_school_, $has_projects, $email_, $district_id_, $company_, $aff_admin_)
    {
		global $db_link;
		$sql = "Select user.* from user";
        if (!empty($userid_))
            $where .= " user.user_id = '$userid_'";
        elseif (!empty($login_))
            $where .= " user.login = '$login_'";
        else {
            if (!empty($first_name_))
                $where = " user.first_name like '%$first_name_%'";
            if (!empty($last_name_))
                if (empty($where))
                    $where = " user.last_name like '%$last_name_%'";
                else
                    $where .= " and user.last_name like '%$last_name_%'";
            if (!empty($company_))
                if (empty($where))
                    $where = " user.company like '%$company_%'";
                else
                    $where .= " and user.company like '%$company_%'";
            if (!empty($street_))
                if (empty($where))
                    $where = " user.street like '%$street_%'";
                else
                    $where .= " and user.street like '%$street_%'";
            if (!empty($city_))
                if (empty($where))
                    $where = " user.city like '%$city_%'";
                else
                    $where .= " and user.city like '%$city_%'";
            if (!empty($state_))
                if (empty($where))
                    $where = " user.state like '%$state_%'";
                else
                    $where .= " and user.state like '%$state_%'";
            if (!empty($zip_))
                if (empty($where))
                    $where = " user.zip like '%$zip_%'";
                else
                    $where .= " and user.zip like '%$zip_%'";
            if (!empty($phone_))
                if (empty($where))
                    $where = " user.phone like '%$phone_%'";
                else
                    $where .= " and user.phone like '%$phone_%'";
            if (!empty($referral_firstname_))
                if (empty($where))
                    $where = " user.referral_firstname like '%$referral_firstname_%'";
                else
                    $where .= " and user.referral_firstname like '%$referral_firstname_%'";
            if (!empty($referral_lastname_))
                if (empty($where))
                    $where = " user.referral_lastname like '%$referral_lastname_%'";
                else
                    $where .= " and user.referral_lastname like '%$referral_lastname_%'";
            if (!empty($email_))
                if (empty($where))
                    $where = " user.email like '%$email_%'";
                else
                    $where .= " and user.email like '%$email_%'";
            if ($district_id_ == "ACTIVE")
            {
                $sql .= " inner join district on user.district_id = district.district_id";
                if (empty($where))
                    $where = " not ifnull(district.inactive,'N') = 'Y'";
                else
                    $where = "($where) and not ifnull(district.inactive,'N') = 'Y'";
            }
            elseif ($district_id_ == "INACTIVE")
            {
                $sql .= " inner join district on user.district_id = district.district_id";
                if (empty($where))
                    $where = " ifnull(district.inactive,'N') = 'Y'";
                else
                    $where = "($where) and ifnull(district.inactive,'N') = 'Y'";
            }
            elseif ($district_id_ != "-1" && $district_id_ != "")
                if (empty($where))
                    $where = " user.district_id = '$district_id_'";
                else
                    $where = "($where) and user.district_id = '$district_id_'";
            if (is_array($type_id_))
            {
                if (!empty($where))
                    $where = "($where) and (";

                for($i=0 ; $i<count($type_id_) ; $i++)
                {
                    $where .= " user.user_type_id = '$type_id_[$i]]'";
                    if($i<count($type_id_)-1)
                        $where .= " or ";
                }
                $where .= ")";
            }
            elseif ($type_id_ != '0' && $type_id_ <>"")
            {
                if (empty($where))
                    $where = " user.user_type_id = '$type_id_'";
                else
                    $where = "($where) and user.user_type_id = '$type_id_'";
            }
            if ($has_projects) {
                $sql .= " inner join project on user.user_id = project.submitted_user_id and project.project_status_id = '3'";
            }
            if (($aff_school_[1]->school_id) && (!$aff_admin_)) {
                $sql .= " inner join user_affiliation ua on ua.user_id = user.user_id and ua.school_id in (";
                foreach($aff_school_ as $affid => $affilation) {
                    $sql .= $affilation->school_id.",";
                }
                $sql = substr($sql,0,-1).")";
            }
            if (($aff_school_[1]->school_id) && ($aff_admin_)) {
                $sql .= " inner join user_affiliation ua on ua.user_id = user.user_id and ua.administration_flag = 'Y'and ua.school_id in (";
                foreach($aff_school_ as $affid => $affilation) {
                    $sql .= $affilation->school_id.",";
                }
                $sql = substr($sql,0,-1).")";
            }
            if ((!$aff_school_[1]->school_id) && ($aff_admin_)) {
                $sql .= " inner join user_affiliation ua on ua.user_id = user.user_id and ua.administration_flag = 'Y'";
            }
        }
        if (!empty($where))
            $where = " where ".$where;
        $sql .= $where." order by user.last_name, user.first_name";
        if ($results = $db_link->query($sql)) {
            while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
                $this->add_user($row);
            }
	        $results->close();
        }
        $this->error_message = $sql;
    }

    function unverified_users()
    {
		global $db_link;
        $sql = "Select user.* from user where verified = 'N' and user_type_id = 20";
        $sql .= " order by user.last_name, user.first_name";
        if ($results = $db_link->query($sql)) {
            while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
                $this->add_user($row);
            }
        }
        $results->close();
    }

    function email_users($email)
    {
		global $db_link;
        $this->user_list = array();
        $sql = "Select user.* from user where email = '$email'";
        $sql .= " order by user.last_name, user.first_name";
        if ($results = $db_link->query($sql)) {
            while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
                $this->add_user($row);
            }
        }
        $results->close();
    }

    function donor_users($mindate, $maxdate)
    {
		global $db_link;
        $this->user_list = array();
        $sql = "Select distinct u.user_id from user u";
        $sql .= " inner join donation d on d.user_id = u.user_id";
        $sql .= " where donation_date >= '$mindate' and donation_date <= '$maxdate'";
        $sql .= " order by u.last_name, u.first_name";

        if ($results = $db_link->query($sql))
        {
            while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC))
            {
                $newuser = new user();
                $newuser->load_user($row["user_id"]);
                $this->user_list[] = $newuser;
            }
        }
        $results->close();
    }

    function user_name($userid)
    {
		global $db_link;
        $results = $db_link->query("Select first_name, last_name, company from user where user_id = '$userid'");
        list($firstname, $lastname, $company) = mysqli_fetch_row($results);
        $results->close();
        if (empty($company))
            return "$firstname $lastname";
        else
            return $company;
    }

    function count()
    {
        return count($this->user_list);
    }
}
?>