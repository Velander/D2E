<?
require_once "inc/class_project_comment.php";
class project
{
    var $project_id;
    var $project_name;
    var $project_description;
    var $entered_date;
    var $materials_needed;
    var $amount_needed;
    var $grade_level_id;
    var $school_id;
    var $project_status_id;
    var $project_type_id;
    var $entered_user_id;
    var $submitted_user_id;
    var $submitted_date;
    var $required_by_date;
    var $review_user_id;
    var $review_date;
    var $review_notes;
    var $expiration_date;
    var $funds_dispersed;
    var $funds_dispersed_amount;
    var $funds_dispersed_date;
    var $completed_date;
    var $date_receipts_received;
    var $date_thankyous_sent;
    var $date_last_updated;
    var $date_last_warning;
    var $date_status_changed;
    var $warning_key;
    var $view_count;
    var $search_count;
    var $comment_list;
    var $addl_project_types;
    var $handling_charge;
    var $shipping_charge;
    var $error_message;
    var $new_project_id;

    function __construct()
    {
        $this->review_user_id = 0;
        $this->search_count = 0;
        $this->view_count = 0;
        $this->comment_list = array();
        $this->addl_project_types = array();
    }

    function load_project($projectid, $userid=0)	{
        global $db_link;
        global $debug, $user;
        $sql = "select project.* from project left join user_affiliation ua on ua.user_id = project.submitted_user_id".
            " left join user_affiliation ua2 on ua.school_id = ua2.school_id";
        if ($user->type_id < 25)
            $sql .= " and ua2.administration_flag = 'Y'";
        $sql .= " where project.project_id = '$projectid'".($userid == 0 ? "":" and (project.submitted_user_id = '$userid' or (ua2.user_id = '$userid' and (project.project_status_id = '1' || ($user->type_id = 25 && project.project_status_id <= 2) || $user->type_id >= 30)))");
        $results = $db_link->query($sql);
        if (mysqli_num_rows($results) == 0) {
            $results->close();
            return false;
        } else {
            $row = mysqli_fetch_assoc($results);
            $this->project_id               = $row["project_id"];
            $this->project_name             = $row["project_name"];
            $this->project_description      = $row["project_description"];
            $this->materials_needed         = $row["materials_needed"];
            $this->amount_needed            = $row["amount_needed"];
            $this->entered_date             = (is_null($row["entered_date"]) ? "" : $row["entered_date"]);
            $this->grade_level_id           = $row["grade_level_id"];
            $this->school_id                = $row["school_id"];
            $this->project_status_id        = $row["project_status_id"];
            $this->project_type_id          = $row["project_type_id"];
            $this->submitted_user_id        = $row["submitted_user_id"];
            $this->submitted_date           = (is_null($row["submitted_date"]) ? "" : $row["submitted_date"]);
            if($this->project_status_id == 1 && !$this->submitted_date)
                    $this->submitted_date = date("Y-m-d H:i:s");
            $this->required_by_date         = (is_null($row["required_by_date"]) ? "" : $row["required_by_date"]);
            $this->expiration_date          = (is_null($row["expiration_date"]) ? "": $row["expiration_date"]);
            $this->review_user_id           = $row["review_user_id"];
            $this->review_date              = (is_null($row["review_date"]) ? "" : $row["review_date"]);
            $this->review_notes             = $row["review_notes"];
            $this->funds_dispersed          = $row["funds_dispersed"];
            $this->funds_dispersed_amount   = $row["funds_dispersed_amount"];
            $this->funds_dispersed_date     = (is_null($row["funds_dispersed_date"]) ? "" : $row["funds_dispersed_date"]);
            $this->completed_date           = (is_null($row["completed_date"]) ? "" : $row["completed_date"]);
            $this->date_receipts_received   = (is_null($row["date_receipts_received"]) ? "" : $row["date_receipts_received"]);
            $this->date_thankyous_sent      = (is_null($row["date_thankyous_sent"]) ? "" : $row["date_thankyous_sent"]);
            $this->date_last_updated        = (is_null($row["date_last_updated"]) ? "" : $row["date_last_updated"]);
            $this->date_last_warning        = (is_null($row["date_last_warning"]) ? "" : $row["date_last_warning"]);
            $this->date_status_changed      = (is_null($row["date_status_changed"]) ? "" : $row["date_status_changed"]);
            $this->warning_key              = (is_null($row["warning_key"]) ? "" : $row["warning_key"]);
            $this->handling_charge          = $row["handling_charge"];
            $this->shipping_charge          = $row["shipping_charge"];
            $this->view_count               = $row["view_count"];
            $this->search_count             = $row["search_count"];
            $this->load_comments();
            $results->close();
            $this->load_addl_project_types();
            return true;
        }
    } // end load_project

