<?
class district
{
	var $district_id;
	var $district_name;
	var $district_state;
	var $administrator;
	var $contact_name;
	var $homepage;
	var $district_contact;
	var $district_street;
	var $district_street2;
	var $district_city;
	var $district_zip;
	var $district_faxno;
	var $district_phone;
	var $payment_contact;
	var $payment_street;
	var $payment_state;
	var $payment_city;
	var $payment_zip;
	var $payment_faxno;
	var $payment_phone;
	var $tax_id;
	var $email;
	var $email_domain;
	var $accept_cc;
	var $cc_login;
	var $cc_transactionid;
	var $cc_prefix;
	var $cc_currency;
	var $cc_live;
	var $inactive;
	var $receives_funds;
	var $alt_donation_url;
    var $funded_email_override;
	var $funded_email_subject;
	var $funded_email_body;
    var $funded_email_cc;
	var $schools;

	function __construct() {
		$this->schools	= new schools();
	}

	function load_district($district_id) {
		global $db_link;
		if($results = $db_link->query("Select * from district where district_id = '$district_id'"))
		{
			if ($row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
				$this->district_id          = $row["district_id"];
				$this->district_name        = $row["district_name"];
				$this->district_state       = $row["district_state"];
				$this->administrator        = $row["administrator"];
				$this->contact_name         = $row["contact_name"];
				$this->homepage             = $row["homepage"];
				$this->district_contact     = $row["district_contact"];
				$this->district_street      = $row["district_street"];
				$this->district_street2     = $row["district_street2"];
				$this->district_city        = $row["district_city"];
				$this->district_zip         = $row["district_zip"];
				$this->district_faxno       = $row["district_faxno"];
				$this->district_phone       = $row["district_phone"];
				$this->payment_contact      = $row["payment_contact"];
				$this->payment_street       = $row["payment_street"];
				$this->payment_city         = $row["payment_city"];
				$this->payment_state        = $row["payment_state"];
				$this->payment_zip          = $row["payment_zip"];
				$this->payment_faxno        = $row["payment_faxno"];
				$this->payment_phone        = $row["payment_phone"];
				$this->tax_id               = $row["tax_id"];
				$this->email                = $row["email"];
				$this->email_domain         = $row["email_domain"];
				$this->accept_cc            = $row["accept_cc"];
				$this->cc_login             = $row["cc_login"];
				$this->cc_transactionid     = $row["cc_transactionid"];
				$this->cc_prefix            = $row["cc_prefix"];
				$this->cc_currency          = $row["cc_currency"];
				$this->cc_live              = $row["cc_live"];
				$this->inactive             = $row["inactive"];
				$this->receives_funds       = $row["receives_funds"];
				$this->alt_donation_url     = $row["alt_donation_url"];
							$this->funded_email_override= $row["funded_email_override"];
				$this->funded_email_subject = $row["funded_email_subject"];
				$this->funded_email_body    = $row["funded_email_body"];
				$this->funded_email_cc      = $row["funded_email_cc"];
				$this->schools			= new schools();
				$this->schools->load_district_schools($district_id);
				$results->close();
				return true;
			} else {
				return false;
			}
		}
	}

