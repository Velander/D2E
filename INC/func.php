<?
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| X-Cart                                                                      |
| Copyright (c) 2001-2003 Ruslan R. Fazliev <rrf@rrf.ru>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION. THE AGREEMENT TEXT IS ALSO AVAILABLE  |
| AT THE FOLLOWING URL: http://www.x-cart.com/license.php                     |
|                                                                             |
| THIS  AGREEMENT  EXPRESSES  THE  TERMS  AND CONDITIONS ON WHICH YOU MAY USE |
| THIS SOFTWARE   PROGRAM   AND  ASSOCIATED  DOCUMENTATION   THAT  RUSLAN  R. |
| FAZLIEV (hereinafter  referred to as "THE AUTHOR") IS FURNISHING  OR MAKING |
| AVAILABLE TO YOU WITH  THIS  AGREEMENT  (COLLECTIVELY,  THE  "SOFTWARE").   |
| PLEASE   REVIEW   THE  TERMS  AND   CONDITIONS  OF  THIS  LICENSE AGREEMENT |
| CAREFULLY   BEFORE   INSTALLING   OR  USING  THE  SOFTWARE.  BY INSTALLING, |
| COPYING   OR   OTHERWISE   USING   THE   SOFTWARE,  YOU  AND  YOUR  COMPANY |
| (COLLECTIVELY,  "YOU")  ARE  ACCEPTING  AND AGREEING  TO  THE TERMS OF THIS |
| LICENSE   AGREEMENT.   IF  YOU    ARE  NOT  WILLING   TO  BE  BOUND BY THIS |
| AGREEMENT, DO  NOT INSTALL OR USE THE SOFTWARE.  VARIOUS   COPYRIGHTS   AND |
| OTHER   INTELLECTUAL   PROPERTY   RIGHTS    PROTECT   THE   SOFTWARE.  THIS |
| AGREEMENT IS A LICENSE AGREEMENT THAT GIVES  YOU  LIMITED  RIGHTS   TO  USE |
| THE  SOFTWARE   AND  NOT  AN  AGREEMENT  FOR SALE OR FOR  TRANSFER OF TITLE.|
| THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY GRANTED BY THIS AGREEMENT.      |
|                                                                             |
| The Initial Developer of the Original Code is Ruslan R. Fazliev             |
| Portions created by Ruslan R. Fazliev are Copyright (C) 2001-2003           |
| Ruslan R. Fazliev. All Rights Reserved.                                     |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

#
# $Id: func.php,v 1.116.2.28 2003/09/09 07:08:46 mclap Exp $
#

#
# Database abstract layer functions
#
function db_connect($sql_host, $sql_user, $sql_password) {
        return mysql_connect($sql_host, $sql_user, $sql_password);
}

function db_select_db($db_link, $sql_db) {
        return $db_link->select_db($sql_db) || die("Could not connect to SQL db");
}

function db_query($db_link, $query) {
		global $debug_mode;
		global $mysql_autorepair;

		$result = $db_link->query($query);
		#
		# Auto repair
		#
		if( !$result && $mysql_autorepair && preg_match("/'(\S+)\.MYI/",mysql_error(), $m) ){
			$stm = "REPAIR TABLE $m[1]";
			error_log("Repairing table $m[1]", 0);
			if ($debug_mode == 1 || $debug_mode == 3) {
				echo "<B><FONT COLOR=DARKRED>Repairing table $m[1]...</FONT></B>$mysql_error<BR>";
				flush();
			}
			$result = $db_link->query($query);
		}
		if (db_error($result, $query) && $debug_mode==1)
			exit;
		return $result;
}

#
# Use this function to load code of functions on demand (include/func/func.*.php)
#
function x_load() {
	global $xcart_dir;

	$names = func_get_args();
	foreach ($names as $n) {
		$n = str_replace("..", "", $n);
		$f = $xcart_dir."/include/func/func.$n.php";

		if (file_exists($f)) {
			require_once $f;
		}
	}
}

#
# This function selects which https module to use
#
function test_active_bouncer($force=false) {
	global $config;
	global $var_dirs;
	static $module_active = null;

	if (!$force && !is_null($module_active))
		return $module_active;

	$bouncers = array ('libcurl', 'curl', 'openssl', 'ssleay', 'httpscli');

	if ($config["General"]["httpsmod"])
		array_unshift($bouncers, $config["General"]["httpsmod"]);

	$result = false;
	foreach ($bouncers as $k=>$bouncer ){
		$fn = "test_$bouncer";
		if (function_exists($fn) && $fn()) {
			$result = $bouncer;
			break;
		}
	}

	$old_module = false;
	$data_file = $var_dirs["log"]."/data.httpsmodule.php";
	if (file_exists($data_file)) {
		ob_start();
		readfile($data_file);
		$old_module = ob_get_contents();
		ob_end_clean();
		$old_module = substr($old_module, strlen(X_LOG_SIGNATURE));
	}

	if (!empty($old_module) && strcmp($old_module, $result)) {
		x_log_add('ENV', "HTTPS module is changed to: $result (was: $old_module)");
	}

	if ($old_module === false || strcmp($old_module, $result)) {
		$_tmp_fp = @fopen($data_file, "wb");
		if ($_tmp_fp !== false) {
			@fwrite($_tmp_fp, X_LOG_SIGNATURE.$result);
			@fclose($_tmp_fp);
		}
	}


	$module_active = $result;

	return $result;
}

function db_result($result, $offset) {
        return $result->result($result, $offset);
}

function db_fetch_row($result) {
        return $result->fetch_row();
}

function db_fetch_array($result, $flag=MYSQLI_ASSOC) {
    return $result->fetch_array($flag);
}

#
# Executable lookup
# Check prefered file first, then do search in PATH environment variable.
# Will return false if no executable is found.
#
function func_find_executable($filename, $prefered_file = false) {
	global $xcart_dir;

	if (ini_get("open_basedir") != "" && !empty($prefered_file))
		return $prefered_file;

	$path_sep = X_DEF_OS_WINDOWS ? ';' : ':';

	if ($prefered_file) {
		if (!X_DEF_OS_WINDOWS && func_is_executable($prefered_file))
			return $prefered_file;

		if (X_DEF_OS_WINDOWS) {
			$info = pathinfo($prefered_file);
			if (empty($info["extension"])) $prefered_file .= ".exe";
			if (func_is_executable($prefered_file)) return $prefered_file;
		}
	}

	$directories = split($path_sep, getenv("PATH"));
	array_unshift($directories, $xcart_dir.DIRECTORY_SEPARATOR."payment");

	foreach ($directories as $dir){
		$file = $dir.DIRECTORY_SEPARATOR.$filename;
		if (!X_DEF_OS_WINDOWS && func_is_executable($file) ) return $file;
		if (X_DEF_OS_WINDOWS && func_is_executable($file.".exe") ) return $file.".exe";
	}

	return false;
}

#
# This function creates a temporary file and store some data in it
# It will return filename if successful and "false" if it fails.
#
function func_temp_store($data) {
	global $file_temp_dir;
	$tmpfile = @tempnam($file_temp_dir,"xctmp");
	if (empty($tmpfile)) return false;

	$fp = @fopen($tmpfile,"w");
	if (!$fp) {
		@unlink($tmpfile);
		return false;
	}

	fwrite($fp,$data);
	fclose($fp);

	return $tmpfile;
}

function db_free_result($result) {
        $result->close();
}

function db_num_rows($result) {
       return mysql_num_rows($result);
}

function db_insert_id($db_link) {
       return $db_link->insert_id;
}

function db_affected_rows($db_link) {
	return $db_link->affected_rows();
}

function db_error($mysql_result, $query) {
	global $debug_mode, $error_file_size_limit, $error_file_path, $PHP_SELF;

	if ($mysql_result)
		return false;
	else {
		$mysql_error = mysql_errno()." : ".mysql_error();
		if ($debug_mode == 1 || $debug_mode == 3) {
			echo "<B><FONT COLOR=DARKRED>INVALID SQL: </FONT></B>$mysql_error<BR>";
			echo "<B><FONT COLOR=DARKRED>SQL QUERY FAILURE:</FONT></B> $query <BR>";
			flush();
		}
		if ($debug_mode == 2 || $debug_mode == 3) {
			$filename = $error_file_path."/x-errors_sql.txt";
			if ($error_file_size_limit!=0 && @filesize($filename)>$error_file_size_limit*1024)
				@unlink($filename);
			if ($fp = @fopen($filename, "a+")) {
				$err_str = date("[d-M-Y H:i:s]")." SQL error: $PHP_SELF\n".$query."\n".$mysql_error;
				$err_str .= "\n-------------------------------------------------\n";
				fwrite($fp, $err_str);
				fclose($fp);
			}
		}
	}
	return true;
}

#
# Execute mysql query adn store result into associative array with
# column names as keys...
#
function func_query($db_link, $query) {

        #$result=array();
        if ($p_result = db_query($db_link, $query)) {
 	       while($arr = db_fetch_array($p_result))
				$result[]=$arr;
				db_free_result($p_result);
        }

        return $result;

}

#
# Execute mysql query and store result into associative array with
# column names as keys and then return first element of this array
# If array is empty return array().
#
function func_query_first($db_link, $query) {

		if ($p_result = db_query($db_link, $query)) {
			$result = db_fetch_array($p_result);
			db_free_result($p_result);
        }
        return is_array($result)?$result:array();

}

#
# This function replaced standard PHP function header("Location...")
#
function func_header_location($location) {

	global $XCART_SESSION_NAME, $XCARTSESSID, $_COOKIE;
	global $use_sessions_type;

	x_session_save();

	if ($use_sessions_type < 3) {
		session_write_close();
	}

	if (!empty($XCARTSESSID) && !isset($_COOKIE[$XCART_SESSION_NAME]) && !eregi("$XCART_SESSION_NAME=", $location)) {
		$location .= ((strpos($location, '?') != false)?'&':'?')."$XCART_SESSION_NAME=".$XCARTSESSID;
	}

	$header_location = (@preg_match("/Microsoft|WebSTAR|Xitami/", getenv("SERVER_SOFTWARE")) ? "Refresh: 0; URL=" : "Location: ");
	header($header_location.$location);
	exit();

}

#
# Get image size abstract function
#
function func_get_image_size($filename) {
   list($width, $height, $type) = getimagesize($filename);
    switch($type) {
        case "1": $type = "image/gif";
                  break;
        case "2": $type = "image/pjpeg";
                  break;
        case "3": $type = "image/png";
                  break;
        default:  $type = "";
    }
    return array(filesize($filename),$width,$height,$type);
}

#
# Determine that $userfile is image file with non zero size
#
function func_is_image_userfile($userfile, $userfile_size, $userfile_type) {
	if (($userfile != "none") && ($userfile != "") && ($userfile_size > 0) && (substr($userfile_type, 0, 6) == 'image/'))
		return true;
	else
		return false;
}

#
# Send mail abstract function
# $from - from/reply-to address
#
function func_send_mail($to, $subject_template, $body_template, $from, $to_admin, $crypted=false) {
        global $mail_smarty, $sql_tbl;
        global $config, $customer_language, $admin_language;
		global $current_language, $store_language;


        if ($to_admin or !$customer_language) {
				if ($current_language)
					$charset = array_pop(func_query_first ("SELECT charset FROM $sql_tbl[countries] WHERE code='$current_language'"));
				else
					$charset = array_pop(func_query_first ("SELECT charset FROM $sql_tbl[countries] WHERE code='".$config["default_admin_language"]."'"));
                $mail_smarty->assign ("lng", $admin_language);
		}
        else {
				$charset = array_pop(func_query_first ("SELECT charset FROM $sql_tbl[countries] WHERE code='$store_language'"));
                $mail_smarty->assign ("lng", $customer_language);
		}
        $mail_smarty->assign ("config", $config);

    $mail_message = $mail_smarty->fetch("$body_template");
    $mail_subject = chop($mail_smarty->fetch("$subject_template"));

        if (($config["PGP"]["enable_pgp"]=="Y") and ($crypted)) {
                $mail_message = func_pgp_encrypt ($mail_message);
        }

        if (stristr(PHP_OS, "win")) {
            $mail_message=str_replace("\n","\r\n",$mail_message);
            $lend = "\r\n";
        }
        else
            $lend = "\n";


		$headers = "From: $from".$lend."Reply-to: $from".$lend."X-Mailer: PHP/".phpversion().$lend;

		if ($config["Email"]["html_mail"] == "Y")
			$headers .= "Content-Type: text/html; charset: ".$charset.$lend;
		else
			$headers .= "Content-Type: text/plain; charset: ".$charset.$lend;

		mail($to,$mail_subject,$mail_message,$headers, "-f$from");
}

function func_send_simple_mail($to, $subject, $body, $from) {
		global  $config;

        if (stristr(PHP_OS, "win")) {
            $body=str_replace("\n","\r\n",$body);
            $lend = "\r\n";
        }
        else
            $lend = "\n";


		$headers = "From: $from".$lend."Reply-to: $from".$lend."X-Mailer: PHP/".phpversion().$lend;

		if ($config["Email"]["html_mail"] == "Y")
			$headers .= "Content-Type: text/html; charset: ".$charset.$lend;
		else
			$headers .= "Content-Type: text/plain; charset: ".$charset.$lend;

		mail($to,$subject,$body,$headers, "-f$from");
}