    function load_addl_project_types()
    {
        global $db_link;
        $new_addl_project_types = array();
        $sql = "select project_types_id, project_type_id from project_types where project_id = '$this->project_id'";
        $results = $db_link->query($sql);
        if (mysqli_num_rows($results) != 0) {
            while($row = mysqli_fetch_assoc($results))
            {
                $new_addl_project_types[$row["project_types_id"]] = $row["project_type_id"];
            }
        } else {
            $results->close();
            return false;
        }
        $this->addl_project_types = $new_addl_project_types;
    } // end load_addl_project_types


    function clear_addl_project_types()
    {
        global $db_link;
        if($this->project_id)
        {
            $db_link->query("delete from project_types where project_id = '$this->project_id'");
            $this->addl_project_types = array();
        }

    }

    function add_addl_project_type($project_type_id)
    {
        // Check to see if the project_type_id is already assigned to thei project
        global $db_link;
        if($this->project_type_id == $project_type_id)
            return false;
        reset($this->addl_project_types);
        foreach($this->addl_project_types as $key => $projecttypeid)
        {
            if($projecttypeid == $project_type_id)
                return false;
        }
        if($db_link->query("insert into project_types (project_id, project_type_id) values ('$this->project_id','$project_type_id')"))
        {
            $key = mysqli_insert_id($db_link);
            $this->addl_project_types[$key] = $project_type_id;
            return true;
        }
        else
            return false;
    } // end add_addl_project_type

    function delete_addl_project_type($project_type_id)
    {
        // Delete an additional project id from this project
        global $db_link;
        $new_project_types = array();
        reset($this->addl_project_types);
        foreach($this->addl_project_types as $key => $projecttypeid)
        {
            if($projecttypeid == $project_type_id)
                $db_link->query("delete from project_types where project_types_id = '$key'");
            else
                $new_project_types[$key] = $projecttypeid;
        }
        $this->addl_project_types = $new_project_types;
    } // end delete_addl_project_type

    function addl_project_type_exists($project_type_id)
    {
        // Find out if a project type is in the addl project types
        reset($this->addl_project_types);
        foreach($this->addl_project_types as $key => $projecttypeid)
        {
            if($projecttypeid == $project_type_id)
                return true;
        }
        return false;
    } // end addl_project_type_exists

    function amount_donated($mindate = null, $maxdate = null)
    {
        global $db_link;
        $sql = "Select sum(donation_project.amount) amount from donation inner join donation_project on donation.donation_id = donation_project.donation_id where donation_project.project_id = ".$this->project_id." and donation.payment_authorized = 'Y' and donation.payment_received = 'Y'";
        if (!empty($mindate))
            $sql .= " and donation.donation_date >= '$mindate'";
        if (!empty($maxdate))
            $sql .= " and donation.donation_date <= '$maxdate'";
        if ($results = $db_link->query($sql)) {
            list($amount) = mysqli_fetch_row($results);
            return $amount;
        } else {
            $this->error_message .= mysqli_error($db_link);
            return 0;
        }
    } // end amount_donated

    function submitted_by_name()
    {
        $suser = new user;
        $suser->load_user($this->submitted_user_id);
        return "$suser->first_name $suser->last_name";
    } // end submitted_by_name

    function submitted_by_lastfirstname()
    {
        $suser = new user;
        $suser->load_user($this->submitted_user_id);
        return "$suser->last_name, $suser->first_name";
    } // end submitted_by_lastfirstname

