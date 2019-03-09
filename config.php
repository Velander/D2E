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
# $Id: config.php,v 1.273.2.12 2003/11/03 12:58:54 mclap Exp $
#
# Global definitions & common functions
#

#
# Full path to X-Cart directory
#
$xcart_dir = dirname(__FILE__);

$reg_globals = ini_get("register_globals");

if (!$reg_globals) {
#
# register_globals=On emulation
#
	include_once($xcart_dir."/globals.php");
}

#
# SQL database details
#
$sql_host ='localhost';
$sql_user ='d2esite';
$sql_db ='d2e';
$sql_password ='ruby1465';

#
# X-Cart HTTP & HTTPS host
# and web directory where X-Cart installed
#
# NOTE:
# You should put here hostname ONLY without http:// or https:// prefixes
# Do not put slashes after the hostname
# Web dir is the directory in the URL, not the filesystem path
# Web dir must start with slash and have no slash at the end
# The only exception is when you configure for the root of the site,
# in which case you write single slash in it
#
# EXAMPLE 1:
# $xcart_http_host ="www.yourhost.com";
# $xcart_https_host ="www.securedirectories.com/yourhost.com";
# $xcart_web_dir ="/xcart";
# will result in the following URLs:
# http://www.yourhost.com/xcart
# https://www.securedirectories.com/yourhost.com/xcart
#
# EXAMPLE 2:
# $xcart_http_host ="www.yourhost.com";
# $xcart_https_host ="www.yourhost.com";
# $xcart_web_dir ="";
# will result in the following URLs:
# http://www.yourhost.com/
# https://www.yourhost.com/
#
$xcart_http_host ="www.donate2educate.org";
$xcart_https_host ="www.donate2educate.org";
#$xcart_http_host ="www.donate2educate.com";
#$xcart_https_host ="www.donate2educate.com";
$xcart_web_dir ="";

@include_once($xcart_dir."/config.local.php");

#
# SQL tables aliases...
#
$sql_tbl=array(
	"pc_icons" => "xcart_pc_icons",
	"build_wizards" => "xcart_build_wizards",
	"compatibility" => "xcart_compatibility",
	"compatibility_mode" => "xcart_compatibility_mode",
	"build_steps" => "xcart_build_steps",
	"build_parts" => "xcart_build_parts",
	"build_types" => "xcart_build_types",
	"categories" => "xcart_categories",
	"categories_lng" => "xcart_categories_lng",
	"cc_gestpay_data" => "xcart_cc_gestpay_data",
	"cc_pp3_data" => "xcart_cc_pp3_data",
	"ccprocessors" => "xcart_ccprocessors",
	"chprocessors" => "xcart_chprocessors",
	"config" => "xcart_config",
	"countries" => "xcart_countries",
	"country_tax" => "xcart_country_tax",
	"country_zones" => "xcart_country_zones",
	"customers" => "xcart_customers",
	"delivery" => "xcart_delivery",
	"discount_coupons" => "xcart_discount_coupons",
	"discounts" => "xcart_discounts",
	"download_keys" => "xcart_download_keys",
	"extra_fields" => "xcart_extra_fields",
	"featured_products" => "xcart_featured_products",
	"fedex_rates" => "xcart_fedex_rates",
	"fedex_zips" => "xcart_fedex_zips",
	"giftcerts" => "xcart_giftcerts",
	"icons" => "xcart_icons",
	"images" => "xcart_images",
	"languages" => "xcart_languages",
	"login_history" => "xcart_login_history",
	"maillist" => "xcart_maillist",
	"modules" => "xcart_modules",
	"newsletter" => "xcart_newsletter",
	"order_details" => "xcart_order_details",
	"orders" => "xcart_orders",
	"pages" => "xcart_pages",
	"partner_banners" => "xcart_partner_banners",
	"partner_clicks" => "xcart_partner_clicks",
	"partner_commissions" => "xcart_partner_commissions",
	"partner_payment" => "xcart_partner_payment",
	"partner_plans" => "xcart_partner_plans",
	"partner_plans_commissions" => "xcart_partner_plans_commissions",
	"partner_views" => "xcart_partner_views",
	"payment_methods" => "xcart_payment_methods",
	"php_sessions" => "xcart_php_sessions",
	"pricing" => "xcart_pricing",
	"product_bookmarks" => "xcart_product_bookmarks",
	"product_links" => "xcart_product_links",
	"product_options" => "xcart_product_options",
	"product_options_lng" => "xcart_product_options_lng",
	"product_options_ex" => "xcart_product_options_ex",
	"product_options_js" => "xcart_product_options_js",
	"product_reviews" => "xcart_product_reviews",
	"product_votes" => "xcart_product_votes",
	"products" => "xcart_products",
	"products_lng" => "xcart_products_lng",
	"referers" => "xcart_referers",
	"sessions_data" => "xcart_sessions_data",
	"shipping" => "xcart_shipping",
	"shipping_options" => "xcart_shipping_options",
	"shipping_rates" => "xcart_shipping_rates",
	"state_tax" => "xcart_state_tax",
	"state_zones" => "xcart_state_zones",
	"states" => "xcart_states",
	"stats_cart_funnel" => "xcart_stats_cart_funnel",
	"stats_customers_products" => "xcart_stats_customers_products",
	"stats_pages" => "xcart_stats_pages",
	"stats_pages_paths" => "xcart_stats_pages_paths",
	"stats_pages_views" => "xcart_stats_pages_views",
	"stats_shop" => "xcart_stats_shop",
	"subscription_customers" => "xcart_subscription_customers",
	"subscriptions" => "xcart_subscriptions",
	"tax_rates" => "xcart_tax_rates",
	"thumbnails" => "xcart_thumbnails",
	"vat_rates" => "xcart_vat_rates",
	"wishlist" => "xcart_wishlist",
	"zipcode_tax" => "xcart_zipcode_tax"
);

