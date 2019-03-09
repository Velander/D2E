<?
	include_once "../inc/class_cart_item.php";
	include_once "../inc/class_user_friend.php";
	include_once "../inc/class_donation.php";

class user
{
	var $user_id;
	var $unique_id;
	var $login;
	var $password;
	var $first_name;
	var $last_name;
	var $company;
	var $street;
	var $city;
	var $state;
	var $country;
	var $zip;
	var $email;
	var $phone;
	var $fax;
	var $url;
	var $banner_link;
	var $half_banner_link;
	var $type_id;
	var $verified;
	var $setup_date;
	var $newsletter;
	var $ip_address;
	var $opt_date;
	var $referral_firstname;
	var $referral_lastname;
	var $referral_schoolid;
	var $district_id;
	var $cart_item_list;
	var $donation_list;
	var $friend_list;
	var $error_message;

	function user()
	{
		$this->setup_date 		= date("Y-m-d");
		$this->cart_item_list 	= array();
		$this->donation_list 	= array();
		$this->friend_list		= array();
		$this->unique_id 		= md5(uniqid(rand(),1));
		$this->district_id 		= 0;
		$this->referral_schoolid = 0;
	}

	function validate_login($login, $password)
	{
		// Check login
		global $db_link;
		$login = ereg_replace("'", "", ereg_replace("--", "", $login));
		$sql = "Select user_id, password from user where login = '$login'";
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
		$login = ereg_replace("--", "", $login);
		$email = ereg_replace("--", "", $email);
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
		$login = ereg_replace("--", "", $login);
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
		$email = ereg_replace("--", "", $email);
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
		$this->phone 		= $row["phone"];
		$this->fax 			= $row["fax"];
		$this->url 			= $row["url"];
		$this->banner_link	= $row["banner_link"];
		$this->half_banner_link	= $row["half_banner_link"];
		$this->type_id 		= $row["user_type_id"];
		$this->verified 	= $row["verified"];
		$this->newsletter	= $row["newsletter"];
		$this->district_id	= $row["district_id"];
		$this->ip_address	= $row["ip_address"];
		$this->referral_firstname = $row["referral_firstname"];
		$this->referral_lastname = $row["referral_lastname"];
		$this->referral_schoolid = $row["referral_schoolid"];
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
		if (empty($this->user_id)) {
			$sql = "insert user (login, unique_id, setup_date, password, first_name, last_name, company, street, city, state, country";
			$sql .= ", zip, email, phone, fax, url, banner_link, half_banner_link, referral_firstname, referral_lastname, referral_schoolid, user_type_id, verified, newsletter, district_id, opt_date, ip_address) values";
			$sql .= "('".mysqli_escape_string($db_link, $this->login)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->unique_id)."'";
			$sql .= ", now(), '".mysqli_escape_string(addslashes(text_crypt($this->password)))."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->first_name)."', '".mysqli_escape_string($db_link, $this->last_name)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->company)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->street)."', '".mysqli_escape_string($db_link, $this->city)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->state)."', '".mysqli_escape_string($db_link, $this->country)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->zip)."', '".mysqli_escape_string($db_link, $this->email)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->phone)."', '".mysqli_escape_string($db_link, $this->fax)."', '".mysqli_escape_string($db_link, $this->url)."', '".mysqli_escape_string($db_link, $this->banner_link)."', '".mysqli_escape_string($db_link, $this->half_banner_link)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->referral_firstname)."', '".mysqli_escape_string($db_link, $this->referral_lastname)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->referral_schoolid)."'";
			$sql .= ", '$this->type_id', 'N', '$this->newsletter', '$this->district_id', now(), '".$_SERVER["REMOTE_ADDR"]."')";
			$results = $db_link->query($sql);
			if (mysqli_errno()  == 0) {
				$this->user_id = mysqli_insert_id($db_link);
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
			$sql .= ", password = '".mysqli_escape_string(addslashes(text_crypt($this->password)))."', first_name = '".mysqli_escape_string($db_link, $this->first_name)."', last_name = '".mysqli_escape_string($db_link, $this->last_name)."', company = '".mysqli_escape_string($db_link, $this->company)."', street = '".mysqli_escape_string($db_link, $this->street)."', city = '".mysqli_escape_string($db_link, $this->city)."', state = '".mysqli_escape_string($db_link, $this->state)."', country = '".mysqli_escape_string($db_link, $this->country)."', zip = '".mysqli_escape_string($db_link, $this->zip)."', email = '".mysqli_escape_string($db_link, $this->email)."', phone = '".mysqli_escape_string($db_link, $this->phone)."', fax = '".mysqli_escape_string($db_link, $this->fax)."', url = '".mysqli_escape_string($db_link, $this->url)."', banner_link = '".mysqli_escape_string($db_link, $this->banner_link)."', half_banner_link = '".mysqli_escape_string($db_link, $this->half_banner_link)."', user_type_id = '$this->type_id', verified = '$this->verified'";
			$sql .= ", newsletter = '$this->newsletter', district_id = '$this->district_id', referral_firstname = '$this->referral_firstname', referral_lastname = '$this->referral_lastname', referral_schoolid = '$this->referral_schoolid'";
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
			if ($freind->email == $email) {
				$this->error_message = "Friend $email is already in your list.";
				return false;
			}
		}
		$friend = new user_friend();
		$friend->friend_id = "0";
		$friend->email	= $email;
		$friend->name	= $name;
		$friend->include= $include;
		$this->friend_list[] = $friend;
		return true;
	}

	function delete_friend($friendid)
	{
		// Check to see if the friend's email is already in list.
		reset($this->friend_list);
		$new_friend_list = array();
		while(list($friend_id, $friend) = each($this->friend_list)) {
			if ($friend->friend_id == $friendid) {
				$friend->delete_friend();
			} else
				$new_friend_list[] = $friend;
		}
		$this->friend_list = new_friend_list;
		return true;
	}

	function save_friends()
	{
		global $db_link;
		if (!empty($this->user_id)) {
			$new_list = array();
			reset($this->friend_list);
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
				$new_list[$frienditem->friend_id] = $frienditem;
			}
			$this->friend_list = $new_list;
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

	function cart_item_count()
	{
		return count($this->cart_item_list);
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
		$sql = "select donation_id from donation where user_id = '$this->user_id'";
		$results = $db_link->query($sql);
		while (list($donation_id) = mysqli_fetch_row($results)) {
			$donation = new donation();
			if ($donation->load_donation($donation_id))
				$this->donation_list[$donation_id] = $donation;
		}
		mysqli_free_results($results);
	}

}	// end of class user
?>