    function amount_pledged()
    {
        global $db_link;
        $sql = "Select sum(donation_project.amount) amount from donation inner join donation_project on donation.donation_id = donation_project.donation_id where donation_project.project_id = ".$this->project_id." and donation.payment_authorized = 'Y' and donation.payment_received = 'N'";
        if ($results = $db_link->query($sql)) {
            list($amount) = mysqli_fetch_row($results);
            return $amount;
        } else {
            $this->error_message .= mysqli_error($db_link);
            return 0;
        }
    } // end amount_pledged

    function notify_emails()
    {
        if (strstr($HTTP_HOST, "testing"))
            return "eric@donate2educate.org";
        $rev_emails = $this->reviewer();
        $acct_emails = $this->accountant();
        $emails = $rev_emails;
        $emails .= (!empty($emails) && !empty($acct_emails) ? ", " : "").$acct_emails;
        return $emails;
    } // end notify_emails

    function accountant()
    {
        global $db_link;
        if (isset($this->project_id) && ($this->project_id != 0))
        {
            $sql = 	"select u.first_name, u.last_name, u.email from project p inner join school sc on p.school_id = sc.school_id".
                " inner join district d on d.district_id = sc.district_id".
                " inner join user u on sc.district_id = u.district_id".
                " where p.project_id = ".$this->project_id." and u.user_type_id = 27";
            $emails = "";
            $results = $db_link->query($sql);
            while (list($first_name, $last_name, $email) = mysqli_fetch_row($results)) {
                $emails .= (!empty($emails) ? ", ": "")."$email";
            }
            return $emails;
        }
    } // end accountant

    function reviewer()
    {
        global $db_link;
        if (isset($this->project_id) && ($this->project_id != 0))	{
            $sql = 	"select u.first_name, u.last_name, u.email from project p inner join user_affiliation ua on p.school_id = ua.school_id".
                " inner join user u on ua.user_id = u.user_id and ua.administration_flag = 'Y'".
                " where p.project_id = ".$this->project_id;
            $emails = "";
            $results = $db_link->query($sql);
            while (list($first_name, $last_name, $email) = mysqli_fetch_row($results)) {
                $emails .= (!empty($emails) ? ", ": "")."$email";
            }
            return $emails;
        }
    } // end reviewer

    function volunteer() {
        global $db_link;
        if (isset($this->project_id) && ($this->project_id != 0))	{
            $sql = 	"select u.first_name, u.last_name, u.email from project p inner join school sc on p.school_id = sc.school_id".
                " inner join user u on sc.volunteer_user_id = u.user_id".
                " where p.project_id = ".$this->project_id;
            $emails = "";
            $results = $db_link->query($sql);
            while (list($first_name, $last_name, $email) = mysqli_fetch_row($results)) {
                $emails .= (!empty($emails) ? ", ": "")."$email";
            }
            return $emails;
        }
    } // end volunteer

    function donors() {
        global $db_link;
        if (isset($this->project_id) && ($this->project_id != 0))	{
            $sql = 	"select distinct u.first_name, u.last_name, u.street, u.city, u.state, u.zip, uc.name country, d.contact_flag, ".
            "d.gift_first_name, d.gift_last_name, d.gift_street, d.gift_city, d.gift_state, d.gift_zip, gc.name gift_country ".
            "from project p inner join donation_project dp on dp.project_id = p.project_id ".
            "inner join donation d on d.donation_id = dp.donation_id ".
            "inner join user u on u.user_id = d.user_id ".
            "left join country uc on uc.code = u.country ".
            "left join country gc on gc.code = d.gift_country ".
            "where p.project_id = ".$this->project_id;
            $donors = "";
            $results = $db_link->query($sql);
            while ($row = mysqli_fetch_array($results)) {
                $donors .= "\n\t\t";
                if ($row[contact_flag] == "D")
                    $donors .= "$row[first_name] $row[last_name], $row[street], $row[city] $row[state] $row[zip], $row[country]";
                elseif ($row[contact_flag] == "A")
                    $donors .= "Anonymous Donor";
                else
                    $donors .= "$row[gift_first_name] $row[gift_last_name], $row[gift_street], $row[gift_city] $row[gift_state] $row[gift_zip], $row[gift_country]";
            }
            return $donors;
        }
    } // end donors

