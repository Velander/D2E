<?
include_once "inc/class_cart_item.php";
include_once "inc/class_user_friend.php";
include_once "inc/class_donation.php";

class user
{
	public $user_id;
	public $unique_id;
	public $login;
	public $password;
	public $first_name;
	public $last_name;
	public $company;
	public $street;
	public $city;
	public $state;
	public $country;
	public $zip;
	public $email;
	public $bad_email;
	public $phone;
	public $fax;
	public $url;
	public $banner_link;
	public $half_banner_link;
	public $type_id;
	public $verified;
	public $setup_date;
	public $newsletter;
	public $email_verified;
	public $ip_address;
	public $opt_date;
	public $referral_firstname;
	public $referral_lastname;
	public $referral_schoolid;
	public $district_id;
	public $allow_matching;
	public $direct_donations;
	public $notes;
	public $cart_item_list;
	public $donation_list;
	public $friend_list;
	public $matching_list;
	public $error_message;

	function __construct()
	{
		$this->setup_date 		= date("Y-m-d");
		$this->cart_item_list 	= array();
		$this->donation_list 	= array();
		$this->friend_list		= array();
		$this->matching_list	= array();
		$this->unique_id 		= md5(uniqid(rand(),1));
		$this->district_id 		= 0;
		$this->allow_matching	= "N";
		$this->direct_donations = 'N';
		$this->referral_schoolid = 0;
	}