	function save_district()
	{
		global $db_link;
		if (empty($this->district_id)) {
			$sql = "Insert district (district_name, district_state, administrator, contact_name, homepage, email, email_domain, phone, tax_id, accept_cc, cc_login, cc_transactionid";
			$sql .= ", cc_prefix, cc_currency, cc_live, inactive, receives_funds, district_contact, district_street, district_street2, district_city, district_zip, district_faxno, district_phone";
                        $sql .= ", payment_contact, payment_street, payment_city, payment_state, payment_zip, payment_faxno, payment_phone, alt_donation_url, funded_email_subject, funded_email_override, funded_email_body, funded_email_cc) values";
			$sql .= "('".mysqli_escape_string($db_link, $this->district_name)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->district_state)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->administrator)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->contact_name)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->homepage)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->email)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->email_domain)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->phone)."', '".mysqli_escape_string($db_link, $this->tax_id)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->accept_cc)."', '".mysqli_escape_string($db_link, $this->cc_login)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->cc_transactionid)."', '".mysqli_escape_string($db_link, $this->cc_prefix)."'";			$sql .= ", '".mysqli_escape_string($db_link, $this->cc_currency)."', '".mysqli_escape_string($db_link, $this->cc_live)."', '".mysqli_escape_string($db_link, $this->inactive)."', '".mysqli_escape_string($db_link, $this->receives_funds)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->district_contact)."', '".mysqli_escape_string($db_link, $this->district_street)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->district_street2)."', '".mysqli_escape_string($db_link, $this->district_city)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->district_zip)."', '".mysqli_escape_string($db_link, $this->district_faxno)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->district_phone)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->payment_contact)."', '".mysqli_escape_string($db_link, $this->payment_street)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->payment_city)."', '".mysqli_escape_string($db_link, $this->payment_state)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->payment_zip)."', '".mysqli_escape_string($db_link, $this->payment_faxno)."'";
			$sql .= ", '".mysqli_escape_string($db_link, $this->payment_phone)."', '".mysqli_escape_string($db_link, $this->alt_donation_url)."'";
                        $sql .= ", '".mysqli_escape_string($db_link, $this->funded_email_override)."'";
                        $sql .= ", '".mysqli_escape_string($db_link, $this->funded_email_subject)."', '".mysqli_escape_string($db_link, $this->funded_email_body)."'";
                        $sql .= ", '".mysqli_escape_string($db_link, $this->funded_email_cc)."')";
			$results = $db_link->query($sql);
			if (mysqli_errno()  == 0) {
				$this->district_id = mysqli_insert_id($db_link);
				return true;
			} else {
				$this->error_message = mysqli_error($db_link)."<BR>".$sql;
				return false;
			}
		} else {
			$sql = "Update district Set district_name = '".mysqli_escape_string($db_link, $this->district_name)."', district_state = '".mysqli_escape_string($db_link, $this->district_state)."'";
			$sql .= ", contact_name = '".mysqli_escape_string($db_link, $this->contact_name)."', homepage = '".mysqli_escape_string($db_link, $this->homepage)."'";
			$sql .= ", administrator = '".mysqli_escape_string($db_link, $this->administrator)."', email = '".mysqli_escape_string($db_link, $this->email)."'";
                        $sql .= ", email_domain = '".mysqli_escape_string($db_link, $this->email_domain)."', phone = '".mysqli_escape_string($db_link, $this->phone)."', tax_id = '".mysqli_escape_string($db_link, $this->tax_id)."'";
                        $sql .= ", accept_cc = '".mysqli_escape_string($db_link, $this->accept_cc)."', cc_login = '".mysqli_escape_string($db_link, $this->cc_login)."', cc_transactionid = '".mysqli_escape_string($db_link, $this->cc_transactionid)."'";
                        $sql .= ", cc_prefix = '".mysqli_escape_string($db_link, $this->cc_prefix)."', cc_currency = '".mysqli_escape_string($db_link, $this->cc_currency)."', cc_live = '".mysqli_escape_string($db_link, $this->cc_live)."'";
                        $sql .= ", receives_funds = '".mysqli_escape_string($db_link, $this->receives_funds)."', inactive = '".mysqli_escape_string($db_link, $this->inactive)."'";
                        $sql .= ", district_contact = '".mysqli_escape_string($db_link, $this->district_contact)."', district_street = '$this->district_street', district_street2 = '$this->district_street2'";
			$sql .= ", district_city = '$this->district_city', district_zip = '$this->district_zip', district_faxno = '$this->district_faxno', district_phone = '$this->district_phone'";
                        $sql .= ", payment_contact = '".mysqli_escape_string($db_link, $this->payment_contact)."', payment_street = '$this->payment_street', payment_city = '$this->payment_city'";
			$sql .= ", payment_state = '$this->payment_state', payment_zip = '$this->payment_zip', payment_faxno = '$this->payment_faxno', payment_phone = '$this->payment_phone'";
			$sql .= ", alt_donation_url = '$this->alt_donation_url', funded_email_override = '$this->funded_email_override', funded_email_subject = '$this->funded_email_subject'";
			$sql .= ", funded_email_body = '$this->funded_email_body', funded_email_cc = '$this->funded_email_cc'";
			$sql .= " where district_id = '$this->district_id'";
			$results = $db_link->query($sql);
			if (mysqli_errno()  == 0) {
				return true;
			} else {
				$this->error_message = mysqli_error($db_link)."<BR>".$sql;
				return false;
			}
		}
	}

	function delete_district()
	{
		global $db_link;
		if (empty($this->district_id)) {
			$this->error_message = "No district loaded to delete.";
			return false;
		} else {
			if ($this->schools->count() > 0) {
				$this->error_message = "District has schools assigned.";
				return false;
			} else {
				$sql = "Delete from district ";
				$sql .= " where district_id = '$this->district_id'";
				$results = $db_link->query($sql);
				if (mysqli_errno()  == 0) {
					$this->district_id = "";
					return true;
				} else {
					$this->error_message = mysqli_error($db_link)."<BR>".$sql;
					return false;
				}
			}
		}
	}

}	// end of class district
?>