#
# Simple crypt function. Returns an encrypted version of argument.
# Does not matter what type of info you encrypt, the function will return
# a string of ASCII chars representing the encrypted version of argument.
# Note: text_crypt returns string, which length is 2 time larger
#
function text_crypt_symbol($c) {
# $c is ASCII code of symbol. returns 2-letter text-encoded version of symbol

        global $START_CHAR_CODE;

        return chr($START_CHAR_CODE + ($c & 240) / 16).chr($START_CHAR_CODE + ($c & 15));
}

function text_crypt($s) {
    global $START_CHAR_CODE, $CRYPT_SALT;

    if ($s == "")
        return $s;
    $enc = rand(1,255); # generate random salt.
    $result = text_crypt_symbol($enc); # include salt in the result;
    $enc ^= $CRYPT_SALT;
    for ($i = 0; $i < strlen($s); $i++) {
        $r = ord(substr($s, $i, 1)) ^ $enc++;
        if ($enc > 255)
            $enc = 0;
        $result .= text_crypt_symbol($r);
    }
    return $result;
}

function text_decrypt_symbol($s, $i) {
# $s is a text-encoded string, $i is index of 2-char code. function returns number in range 0-255

        global $START_CHAR_CODE;

        return (ord(substr($s, $i, 1)) - $START_CHAR_CODE)*16 + ord(substr($s, $i+1, 1)) - $START_CHAR_CODE;
}

function text_decrypt($s) {
    global $START_CHAR_CODE, $CRYPT_SALT;

    if ($s == "")
        return $s;
    $enc = $CRYPT_SALT ^ text_decrypt_symbol($s, 0);
    for ($i = 2; $i < strlen($s); $i+=2) { # $i=2 to skip salt
        $result .= chr(text_decrypt_symbol($s, $i) ^ $enc++);
        if ($enc > 255)
            $enc = 0;
    }
    return $result;
}

#
# Recursively deletes category with all its contents
#

function func_rm_dir_files ($path) {
        $dir = opendir ($path);

        while ($file = readdir ($dir)) {
                if (($file == ".") or ($file == ".."))
                        continue;
                if (filetype ("$path/$file") == "dir") {
                        func_rm_dir_files ("$path/$file");
                        rmdir ("$path/$file");
                } else {
                        unlink ("$path/$file");
                }
        }
		closedir($dir);
}

function func_rm_dir ($path) {
        func_rm_dir_files ($path);
        rmdir ($path);
}

#
# Delete product from products table + all associated information
# $productid - product's id
#
function func_delete_product($productid) {
        global $sql_tbl;

		$product_categories = func_query_first("SELECT categoryid, categoryid1, categoryid2, categoryid3 FROM $sql_tbl[products] WHERE productid='$productid'");

        db_query("delete from $sql_tbl[pricing] where productid='$productid'");
        db_query("delete from $sql_tbl[product_links] where productid1='$productid' or productid2='$productid'");
        db_query("delete from $sql_tbl[featured_products] where productid='$productid'");
        db_query("delete from $sql_tbl[products] where productid='$productid'");
        db_query("delete from $sql_tbl[delivery] where productid='$productid'");
        db_query("delete from $sql_tbl[images] where productid='$productid'");
        db_query("delete from $sql_tbl[thumbnails] where productid='$productid'");
        db_query("delete from $sql_tbl[product_options] where productid='$productid'");
        db_query("DELETE FROM $sql_tbl[product_options_ex] WHERE productid='$productid'");
        db_query("DELETE FROM $sql_tbl[product_options_js] WHERE productid='$productid'");
        db_query("DELETE FROM $sql_tbl[product_votes] WHERE productid='$productid'");
        db_query("DELETE FROM $sql_tbl[product_reviews] WHERE productid='$productid'");
        db_query("DELETE FROM $sql_tbl[products_lng] WHERE productid='$productid'");
        db_query("DELETE FROM $sql_tbl[subscriptions] where productid='$productid'");
        db_query("DELETE FROM $sql_tbl[subscription_customers] where productid='$productid'");
	db_query("DELETE FROM $sql_tbl[tax_rates] where productid='$productid'");
	db_query("DELETE FROM $sql_tbl[download_keys] where productid='$productid'");
	db_query("DELETE FROM $sql_tbl[discount_coupons] where productid='$productid'");
	db_query("DELETE FROM $sql_tbl[stats_customers_products] where productid='$productid'");
	db_query("DELETE FROM $sql_tbl[wishlist] where productid='$productid'");
	db_query("DELETE FROM $sql_tbl[product_bookmarks] where productid='$productid'");

	$product_configurator = array_pop(func_query_first("SELECT module_name FROM $sql_tbl[modules] WHERE module_name='Product_Configurator'"));

	if ($product_configurator)
		db_query("DELETE FROM $sql_tbl[compatibility] where productid='$productid'");

#
# Update product count for categories
#
	if (is_array($product_categories)) {
		foreach($product_categories as $k=>$catid) {
			if ($catid) {
				$product_count = array_pop(func_query_first("SELECT COUNT(*) FROM $sql_tbl[products] WHERE forsale='Y' AND (categoryid='$catid' OR categoryid1='$catid' OR categoryid2='$catid' OR categoryid3='$catid')"));
					db_query("UPDATE $sql_tbl[categories] SET product_count='$product_count' WHERE categoryid='$catid'");
			}
		}
	}

}

#
# Delete profile from customers table + all associated information
#
function func_delete_profile($user,$usertype) {

        global $files_dir_name, $single_mode, $sql_tbl;

        if($usertype=="P" && !$single_mode) {
# If user is provider delete some associated info to keep DB integrity
# Delete products
#
                $products = func_query("SELECT productid FROM $sql_tbl[products] WHERE provider='$user'");
                if (!empty($products))
                        foreach($products as $product)
                                func_delete_product($product["productid"]);
#
# Delete Shipping, Discounts, Coupons, States/Tax, Countries/Tax
#
                db_query("delete from $sql_tbl[shipping_rates] where provider='$user'");
                db_query("delete from $sql_tbl[discounts] where provider='$user'");
                db_query("delete from $sql_tbl[discount_coupons] where provider='$user'");
                db_query("delete from $sql_tbl[state_tax] where provider='$user'");
                db_query("delete from $sql_tbl[country_tax] where provider='$user'");
#
# Delete provider's file dir
#
                @func_rm_dir ("$files_dir_name/$user");
        }

#
# If it is partner, then remove all his information
#
        if ($usertype == "B") {
                db_query ("DELETE FROM $sql_tbl[partner_clicks] WHERE login='$user'");
                db_query ("DELETE FROM $sql_tbl[partner_commissions] WHERE login='$user'");
                db_query ("DELETE FROM $sql_tbl[partner_payment] WHERE login='$user'");
                db_query ("DELETE FROM $sql_tbl[partner_views] WHERE login='$user'");
        }

        db_query("DELETE FROM $sql_tbl[customers] WHERE login='$user' AND usertype='$usertype'");
}

#
# Get information associated with user
#
function func_userinfo($user,$usertype) {
        global $sql_tbl;

    $userinfo = func_query_first("SELECT $sql_tbl[customers].*, $sql_tbl[countries].country FROM $sql_tbl[customers], $sql_tbl[countries] WHERE $sql_tbl[customers].login='$user' AND $sql_tbl[customers].usertype='$usertype' AND $sql_tbl[countries].code=$sql_tbl[customers].b_country");

	$userinfo["passwd1"] = stripslashes(text_decrypt($userinfo["password"]));
	$userinfo["passwd2"] = stripslashes(text_decrypt($userinfo["password"]));
	$userinfo["password"] = stripslashes(text_decrypt($userinfo["password"]));
    $userinfo["card_number"] = text_decrypt($userinfo["card_number"]);
	$userinfo["b_statename"] = func_get_state($userinfo["b_state"]);
	$userinfo["b_countryname"] = func_get_country($userinfo["b_country"]);
	$userinfo["s_statename"] = func_get_state($userinfo["s_state"]);
	$userinfo["s_countryname"] = func_get_country($userinfo["s_country"]);

        $email = $userinfo["email"];

        if(func_query_first("SELECT email FROM $sql_tbl[maillist] WHERE email='$email'")) $userinfo["newsletter"]="Y";

        return $userinfo;
}

#
# Get state by code
#
function func_get_state ($state_code) {
	global $sql_tbl;

	$state_name = array_pop(func_query_first("SELECT state  FROM $sql_tbl[states] WHERE code='$state_code'"));
	return ($state_name ? $state_name : $state_code);
}

#
# Get country by code
#
function func_get_country ($country_code) {
	global $sql_tbl;

	$country_name = array_pop(func_query_first("SELECT country  FROM $sql_tbl[countries] WHERE code='$country_code'"));
	return ($country_name ? $country_name : $country_code);
}

#
# Convert price to "XXXXX.XX" format
#
function price_format($price) {
	return sprintf("%.2f",round((double)$price+0.00000000001,2));
}

function func_get_products_providers ($products) {
        $products_providers = array ();
        foreach ($products as $product) {
                if (!in_array ($product["provider"], $products_providers)) {
                        $products_providers [] = $product["provider"];
                }
        }

        return $products_providers;
}

function func_get_products_by_provider ($products, $provider) {
        global $single_mode;

        $result = array ();

        if ($single_mode) {
                $result = $products;
        } else {
                foreach ($products as $product) {
                        if ($product["provider"] == $provider)
                                $result[] = $product;
                }
        }

        return $result;
}

#
# This function do real shipping calcs
#
function func_real_shipping($delivery) {

        global $intershipper_rates, $sql_tbl;

        $shipping_codes = func_query_first("select code, subcode from $sql_tbl[shipping] where shippingid='$delivery'");

        if ($intershipper_rates) {
                foreach($intershipper_rates as $rate)
                        if ($rate["methodid"]==$shipping_codes["subcode"])
                                return $rate["rate"];
        } else
                return "0.00";

}
#
# This function calculates costs of contents of shopping cart
#
function func_calculate($cart, $products, $login, $login_type) {
        global $single_mode, $sql_tbl;

        if ($single_mode) {
                $return = array ();
                $result = func_calculate_single ($cart, $products, $login, $login_type);
                $return = $result;
                $return ["orders"] = array ();
                $return ["orders"][0] = $result;
                $return ["orders"][0]["provider"] = $products[0]["provider"];
        } else {
                $products_providers = func_get_products_providers ($products);

                $return = array ();
                $return["orders"] = array ();
                $key = 0;
                foreach ($products_providers as $provider_for) {
                        $_products = func_get_products_by_provider ($products, $provider_for);
                        $result = func_calculate_single ($cart, $_products, $login, $login_type);
                        $return ["total_cost"] += $result ["total_cost"];
                        $return ["shipping_cost"] += $result ["shipping_cost"];
                        $return ["tax_cost"] += $result ["tax_cost"];
						$return ["tax_gst"] += $result ["tax_gst"];
						$return ["tax_pst"] += $result ["tax_pst"];
                        $return ["discount"] += $result ["discount"];
                        if ($result["coupon"]) {
                                $return ["coupon"] = $result ["coupon"];
                        }
                        $return ["coupon_discount"] += $result ["coupon_discount"];
                        $return ["sub_total"] += $result ["sub_total"];
                        $return ["total_vat"] += $result ["total_vat"];

                        $return ["orders"][$key] = $result;
                        $return ["orders"][$key]["provider"] = $provider_for;

                        $key ++;
                }
                if ($cart["giftcerts"]) {
                        $_products = array ();
                        $result = func_calculate_single ($cart, $_products, $login, $login_type);
                        $return ["total_cost"] += $result ["total_cost"];
                        $return ["shipping_cost"] += $result ["shipping_cost"];
                        $return ["tax_cost"] += $result ["tax_cost"];
						$return ["tax_gst"] += $result ["tax_gst"];
						$return ["tax_pst"] += $result ["tax_pst"];
                        $return ["discount"] += $result ["discount"];
                        $return ["sub_total"] += $result ["sub_total"];
                        $return ["total_vat"] += $result ["total_vat"];
                        $return ["coupon_discount"] += $result ["coupon_discount"];

                        $return ["orders"][$key] = $result;
                        $return ["orders"][$key]["provider"] = ""; #$provider_for;
                        $key++;
                }
        }

#
# Recalculating applied gift certificates
#
		if ($cart["applied_giftcerts"]) {
			$gc_payed_sum = 0;
			$applied_giftcerts = array();
			foreach($cart["applied_giftcerts"] as $k=>$v) {
				if (($gc_payed_sum + $v["giftcert_cost"]) <= $return["total_cost"]) {
					$gc_payed_sum += $v["giftcert_cost"];
					$applied_giftcerts[] = $v;
					continue;
				}
				else
					db_query("UPDATE $sql_tbl[giftcerts] SET status='A' WHERE gcid='$v[giftcert_id]'");
			}
			$giftcert_cost = $gc_payed_sum;
		}

		if ($return["total_cost"] >= $giftcert_cost)
			$return["giftcert_discount"] = $giftcert_cost;
		else
			$return["giftcert_discount"] = $giftcert_cost - $return["total_cost"];

		$return["total_cost"] = price_format($return["total_cost"] - $return["giftcert_discount"]);
		$return["applied_giftcerts"] = $applied_giftcerts;

		if ($single_mode)
			$return ["orders"][0]["total_cost"] = $return["total_cost"];
#
# Apply GC to all orders in cart in single_mode Off
#
		elseif (is_array($applied_giftcerts)) {
			foreach($return["orders"] as $k=>$order) {
				$giftcert_discount = 0;
				foreach($applied_giftcerts as $k1=>$applied_giftcert) {
					if ($applied_giftcert["giftcert_cost"] == 0)
						continue;
					if ($applied_giftcert["giftcert_cost"] > $order["total_cost"])
						$applied_giftcert["giftcert_cost"] = $order["total_cost"];
					$giftcert_discount += $applied_giftcert["giftcert_cost"];
					$order["total_cost"] = $order["total_cost"] - $giftcert_discount;
					$applied_giftcert["giftcert_cost"] = price_format($applied_giftcert["giftcert_cost"]);
					$applied_giftcerts[$k1]["giftcert_cost"] -= $applied_giftcert["giftcert_cost"];
					$return["orders"][$k]["applied_giftcerts"][] = $applied_giftcert;
					$return["orders"][$k]["giftcert_discount"] = price_format($giftcert_discount);
				}
				$return["orders"][$k]["total_cost"] = price_format($return["orders"][$k]["total_cost"] - $return["orders"][$k]["giftcert_discount"]);
			}
		}

        return $return;
}