	function validate_login($login, $password)
	{
		// Check login
		global $db_link;
		$login = preg_replace("/'/", NULL, preg_replace("/\-/", NULL, $login));
		$sql = "Select user_id, password from user where login = '$login' or email = '$login'";
		if ($results = $db_link->query($sql)) {
			if (mysqli_num_rows($results) == 0) {
				if ($results = $db_link->query("Select user_id from user where email = '$login'")) {
					if (mysqli_num_rows($results) == 0) {
						$db_link->query("insert login_log (login_date, invalid_id, valid_login) values (now(), '$login', 'N')");
						$this->unique_id = md5(uniqid(rand(),1));
						$db_link->query("update user set unique_id = '".addslashes($this->unique_id)."' where user_id = '".$row["user_id"]."'");
						return false;
					}
				} else {
					$this->error_message = mysqli_error($db_link)."<BR>";
					return false;
				}
			}
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
				if (($password == stripslashes(text_decrypt($row["password"]))) || ($password == $row["password"])) {
					$this->load_user($row["user_id"]);
					$results->close();
					$db_link->query("insert login_log (user_id, login_date, valid_login) values ('$this->user_id', now(), 'Y')");
					$this->unique_id = md5(uniqid(rand(),1));
					$db_link->query("update user set unique_id = '".addslashes($this->unique_id)."' where user_id = '".$row["user_id"]."'");
					return true;
				}
			}
			$results->close();
			$db_link->query("insert login_log (login_date, invalid_id, valid_login) values (now(), '$login', 'N')");
			return false;
		} else {
			$this->error_message = mysqli_error($db_link)."<BR>";
			return false;
		}
	}

	function lookup_login($login, $email)
	{
		// Check login
		global $db_link;
		$login = preg_replace("/\-/", NULL, $login);
		$email = preg_replace("/\-/", NULL, $email);
		$sql = "Select user_id from user where ".((!empty($login)) ? "login = '$login'":"").(!empty($email) ? (!empty($login) ? " and ":""."email = '$email'") : "");
		if ($results = $db_link->query($sql)) {
			if (mysqli_num_rows($results) == 0) {
				$this->error_message = mysqli_error($db_link)."<BR>";
				return false;
			} else {
				while (list($userid) = mysqli_fetch_row($results))
					$userids[] = $userid;
			}
			$results->close();
			return $userids;
		} else {
			$this->error_message = mysqli_error($db_link)."<BR>";
			return false;
		}
	}

	function login_exists($login)
	{
		// Check login
		global $db_link;
		$login = preg_replace("/\-/", NULL, $login);
		$results = $db_link->query("Select user_id from user where login = '$login'");
		if (mysqli_num_rows($results) == 0) {
			$results->close();
			return false;
		} else {
			$results->close();
			return true;
		}
	}

	function email_exists($email)
	{
		// Check login
		global $db_link;
		$email = preg_replace("/\-/", NULL, $email);
		$results = $db_link->query("Select user_id from user where email = '$email'");
		if (mysqli_num_rows($results) == 0) {
			$results->close();
			return false;
		} else {
			$results->close();
			return true;
		}
	}

	function load_unique_id($unique_id)
	{
		// Check login
		global $db_link;
		if (empty($unique_id))
			return false;
		else {
			$results = $db_link->query("Select user_id from user where unique_id = '$unique_id'");
			if (mysqli_num_rows($results) == 0) {
				$results->close();
				return false;
			} else {
				$row = mysqli_fetch_array($results, MYSQLI_ASSOC);
				$this->assign_user($row);
				setcookie ("User_ID", $this->user_id, time()+3600);  /* expire in 1 hour */
				$results->close();
				return true;
			}
		}
	}

	function assign_user($row)
	{
		$this->user_id 		= $row["user_id"];
		$this->unique_id	= $row["unique_id"];
		$this->login 		= $row["login"];
		$this->setup_date	= (is_null($row["setup_date"]) ? "" : $row["setup_date"]);
		$this->password 	= stripslashes(text_decrypt($row["password"]));
		$this->first_name 	= $row["first_name"];
		$this->last_name 	= $row["last_name"];
		$this->company 		= $row["company"];
		$this->street 		= $row["street"];
		$this->city			= $row["city"];
		$this->state 		= $row["state"];
		$this->country		= $row["country"];
		$this->zip 			= $row["zip"];
		$this->email 		= $row["email"];
		$this->bad_email    = $row["bad_email"];
		$this->phone 		= $row["phone"];
		$this->fax 			= $row["fax"];
		$this->url 			= $row["url"];
		$this->banner_link	= $row["banner_link"];
		$this->half_banner_link	= $row["half_banner_link"];
		$this->type_id 		= $row["user_type_id"];
		$this->verified 	= $row["verified"];
		$this->newsletter	= $row["newsletter"];
		$this->email_verified = $row["email_verified"];
		$this->district_id	= $row["district_id"];
		$this->allow_matching = $row["allow_matching"];
		$this->direct_donations = $row["direct_donations"];
		$this->ip_address	= $row["ip_address"];
		$this->referral_firstname = $row["referral_firstname"];
		$this->referral_lastname = $row["referral_lastname"];
		$this->referral_schoolid = $row["referral_schoolid"];
		$this->notes			= $row["notes"];
		$this->opt_date		= (is_null($row["opt_date"]) ? "" : $row["opt_date"]);
		$this->load_cart_items();
		$this->load_friends();
	}

	function load_user($userid)
	{
		global $db_link;
		$results = $db_link->query("Select * from user where user_id = '$userid'");
		if (mysqli_num_rows($results) == 0) {
			$this->error_message = mysqli_error($db_link)."<BR>";
			return false;
		}
		$row = mysqli_fetch_array($results, MYSQLI_ASSOC);
		#Reset the unique ID on every login.
		if (empty($row["unique_id"])) {
			$row["unique_id"] = md5(uniqid(rand(),1));
			$db_link->query("update user set unique_id = '".addslashes($row["unique_id"])."' where user_id = '".$row["user_id"]."'");
		}
		$this->assign_user($row);
		$results->close();
		return true;
	}

	function save_cart_items()
	{
		global $db_link;
		if (!empty($this->user_id)) {
			$sql = "Delete from user_cart_item where user_id = '$this->user_id'";
			if ($db_link->query($sql) == 0) {
				$this->error_message = mysqli_error($db_link)."<BR>$sql";
				return false;
			} else {
				reset($this->cart_item_list);
				while (list($cartid, $cartitem) = each($this->cart_item_list)) {
					$sql = "insert user_cart_item(user_id, project_id, donation_amount) values ('".$this->user_id."','".$cartitem->project_id."','".$cartitem->donation_amount."')";
					if (!$db_link->query($sql)) {
						$this->error_message = mysqli_error($db_link)."<BR>$sql";
						return false;
					}
				}
				return true;
			}
		} else {
			$this->error_message = "No User Loaded.";
			return false;
		}
	}

	function save_user()
	{
		global $db_link;
		if (empty($this->unique_id))
			$this->unique_id = md5(uniqid(rand(),1));
		if (!$this->user_id) {
			$newuser = true;
		} else {
			$userexists = new user();
			if (!$userexists->load_user($this->user_id))
				$newuser = true;
			else
				$newuser = false;
		}
		if ($newuser) {
			$sql = "insert user (";
			if ($this->user_id) {
				$sql .= "user_id, ";
			}
			$sql .= "login, unique_id, setup_date, password, first_name, last_name, company, street, city, state, country";
			$sql .= ", zip, email, bad_email, phone, fax, url, banner_link, half_banner_link, referral_firstname, referral_lastname, referral_schoolid, notes, user_type_id, verified, newsletter, email_verified, district_id, allow_matching, direct_donations, opt_date, ip_address) values (";
			if ($this->user_id) {
				$sql .= "'$this->user_id', ";
			}
			$sql .= "'".mysqli_escape_string($db_link, $this->login)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->unique_id)."'";
			$sql .= ", now(), '".mysqli_escape_string(addslashes(text_crypt($this->password)))."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->first_name)."', '".mysqli_escape_string($db_link, $this->last_name)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->company)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->street)."', '".mysqli_escape_string($db_link, $this->city)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->state)."', '".mysqli_escape_string($db_link, $this->country)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->zip)."', '".mysqli_escape_string($db_link, $this->email)."', '".mysqli_escape_string($db_link, $this->bad_email)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->phone)."', '".mysqli_escape_string($db_link, $this->fax)."', '".mysqli_escape_string($db_link, $this->url)."', '".mysqli_escape_string($db_link, $this->banner_link)."', '".mysqli_escape_string($db_link, $this->half_banner_link)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->referral_firstname)."', '".mysqli_escape_string($db_link, $this->referral_lastname)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->referral_schoolid)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->notes)."'";
			$sql .= ", '$this->type_id', 'N', '$this->newsletter', '$this->email_verified','$this->district_id', '$this->allow_matching', '$this->direct_donations', now(), '".$_SERVER["REMOTE_ADDR"]."')";
			$results = $db_link->query($sql);
			if (mysqli_errno()  == 0) {
				if (!$this->user_id) {
					$this->user_id = mysqli_insert_id($db_link);
				}
				if ($this->save_cart_items())
					return true;
				else
					return false;
			} else {
				$this->error_message = mysqli_error($db_link)."<BR>".$sql;
				return false;
			}
		} else {
			$sql = "update user Set login = '".mysqli_escape_string($db_link, $this->login)."', unique_id = '".mysqli_escape_string($db_link, $this->unique_id)."'";
			$sql .= ", password = '".mysqli_escape_string(addslashes(text_crypt($this->password)))."'";
			$sql .= ", first_name = '".mysqli_escape_string($db_link, $this->first_name)."', last_name = '".mysqli_escape_string($db_link, $this->last_name)."'";
			$sql .= ", company = '".mysqli_escape_string($db_link, $this->company)."', street = '".mysqli_escape_string($db_link, $this->street)."'";
			$sql .= ", city = '".mysqli_escape_string($db_link, $this->city)."', state = '".mysqli_escape_string($db_link, $this->state)."'";
			$sql .= ", country = '".mysqli_escape_string($db_link, $this->country)."', zip = '".mysqli_escape_string($db_link, $this->zip)."'";
			$sql .= ", email = '".mysqli_escape_string($db_link, $this->email)."', bad_email = '".mysqli_escape_string($db_link, $this->bad_email)."'";
			$sql .= ", phone = '".mysqli_escape_string($db_link, $this->phone)."'";
			$sql .= ", fax = '".mysqli_escape_string($db_link, $this->fax)."', url = '".mysqli_escape_string($db_link, $this->url)."'";
			$sql .= ", banner_link = '".mysqli_escape_string($db_link, $this->banner_link)."', half_banner_link = '".mysqli_escape_string($db_link, $this->half_banner_link)."'";
			$sql .= ", user_type_id = '$this->type_id', verified = '$this->verified'";
			$sql .= ", newsletter = '$this->newsletter', email_verified = '$this->email_verified', district_id = '$this->district_id', allow_matching = '$this->allow_matching'";
			$sql .= ", direct_donations = '$this->direct_donations', referral_firstname = '$this->referral_firstname'";
			$sql .= ", referral_lastname = '$this->referral_lastname', referral_schoolid = '$this->referral_schoolid', notes = '".mysqli_escape_string($db_link, $this->notes)."'";
			$results = $db_link->query("select newsletter from user where user_id = '$this->user_id'");
			list($prev_newsletter) = mysqli_fetch_row($results);
			$results->close();
			if ($prev_newsletter != $this->newsletter)
				$sql .= ", opt_date = now(), ip_address = '".$_SERVER["REMOTE_ADDR"]."'";
			$sql .= " where user_id = '$this->user_id'";
			$results = $db_link->query($sql);
			if (mysqli_errno()  == 0) {
				if ($this->save_cart_items())
					if ($this->save_friends())
						return true;
					else
						return false;
				else
					return false;
			} else {
				$this->error_message = mysqli_error($db_link)."<BR>".$sql;
				return false;
			}
		}
	}

	function load_friends()
	{
		global $db_link;
		if (!empty($this->user_id)) {
			$this->friend_list = array();
			$results = $db_link->query("Select friend_id, email, name, include from user_friend where user_id = ".$this->user_id." order by name");
			while (list($friend_id, $email, $name, $include) = mysqli_fetch_row($results)) {
				$friend = new user_friend();
				$friend->friend_id = $friend_id;
				$friend->name = $name;
				$friend->email = $email;
				$friend->include = $include;
				$this->friend_list[] = $friend;
			}
			$results->close();
		} else {
			$this->error_message = "No Friends Loaded.";
			return false;
		}
	}

	function update_friend($friendid, $email, $name, $include)
	{
		// Check to see if the friend's email is already in list.
		global $db_link;
		if ($friendid == "0") {
			$this->add_friend($email, $name, $include);
		} else {
			reset($this->friend_list);
			while(list($friend_id, $friend) = each($this->friend_list)) {
				if ($friend->friend_id == $friendid) {
					$friend->email	= $email;
					$friend->name	= $name;
					$friend->include= $include;
					$this->friend_list[] = $friend;
					if ($this->save_friends()) {
						return true;
					} else {
						return false;
					}
				}
			}
		}
		return false;
	}

	function add_friend($email, $name, $include)
	{
		// Check to see if the friend's email is already in list.
		reset($this->friend_list);
		while(list($friend_id, $friend) = each($this->friend_list)) {
			if ($friend->email == $email) {
				$this->error_message = "Friend $email is already in your list.";
				return false;
			}
		}
		$friend = new user_friend();
		$friend->friend_id  = "0";
		$friend->email	= $email;
		$friend->name	= $name;
		$friend->include    = $include;
		$this->friend_list[] = $friend;
		return true;
	}

	function delete_friend($friendid)
	{
		// Check to see if the friend's email is already in list.
		reset($this->friend_list);
		$status = false;
		$this->error_message = "Friend $friendid not found.".count($this->friend_list);
		while(list($friend_id, $friend) = each($this->friend_list)) {
			if ($friend->friend_id == $friendid) {
				if (!$friend->delete_friend()) {
					$this->error_message = $friend->error_message;
					$status = false;
					break;
				} else {
					$this->error_message = "";
					$status = true;
					break;
				}
			}
		}
		$this->load_friends();
		return $status;
	}

	function save_friends()
	{
		global $db_link;
		if (!empty($this->user_id)) {
			reset($this->friend_list);
			$this->error_message = "";
			while (list($friendid, $frienditem) = each($this->friend_list)) {
				if ($frienditem->friend_id == "0") {
					$sql = "insert user_friend (user_id, email, name, include) values ('".$this->user_id."','".$frienditem->email."','".$frienditem->name."','".$frienditem->include."')";
					if (!$db_link->query($sql)) {
						$this->error_message .= mysqli_error($db_link)."<BR>$sql<BR>";
					} else {
						$frienditem->friend_id = mysqli_insert_id($db_link);
					}
				} else {
					$sql = "update user_friend set email = '".$frienditem->email."', name = '".$frienditem->name."', include = '".$frienditem->include."' where user_id = '".$this->user_id."' and friend_id = '".$frienditem->friend_id."'";
					if (!$db_link->query($sql)) {
						$this->error_message .= mysqli_error($db_link)."<BR>$sql<BR>";
					}
				}
			}

			$this->load_friends();
			if ($this->error_message) {
				return false;
			} else {
				return true;
			}
		} else {
			$this->error_message = "No User Loaded.";
			return false;
		}
	}

	function friend_send_count()
	{
		$count = 0;
		reset($this->friend_list);
		while (list($friendid, $frienditem) = each($this->friend_list)) {
			if ($frienditem->include == "Y")
				$count += 1;
		}
		return $count;
	}

	function add_cart_item($project_id, $donation_amount)
	{
		reset($this->cart_item_list);
		while (list($cartid, $cartitem) = each($this->cart_item_list)) {
			if ($cartid == $projectid) {
				$this->error_message = "Project $projectid is already in your cart.";
				return false;
			}
		}
		$cartitem = new cart_item();
		$cartitem->project_id 		= $project_id;
		$cartitem->donation_amount 	= $donation_amount;
		$this->cart_item_list[$project_id] = $cartitem;
		if ($this->save_cart_items()) {
			return true;
		} else {
			return false;
		}
	}

	function change_cart_item($projectid, $donation_amount, $amount_needed)
	{
		global $db_link;
		reset($this->cart_item_list);
		while (list($cartid, $cartitem) = each($this->cart_item_list)) {
			if ($cartid == $projectid) {
				if ($donation_amount <= 0) {
					$db_link->query("delete from user_cart_item where project_id = '$projectid' and user_id = '$this->user_id'");
				} else {
					if ($amount_needed < $donation_amount) {
						$db_link->query("update user_cart_item set donation_amount = '$amount_needed' where project_id = '$projectid' and user_id = '$this->user_id'");
					} else {
						$db_link->query("update user_cart_item set donation_amount = '$donation_amount' where project_id = '$projectid' and user_id = '$this->user_id'");
					}
				}
			}
		}
	}

	function remove_cart_item($remove_id)
	{
		global $db_link;
		reset($this->cart_item_list);
		while (list($cartid, $cartitem) = each($this->cart_item_list)) {
			if ($cartid == $remove_id) {
				$db_link->query("delete from user_cart_item where project_id = '$cartid' and user_id = '$this->user_id'");
			}
		}
	}

	function clear_cart()
	{
		global $db_link;
		reset($this->cart_item_list);
		while (list($cartid, $cartitem) = each($this->cart_item_list)) {
			$db_link->query("delete from user_cart_item where project_id = '$cartid' and user_id = '$this->user_id'");
		}
		$this->cart_item_list = array();
	}

	function cart_item_count()
	{
		return count($this->cart_item_list);
	}

	function last_login()
	{
		global $db_link;
		$lastlogin = "";
		if (!empty($this->user_id)) {
			$results = $db_link->query("Select max(login_date) from login_log where user_id = '".$this->user_id."'");
			list($lastlogin) = mysqli_fetch_row($results);
		}
		return $lastlogin;
	}

	function cart_item_total()
	{
		$total = 0;
		reset($this->cart_item_list);
		while (list($cartid, $cartitem) = each($this->cart_item_list)) {
			$total += $cartitem->donation_amount;
		}
		return $total;
	}

	function load_cart_items()
	{
		global $db_link;
		if (!empty($this->user_id)) {
			$results = $db_link->query("Select project_id, donation_amount from user_cart_item where user_id = ".$this->user_id." order by project_id");
			while (list($project_id, $donation_amount) = mysqli_fetch_row($results)) {
				$this->add_cart_item($project_id, $donation_amount);
			}
			$results->close();
		} else {
			$this->error_message = "No User Loaded.";
			return false;
		}
	}

	function delete_cart_items()
	{
		global $db_link;
		if (!empty($this->user_id)) {
			$sql = "Delete from user_cart_item where user_id = '$this->user_id'";
			if ($db_link->query($sql) == 0) {
				$this->error_message = mysqli_error($db_link)."<BR>$sql";
				return false;
			} else {
				$this->cart_item_list = array();
				return true;
			}
		} else {
			return true;
		}
	}

	function delete_user()
	{
		global $db_link;
		if (!empty($this->user_id)) {
			if ($this->delete_cart_items()) {
				$sql = "Delete from user where user_id = '$this->user_id'";
				if ($db_link->query($sql) == 0) {
					$this->error_message = mysqli_error($db_link)."<BR>$sql";
					return false;
				} else {
					unset ($this->user_id);
					return true;
				}
			} else {
				$this->error_message = "Unable to delete cart items.";
				return false;
			}
		} else {
			$this->error_message = "No User Loaded.";
			return false;
		}
	}

	function load_donation_list()
	{
		global $db_link;
		$this->donation_list = array();
		$sql = "select donation_id from donation where user_id = '$this->user_id' order by donation_date";
		$results = $db_link->query($sql);
		while (list($donation_id) = mysqli_fetch_row($results)) {
			$donation = new donation();
			if ($donation->load_donation($donation_id))
				$this->donation_list[$donation_id] = $donation;
		}
	}

	function load_matching_list()
	{
		global $db_link;
		$this->matching_list = array();
		$sql = "select matching_id from matching where user_id = '$this->user_id' order by date_created";
		$results = $db_link->query($sql);
		while (list($matching_id) = mysqli_fetch_row($results)) {
			$matching = new matching();
			if ($matching->load_matching($matching_id))
				$this->matching_list[$matching_id] = $matching;
		}
	}

}	// end of class user
?>