<?
class districts
{
	var $district_list;
	var $error_message;

	function __construct() {
		$this->district_list = array();
	}

	function add_district($row) {
		$newdistrict = new district();
		$newdistrict->district_id 	= $row["district_id"];
		$newdistrict->district_name     = $row["district_name"];
		$newdistrict->district_state    = $row["district_state"];
		$newdistrict->contact_name 	= $row["contact_name"];
		$newdistrict->homepage	 	= $row["homepage"];
		$newdistrict->administrator     = $row["administrator"];
		$newdistrict->district_contact 	= $row["district_contact"];
		$newdistrict->district_street	= $row["district_street"];
		$newdistrict->district_street2 	= $row["district_street2"];
		$newdistrict->district_city     = $row["district_city"];
		$newdistrict->district_zip	= $row["district_zip"];
		$newdistrict->district_faxno    = $row["district_faxno"];
		$newdistrict->district_phone    = $row["district_phone"];
		$newdistrict->payment_contact 	= $row["payment_contact"];
		$newdistrict->payment_street	= $row["payment_street"];
		$newdistrict->payment_city 	= $row["payment_city"];
		$newdistrict->payment_state     = $row["payment_state"];
		$newdistrict->payment_zip	= $row["payment_zip"];
		$newdistrict->payment_faxno     = $row["payment_faxno"];
		$newdistrict->payment_phone     = $row["payment_phone"];
		$newdistrict->tax_id		= $row["tax_id"];
		$newdistrict->email		= $row["email"];
		$newdistrict->email_domain	= $row["email_domain"];
		$newdistrict->accept_cc		= $row["accept_cc"];
		$newdistrict->cc_login		= $row["cc_login"];
		$newdistrict->cc_transactionid 	= $row["cc_transactionid"];
		$newdistrict->cc_prefix		= $row["cc_prefix"];
		$newdistrict->cc_currency	= $row["cc_currency"];
		$newdistrict->cc_live		= $row["cc_live"];
		$newdistrict->inactive		= $row["inactive"];
		$newdistrict->receives_funds= $row["receives_funds"];
		$newdistrict->alt_donation_url = $row["alt_donation_url"];
		$newdistrict->funded_email_override = $row["funded_email_override"];
		$newdistrict->funded_email_subject = $row["funded_email_subject"];
		$newdistrict->funded_email_body = $row["funded_email_body"];
		$newdistrict->funded_email_cc = $row["funded_email_cc"];
		$this->district_list[$row["district_id"]] = $newdistrict;
	}