#
# Calculate total products price
# 1) calculate total sum,
# 2) a) total = total - discount
#    b) total = total - coupon_discount
# 3) calculate shipping
# 4) calculate tax
# 5) total_cost = total + shipping + tax
# 6) total_cost = total_cost + giftcerts_cost
#
function func_calculate_single($cart, $products, $login, $login_type) {
        global $single_mode;
        global $active_modules, $config, $sql_tbl;

        if (!$products)
                $provider_for = "";
        else
                $provider_for = $products[0]["provider"];

        $delivery = $cart["shippingid"];
        $giftcerts = $cart["giftcerts"];
        $discount_coupon = $cart["discount_coupon"];

        $provider_condition=($single_mode?"":"and provider='$provider_for'");

        if(!empty($login)) $customer_info = func_userinfo($login,$login_type);

        $total=0;
        $sub_total=0;
        $total_weight=0;
        $total_items=0;
        $avail_discount_total=0;
        $total_weight_shipping = 0;
        $total_items_shipping = 0;
        $total_shipping = 0;
        $total_vat = 0;
        $avail_discount_total=0;
        $discount=0;
		$giftcert_cost = 0;


        foreach($products as $k=>$product) {
				if ($product["free_tax"]=="N")
					$total_taxable += $product["price"]*$product["amount"];

				if ($product["apply_gst"]=="Y")
					$total_taxable_gst += $product["price"]*$product["amount"];

				if ($product["apply_pst"]=="Y")
					$total_taxable_pst += $product["price"]*$product["amount"];

				if ($product["discount_avail"]=="Y")
						$avail_discount_total+=$product["price"]*$product["amount"];
                $total+=$product["price"]*$product["amount"];
                $total_weight+=$product["weight"]*$product["amount"];

				if ($config["Taxes"]["use_vat"]=="Y") {
					$inc_vat = ($product["price"] - $product["price"]*100/($product["vat"]+100))*$product["amount"];
					$total_vat += $inc_vat;
				}

                if ($product["distribution"]=="") $total_items+=$product["amount"];
				elseif ($total_items==0) $total_items+=$product["amount"];
                if ($product["free_shipping"] != "Y") {
                        $total_shipping += $product["price"]*$product["amount"];
                        $total_weight_shipping += $product["weight"]*$product["amount"];
				if ($product["distribution"]=="") $total_items_shipping += $product["amount"];
                }
       }

        $sub_total = $total;
#
# Deduct discount
#
		$discount_info = func_query_first("select * from $sql_tbl[discounts] where minprice<='$avail_discount_total' $provider_condition and membership='$customer_info[membership]' order by minprice desc");
		$discountall_info = func_query_first("select * from $sql_tbl[discounts] where minprice<='$avail_discount_total' $provider_condition and membership='' order by minprice desc");
		if ($discount_info["discount_type"]=="absolute" && $avail_discount_total>0)
				$discount += $discount_info["discount"];
		elseif ($discount_info["discount_type"]=="percent")
				$discount += $avail_discount_total*$discount_info["discount"]/100;
       	elseif ($discountall_info["discount_type"]=="absolute" && $avail_discount_total>0)
                $discount += $discountall_info["discount"];
		elseif ($discountall_info["discount_type"]=="percent")
				$discount += $avail_discount_total*$discountall_info["discount"]/100;

		$total = $total-$discount;
                $total_shipping -= $discount;
                if ($total_shipping < 0)
                        $total_shipping = 0;
#
# Deduct discount by discount coupon
#
        $coupon_discount=0;
        $coupon_total = 0;
        $coupon_amount = 0;

        $discount_coupon_data = func_query_first("select * from $sql_tbl[discount_coupons] where coupon='$discount_coupon' $provider_condition");

        if (($discount_coupon_data["productid"]>0) and (($discount_coupon_data["coupon_type"]=="absolute") or ($discount_coupon_data["coupon_type"]=="percent"))) {
                foreach($products as $product) {
                        if ($product["productid"] == $discount_coupon_data["productid"]) {
                                $coupon_total += $product["price"]*$product["amount"];
                                $coupon_amount += $product["amount"];
                        }
                }
                if ($discount_coupon_data["coupon_type"]=="absolute") {
                        $coupon_discount = $coupon_amount*$discount_coupon_data["discount"];
                } else {
                        $coupon_discount = $coupon_total*$discount_coupon_data["discount"]/100;
                }
        } elseif (($discount_coupon_data["categoryid"]>0) and (($discount_coupon_data["coupon_type"]=="absolute") or ($discount_coupon_data["coupon_type"]=="percent"))) {
                foreach ($products as $product) {
                        if ($product["categoryid"] == $discount_coupon_data["categoryid"]) {
                                $coupon_total += $product["price"]*$product["amount"];
                                $coupon_amount += $product["amount"];
                        }
                }
                if ($discount_coupon_data["coupon_type"]=="absolute") {
                        $coupon_discount = $coupon_amount*$discount_coupon_data["discount"];
                } else {
                        $coupon_discount = $coupon_total*$discount_coupon_data["discount"]/100;
                }
        } else {
                if ($discount_coupon_data["coupon_type"]=="absolute")
                $coupon_discount = $discount_coupon_data["discount"];
        elseif ($discount_coupon_data["coupon_type"]=="percent")
                $coupon_discount = $total*$discount_coupon_data["discount"]/100;
        }

        if ((!$single_mode) and (($discount_coupon_data["provider"] != $provider_for) or (!$products)))
                $discount_coupon = "";

        $total = $total-$coupon_discount;
        $total_shipping -= $coupon_discount;
        if ($total_shipping<0)
                $total_shipping = 0;

#
# Calculate shipping cost
#
# Shipping also calculated based on zones
#
# Advanced shipping formula:
# AMOUNT = amount of ordered products
# SUM = total sum of order
# TOTAL_WEIGHT = total weight of products
#
# SHIPPING = rate+TOTAL_WEIGHT*weight_rate+AMOUNT*item_rate+SUM*rate_p/100
#

        $shipping_cost = 0;
        $shipping_freight = 0;

#
# Enable shipping and taxes calculation if "apply_default_country" is ticked.
#
		$calculate_enable_flag = true;

		if (!$login && $config["General"]["apply_default_country"]!="Y")
			$calculate_enable_flag = false;
		else {
			if (!$login && $config["General"]["apply_default_country"]=="Y") {
				$customer_info["s_country"] = $config["General"]["default_country"];
				$customer_info["s_state"] = $config["General"]["default_state"];
				$customer_info["s_zipcode"] = $config["General"]["default_zipcode"];
				$customer_info["s_city"] = $config["General"]["default_city"];

			}
		}

	if ($config["Shipping"]["disable_shipping"] != "Y") {
#
# Zones code
#
        if($calculate_enable_flag) {
                $customer_zone = array_pop(func_query_first("select zoneid from $sql_tbl[country_zones] where code='$customer_info[s_country]'  $provider_condition"));
                if ($customer_info["s_country"]==$config["Company"]["location_country"]) {
				$customer_zone_tmp = array_pop(func_query_first("select zoneid from $sql_tbl[state_zones] where code='$customer_info[s_state]' $provider_condition"));
				if($customer_zone_tmp) $customer_zone=$customer_zone_tmp;
			}
        }
        if(!$customer_zone) $customer_zone=0;

#
# if $products is empty then shipping and tax are alwayz zero
#
        if ($total_items_shipping && $calculate_enable_flag) {
                $shipping = func_query("select * from $sql_tbl[shipping_rates] where shippingid='$delivery' $provider_condition and zoneid='$customer_zone' and maxtotal>=$total_shipping and maxweight>=$total_weight_shipping and type='D' order by maxtotal, maxweight");

                if($shipping)
                        $shipping_cost = $shipping[0]["rate"]+$total_weight_shipping*$shipping[0]["weight_rate"]+$total_items_shipping*$shipping[0]["item_rate"]+$total_shipping*$shipping[0]["rate_p"]/100;
        }

		$free_shipping=1;
		$is_distribution=1;
        foreach($products as $product){
                $shipping_freight += $product["shipping_freight"]*$product["amount"];
                if ($product["free_shipping"] != "Y" and $product["distribution"] == "") $free_shipping=0;
                if ($product["distribution"] == "") $is_distribution=0;
		}

#
# Realtime shipping rates
#
        $result = func_query_first ("SELECT * FROM $sql_tbl[shipping] WHERE shippingid='$delivery' AND code!=''");
        if($config["Shipping"]["realtime_shipping"]=="Y" and $result and $free_shipping==0 and $is_distribution==0) {
                $shipping_cost = func_real_shipping($delivery);
                $shipping_rt = func_query("select * from $sql_tbl[shipping_rates] where shippingid='$delivery' $provider_condition and zoneid='$customer_zone' and maxtotal>=$total_shipping and maxweight>=$total_weight_shipping and type='R' order by maxtotal, maxweight");
                if($shipping_rt && $shipping_cost>0)
                        $shipping_cost += $shipping_rt[0]["rate"]+$total_weight_shipping*$shipping_rt[0]["weight_rate"]+$total_items_shipping*$shipping_rt[0]["item_rate"]+$total_shipping*$shipping_rt[0]["rate_p"]/100;
		}

		$shipping_cost += $shipping_freight;

        if ($discount_coupon_data["coupon_type"]=='free_ship') {
                if (($single_mode) or ($provider_for == $discount_coupon_data["provider"])) {
                        $coupon_discount = $shipping_cost;
                }
        }

	}

#
# Calculate tax cost
# SUM = total sum of order
#
# TAX_US = country_tax_flat + SUM*country_tax_percent/100 + state_tax_flat + SUM*state_tax_percent/100;
#
# TAX_CAN = SUM*gst_tax/100 + SUM*pst_tax/100;
#

		if ($config["Taxes"]["apply_taxes_to_shipping"] != "Y") {

			if ($customer_info["s_country"]==$config["Company"]["location_country"]) {

				if ($customer_info["s_state"])
					$apply_taxes_to_shipping = array_pop(func_query_first("SELECT $sql_tbl[state_tax].tax_shipping FROM $sql_tbl[states], $sql_tbl[state_tax] WHERE $sql_tbl[states].code=$sql_tbl[state_tax].code $provider_condition AND $sql_tbl[state_tax].code='".$customer_info["s_state"]."'"));

				if ($apply_taxes_to_shipping != "Y" && $customer_info["s_zipcode"])
					$apply_taxes_to_shipping = array_pop(func_query_first("SELECT tax_shipping FROM $sql_tbl[zipcode_tax] WHERE '".addslashes($customer_info["s_zipcode"])."' LIKE zipcode_mask $provider_condition"));
			}
		}

		if ($config["Taxes"]["apply_taxes_to_shipping"] == "Y" || $apply_taxes_to_shipping == "Y") {
#
# Include shipping cost to the taxable sum
#
			if (!empty($config["Taxes"]["vat_shipping"]) && $config["Taxes"]["use_vat"]=="Y" && $config["General"]["eu_national"]=="Y") {
				$total_vat += $shipping_cost*$config["Taxes"]["vat_shipping"]/100;
				$shipping_cost += $shipping_cost*$config["Taxes"]["vat_shipping"]/100;
			}

			$total_taxable += $shipping_cost;
			$total_taxable_gst += $shipping_cost;
			#
			# Comment line below if you want PST do not applies on shipping cost
			#
			$total_taxable_pst += $shipping_cost;
		}

        $tax_cost = 0;

		if ($config["Taxes"]["use_us_taxes"] == "Y") {
#
# Calculate generic taxes if enabled
#
        if ((!empty($login) || $calculate_enable_flag) && ($active_modules["Tax_Zones"])) {
                include "../modules/Tax_Zones/calc_tax.php";
        }

        if ((!empty($login) || $calculate_enable_flag) && $total_items) {
                $country_tax = array();
                $state_tax = array();

                $country_tax = func_query_first("select $sql_tbl[country_tax].* from $sql_tbl[country_tax], $sql_tbl[countries] where $sql_tbl[country_tax].code='$customer_info[s_country]' $provider_condition and $sql_tbl[country_tax].code=$sql_tbl[countries].code");

                if ($customer_info["s_country"]==$config["Company"]["location_country"]) {

                        if ($customer_info["s_state"])
                                $state_tax = func_query_first("select $sql_tbl[state_tax].* from $sql_tbl[states], $sql_tbl[state_tax] where $sql_tbl[states].code=$sql_tbl[state_tax].code $provider_condition and $sql_tbl[state_tax].code='".$customer_info["s_state"]."'");

                        if ($customer_info["s_zipcode"])
                                $zipcode_tax = func_query_first("select sum(tax_percent) as tax_percent, sum(tax_flat) as tax_flat from $sql_tbl[zipcode_tax] where '".addslashes($customer_info[s_zipcode])."' like zipcode_mask $provider_condition");
                }

                $tax_cost += $country_tax["tax_flat"]+$total_taxable*$country_tax["tax_percent"]/100+$state_tax["tax_flat"]+$total_taxable*$state_tax["tax_percent"]/100;
                $tax_cost += $zipcode_tax["tax_flat"]+$total_taxable*$zipcode_tax["tax_percent"]/100;
        }

		} # /if ($config["Taxes"]["use_us_taxes"] == "Y")

		$tax_gst = 0;
		$tax_pst = 0;

		if ($config["Taxes"]["use_canadian_taxes"] == "Y") {

	        if ((!empty($login) || $calculate_enable_flag) && ($total_taxable_gst>0 || $total_taxable_pst>0)) {

				$tax_canada_data = func_query_first("select $sql_tbl[state_tax].gst_rate, $sql_tbl[state_tax].pst_rate from $sql_tbl[states], $sql_tbl[state_tax] where $sql_tbl[states].code=$sql_tbl[state_tax].code $provider_condition and $sql_tbl[state_tax].code='".$customer_info["s_state"]."'");

				$tax_gst = $total_taxable_gst*$tax_canada_data["gst_rate"]/100;
				$tax_pst = $total_taxable_pst*$tax_canada_data["pst_rate"]/100;

			}
		} # /if ($config["Taxes"]["use_canadian_taxes"] == "Y")

#
# Calculate Gift Certificates cost (purchased giftcerts)
#
        $giftcerts_cost=0;

        if ((($single_mode) or (!$provider_for)) and ($giftcerts))
                foreach($giftcerts as $giftcert)
                        $giftcerts_cost+=$giftcert["amount"];

		$sub_total = $sub_total+$giftcerts_cost;

#
# Calculate total
#
        $total+=$shipping_cost+$tax_cost+$tax_gst+$tax_pst+$giftcerts_cost;

        if ($discount_coupon_data["coupon_type"]=='free_ship') {
                if (($single_mode) or ($provider_for == $discount_coupon_data["provider"])) {
                        $total -= $coupon_discount;
                }
        }

        return array("total_cost"=>price_format($total), "shipping_cost"=>price_format($shipping_cost), "tax_cost"=>price_format($tax_cost), "discount"=>price_format($discount), "coupon"=>$discount_coupon, "coupon_discount"=>price_format($coupon_discount), "sub_total"=>price_format($sub_total), "total_vat"=>price_format($total_vat), "tax_gst"=>price_format($tax_gst), "tax_pst"=>price_format($tax_pst));
}