    function donornamess() {
        global $db_link;
        if (isset($this->project_id) && ($this->project_id != 0))	{
            $sql = 	"select distinct u.first_name, u.last_name, u.street, u.city, u.state, u.zip, uc.name country, d.contact_flag, ".
            "d.gift_first_name, d.gift_last_name, d.gift_street, d.gift_city, d.gift_state, d.gift_zip, gc.name gift_country ".
            "from project p inner join donation_project dp on dp.project_id = p.project_id ".
            "inner join donation d on d.donation_id = dp.donation_id ".
            "inner join user u on u.user_id = d.user_id ".
            "left join country uc on uc.code = u.country ".
            "left join country gc on gc.code = d.gift_country ".
            "where p.project_id = ".$this->project_id;
            $donornames = "";
            $results = $db_link->query($sql);
            while ($row = mysqli_fetch_array($results)) {
                $donornames .= "\n\t\t";
                if ($row[contact_flag] == "D")
                    $donornames .= "$row[first_name] $row[last_name]";
                elseif ($row[contact_flag] == "A")
                    $donornames .= "Anonymous Donor";
                else
                    $donornames .= "$row[gift_first_name] $row[gift_last_name]";
            }
            return $donornames;
        }
    } // end donornames

    function save_project()	{
        global $db_link;
        if (!empty($this->new_project_id) && ($this->new_project_id != 0) && !empty($this->project_id) && ($this->project_id != 0)) {
            $db_link->query("update project Set project_id = '$this->new_project_id' where project_id = '$this->project_id' LIMIT 1");
            if (mysqli_errno == 0) {
                $this->project_id = $this->new_project_id;
            }
        }
        if (isset($this->project_id) && ($this->project_id != 0))	{
            // Update an existing record.
            $sql = "update project Set project_name = '".(get_magic_quotes_gpc() ? addslashes(stripslashes($this->project_name)) : addslashes($this->project_name))."'".
                ", project_description      = '".(get_magic_quotes_gpc() ? addslashes(stripslashes($this->project_description)) : addslashes($this->project_description))."'".
                ", materials_needed         = '".(get_magic_quotes_gpc() ? addslashes(stripslashes($this->materials_needed)) : addslashes($this->materials_needed))."'".
                ", amount_needed            = '$this->amount_needed'".
                ", entered_date             = ".(empty($this->entered_date) ? "now()" : "'$this->entered_date'").
                ", grade_level_id           = '$this->grade_level_id'".
                ", school_id                = '$this->school_id'".
                ", project_status_id        = '$this->project_status_id'".
                ", project_type_id          = '$this->project_type_id'".
                ", submitted_user_id        = '$this->submitted_user_id'".
                ", submitted_date           = ".(empty($this->submitted_date) ? "NULL" : "'$this->submitted_date'").
                ", required_by_date         = ".(empty($this->required_by_date) ? "NULL" : "'$this->required_by_date'").
                ", expiration_date          = ".(empty($this->expiration_date) ? "NULL" : "'$this->expiration_date'").
                ", review_user_id           = '$this->review_user_id'".
                ", review_date              = ".(empty($this->review_date) ? "NULL" : "'$this->review_date'").
                ", review_notes             = '".(get_magic_quotes_gpc() ? addslashes(stripslashes($this->review_notes)) : addslashes($this->review_notes))."'".
                ", funds_dispersed          = '$this->funds_dispersed'".
                ", funds_dispersed_amount   = '$this->funds_dispersed_amount'".
                ", funds_dispersed_date     = ".(empty($this->funds_dispersed_date) ? "NULL" : "'$this->funds_dispersed_date'").
                ", completed_date           = ".(empty($this->completed_date) ? "NULL" : "'$this->completed_date'").
                ", date_receipts_received   = ".(empty($this->date_receipts_received) ? "NULL" : "'$this->date_receipts_received'").
                ", date_thankyous_sent      = ".(empty($this->date_thankyous_sent) ? "NULL" : "'$this->date_thankyous_sent'").
                ", date_last_updated        = ".(empty($this->date_last_updated) ? "NULL" : "'$this->date_last_updated'").
                ", date_last_warning        = ".(empty($this->date_last_warning) ? "NULL" : "'$this->date_last_warning'").
                ", date_status_changed      = ".(empty($this->date_status_changed) ? "NULL" : "'$this->date_status_changed'").
                ", handling_charge          = '$this->handling_charge'".
                ", shipping_charge          = '$this->shipping_charge'".
                ", warning_key              = ".(empty($this->warning_key) ? "NULL" : "'$this->warning_key'").
                " where project_id          = '$this->project_id' LIMIT 1";
            if ($db_link->query($sql)) {
                if (mysqli_errno == 0) {
                    return true;
                } else {
                    $this->error_message = mysqli_error($db_link)."<br>$sql";
                    return false;
                }
            } else {
                $this->error_message = mysqli_error($db_link)."<br>$sql";
                return false;
            }
        } else {
            // Insert a new project.
            $sql = "insert project (project_name, project_description, entered_date, amount_needed, required_by_date, expiration_date".
                ", materials_needed, grade_level_id, school_id, project_status_id, project_type_id, submitted_user_id".
                ", submitted_date, handling_charge, shipping_charge, view_count) values (".
                "'".(get_magic_quotes_gpc() ? addslashes(stripslashes($this->project_name)) : addslashes($this->project_name))."', '".(get_magic_quotes_gpc() ? addslashes(stripslashes($this->project_description)) : addslashes($this->project_description))."', now(), '$this->amount_needed'".
                ", ".(empty($this->required_by_date) ? "NULL" : "'$this->required_by_date'").
                ", ".(empty($this->expiration_date) ? "NULL" : "'$this->expiration_date'").
                ", '".(get_magic_quotes_gpc() ? addslashes(stripslashes($this->materials_needed)) : addslashes($this->materials_needed))."', '$this->grade_level_id'".
                ", '$this->school_id', '$this->project_status_id'".
                ", '$this->project_type_id', '$this->submitted_user_id'".
                ", ".(empty($this->submitted_date) ? "NULL" : "'$this->submitted_date'").
                ", '$this->handling_charge', '$this->shipping_charge'".
                ", '0')";
            $db_link->query($sql);
            if (mysqli_errno == 0) {
                $this->project_id = mysqli_insert_id($db_link);
                if ($this->project_id == 0) {
                    $this->error_message = mysqli_error($db_link)."<br>$sql";
                    return false;
                } else {
                    # Check to see if a Project ID had been specified.
                    if (!empty($this->new_project_id) && ($this->new_project_id != 0) && !empty($this->project_id) && ($this->project_id != 0)) {
                        $db_link->query("update project Set project_id = '$this->new_project_id' where project_id = '$this->project_id' LIMIT 1");
                        if (mysqli_errno == 0) {
                            $this->project_id = $this->new_project_id;
                        }
                        $sql = "update project Set project_name = '".(get_magic_quotes_gpc() ? addslashes(stripslashes($this->project_name)) : addslashes($this->project_name))."'".
                            ", project_description 	= '".(get_magic_quotes_gpc() ? addslashes(stripslashes($this->project_description)) : addslashes($this->project_description))."'".
                            ", materials_needed 	= '".(get_magic_quotes_gpc() ? addslashes(stripslashes($this->materials_needed)) : addslashes($this->materials_needed))."'".
                            ", amount_needed 		= '$this->amount_needed'".
                            ", entered_date 		= ".(empty($this->entered_date) ? "now()" : "'$this->entered_date'").
                            ", grade_level_id 		= '$this->grade_level_id'".
                            ", school_id 		= '$this->school_id'".
                            ", project_status_id 	= '$this->project_status_id'".
                            ", project_type_id 		= '$this->project_type_id'".
                            ", submitted_user_id 	= '$this->submitted_user_id'".
                            ", submitted_date 		= ".(empty($this->submitted_date) ? "NULL" : "'$this->submitted_date'").
                            ", expiration_date 		= ".(empty($this->expiration_date) ? "NULL" : "'$this->expiration_date'").
                            ", required_by_date 	= ".(empty($this->required_by_date) ? "NULL" : "'$this->required_by_date'").
                            ", review_user_id 		= '$this->review_user_id'".
                            ", review_date 		= ".(empty($this->review_date) ? "NULL" : "'$this->review_date'").
                            ", review_notes 		= '".(get_magic_quotes_gpc() ? addslashes(stripslashes($this->review_notes)) : addslashes($this->review_notes))."'".
                            ", funds_dispersed		= '$this->funds_dispersed'".
                            ", funds_dispersed_amount   = '$this->funds_dispersed_amount'".
                            ", funds_dispersed_date     = ".(empty($this->funds_dispersed_date) ? "NULL" : "'$this->funds_dispersed_date'").
                            ", completed_date 		= ".(empty($this->completed_date) ? "NULL" : "'$this->completed_date'").
                            ", date_receipts_received   = ".(empty($this->date_receipts_received) ? "NULL" : "'$this->date_receipts_received'").
                            ", date_thankyous_sent      = ".(empty($this->date_thankyous_sent) ? "NULL" : "'$this->date_thankyous_sent'").
                            ", date_last_updated        = ".(empty($this->date_last_updated) ? "NULL" : "'$this->date_last_updated'").
                            ", date_last_warning        = ".(empty($this->date_last_warning) ? "NULL" : "'$this->date_last_warning'").
                            ", date_status_changed      = ".(empty($this->date_status_changed) ? "NULL" : "'$this->date_status_changed'").
                            ", handling_charge          = '$this->handling_charge'".
                            ", shipping_charge          = '$this->shipping_charge'".
                            ", warning_key              = ".(empty($this->warning_key) ? "NULL" : "'$this->warning_key'").
                            " where project_id          = '$this->project_id' LIMIT 1";
                        if ($db_link->query($sql)) {
                            if (mysqli_errno == 0) {
                                return true;
                            } else {
                                $this->error_message = mysqli_error($db_link)."<br>$sql";
                                return false;
                            }
                        } else {
                            $this->error_message = mysqli_error($db_link)."<br>$sql";
                            return false;
                        }
                    }
                    return true;
                }
            } else {
                $this->error_message = mysqli_error($db_link)."<br>$sql";
                return false;
            }
        }
    } // end save_project