# Store Credit Cards
# if set to true, X-Cart will store Credit Card numbers in database
# If you want to use Subscription module set it to true
$store_cc = false;

# Store Check Attributes
# if set to true, X-Cart will store Check attributes in database
$store_ch = false;

# Store CVV2 code
# if set to true, X-Cart will store CVV2 in database
# Please note VISA International does not recommend to store CVV2 code with CC numbers
$store_cvv2 = false;

#
# Which image to show when product has no photo.
#
$default_image = "default_image.gif";
$default_icon = "default_icon.gif";
$default_banner = "default_banner.gif";
$default_pcicon = "prodconf.gif";
$shop_closed_file = "shop_closed.html";

#
# Single Store mode (Pro package only)
#
# If you own a Gold package, this value should always be equal to "true".
#
# If $single_mode is set to true the store has shipping
# rates, discounts, taxes and discounts coupons shared between all product
# providers. All product providers can edit each other's products.
# If $single_mode is set to false, each provider has his own
# rates, taxes which applied to on his products only.
#
$single_mode = true;

#
# FedEx Rates Directory
#
$fedex_default_rates_dir = dirname(__FILE__)."/shipping/FedEx/";

#
# Directory where logs are stored
#
$log_dir = "/log";

#
#
# DO NOT CHANGE ANYTHING BELOW THIS LINE UNLESS
# YOU REALLY KNOW WHAT ARE YOU DOING
#
#

if (!defined('PATH_SEPARATOR')) {
	if (strncasecmp(PHP_OS,'win',3))
		define('PATH_SEPARATOR', ';');
	else
		define('PATH_SEPARATOR', ':');
}

#
# Automatic repair of the broken indexes in mySQL tables
#
$mysqli_autorepair = false;

############################################################
# THE ERRORS TRACKING CODE
############################################################
#
# Debug mode turn on/off
# 0 - no debug info;
# 1 - display error (and exit script - for SQL errors);
# 2 - write errors to the log file (templates_c/xerrors.log)
# 3 - display error and write it to the file.
#
$debug_mode = 0;

$error_file_size_limit = 150; # (Kbytes) if 0 - no limit


$error_file_path = $log_dir;
$error_file_path = realpath("..").$error_file_path;

# Uncomment the line below if you know absolute path
# $error_file_path = "/home/username/public_html/xcart";
#

#
# Error reporting level:
#
if ($debug_mode)
	$x_error_reporting = E_ALL ^ E_NOTICE;
else
	$x_error_reporting = 0;

error_reporting ($x_error_reporting);

set_magic_quotes_runtime(0);
ini_set("magic_quotes_sybase",0);
ini_set("session.bug_compat_42",1);
ini_set("session.bug_compat_warn",0);

if ($debug_mode==2 || $debug_mode==0) {
	ini_set("display_errors",0);
	ini_set("display_startup_errors",0);
}
if ($debug_mode==2 || $debug_mode==3) {
	ini_set("log_errors", 1);
	ini_set("error_log", $error_file_path."/x-errors_php.txt");
	ini_set("log_errors_max_len", $error_file_size_limit);
	ini_set("ignore_repeated_errors", 1);
}
############################################################
# / THE ERRORS TRACKING CODE
############################################################

#
# Demo mode - protects several pages from writing
#
$admin_safe_mode = false;

#
# HTTP & HTTPS locations
#
$http_location = "http://$xcart_http_host".$xcart_web_dir;
$https_location = "https://$xcart_https_host".$xcart_web_dir;

$smarty->assign("http_location",$http_location);
$mail_smarty->assign("http_location",$http_location);
$smarty->assign("https_location",$https_location);
$mail_smarty->assign("https_location",$https_location);

#
# Files directory
#
$files_dir_name = "../files";
$files_http_location = $http_location."/files";

#
# Templates repository
# where original templates are located for "restore" facility
#
$templates_repository = "../skin1_original";

#
# Store sessions data in database
#

#
# Select the sessions mechanism:
# 1 - PHP sessions data stores in the file system
# 2 - PHP sessions data stores in the MySQL database
# 3 - use alternative sessions mechanism
$use_sessions_type = 3;