#
# Search for products in products database
#
function func_search_products($query, $membership,$first=0,$count_all=-1,$get_all=0, $orderby="orderby") {
        global $current_area;
        global $store_language, $sql_tbl;
		global $config;
		global $cart;

        if ($current_area == "C") {
                $membership_condition = " AND ($sql_tbl[categories].membership='$membership' OR $sql_tbl[categories].membership='') ";
        } else {
                $membership_condition = "";
        }
        $tail = "";
		$result = array();
		if($count_all != -1) {
			if($first > $count_all) return $result;
			if(!$get_all) {
		    	$count_all_max = $config["Appearance"]["products_per_page"]*$config["Appearance"]["max_nav_pages"];
			    if($count_all > $count_all_max) $count_all = $count_all_max;
			    $count = $config["Appearance"]["products_per_page"];
			    if(($first+$count)>$count_all) $count = $count_all - $first;
			} else {
				$count = $count_all - $first;
			}
		    $tail = " limit ".$count_all;
			$query .= " group by $sql_tbl[products].productid ";
	     }
        if($count_all == 0 or $count_all < -1) return $result;

        if (($current_area == "C" || $current_area == "B") && !eregi("GROUP BY", $query)) {
            if (eregi("ORDER BY(.*)", $query))
                $query = eregi_replace("order by", "GROUP BY $sql_tbl[products].productid ORDER BY", $query);
            else
                $query = eregi_replace("^(.+)$", "\\1 GROUP BY $sql_tbl[products].productid", $query);
        }

        if ($config["General"]["unlimited_products"]=="N" && (($current_area == "C" || $current_area == "B") && $config["General"]["disable_outofstock_products"] == "Y"))
			$avail_condition = "$sql_tbl[products].avail>0 and ";
		else
			$avail_condition = "";

		if ($current_area == "C" && $store_language != $config["default_customer_language"])
			$search_query = "select $sql_tbl[products].*, $sql_tbl[categories].category, min($sql_tbl[pricing].price) as price from $sql_tbl[pricing], $sql_tbl[categories], $sql_tbl[products] LEFT JOIN $sql_tbl[products_lng] ON $sql_tbl[products].productid=$sql_tbl[products_lng].productid where $sql_tbl[pricing].productid=$sql_tbl[products].productid and $sql_tbl[pricing].quantity=1 and $sql_tbl[products].categoryid=$sql_tbl[categories].categoryid $membership_condition and ($sql_tbl[pricing].membership='$membership' or $sql_tbl[pricing].membership='') and $avail_condition ".$query.$tail;
		else
			$search_query = "select $sql_tbl[products].*, $sql_tbl[categories].category, min($sql_tbl[pricing].price) as price from $sql_tbl[products], $sql_tbl[pricing], $sql_tbl[categories] where $sql_tbl[pricing].productid=$sql_tbl[products].productid and $sql_tbl[pricing].quantity=1 and $sql_tbl[products].categoryid=$sql_tbl[categories].categoryid $membership_condition and ($sql_tbl[pricing].membership='$membership' or $sql_tbl[pricing].membership='') and $avail_condition ".$query.$tail;

        $result_p = func_query ($search_query);
# sorting by "orderby" field
		if($result_p) {
			if($count_all!=-1) {
				$productorderbys = array();
				foreach($result_p as $result_line) {
					$productorderbys[]=$result_line[$orderby];
					$productorderbyname[]=$result_line["product"];
				}
#				array_multisort($result_p,($orderby=="orderby" && $config["Appearance"]["product_order_reversed"]=="Y"?SORT_DESC:SORT_ASC),SORT_STRING, $productorderbys,($orderby=="orderby" && $config["Appearance"]["product_order_reversed"]=="Y"?SORT_DESC:SORT_ASC),SORT_NUMERIC,$productorderbyname,SORT_STRING);
				array_multisort($result_p,($config["Appearance"]["product_order_reversed"]=="Y"?SORT_DESC:SORT_ASC),SORT_STRING,$productorderbys,($config["Appearance"]["product_order_reversed"]=="Y"?SORT_DESC:SORT_ASC),SORT_REGULAR,$productorderbyname,SORT_STRING);
				for($i=$first; $i < min(count($result_p),$first+$count); $i++)
					$result[]=$result_p[$i];
			} else {
				foreach($result_p as $key => $value)
					$result[] = $result_p[$key];
			}
		}

        if ($result and $current_area=="C") {
			foreach ($result as $key=>$value) {
#
# Update quantity for products that already placed into the cart
#
				if($cart["products"]) {
					$in_cart = 0;
					foreach($cart["products"] as $cart_item)
						if($cart_item["productid"]==$value["productid"])
							$in_cart+=$cart_item["amount"];
					$result[$key]["avail"] -= $in_cart;
				}
#
# Get thumbnail's URL (uses only if images stored in FS)
#
				$result[$key]["tmbn_url"] = func_get_thumbnail_url($result[$key]["productid"]);
#
# Recalculate prices if VAT is enabled and prices do not includes VAT
#
				list($result[$key]["price"], $result[$key]["vat"]) = func_get_price_vat($result[$key]);
#
# Check if product have product options
#
				$result[$key]["product_options"] = array_pop(func_query_first("SELECT COUNT(*) FROM $sql_tbl[product_options] WHERE productid='".$value["productid"]."'"));

				$int_res = func_query_first ("SELECT * FROM $sql_tbl[products_lng] WHERE code='$store_language' AND productid='$value[productid]'");
				if ($int_res[product])
					$result[$key][product] = stripslashes($int_res[product]);
				if ($int_res[descr])
					$result[$key][descr] = str_replace("\n","<br>", stripslashes($int_res[descr]));
				if ($int_res[full_descr])
					$result[$key][full_descr] = str_replace("\n","<br>", stripslashes($int_res[full_descr]));
			}
		}

        return $result;
}

#
# Delete category recursively and all subcategories and products
#
function func_delete_category($cat) {
        global $sql_tbl;

        $cat_name = array_pop(func_query_first("select category from $sql_tbl[categories] where categoryid='$cat'"));
        $cat_name = addslashes($cat_name);
#
# Delete products from subcategories
#
        $prods = func_query("select productid from $sql_tbl[products], $sql_tbl[categories] where ($sql_tbl[categories].category='$cat_name' or $sql_tbl[categories].category like '$cat_name/%') and $sql_tbl[products].categoryid=$sql_tbl[categories].categoryid");
        if($prods)
                while(list($key,$prod)=each($prods))
                        func_delete_product($prod["productid"]);
#
# Delete subcategories
#
        $subcats = func_query("select categoryid from $sql_tbl[categories] where category like '$cat_name/%' or category='$cat_name'");

        while(list($key,$subcat)=each($subcats)) {
                $cat_id=$subcat["categoryid"];
                db_query("delete from $sql_tbl[categories] where categoryid='$cat_id'");
        }
#
# Delete associated data
#
                db_query("delete from $sql_tbl[icons] where categoryid='$cat'");
                db_query("delete from $sql_tbl[featured_products] where categoryid='$cat'");
}

#
# Count products in category
#
function func_products_count($cat) {
        global $sql_tbl;

        $cat_name = array_pop(func_query_first("select category from $sql_tbl[categories] where categoryid='$cat'"));
        $cat_name = addslashes($cat_name);
#
# Select products from subcategories
#
        $prods = func_query("select count(productid) from $sql_tbl[products], $sql_tbl[categories] where ($sql_tbl[categories].category='$cat_name' or $sql_tbl[categories].category like '$cat_name/%') and $sql_tbl[products].categoryid=$sql_tbl[categories].categoryid");
        if ($prods) return array_pop(array_pop($prods));
}

#
# Put all product info into $product array
#
function func_select_product($id, $membership) {

        global $login, $login_type, $current_area, $single_mode, $cart;
        global $store_language, $sql_tbl, $config;

        $in_cart=0;

        if ($current_area == "C") {
                $membership_condition = " AND ($sql_tbl[categories].membership='$membership' OR $sql_tbl[categories].membership='') ";
        } else {
                $membership_condition = "";
        }

        if($current_area=="C" && $cart["products"])
                foreach($cart["products"] as $cart_item)
                        if($cart_item["productid"]==$id) $in_cart+=$cart_item["amount"];

        if(!$single_mode)
                $login_condition=(($login!="" and $login_type=="P")?"and $sql_tbl[products].provider='$login'":"");

        $product = func_query_first("select $sql_tbl[products].*, $sql_tbl[products].avail-$in_cart as avail, $sql_tbl[categories].category as category_text, min($sql_tbl[pricing].price) as price from $sql_tbl[products], $sql_tbl[pricing], $sql_tbl[categories] where $sql_tbl[products].productid='$id' ".$login_condition." and $sql_tbl[products].categoryid=$sql_tbl[categories].categoryid $membership_condition and $sql_tbl[pricing].productid=$sql_tbl[products].productid and $sql_tbl[pricing].quantity=1 and ($sql_tbl[pricing].membership = '$membership' or $sql_tbl[pricing].membership = '') group by $sql_tbl[products].productid");

#
# Error handling
#
        if(!$product) {
	        func_header_location("error_message.php?access_denied");
        }

		if ($current_area == "C") {
#
# Recalculate prices if VAT is enabled and prices do not includes VAT
#
			list($product["price"], $product["vat"]) = func_get_price_vat($product);
		}

        $int_res = func_query_first ("SELECT * FROM $sql_tbl[products_lng] WHERE code='$store_language' AND productid='$id'");
        if ($current_area == "C") {
				if ($int_res["product"])
					$product["product"] = stripslashes($int_res["product"]);
                if ($int_res["descr"])
                        $product["descr"] = str_replace("\n","<br>", stripslashes($int_res["descr"]));
                if ($int_res["full_descr"])
                        $product["fulldescr"] = str_replace("\n","<br>", stripslashes($int_res["full_descr"]));
        }

        //$product["producttitle"]="$product[product] $product[brand] $product[model] #$product[productid]";
        $product["producttitle"]="$product[product]  #$product[productid]";
#
# Get thumbnail's URL (uses only if images stored in FS)
#
		$product["tmbn_url"] = func_get_thumbnail_url($product["productid"]);

#
# Shipping data
# There is no need in this query since we do not use product-specific
# shipping methods
#
#       $product["delivery"] = func_select_product_delivery($id);
#
        return $product;

}