    function add_disbursement($amount, $tran_date) {
        global $db_link;
        if (isset($this->project_id)) {
            $this->funds_dispersed_amount += $amount;
            if ($this->funds_dispersed_amount > 0) {
                $this->funds_dispersed_date = $tran_date;
                $this->funds_dispersed = "Y";
            } else {
                $this->funds_dispersed_date = "";
                $this->funds_dispersed = "N";
            }
            $sql = "update project set funds_dispersed_amount = '".$this->funds_dispersed_amount."'	, funds_dispersed_date = ".($this->funds_dispersed_date == "" ? "NULL" : "'$this->funds_dispersed_date'").", funds_dispersed = '".$this->funds_dispersed."', project_status_id = '".($this->funds_dispersed_amount < $this->amount_needed ? ($this->amount_donated() < $this->amount_needed ? "3" : "4") : "5")."' where project_id = '$this->project_id'";
            if ($db_link->query($sql)) {
                return true;
            } else {
                $this->error_message = mysqli_error($db_link).": $sql";
                return false;
            }
        } else {
            $this->error_message = "No project loaded.";
            return false;
        }
    } // end add_disbursement

    function project_viewed()
    {
        global $db_link;
        if (isset($this->project_id)) {
            if ($db_link->query("update project set view_count = view_count + 1 where project_id = '$this->project_id'")) {
                $this->view_count += 1;
                return true;
            } else {
                $this->error_message = mysqli_error($db_link);
                return false;
            }
        }
    } // end project_viewed

    function project_searched()
    {
        global $db_link;
        if (isset($this->project_id))
        {
            if ($db_link->query("update project set search_count = search_count + 1 where project_id = '$this->project_id'")) {
                $this->view_count += 1;
                return true;
            } else {
                $this->error_message = mysqli_error($db_link);
                return false;
            }
        }
    } // end project_searched

    function load_comments()
    {
        global $db_link;
        $this->comment_list = array();
        if (isset($this->project_id))
        {
            $results = $db_link->query("select project_comment_id from project_comments where project_id = '$this->project_id' order by date_entered");
            while ($row = mysqli_fetch_array($results))
            {
                $comment = new project_comment();
                if ($comment->load_project_comment($row[project_comment_id]))
                {
                    $this->comment_list[] = $comment;
                }
            }
        }
    } // end load_comments

    function comment_count() {
        return count($this->comment_list);
    } // end comment_count

} // end of class project
?>