#
# Set the session name here
#
$XCART_SESSION_NAME = "xid";

#
# Skin configuration file.
# Configuration files are located under ./configs directory
#
$smarty->assign("skin_config","skin1.conf");
$mail_smarty->assign("skin_config","skin1.conf");

#
# Defined Titles
#
$name_titles = array("Mr.","Mrs.","Ms.");
$smarty->assign("name_titles",$name_titles);

#
# Defined Tax rules (NOT USED)
# tax_type =
# {"disabled", "all","national","international","in our state","out of our state"}
#
# $tax_names = array(
# array("name"=>"VAT", "type"=>"national"),
# array("name"=>"PST", "type"=>"disabled"),
# array("name"=>"GST", "type"=>"disabled")
# );
#
# $smarty->assign("tax_names",$tax_names);
#


#
# Anonimous user name
#
$anonymous_username_prefix="anonymous";

#
# Anonymous user password
#
$anonymous_password="42a51f1538a39636879414b681dd7df6";

#
# SALT & CODE for user password & credit card encryption
#
$CRYPT_SALT = 85; # any number ranging 1-255
$START_CHAR_CODE = 100; # 'd' letter

#
# Include functions
#
if (!@include_once("../include/func.php"))
	@include_once("./include/func.php");

#
# Connect to database
#

db_connect($sql_host, $sql_user, $sql_password);
db_select_db($sql_db) || die("Could not connect to SQL db");


#
# Read config variables from Database
# This variables are used iside php scripts, not in smarty templates
#
$c_result = db_query("select name, value, category from $sql_tbl[config]");
while ($row = db_fetch_row($c_result)) {
        #${$row[0]} = $row[1];
		if(!empty($row[2]))
			$config[$row[2]][$row[0]] = $row[1];
		else
			$config[$row[0]] = $row[1];
}
db_free_result($c_result);

$config["Sessions"]["session_length"] = 1800; # Session duraion (in seconds)

#
# Timezone offset (sec) = N hours x 60 minutes x 60 seconds
#
$config["General"]["timezone_offset"] = intval($config["General"]["timezone_offset"])*3600;

#
# End year for templates where date selectors is there are
#
$config["Company"]["end_year"] = date("Y", time()+$config["General"]["timezone_offset"]);

#
# Last database backup date
#
if ($config["db_backup_date"])
	$config["db_backup_date"] += $config["General"]["timezone_offset"];


if ($config["General"]["httpsmod"])
    if (!@include_once("../payment/func_https_".$config["General"]["httpsmod"].".php"))
	        @include_once("./payment/func_https_".$config["General"]["httpsmod"].".php");

if (empty($config["Appearance"]["thumbnail_width"]))
	$config["Appearance"]["thumbnail_width"] = 0;

#
# Assign config array to smarty
#
$smarty->assign("config",$config);
$mail_smarty->assign("config",$config);

#
# Including file for storing of PHP-sessions in MySQL database
#
if ($use_sessions_type == 2)
	if (!@include_once("../include/mysqli_sessions.php"))
		@include_once("./include/mysqli_sessions.php");

#
# Prepare session
#
if (!@include_once("../include/sessions.php"))
    @include_once("./include/sessions.php");

#
# Unserialize & Assign memberships levels
#
if ($config["membership_levels"])
	$config["membership_levels"] = unserialize ($config["membership_levels"]);
$smarty->assign ("membership_levels", $config["membership_levels"]);

#
# Unserialize & Assign card types
#
if ($config["card_types"])
	$config["card_types"] = unserialize ($config["card_types"]);
$smarty->assign ("card_types", $config["card_types"]);

#
# Include webmaster mode
#
if (!@include_once("../include/webmaster.php"))
	@include_once("./include/webmaster.php");

x_session_register("editor_mode");
if($config["General"]["enable_debug_console"]=="Y" || $editor_mode=='editor')
	$smarty->debugging=true;

$license ='02522A39';

#
# Read Modules and put in into $active_modules
#
$all_active_modules=func_query("select * from $sql_tbl[modules] where active='Y'");
if($all_active_modules){
	foreach($all_active_modules as $active_module)
		$active_modules[$active_module["module_name"]]=true;
}

$active_modules["Simple_Mode"]=true;

unset($all_active_modules);

$smarty->assign("active_modules",$active_modules);
$mail_smarty->assign("active_modules",$active_modules);

#
# Product Configurator active wizardz
#
if($active_modules[Product_Configurator])
{
	$pc_active = func_query("select wizard,titmenu from $sql_tbl[build_wizards] where status='Y'");
	$smarty->assign("pc_active",$pc_active);
}

if ($HTTP_GET_VARS["delimiter"]=="tab" || $HTTP_POST_VARS["delimiter"]=="tab")
	$delimiter = "\t";

#
# WARNING :
# Please ensure that you have no whitespaces / empty lines below this message.
# Adding a whitespace or an empty line below this line will cause a PHP error.
#
?>