#
# Get delivery options by product ID
#
function func_select_product_delivery($id) {
        global $sql_tbl;

        return func_query("select $sql_tbl[shipping].*, count($sql_tbl[delivery].productid) as avail from $sql_tbl[shipping] left join $sql_tbl[delivery] on $sql_tbl[delivery].shippingid=$sql_tbl[shipping].shippingid and $sql_tbl[delivery].productid='$id' where $sql_tbl[shipping].active='Y' group by shippingid");
}

#
# Return number of available products
#
function insert_productsonline() {
        global $user_account,$sql_tbl;

        return array_pop(array_pop(func_query("select count($sql_tbl[products].productid) from $sql_tbl[products], $sql_tbl[categories] where  $sql_tbl[products].forsale='Y' AND $sql_tbl[products].categoryid=$sql_tbl[categories].categoryid AND ($sql_tbl[categories].membership='$user_account[membership]' OR $sql_tbl[categories].membership='')")));

}

#
# Return number of available items
#
function insert_itemsonline() {
        global $user_account,$sql_tbl;

    return array_pop(array_pop(func_query("select sum($sql_tbl[products].avail) from $sql_tbl[products], $sql_tbl[categories] WHERE  $sql_tbl[products].forsale='Y' AND  $sql_tbl[products].categoryid=$sql_tbl[categories].categoryid AND ($sql_tbl[categories].membership='$user_account[membership]' OR $sql_tbl[categories].membership='')")));

}

#
# Return string of search
#
function insert_stripslashes() {
		global $substring;

	$substring=trim($substring);
	while(strstr($substring,"\\")){
	$substring=stripslashes($substring);
	}
	return  $substring;
}

#
# Generate products array in $cart
#
function func_products_in_cart($cart, $membership) {

        global $active_modules,$sql_tbl, $config;
        global $current_area, $store_language;

    $products = array();

        if($cart["products"])
    foreach($cart["products"] as $product_data) {

                $productid = $product_data["productid"];
                $amount = $product_data["amount"];
                $options = $product_data["options"];

#
# Product options code
#
                $product_option_lines = func_query("select * from $sql_tbl[product_options] where productid='$productid' order by orderby");
                $product_options = array();

		if($product_option_lines)
		    foreach($product_option_lines as $product_option_line) {
        		$product_options[] = array_merge($product_option_line,array("options" => func_parse_options($product_option_line["options"])));
			}

		$product_options = func_product_options_lng($product_options);

		foreach($product_options as $k=>$v) {
			$tmp[$v["optclass"]] = $v["options"];
		}

		$product_options = $tmp;

                $absolute_surcharge = 0;
                $percent_surcharge = 0;
                $this_product_options = "";

#
# Calculate option surcharges
#
                foreach($options as $option) {
                        $my_option = $product_options[$option["optclass"]][$option["optionindex"]];

                        if(!empty($product_options[$option["optclass"]])) {
							if (!empty($my_option["optclass"]))
                                $this_product_options.="$my_option[optclass]: $my_option[option]\n";
							else
                                $this_product_options.="$option[optclass]: $my_option[option]\n";

                                if($my_option["type"]=="absolute")
                                        $absolute_surcharge+=$my_option["surcharge"];
                                elseif($my_option["type"]=="percent")
                                        $percent_surcharge+=$my_option["surcharge"];
                        } else
                                $this_product_options.="$option[optclass]: $option[optionindex]\n";

                }
                $this_product_options = chop($this_product_options);
#
# /Product options
#
		if ($config["General"]["unlimited_products"]=="N")
			$avail_condition = "$sql_tbl[products].avail>=$amount and ";
        $products_array = func_query_first("select $sql_tbl[products].*, min(round($sql_tbl[pricing].price+$absolute_surcharge+$percent_surcharge*$sql_tbl[pricing].price/100,2)) as price from $sql_tbl[products], $sql_tbl[pricing] where $sql_tbl[pricing].productid=$sql_tbl[products].productid and $sql_tbl[products].productid='$productid' and $avail_condition $sql_tbl[pricing].quantity<=$amount and ($sql_tbl[pricing].membership='$membership' or $sql_tbl[pricing].membership='') group by $sql_tbl[products].productid order by $sql_tbl[pricing].quantity desc");

        if($products_array) {
#
# Get thumbnail's URL (uses only if images stored in FS)
#
		$products_array["tmbn_url"] = func_get_thumbnail_url($products_array["productid"]);
#
# If priduct's price is 0 then use customer-defined price
#
                        if($products_array["price"]==0)
                                $products_array["price"]=price_format($product_data["price"]?$product_data["price"]:0);

						list($products_array["price"], $products_array["vat"]) = func_get_price_vat($products_array);

                        $products_array["total"]=price_format($amount*$products_array["price"]);
                        $products_array["product_options"]=$this_product_options;
                        $products_array["amount"]=$amount;


						$int_res = func_query_first ("SELECT * FROM $sql_tbl[products_lng] WHERE code='$store_language' AND productid=$productid");
	                    if ($int_res["product"])
                                $products_array["product"] = stripslashes($int_res["product"]);
                        if ($int_res["descr"])
                                $products_array["descr"] = str_replace("\n","<br>", stripslashes($int_res["descr"]));
                        if ($int_res["full_descr"])
                                $products_array["full_descr"] = str_replace("\n","<br>", stripslashes($int_res["full_descr"]));

						$products[] = $products_array;
                }
    }

        return $products;
}

#
# This function returns array with corrected values of product price and VAT:
# INPUT: array $product_data; OUTPUT: array($price, $vat)
#
function func_get_price_vat($product) {
	global $config, $sql_tbl;

	$price = $product["price"];		# Product price
	$vat_index = $product["vat"];	# VAT rate index
	$vat_percent = 0;				# VAT value (percent)
	$vat = 0;						# VAT value (currency)

	if ($config["Taxes"]["use_vat"]=="Y" && ($config["General"]["eu_national"]=="Y" || $config["Taxes"]["price_includes_vat"]=="Y") && $vat_index>0) {
		$vat_percent = doubleval(array_pop(func_query_first("SELECT value FROM $sql_tbl[vat_rates] WHERE rateid='$vat_index'")));

		if ($config["Taxes"]["price_includes_vat"]=="N") {
			$price += price_format($vat=($price*$vat_percent/100));
		}
		else {
			$vat = ($price - $price*100/($vat_percent+100));
		}
		if ($config["General"]["eu_national"]!="Y") {
			$price -= price_format($vat);
			$vat_percent = 0;
		}
	}
	else
		$vat_percent = 0;

	return array($price, $vat_percent);
}

#
# Calculate total weight of all products in cart
#
function func_weight_products($products) {

        foreach($products as $product)
                $total_weight+=$product["weight"]*$product["amount"];

        if (!$total_weight)
                $total_weight = 1;

        return $total_weight;
}

function func_weight_shipping_products ($products) {
        $total_weight = 0;

        foreach ($products as $product) {
                if ($product["free_shipping"] != "Y" && $product["distribution"] == "")
                        $total_weight += $product["weight"]*$product["amount"];
        }

        return $total_weight;
}

#
# This function increments product rating
#
function func_increment_rating($productid) {
        global $sql_tbl;

        db_query("update $sql_tbl[products] set rating=rating+1 where productid='$productid'");
}

#
# This function creates array with order data
#
function func_select_order($orderid) {
        global $sql_tbl, $config;

        #$order = func_query_first("select $sql_tbl[orders].*, $sql_tbl[shipping].shipping from $sql_tbl[orders], $sql_tbl[shipping] where $sql_tbl[orders].orderid='$orderid' and $sql_tbl[shipping].shippingid=$sql_tbl[orders].shippingid");
		$o_date = "date+'".$config["General"]["timezone_offset"]."' as date";
        $order = func_query_first("select *, $o_date from $sql_tbl[orders] where $sql_tbl[orders].orderid='$orderid'");

	    if ($order["giftcert_ids"]) {
    	    $order["applied_giftcerts"] = split("\*", $order["giftcert_ids"]);
	        if ($order["applied_giftcerts"]) {
	            $tmp = array();
	            foreach($order["applied_giftcerts"] as $k=>$v) {
	                if ($v) {
	                    list($arr[giftcert_id], $arr[giftcert_cost]) = split(":", $v);
	                    $tmp[] = $arr;
	                }
	            }
	            $order["applied_giftcerts"] = $tmp;
	        }
	    }

        $shipping = func_query_first("select shipping from $sql_tbl[shipping] where shippingid='".$order["shippingid"]."'");

        $order["shipping"] = $shipping["shipping"];

        $order["details"]=text_decrypt($order["details"]);
		$order["details"]=stripslashes($order["details"]);
		$order["notes"]=stripslashes($order["notes"]);
		if ($order["reg_numbers"])
			$order["reg_numbers"]=unserialize($order["reg_numbers"]);
		if ($order["taxes_applyed"]) {
			$taxes_applyed = explode("-", $order["taxes_applyed"]);
			foreach($taxes_applyed as $k=>$v)
				$order["applied_taxes"][$v] = $v;
		}

        $order["b_statename"] = func_get_state($order["b_state"]);
        $order["b_countryname"] = func_get_country($order["b_country"]);
        $order["s_statename"] = func_get_state($order["s_state"]);
        $order["s_countryname"] = func_get_country($order["s_country"]);

        return($order);
}

#
# This function returns data about specified order ($orderid)
#
function func_order_data($orderid) {
        global $sql_tbl, $config;

		$gc_add_date = "add_date+'".$config["General"]["timezone_offset"]."' as add_date";
		$o_date = "date+'".$config["General"]["timezone_offset"]."' as date";

        $products = func_query("select $sql_tbl[products].*, $gc_add_date, $sql_tbl[order_details].* from $sql_tbl[order_details], $sql_tbl[products] where $sql_tbl[order_details].orderid='$orderid' and $sql_tbl[order_details].productid=$sql_tbl[products].productid");
#
# If products are not present in products table, but they are present in
# order_details, then create fake $products from order_details data
#
        if(!$products) $products = func_query("select $sql_tbl[order_details].*, 'PRODUCT (deleted from database)' as product from $sql_tbl[order_details] where $sql_tbl[order_details].orderid='$orderid'");

        $giftcerts = func_query("select *, $gc_add_date from $sql_tbl[giftcerts] where orderid='$orderid'");

    	$order = func_select_order($orderid);
	    $userinfo = func_query_first("select *, $o_date from $sql_tbl[orders] where orderid='$orderid'");

		$userinfo["s_countryname"] = $userinfo["s_country_text"] = func_get_country($userinfo["s_country"]);
		$userinfo["s_statename"] = $userinfo["s_state_text"] = func_get_state($userinfo["s_state"]);
		$userinfo["b_statename"] = func_get_state($userinfo["b_state"]);
		$userinfo["b_countryname"] = func_get_country($userinfo["b_country"]);

		if (!$products)
                $products = array ();

        return(array("order"=>$order,"products"=>$products,"userinfo"=>$userinfo, "giftcerts"=>$giftcerts));
}