	function load_districts() {
		global $db_link;
		$results = $db_link->query("Select * from district order by district_name");
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_district($row);
		}
		mysqli_free_result($results);
	}

	function load_donation_districts() {
		global $db_link;
		$results = $db_link->query("Select distinct district.* from district left join school on school.district_id = district.district_id left join project on school.school_id = project.school_id where district.inactive <> 'Y' and (project.project_status_id = 3 or district.district_id = 1) order by district.district_name");
		while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
			$this->add_district($row);
		}
		mysqli_free_result($results);
	}

	function district_name($districtid) {
		$district = $this->district_list["$districtid"];
		return $district->district_name;
	}

	function district_administrator($districtid) {
		$district = $this->district_list["$districtid"];
		return $district->administrator;
	}

	function count() {
		return count($this->district_list);
	}

	function find_districts($district_id_, $district_name_, $district_state_, $administrator_, $email_, $email_domain_, $phone_, $tax_id_, $accept_cc_, $cc_login_, $cc_transactionid_, $cc_prefix_, $cc_currency_, $cc_live_, $inactive_, $receives_funds_, $payment_contact_, $payment_street_, $payment_city_, $payment_state_, $payment_zip_, $payment_faxno_, $payment_phone) {
		global $db_link;
		$sql = "Select district.* from district";
		if (!empty($district_id_)) $where = " district.district_id = '$district_id_'";
		if (!empty($district_name_))
			if (empty($where))
				$where = " district.district_name like '%$district_name_%'";
			else
				$where .= " and district.district_name like '%$district_name_%'";
		if (!empty($district_state_))
			if (empty($where))
				$where = " district.district_state = '$district_state_'";
			else
				$where .= " and district.district_state = '$district_state_'";
		if (!empty($administrator_))
			if (empty($where))
				$where = " district.administrator like '%$administrator_%'";
			else
				$where .= " and district.administrator like '%$administrator_%'";
		if (!empty($email_))
			if (empty($where))
				$where = " district.email like '%$email_%'";
			else
				$where .= " and district.email like '%$email_%'";
		if (!empty($email_domain_))
			if (empty($where))
				$where = " district.email_domain like '%$email_domain_%'";
			else
				$where .= " and district.email_domain like '%$email_domain_%'";
		if (!empty($phone_))
			if (empty($where))
				$where = " district.phone like '%$phone_%'";
			else
				$where .= " and district.phone like '%$phone_%'";
		if (!empty($accept_cc_))
			if (empty($where))
				$where = " district.accept_cc = '$accept_cc_'";
			else
				$where .= " and district.accept_cc = '$accept_cc_'";
		if (!empty($cc_login_))
			if (empty($where))
				$where = " district.cc_login like '%$cc_login_%'";
			else
				$where .= " and district.cc_login like '%$cc_login_%'";
		if (!empty($cc_transactionid_))
			if (empty($where))
				$where = " district.cc_transactionid like '%$cc_transactionid_%'";
			else
				$where .= " and district.cc_transactionid like '%$cc_transactionid_%'";
		if (!empty($cc_prefix_))
			if (empty($where))
				$where = " district.cc_prefix like '%$cc_prefix_%'";
			else
				$where .= " and district.cc_prefix like '%$cc_prefix_%'";
		if (!empty($cc_curreny_))
			if (empty($where))
				$where = " district.cc_curreny like '%$cc_curreny_%'";
			else
				$where .= " and district.cc_curreny like '%$cc_curreny_%'";
		if (!empty($cc_live_))
			if (empty($where))
				$where = " district.cc_live like '%$cc_live_%'";
			else
				$where .= " and district.cc_live like '%$cc_live_%'";
		if (!empty($inactive_))
			if (empty($where))
				$where = " district.inactive = '$inactive_'";
			else
				$where .= " and district.inactive = '$inactive_'";
		if (!empty($receives_funds_))
			if (empty($where))
				$where = " district.receives_funds = '$receives_funds_'";
			else
				$where .= " and district.receives_funds = '$receives_funds_'";
		if (!empty($payment_contact_))
			if (empty($where))
				$where = " district.payment_contact like '%$payment_contact_%'";
			else
				$where .= " and district.payment_contact like '%$payment_contact_%'";
		if (!empty($payment_street_))
			if (empty($where))
				$where = " district.payment_street like '%$payment_street_%'";
			else
				$where .= " and district.payment_street like '%$payment_street_%'";
		if (!empty($payment_city_))
			if (empty($where))
				$where = " district.payment_city like '%$payment_city_%'";
			else
				$where .= " and district.payment_city like '%$payment_city_%'";
		if (!empty($payment_state_))
			if (empty($where))
				$where = " district.payment_state like '%$payment_state_%'";
			else
				$where .= " and district.payment_state like '%$payment_state_%'";
		if (!empty($payment_zip_))
			if (empty($where))
				$where = " district.payment_zip like '$payment_zip_%'";
			else
				$where .= " and district.payment_zip like '$payment_zip_%'";
		if (!empty($payment_phone_))
			if (empty($where))
				$where = " district.payment_phone like '%$payment_phone_%'";
			else
				$where .= " and district.payment_phone like '%$payment_phone_%'";
		if (!empty($payment_faxno_))
			if (empty($where))
				$where = " district.payment_faxno like '%$payment_faxno_%'";
			else
				$where .= " and district.payment_faxno like '%$payment_faxno_%'";

		if (!empty($where))
			$where = " where ".$where;
		$sql .= $where." order by district.district_name, district.district_state";
		if ($results = $db_link->query($sql)) {
			while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
				$this->add_district($row);
			}
		}
		mysqli_free_result($results);
	}
}
?>