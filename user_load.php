<?
require "inc/db_inc.php";
require_once "inc/func.php";
require_once "inc/class_user.php";
require_once "inc/class_affiliation.php";
require_once "inc/class_affiliations.php";

$users = $db_link->query("Select * from newuser");
echo "users<BR>";
while ($suser = mysqli_fetch_array($users)) {
	$user = new user();
	if ($user->load_user($suser["user_id"])) {
		echo $user->user_id." on file.<BR>";
	} else {
		$user->user_id = $suser["user_id"];
		$user->login = $suser["login"];
		$user->setup_date = $suser["setup_date"];
		$user->password = $suser["password"];
		$user->first_name = $suser["first_name"];
		$user->last_name = $suser["last_name"];
		$user->company	= $suser["company"];
		$user->street	= $suser["street"];
		$user->city		= $suser["city"];
		$user->state	= $suser["state"];
		$user->zip		= $suser["zip"];
		$user->country	= $suser["country"];
		$user->email	= $suser["email"];
		$user->phone	= $suser["phone"];
		$user->fax		= $suser["fax"];
		$user->url		= $suser["url"];
		$user->user_type_id = $suser["user_type_id"];
		$user->verified	= $suser["verified"];
		$user->newsletter = $suser["newsletter"];
		$user->ip_address = $suser["ip_address"];
		$user->opt_date	= $suser["opt_date"];
		$user->referral_firstname = $suser["referral_firstname"];
		$user->referral_lastname = $suser["referral_lastname"];
		$user->referral_schoolid = $suser["referral_schoolid"];
		$user->district_id = $suser["district_id"];
		$user->banner_link = $suser["banner_link"];
		$user->half_banner_link = $suser["half_banner_link"];
		if ($user->save_user()) {
			echo $user->user_id." saved.<BR>";
			$aff = $db_link->query("select * from newuser_affiliation where user_id = '$user->user_id'");
			if (mysqli_num_rows($aff)) {
				$affils = new affiliations();
				while ($affrow = mysqli_fetch_array($aff)) {
					$affils->new_affiliation($affrow["user_id"], $affrow["school_id"], $affrow["administration_flag"]);
					echo "  Affiliation added for user ".$affrow["user_id"]." school ".$affrow["school_id"]." admin: ".$affrow["administration_flag"]."<BR>";
				}
			}
		} else {
			echo "error saving user ".$user->user_id.": ".$user->error_message."<BR>";
		}
	}
}
mysqli_free_result($users);
?>