#
# This function creates order entry in orders table
#
function func_place_order($payment_method, $order_status, $order_details) {

        global $cart, $userinfo, $discount_coupon, $mail_smarty, $config, $active_modules, $single_mode, $partner;
        global $sql_tbl;
		global $wlid;

		$mintime = 10;
		$check_order = func_query_first("SELECT orderid FROM $sql_tbl[orders] WHERE login='".addslashes($userinfo["login"])."' AND '".time()."'-date<'$mintime'");
		if ($check_order) return false;

		if(($order_status != "I") && ($order_status != "Q"))return;

        $orderids = array ();

        $products = func_products_in_cart($cart, $userinfo["membership"]);

		$giftcert_discount = $cart["giftcert_discount"];
		if ($cart["applied_giftcerts"]) {
			foreach($cart["applied_giftcerts"] as $k=>$v) {
				$giftcert_str = join("*", array($giftcert_str, "$v[giftcert_id]:$v[giftcert_cost]"));
				db_query("UPDATE $sql_tbl[giftcerts] SET status='U' WHERE gcid='$v[giftcert_id]'");
			}
		}

		$giftcert_id = $cart["giftcert_id"];

        foreach ($cart["orders"] as $current_order) {

			if (!$single_mode) {
				$giftcert_discount = $current_order["giftcert_discount"];
				$giftcert_str = "";
				if ($current_order["applied_giftcerts"]) {
		            foreach($current_order["applied_giftcerts"] as $k=>$v)
		                $giftcert_str = join("*", array($giftcert_str, "$v[giftcert_id]:$v[giftcert_cost]"));
				}
			}

			$reg_numbers = array();

			if ($config["Taxes"]["regnumber_gst"] && $current_order["tax_gst"]>"0")
				$reg_numbers[] = "GST: ".$config["Taxes"]["regnumber_gst"];
			if ($config["Taxes"]["regnumber_pst"] && $current_order["tax_pst"]>"0")
				$reg_numbers[] = "PST: ".$config["Taxes"]["regnumber_pst"];
			if ($config["Taxes"]["regnumber_vat"] && $current_order["total_vat"]>"0")
				$reg_numbers[] = "VAT: ".$config["Taxes"]["regnumber_vat"];

			$reg_numbers = addslashes(serialize($reg_numbers));

			$taxes_applyed = array();

			if ($config["Taxes"]["use_us_taxes"] == "Y")
				$taxes_applyed[] = "G";
			if ($config["Taxes"]["use_canadian_taxes"] == "Y")
				$taxes_applyed[] = "C";
			if ($config["Taxes"]["use_vat"] == "Y")
				$taxes_applyed[] = "V";
			if ($taxes_applyed) $taxes_applyed = join("-", $taxes_applyed);

#
# Insert into orders
#
        db_query("insert into $sql_tbl[orders] (login, total, giftcert_discount, giftcert_ids, subtotal, shipping_cost, shippingid, tax, tax_gst, tax_pst, total_vat, taxes_applyed, discount, coupon, coupon_discount, date, status, payment_method, flag, details, title, firstname, lastname, company, b_address, b_city, b_state, b_country, b_zipcode, s_address, s_city, s_state, s_country, s_zipcode, phone, fax, email, url, reg_numbers) values ('".addslashes($userinfo["login"])."', '$current_order[total_cost]', '$giftcert_discount', '$giftcert_str', '$current_order[sub_total]','$current_order[shipping_cost]', '$cart[shippingid]', '$current_order[tax_cost]', '$current_order[tax_gst]', '$current_order[tax_pst]', '$current_order[total_vat]', '$taxes_applyed', '$current_order[discount]', '".addslashes($current_order[coupon])."', '$current_order[coupon_discount]', '".time()."', '$order_status', '".addslashes($payment_method)."', 'N', '".addslashes(text_crypt($order_details))."', '".addslashes($userinfo["title"])."', '".addslashes($userinfo["firstname"])."', '".addslashes($userinfo["lastname"])."', '".addslashes($userinfo["company"])."', '".addslashes($userinfo["b_address"])."', '".addslashes($userinfo["b_city"])."', '".addslashes($userinfo["b_state"])."', '".addslashes($userinfo["b_country"])."', '".addslashes($userinfo["b_zipcode"])."', '".addslashes($userinfo["s_address"])."', '".addslashes($userinfo["s_city"])."', '".addslashes($userinfo["s_state"])."', '".addslashes($userinfo["s_country"])."', '".addslashes($userinfo["s_zipcode"])."', '".addslashes($userinfo["phone"])."', '".addslashes($userinfo["fax"])."', '$userinfo[email]', '".addslashes($userinfo["url"])."', '$reg_numbers')");

        $orderid=db_insert_id();


#
# Partner commission
#
		if ($partner)
			include "../inc/partner_commission.php";


		$orderids[] = $orderid;
        $order=func_select_order($orderid);

#
# Insert into order details
#
		foreach($products as $product) {
			if (($single_mode) or ($product["provider"] == $current_order["provider"])) {
				db_query("insert into $sql_tbl[order_details] (orderid, productid, product_options, amount, price, provider) values ('$orderid','$product[productid]','".addslashes($product["product_options"])."','$product[amount]','$product[price]','".addslashes($product["provider"])."')");

#
# Insert into subscription_customers table (for subscription products)
#
				if ($active_modules["Subscriptions"])
					include "../modules/Subscriptions/subscriptions_cust.php";

#
# Check if this product is in Wish list
#
				if ($active_modules["Wishlist"])
					include "../modules/Wishlist/place_order.php";

			}
		}

if ((($single_mode) or (!$current_order["provider"])) and ($cart["giftcerts"])) {
        foreach($cart["giftcerts"] as $giftcert) {

                $gcid = strtoupper(md5(uniqid(rand())));
#
# status == Pending!
#
				db_query("insert into $sql_tbl[giftcerts] (gcid, orderid, purchaser, recipient, send_via, recipient_email, recipient_firstname, recipient_lastname, recipient_address, recipient_city, recipient_state, recipient_country, recipient_zipcode, recipient_phone, message, amount, debit, status, add_date) values ('$gcid', '$orderid','".addslashes($giftcert[purchaser])."','".addslashes($giftcert[recipient])."','$giftcert[send_via]','$giftcert[recipient_email]','".addslashes($giftcert[recipient_firstname])."','".addslashes($giftcert[recipient_lastname])."','".addslashes($giftcert[recipient_address])."','".addslashes($giftcert[recipient_city])."','$giftcert[recipient_state]','$giftcert[recipient_country]','$giftcert[recipient_zipcode]','$giftcert[recipient_phone]','".addslashes($giftcert[message])."','$giftcert[amount]','$giftcert[amount]','P','".time()."')");

        }
}

#
# Mark discount coupons used
#

        $discount_coupon = $current_order[coupon];
        if ($discount_coupon) {
                db_query("update $sql_tbl[discount_coupons] set times_used=times_used+1 where coupon='$discount_coupon'");
                db_query("update $sql_tbl[discount_coupons] set status='U' where coupon='$discount_coupon' and times_used=times");
                $discount_coupon="";
        }

#
# Mail template processing
#

		$admin_notify = (($order_status == "Q") || ($order_status == "I" && $config["Email"]["enable_init_order_notif"] == "Y"));
		$customer_notify = (($order_status == "Q") || ($order_status == "I" && $config["Email"]["enable_init_order_notif_customer"] == "Y"));

        $order_data = func_order_data($orderid);
        $mail_smarty->assign("products",$order_data["products"]);
        $mail_smarty->assign("giftcerts",$order_data["giftcerts"]);
        $mail_smarty->assign("order",$order_data["order"]);

		$prefix = ($order_status=="I"?"init_":"");

		if ($customer_notify) {
#
# Notify customer by email
#
	        func_send_mail($userinfo["email"], "mail/".$prefix."order_customer_subj.tpl", "mail/".$prefix."order_customer.tpl", $config["Company"]["orders_department"], false);
		}

		if ($admin_notify) {
#
# Notify orders department by email
#
			func_send_mail($config["Company"]["orders_department"], "mail/".$prefix."order_notification_subj.tpl", "mail/order_notification_admin.tpl", $userinfo["email"], true, true);

#
# Notify provider (or providers) by email
#
                if ((!$single_mode) and ($current_order["provider"])) {
                        $pr_result = func_query_first ("SELECT email FROM $sql_tbl[customers] WHERE login='$current_order[provider]'");
                        $prov_email = $pr_result ["email"];
						if ($prov_email != $config["Company"]["orders_department"])
							func_send_mail($prov_email, "mail/".$prefix."order_notification_subj.tpl", "mail/order_notification.tpl", $userinfo["email"], true);
                } else {
                        $providers = array();
                        foreach($products as $product) {
                                $pr_result = func_query_first("select email from $sql_tbl[customers] where login='$product[provider]'");
                                if ($pr_result["email"])
									$providers[] = $pr_result["email"];
                        }

						if ($providers) {
	                        $providers = array_unique($providers);

    	                    foreach($providers as $prov_email)
								if ($prov_email != $config["Company"]["orders_department"])
            	                    func_send_mail($prov_email, "mail/".$prefix."order_notification_subj.tpl", "mail/order_notification.tpl", $userinfo["email"], true);
						}
                }
		}
}

        foreach($products as $product) {
#
# Decrease number of products in stock and increase product rating
#
        db_query("update $sql_tbl[products] set rating=rating+1, avail=avail-".($config["General"]["unlimited_products"]=="Y"?"0":$product["amount"])." where productid=".$product["productid"]
);

        $warning = func_query_first("select (avail-low_avail_limit) as warning, provider from $sql_tbl[products] where productid=".$product[productid]);

        if ($warning[warning] <= 0) {
#
# Mail template processing
#
            $product_updated = func_query_first("select avail, low_avail_limit from $sql_tbl[products] where productid='$product[productid]'");
            $mail_smarty->assign("product", $product_updated+$product);

            func_send_mail($config["Company"]["orders_department"], "mail/lowlimit_warning_notification_subj.tpl", "mail/lowlimit_warning_notification_admin.tpl", $config["Company"]["orders_department"], true);

			$pr_result = func_query_first ("SELECT email FROM $sql_tbl[customers] WHERE login='".$warning[provider]."'");
			if((!$single_mode) and ($pr_result["email"]!=$config["Company"]["orders_department"]))
					func_send_mail($pr_result["email"], "mail/lowlimit_warning_notification_subj.tpl", "mail/lowlimit_warning_notification_admin.tpl", $config["Company"]["orders_department"], true);
                }
        }

        return $orderids;
}


#
# This function change order status in orders table
#
function func_change_order_status($orderids, $status, $advinfo="")
{
        global $config, $mail_smarty, $active_modules;
        global $customer_language, $sql_tbl;

	if(!is_array($orderids))$orderids = array($orderids);

	foreach($orderids as $orderid) {
		$order_data = func_order_data($orderid);
		$order=$order_data["order"];

		if($advinfo)
			$info = addslashes(text_crypt($order["details"]."\n--- Advanced info ---\n".$advinfo));

		db_query("update $sql_tbl[orders] set status='$status'".(($advinfo)? ", details='".$info."'" : "")." where orderid='$orderid'");

		if($status == "P" && $order["status"] != "P") {
			func_process_order($orderid);
		}
		elseif($status == "D" && $order["status"] != "D" && $order["status"] != "F") {
			func_decline_order($orderid, $status);
		}
		elseif($status == "F" && $order["status"] != "F" && $order["status"] != "D") {
			func_update_quantity($order_data["products"]);
		}
		elseif ($status == "C" && $order["status"] != "C") {
			func_complete_order($orderid);
		}

	}
}


#
# This function performs activities nedded when order is processed
#
function func_process_order($orderids) {

        global $config, $mail_smarty, $active_modules;
        global $customer_language, $sql_tbl, $partner;
		global $single_mode;

	if(!is_array($orderids))$orderids = array($orderids);

	foreach($orderids as $orderid)
	{
        $order_data = func_order_data($orderid);

        $order = $order_data["order"];
        $userinfo = $order_data["userinfo"];
        $products = $order_data["products"];
        $giftcerts = $order_data["giftcerts"];

        $res = func_query_first ("SELECT language FROM $sql_tbl[customers] WHERE login='$userinfo[login]'");
        $customer_language = func_get_language ($res[language]);

        $mail_smarty->assign("customer",$userinfo);
        $mail_smarty->assign("products",$products);
        $mail_smarty->assign("giftcerts",$giftcerts);
        $mail_smarty->assign("order",$order);

#
# Order processing routine
# Send gift certificates
#
    if ($order["applied_giftcerts"]) {
#
# Search for enabled to applying GC
#
        $flag = true;
        foreach($order["applied_giftcerts"] as $k=>$v) {
            $res = func_query_first("SELECT gcid FROM $sql_tbl[giftcerts] WHERE gcid='$v[giftcert_id]' AND debit>='$v[giftcert_cost]'");
            if (!$res["gcid"]) {
                $flag = false;
                break;
            }
        }
#
# Decrease debit for applied GC
#
        if ($flag)
            foreach($order["applied_giftcerts"] as $k=>$v) {
                db_query("UPDATE $sql_tbl[giftcerts] SET debit=debit-'$v[giftcert_cost]' WHERE gcid='$v[giftcert_id]'");
				db_query("UPDATE $sql_tbl[giftcerts] SET status='A' WHERE debit>0 AND gcid='$v[giftcert_id]'");
				db_query("UPDATE $sql_tbl[giftcerts] SET status='U' WHERE debit<=0 AND gcid='$v[giftcert_id]'");
        }
        else
            return false;
    }


        if($giftcerts)
                foreach($giftcerts as $giftcert) {
                db_query("update $sql_tbl[giftcerts] set status='A' where gcid='$giftcert[gcid]'");
				if ($giftcert["send_via"] == "E")
	                func_send_gc($userinfo["email"], $giftcert);
        }

#
# Send mail notifications
#
	$providers= func_query("select provider from $sql_tbl[order_details] where $sql_tbl[order_details].orderid='$orderid' group by provider");
	if($providers)
		foreach($providers as $provider) {
			$email_pro = array_pop(func_query_first("select email from $sql_tbl[customers] where login='$provider[provider]'"));
			if (!empty($email_pro) && $email_pro != $config["Company"]["orders_department"])
				func_send_mail($email_pro, "mail/order_notification_subj.tpl", "mail/order_notification.tpl", $config["Company"]["orders_department"], false);
		}

	func_send_mail($userinfo["email"], "mail/order_cust_processed_subj.tpl", "mail/order_customer_processed.tpl", $config["Company"]["orders_department"], false);
	func_send_mail($config["Company"]["orders_department"], "mail/order_notification_subj.tpl", "mail/order_notification.tpl", $config["Company"]["orders_department"], false);

#
# Send E-goods download keys
#
        if($active_modules["Egoods"])
                include "../modules/Egoods/send_keys.php";

#
# Update statistics for sold products
#
        if ($active_modules["Advanced_Statistics"])
		{
                include "../modules/Advanced_Statistics/prod_sold.php";
		}
	}
}

#
# This function performs activities nedded when order is complete
#
function func_complete_order($orderid) {

        global $config, $mail_smarty, $active_modules;
        global $customer_language, $sql_tbl;

        $order_data = func_order_data($orderid);

        $order = $order_data["order"];
        $userinfo = $order_data["userinfo"];
        $products = $order_data["products"];
        $giftcerts = $order_data["giftcerts"];

        $res = func_query_first ("SELECT language FROM $sql_tbl[customers] WHERE login='$userinfo[login]'");
        $customer_language = func_get_language ($res[language]);

        $mail_smarty->assign("customer",$userinfo);
        $mail_smarty->assign("products",$products);
        $mail_smarty->assign("giftcerts",$giftcerts);
        $mail_smarty->assign("order",$order);

		#
		# Send mail notifications
		#
		func_send_mail($userinfo["email"], "mail/order_cust_complete_subj.tpl", "mail/order_customer_complete.tpl", $config["Company"]["orders_department"], false);
}

#
# This function joins order_id's and urlencodes 'em
#
function func_get_urlencoded_orderids ($orderids) {
		if (is_array($orderids))
	        return urlencode (join (",", $orderids));
}

#
# This function performs activities nedded when order is declined
# status may be assign (D)ecline or (F)ail
# (D)ecline order sent mail to customer, (F)ail - not
#
function func_decline_order($orderids, $status = "D") {

        global $config, $mail_smarty;
        global $customer_language, $sql_tbl;

		if(($status != "D") && ($status != "F")) return;

	if(!is_array($orderids))$orderids = array($orderids);

	foreach($orderids as $orderid)
	{
#
# Order decline routine
#
        $order_data = func_order_data($orderid);

        $order = $order_data["order"];
        $userinfo = $order_data["userinfo"];
        $products = $order_data["products"];
        $giftcerts = $order_data["giftcerts"];

		# Send mail notifications
	if($status == "D")
   	{
		$res = func_query_first ("SELECT language FROM $sql_tbl[customers] WHERE login='$userinfo[login]'");
        $customer_language = func_get_language ($res[language]);

        $mail_smarty->assign("customer",$userinfo);
        $mail_smarty->assign("products",$products);
        $mail_smarty->assign("giftcerts",$giftcerts);
        $mail_smarty->assign("order",$order);

		func_send_mail($userinfo["email"], "mail/decline_notification_subj.tpl","mail/decline_notification.tpl", $config["Company"]["orders_department"], false);
	}

#
#		χοσσταξαχμιχαεν δισλαυξτ! (c) SDG!
#
        $discount_coupon = $order[coupon];
        if ($discount_coupon) {
                db_query("update $sql_tbl[discount_coupons] set status='A' where coupon='$discount_coupon' and times_used=times");
                db_query("update $sql_tbl[discount_coupons] set times_used=times_used-1 where coupon='$discount_coupon'");
                $discount_coupon="";
        }

#
# ξαδο χεςξυτψ δεξψηι σ ηιζτα (c) SDG!
# Increase debit for declined GC
#
    if ($order["applied_giftcerts"] && ($order["status"]=="P" || $order["status"]=="C"))
    	foreach($order["applied_giftcerts"] as $k=>$v)
		{
         	db_query("UPDATE $sql_tbl[giftcerts] SET debit=debit+'$v[giftcert_cost]' WHERE gcid='$v[giftcert_id]'");
         	db_query("UPDATE $sql_tbl[giftcerts] SET status='A' WHERE debit>0 and gcid='$v[giftcert_id]'");
    	}



# Set GC's status to 'D'
	if ($giftcerts)
		foreach($giftcerts as $giftcert)
		{
			db_query("update $sql_tbl[giftcerts] set status='D' where gcid='$giftcert[gcid]'");
		}

	if ($config["General"]["unlimited_products"] != "Y")
		func_update_quantity ($products);

	}
}

#
# Generate zones list
#
function func_shipping_zones() {

        global $single_mode, $login, $sql_tbl;

        $provider_condition=($single_mode?"":"and provider='$login'");

#
# Prepare all zones list (gather zones from country_zones and state_zones
#
        $all_zones = func_query("select zoneid from $sql_tbl[country_zones] where 1 $provider_condition group by zoneid order by zoneid");

        if(empty($all_zones)) $all_zones = array();

        $all_zones = array_merge($all_zones,func_query("select zoneid from $sql_tbl[state_zones] where 1 $provider_condition group by zoneid order by zoneid"));

        $zones = array();

        foreach($all_zones as $zone_data)
        $zones[]=$zone_data["zoneid"];

        $zones = array_unique($zones);

        $all_zones=array(array("zoneid"=>0,"zone"=>"Zone Default"));

        foreach($zones as $zone_data)
        $all_zones[] = array("zoneid"=>$zone_data, "zone"=>"Zone $zone_data");

        return $all_zones;
}

#
# This function returns true if $cart is empty
#
function func_is_cart_empty($cart) {
        return !($cart["products"] || $cart["giftcerts"]);
}

#
# This function sends GC emails (called from func_place_order
# and provider/order.php"
#
function func_send_gc($from_email, $giftcert) {
        global $mail_smarty, $config;
        global $customer_language;

        $giftcert["purchaser_email"] = $from_email;
        $mail_smarty->assign("giftcert", $giftcert);

#
# Send GC to recipient
#
        func_send_mail($giftcert["recipient_email"], "mail/giftcert_subj.tpl", "mail/giftcert.tpl", $from_email, false);
#
# Send notifs to $orders_department & purchaser
#
        func_send_mail($from_email, "mail/giftcert_notification_subj.tpl", "mail/giftcert_notification.tpl", $config["Company"]["orders_department"], false);
        func_send_mail($config["Company"]["orders_department"], "mail/giftcert_notification_subj.tpl", "mail/giftcert_notification.tpl", $from_email, true);
}

#
# This function checks for exception of product options for product
#
function func_check_product_options ($productid, $productoptions) {
        global $sql_tbl;

        # First parse product options
        $product_options = array ();

        if ($productoptions) {
                foreach ($productoptions as $key=>$value) {
                        $result = func_query_first ("SELECT * FROM $sql_tbl[product_options] WHERE productid='$productid' AND optclass='$key'");
                        $options = func_parse_options ($result[options]);
                        $selected_option = $options [$value][option];
                        $product_options [$key] = $selected_option;
                }
        }

        $result = func_query ("SELECT * FROM $sql_tbl[product_options_ex] WHERE productid='$productid'");

        if (!$result)
                return;

        foreach ($result as $key=>$value) {
                $exceptions = array ();

                $columns = explode (";", $value[exception]);

                # Trim exceptions
                foreach ($columns as $subvalue) {
                        $exception = explode ("=", $subvalue);
                        $exception_optclass = trim ($exception[0]);
                        $exception_option = trim ($exception[1]);

                        $exceptions [$exception_optclass] = $exception_option;
                }

                $ex_size = sizeof($exceptions);
                $ex_found = 0;

                foreach ($exceptions as $subkey => $subvalue) {
                        if ($product_options[$subkey] == $subvalue)
                                $ex_found ++;
                }

                if ($ex_found == $ex_size) {
                        func_header_location("product.php?productid=$productid&err=options");
                }
        }
}

#
# This function parses options string
#
function func_parse_options($option_lines) {
        $return = array();

        if (empty($option_lines)) return;

        $options = explode("\n", $option_lines);

        foreach($options as $option_line) {
                $option_line = chop($option_line);
                if (empty($option_line)) continue;

		if (strpos($option_line, "="))
                        $option = substr($option_line, 0, strpos($option_line, "="));
                else
                        $option = substr($option_line, 0);

                $surcharge = strstr($option_line, "=");
                $surcharge = str_replace("=", "", $surcharge);
                $surcharge_type = (strstr($surcharge, "%") ? "percent" : "absolute");
                $surcharge = str_replace("%", "", $surcharge);

		if ($surcharge == "")
			$surcharge = "0";
#
# Check validity code goes here
#
                $return[] = array("option"=>$option, "surcharge"=>$surcharge, "type"=>$surcharge_type);
        }
        return $return;
}

function func_pgp_encrypt($message) {
        global $config;

        putenv("PGPPATH=".$config["PGP"]["pgp_home_dir"]);
        putenv("PGPHOME=".$config["PGP"]["pgp_home_dir"]);

		$pgp_prog = $config["PGP"]["pgp_prog"];
		$pgp_key = $config["PGP"]["pgp_key"];

        $message = addslashes($message);

        if ($config["PGP"]["use_pgp6"] == "Y") {
                $fn = tempnam("/tmp", "msg");
                $fd = fopen($fn, "w");
                fwrite($fd, $message);
                fclose($fd);

				exec("$pgp_prog +batchmode +force -ea $fn \"$pgp_key\"");

                unlink($fn);
                $fd = fopen("$fn.asc", "r");
                $message = fread($fd, 65535);
                fclose($fd);
                unlink("$fn.asc");
        } else {
                $message = `echo "$message" | $pgp_prog +batchmode +force -fea "$pgp_key" 2>/dev/null`;

        }

        return $message;
}

#
# Move products back to the inventory
#
function func_update_quantity($products) {
        global $sql_tbl;

        if ($products) {
                foreach ($products as $product) {
                        db_query("UPDATE $sql_tbl[products] SET avail=avail+'$product[amount]' WHERE productid='$product[productid]'");
                }
        }
}

function func_pgp_remove_key() {
        global $config;

        putenv("PGPPATH=".$config["PGP"]["pgp_home_dir"]);
        putenv("PGPHOME=".$config["PGP"]["pgp_home_dir"]);

        $pgp_prog = $config["PGP"]["pgp_prog"];
        $pgp_key = $config["PGP"]["pgp_key"];

        if ($config["PGP"]["use_pgp6"] == "Y") {
                `$pgp_prog -kr +force +batchmode '$pgp_key'`;
        } else {
                `$pgp_prog -kr +force '$pgp_key'`;
        }
}

function func_pgp_add_key() {
        global $config;

        putenv("PGPPATH=".$config["PGP"]["pgp_home_dir"]);
        putenv("PGPHOME=".$config["PGP"]["pgp_home_dir"]);

        $fn = tempnam("/tmp", "pub_key");

        $fd = fopen($fn, "w");
        fwrite($fd, $config["PGP"]["pgp_public_key"]);
        fclose($fd);

        $pgp_prog = $config["PGP"]["pgp_prog"];
        $pgp_key = $config["PGP"]["pgp_key"];

        if ($config["PGP"]["use_pgp6"] == "Y") {
                `$pgp_prog +batchmode -ka $fn 2>&1`;
                `$pgp_prog +batchmode -ks "$pgp_key"`;
        } else {
                `$pgp_prog -ka +force +batchmode $fn >/dev/null 2>&1`;
                `$pgp_prog +batchmode -ks '$pgp_key'`;
        }

        unlink($fn);
}

function func_update_pgp() {
        global $config;

        func_pgp_remove_key();
        func_pgp_add_key();
}

function func_get_language($lang) {
        global $sql_tbl;

        $result = func_query("SELECT * FROM $sql_tbl[languages] WHERE code='$lang'");
        if (!$result)
                return array();

        $return = array();

        foreach ($result as $key => $value) {
                $return [$value[name]] = $value[value];
        }

        return $return;
}

function func_parse_cookie_array($cookies) {
	return;
}

function func_http_get_request($host, $post_url, $post_str) {
        global $_COOKIE;

        $cookie = "";

        $result = "";
        $header_passed = false;

        $fp = fsockopen($host, 80, $errno, $errstr, 30);
        if (!$fp) {
                return array ("", "");
        } else {
                fputs ($fp, "GET $post_url?$post_str HTTP/1.0\r\n");
                fputs ($fp, "Host: $host\r\n");
                fputs ($fp, "User-Agent: Mozilla/4.5 [en]\r\n");
                fputs ($fp,"\r\n");

                $http_header = array ();
                $http_header["ERROR"] = chop(fgets($fp,4096));
                $cookies = array ();

                while (!feof($fp)) {
                        if (!$header_passed)
                                $line = fgets($fp, 4096);
                        else
                                $result .= fread($fp, 65536);

                        if ($header_passed == false && ($line == "\n" || $line == "\r\n")) {
				$header_passed = true;
				continue;
			}

			if ($header_passed == false) {
				$header_line = explode(": ", $line, 2);
				$header_line[0] = strtoupper($header_line[0]);
				$http_header[$header_line[0]] = chop($header_line[1]);

				if ($header_line[0] == 'SET-COOKIE')
					array_push($cookies, chop($header_line[1]));
			}
		}

		fclose($fp);
        }

        func_parse_cookie_array($cookies);

        return array($http_header, $result);
}

function func_http_post_request($host, $post_url, $post_str, $cook = "") {
	global $_COOKIE;

	$result = "";
	$header_passed = false;

	$fp = fsockopen($host, 80, $errno, $errstr, 30);
	if (!$fp) {
		#die("Cant connect ($errno)<br>\n");
		return array ("", "");
	} else {
		#fputs ($fp, "POST $post_url HTTP/1.0\r\n");
                fputs($fp, "POST http://$host$post_url HTTP/1.0\r\n");
                fputs($fp, "Host: $host\r\n");

		if (!empty($cook))
			fputs($fp, "Cookie: ".$cook."\r\n");

		fputs($fp, "User-Agent: Mozilla/4.5 [en]\r\n");
		fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
		fputs($fp, "Content-Length: ".strlen($post_str)."\r\n");
		fputs($fp, "\r\n");
		fputs($fp, $post_str."\r\n\r\n");

		$http_header = array();
		$http_header["ERROR"] = chop(fgets($fp,4096));
		#echo $http_header["ERROR"];exit;

		$cookies = array();
		while (!feof($fp)) {
			$line = fgets($fp,4096);
			#echo "$line<br>";

			if ($header_passed == false && ($line == "\n" || $line == "\r\n")) {
				$header_passed = true;
				continue;
			}

			if ($header_passed == false) {
				$header_line = explode(": ", $line, 2);
				$header_line[0] = strtoupper($header_line[0]);
				$http_header[$header_line[0]] = chop($header_line[1]);

				if ($header_line[0] == 'SET-COOKIE')
					array_push($cookies, chop($header_line[1]));
				continue;
			}
			$result .= $line;
		}

		fclose ($fp);
    	}

	func_parse_cookie_array($cookies);

	return array($http_header, $result, $cookies);
}
#
# Control function for HTTPS subsystem
#
# Currently used as internal buffer. Contents of internal buffer are used for
# logging reasons later. (e.g. when payment transaction is failed)
#
# Available commands:
#  PUT - store content in internal buffer
#  GET - get content from internal buffer
#  IGNORE - ignore 'PUT' commands
#  STORE - do not ignore 'PUT' commands
#  PURGE - clean internal buffer
#
function func_https_ctl($command, $arg=false) {
	static $responses = array();
	static $store_responses = true;

	switch ($command) {
	case 'GET':
		return $responses;
	case 'PUT':
		if ($store_responses) {
			list($sec, $usec) = explode(' ', microtime());
			$label = date('d-m-Y H:i:s', $sec).' '.$usec;
			$responses[$label] = $arg;
		}
		return true;
	case 'PURGE':
		$responses = array();
		break;
	case 'STORE':
		$store_responses = true;
		break;
	case 'IGNORE':
		$store_responses = false;
		break;
	}

	return false;
}

#
# Checking that posted image is exist
#
function func_check_image_posted($file_upload_data, $type) {

	global $config;

	$return = false;

	if ($file_upload_data["imtype"] != $type)
		return false;

	if ($file_upload_data["source"] == "U") {
		if ($fd = fopen($file_upload_data["file_path"], "rb")) {
			fclose($fd);
			$return = true;
		}
	}
	else
		$return = file_exists($file_upload_data["file_path"]);

	if ($return) {
		switch ($file_upload_data["imtype"]) {
			case "C":
				$return = ($file_upload_data["file_size"] <= $config["Images"]["icons_size_limit"] || $config["Images"]["icons_size_limit"]=="0");
				break;
			case "T":
				$return = ($file_upload_data["file_size"] <= $config["Images"]["thumbnails_size_limit"] || $config["Images"]["thumbnails_size_limit"]=="0");
				break;
			case "D":
				$return = ($file_upload_data["file_size"] <= $config["Images"]["det_images_size_limit"] || $config["Images"]["det_images_size_limit"]=="0");
				break;
			case "W":
				$return = ($file_upload_data["file_size"] <= $config["Images"]["pcicons_size_limit"] || $config["Images"]["pcicons_size_limit"]=="0");
		}
	}
	return $return;
}

#
# Get image content function
#
function func_get_image_content($file_upload_data, $id) {

	global $config;

	$file_aliases_count_max = 99;

	switch($file_upload_data["imtype"]) {
		case "C":
			$config_data["location"] = $config["Images"]["icons_location"];
			break;
		case "W":
			$config_data["location"] = $config["Images"]["pcicons_location"];
			break;
		case "T":
			$config_data["location"] = $config["Images"]["thumbnails_location"];
			break;
		case "D":
			$config_data["location"] = $config["Images"]["det_images_location"];
			break;
		default:
			return false;
	}

	$file_path = $file_upload_data["file_path"];

	if ($fd = fopen($file_path, "rb")) {

		if ($config_data["location"] == "FS") {
#
# ...else image is path to file
#
			$image = $file_path;
		}
		else
		{
#
# If image should be stored in the database, get image content from file
#
			if ($file_upload_data["source"] == "U")
				$file_size = 1000000;
	        else
				$file_size = filesize($file_path);

			$image = addslashes(fread($fd, $file_size));
		}
		fclose($fd);

		if ($file_upload_data["source"] == "L" && !empty($file_upload_data["dir_upload"])) {

			if ($config_data["location"] == "FS") {
#
# For FS storing. If image has been uploaded, move it to specified directory
#
				$file_name = ($file_upload_data["imtype"]=="W"?"w_$id":($file_upload_data["imtype"]=="C"?"c_$id":($file_upload_data["imtype"]=="T"?"t_$id":"d_$id")));
				$file_type = (strstr($file_path,"gif")?"gif":(strstr($file_path,"png")?"png":"jpg"));

#
# Check the existing file
#
		        $counter = 1;
	    	    $file_name_tmp = $file_name;

		        while (file_exists($file_upload_data["dir_upload"]."/".$file_name_tmp.".".$file_type) && $counter<$file_aliases_count_max) {
		            $file_name_tmp = $file_name."_".sprintf("%02d", $counter);
		            $counter++;
		        }
		        $file_name = $file_name_tmp;

				$image = $file_upload_data["dir_upload"]."/".$file_name.".".$file_type;
				copy($file_path, $image);
				@chmod($image, 0666);
			}
#
# Delete temporary file
#
			@unlink($file_path);
		}

	}
	return array("image"=>$image, "image_type"=>$file_upload_data["image_type"], "image_x"=>$file_upload_data["image_x"], "image_y"=>$file_upload_data["image_y"], "file_size"=>$file_upload_data["file_size"]);
}

function func_weight_in_grams($weight) {
	global $config;
	return $weight*$config["General"]["weight_symbol_grams"];
}

#
# Get international description of options
#
function func_product_options_lng($product_options, $short=false) {
	global $sql_tbl, $store_language;

	foreach($product_options as $k=>$product_option) {
		$res = func_query_first("SELECT * FROM $sql_tbl[product_options_lng] WHERE $sql_tbl[product_options_lng].optionid='".$product_option["optionid"]."' AND $sql_tbl[product_options_lng].code='$store_language'");

		if ($res) {
#
# Parse internatinal options
#
			$option = "";
			$options = explode("\n", $res["options"]);
			foreach($options as $option_line) {
				$option_line = chop($option_line);
				if (empty($option_line)) continue;
				if (strpos($option_line, "="))
					$option[] = substr($option_line, 0, strpos($option_line, "="));
				else
					$option[] = substr($option_line, 0);
			}
			if (!empty($res["opttext"]))
				$product_options[$k]["opttext"] = $res["opttext"];
			if (is_array($product_option["options"]))
				foreach ($product_option["options"] as $i=>$v) {
					$product_options[$k]["options"][$i]["option_orig"] = $product_options[$k]["options"][$i]["option"];
					if (!empty($option[$i]))
						$product_options[$k]["options"][$i]["option"] = $option[$i];
					if (!empty($res["optclass"]))
						$product_options[$k]["options"][$i]["optclass"] = $res["optclass"];
				}
		}
	}
	return $product_options;
}

#
# This module generates download key which is sent to customer
# and inserts this key into database
#
function keygen($productid, $key_TTL) {
	global $sql_tbl;
	$key = md5(uniqid(rand()));
	$expires = time() + $key_TTL*3600;
	db_query("INSERT INTO $sql_tbl[download_keys] (download_key, expires, productid) VALUES('$key', '$expires', '$productid')");
	return $key;
}

#
# Flush output
#
function func_flush() {
    if (preg_match("/Apache(.*)Win/", getenv("SERVER_SOFTWARE")))
        echo str_repeat(" ", 2500);
	elseif (preg_match("/(.*)MSIE(.*)\)$/", getenv("HTTP_USER_AGENT")))
        echo str_repeat(" ", 256);
    ob_end_flush();
	flush();
}

#
# For testing purpose: outputs contents of requested variables
# example:
#  func_print_r($categories,$cart,$userinfo,$GLOBALS);
#
function func_print_r() {
	static $count = 0;
	$args = func_get_args();
	if (!empty($args)) {
		?><DIV ALIGN=LEFT><PRE><FONT><?
		foreach($args as $index=>$variable_content){
			?><b>Debug [<?echo $index."/".$count;?>]:</b> <?
			print_r($variable_content);
			echo "\n";
		}
		?></FONT></PRE></DIV><?
	}
	$count++;
}

#
# For testing purpose: outputs contents of requested global variables
# example:
#   global $categories, $cart, $userinfo;
#   func_print_d("categories","cart","userinfo","GLOBALS");
#

function func_print_d() {
	$varnames = func_get_args();
	?><DIV ALIGN=LEFT><PRE><FONT><?
	if (!empty($varnames)) {
		foreach($varnames as $variable_name){
			if( !is_string($variable_name) || empty($variable_name) ){
				?><b>Debug notice:</b> try to use func_print_d("varname1","varname2") instead of func_print_d($varname1,$varname2); <?
			}
			else {
				echo "<B>$variable_name</B> = ";
				if( $variable_name == 'GLOBALS' )
					print_r($GLOBALS);
				else{
					if( !@isset($GLOBALS[$variable_name]) ){
						?>is unset!<?
					}
					else
						print_r($GLOBALS[$variable_name]);
				}
			}
			echo "\n";
		}
	}
	else {
		?><b>Debug notice:</b> try to use func_print_d("varname1","varname2") instead of func_print_d($varname1,$varname2); <?
	}
	?></FONT></PRE></DIV><?
}

#
# Emulator for the is_executable function if it doesn't exists (f.e. under windows)
#

function func_is_executable($file) {
	if( function_exists("is_executable") ) return is_executable($file);
	return is_readable($file);
}

#
# For curl lookup
# with minor modifications can be used for any executable lookups
#

function func_find_curl() {
	$dirs = split(PATH_SEPARATOR,getenv("PATH"));
	array_unshift($dirs,"..".DIRECTORY_SEPARATOR."payment");
	foreach($dirs as $dir){
		$file = $dir.DIRECTORY_SEPARATOR."curl";
		if( func_is_executable($file) ) return realpath($file);
		$file .= ".exe";
		if( func_is_executable($file) ) return realpath($file);

	}
	return false;
}

function func_get_thumbnail_url($productid) {
	global $config, $sql_tbl, $xcart_dir, $http_location;

	if ($config["Images"]["thumbnails_location"] == "FS") {
#
# Thumbnail data
#
		$thumbnail_info = func_query_first("SELECT image_path, image_type FROM $sql_tbl[thumbnails] WHERE productid='$productid'");
			if (eregi("^(http|ftp)://", $thumbnail_info["image_path"]))
			# image_path is an URL
				return $thumbnail_info["image_path"];
			elseif (eregi($xcart_dir, $thumbnail_info["image_path"])) {
			# image_path is an locally placed image
				$url = $http_location.ereg_replace($xcart_dir, "", $thumbnail_info["image_path"]);
				return $url;
			}
	}
	return false;

}

#
# This function removes orders and related info from the database
# $orders can be: 1) orderid; 2) orders array with orderid keys
function func_delete_order($orders) {
	global $sql_tbl;

	$_orders = array();

	if (is_array($orders)) {
		foreach($orders as $order)
			if (!empty($order["orderid"]))
				$_orders[] = $order["orderid"];
	}
	elseif (is_numeric($orders))
		$_orders[] = $orders;

#
# Delete orders from the database
#
	db_query("LOCK TABLES $sql_tbl[orders] WRITE, $sql_tbl[order_details] WRITE, $sql_tbl[giftcerts] WRITE, $sql_tbl[partner_payment] WRITE, $sql_tbl[subscription_customers] WRITE");
	foreach($_orders as $orderid) {
	    db_query("DELETE FROM $sql_tbl[orders] WHERE orderid='$orderid'");
	    db_query("DELETE FROM $sql_tbl[order_details] WHERE orderid='$orderid'");
	    db_query("DELETE FROM $sql_tbl[giftcerts] WHERE orderid='$orderid'");
	    db_query("DELETE FROM $sql_tbl[partner_payment] WHERE orderid='$orderid'");
	    db_query("DELETE FROM $sql_tbl[subscription_customers] WHERE orderid='$orderid'");
	}
#
# Check if no orders in the database
#
    $total_orders = array_pop(func_query_first("SELECT COUNT(*) FROM $sql_tbl[orders]"));
    if ($total_orders == 0) {
#
# Clear Order ID counter (auto increment field in the xcart_orders table)
#
        db_query("DELETE FROM $sql_tbl[orders]");
        db_query("DELETE FROM $sql_tbl[order_details]");
        db_query("DELETE FROM $sql_tbl[partner_payment]");
        db_query("DELETE FROM $sql_tbl[subscription_customers]");

	}
	db_query("UNLOCK TABLES");
}

#
# Get information about directory:
#  - how many files does directory contain
#  - what size does directory have
#
function func_get_dir_status( $directory ) {
	$result = array("files"=>0, "size"=>0);
	$dp = opendir ($directory);
	while ($file = readdir ($dp)) {
		if( $file == "." || $file == ".." ) continue;
		$path = $directory.DIRECTORY_SEPARATOR.$file;

		if( is_file( $path ) ) {
			$result["files"] ++;
			$result["size"]  += filesize($path);
		}
		else {
			$temp = func_get_dir_status($path);
			$result["files"] += $temp["files"];
			$result["size"]  += $temp["size"];
		}
	}
	closedir($dp);

	return $result;
}
?>
