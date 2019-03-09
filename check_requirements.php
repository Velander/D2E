<?php
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
# $Id: check_requirements.php,v 1.5 2003/11/17 12:07:49 svowl Exp $
#

#
# This script checks requirements
#


function ini_get_bool($param) {
	$res = ini_get($param);
	return (intval($res) || !strcasecmp($res,"on"))?1:"";
}

if ( in_array(basename($PHP_SELF), array("image.php","icon.php","product_image.php", "wizimg.php")) )
	return;

#
# Temporary array for checking requirements
#
$CHECK_REQUIREMENTS = array();

#
# Try to set needed values for some options
#
@ini_set("magic_quotes_runtime", 0);
@ini_set("magic_quotes_sybase", 0);

#
# These arrays contains "Option"=>"value"
# req_vars_real: contains real values
# req_vars: contains required values
#
$CHECK_REQUIREMENTS["req_vars"][] = array("option"  =>"PHP version",
					  "req_val" =>"4.0.6",
					  "real_val"=>"",
					  "critical"=>1);

$CHECK_REQUIREMENTS["req_vars"][] = array("option"  =>"MySQL support is ...",
					  "req_val" =>"On",
					  "real_val"=>"",
					  "critical"=>1);

$CHECK_REQUIREMENTS["req_vars"][] = array("option"  =>"safe_mode",
					  "req_val" =>0,
					  "real_val"=>"",
					  "critical"=>1);

$CHECK_REQUIREMENTS["req_vars"][] = array("option"  =>"disabled functions list",
					  "req_val" =>array("exec","popen", "system"),
					  "real_val"=>array(),
					  "critical"=>0);

$CHECK_REQUIREMENTS["req_vars"][] = array("option"  =>"file_uploads",
					  "req_val" =>1,
					  "real_val"=>"",
					  "critical"=>1);

$CHECK_REQUIREMENTS["req_vars"][] = array("option"  =>"upload_max_filesize",
					  "req_val" =>"2M",
					  "real_val"=>"",
					  "critical"=>0);

$CHECK_REQUIREMENTS["req_vars"][] = array("option"  =>"magic_quotes_gpc",
					  "req_val" =>1,
					  "real_val"=>"",
					  "critical"=>1);

$CHECK_REQUIREMENTS["req_vars"][] = array("option"  =>"magic_quotes_runtime",
					  "req_val" =>0,
					  "real_val"=>"",
					  "critical"=>0);

$CHECK_REQUIREMENTS["req_vars"][] = array("option"  =>"magic_quotes_sybase",
					  "req_val" =>0,
					  "real_val"=>"",
					  "critical"=>0);

$CHECK_REQUIREMENTS["req_vars"][] = array("option"  =>"track_vars",
					  "req_val" =>1,
					  "real_val"=>"",
					  "critical"=>1);

$CHECK_REQUIREMENTS["req_vars"][] = array("option"  =>"register_globals",
					  "req_val" =>1,
					  "real_val"=>"",
					  "critical"=>0);

/*
$CHECK_REQUIREMENTS["req_vars_real"] = $CHECK_REQUIREMENTS["req_vars"] =
			 array("PHP version" => "4.0.6",
			 	   "MySQL support is ..." => "On",
				   "safe_mode" => 0,
				   "disabled functions list" => array("exec","popen", "system"),
				   "file_uploads" => 1,
				   "upload_max_filesize" => "2M",
				   "magic_quotes_gpc" => 1,
				   "magic_quotes_runtime" => 0,
				   "magic_quotes_sybase" => 0,
				   "track_vars" => 1,
				   "register_globals" => 1);
*/
$CHECK_REQUIREMENTS["show_details"] = 0;
$CHECK_REQUIREMENTS["dis_func"] = 0;

foreach ($CHECK_REQUIREMENTS["req_vars"] as $k=>$v) {
	switch ($v["option"]) {
	#
	# Check PHP version
	#
		case "PHP version":
			$CHECK_REQUIREMENTS["req_vars"][$k]["real_val"] = phpversion();
			if ($CHECK_REQUIREMENTS["req_vars"][$k]["real_val"] < $v["req_val"])
				$CHECK_REQUIREMENTS["req_vars"][$k]["trigger"] = 1;
			break;
	#
	# Check the MySQL supporting
	#
		case "MySQL support is ...":
			$CHECK_REQUIREMENTS["req_vars"][$k]["real_val"] = function_exists('mysqli_connect')?"On":"Off";
			if ($CHECK_REQUIREMENTS["req_vars"][$k]["real_val"] != $v["req_val"])
				$CHECK_REQUIREMENTS["req_vars"][$k]["trigger"] = 1;
			break;
	#
	# Check the disabled functions list
	#
		case "disabled functions list":
			$CHECK_REQUIREMENTS["req_vars"][$k]["real_val"] = ini_get("disable_functions");
			if (is_array($CHECK_REQUIREMENTS["req_vars"][$k]["real_val"])) {
				foreach($CHECK_REQUIREMENTS["req_vars"][$k]["real_val"] as $func) {
					if (in_array($func, $v["req_val"])) {
						$CHECK_REQUIREMENTS["dis_func"] = 1;
						$CHECK_REQUIREMENTS["req_vars"][$k]["trigger"] = 1;
						break;
					}
				}
				$CHECK_REQUIREMENTS["req_vars"][$k]["real_val"] = implode(", ", $CHECK_REQUIREMENTS["req_vars"][$k]["real_val"]);
			}
			else
				$CHECK_REQUIREMENTS["req_vars"][$k]["real_val"] = "Empty";
			$CHECK_REQUIREMENTS["req_vars"][$k]["req_val"] = "Not (".implode(", ", $CHECK_REQUIREMENTS["req_vars"][$k]["req_val"]).")";
			break;
	#
	# Check the upload_max_filesize value (as recommendation)
	#
		case "upload_max_filesize":
			$CHECK_REQUIREMENTS["req_vars"][$k]["real_val"] = ini_get("upload_max_filesize");
			break;
	#
	# Check track_vars value (since 4.0.3 this option always "On")
	#
		case "track_vars":
			$CHECK_REQUIREMENTS["req_vars"][$k]["real_val"] = ini_get_bool("track_vars");
			if (phpversion() >= "4.0.3")
				$CHECK_REQUIREMENTS["req_vars"][$k]["real_val"] = 1;
			elseif ($CHECK_REQUIREMENTS["req_vars"][$k]["real_val"] != $CHECK_REQUIREMENTS["req_vars"][$k]["req_val"])
				$CHECK_REQUIREMENTS["req_vars"][$k]["trigger"] = 1;
			break;
	#
	# Check the rest options
	#
		default:
			$CHECK_REQUIREMENTS["req_vars"][$k]["real_val"] = (@ini_get_bool($v["option"])?1:0);
			if ($CHECK_REQUIREMENTS["req_vars"][$k]["real_val"] != $CHECK_REQUIREMENTS["req_vars"][$k]["req_val"])
				$CHECK_REQUIREMENTS["req_vars"][$k]["trigger"] = 1;
	}
	if ($CHECK_REQUIREMENTS["req_vars"][$k]["trigger"] and $CHECK_REQUIREMENTS["req_vars"][$k]["critical"])
		$CHECK_REQUIREMENTS["show_details"] = 1;
}

if ($CHECK_REQUIREMENTS["show_details"] or isset($_GET["checkrequirements"])) {
?>

<HTML>
<HEAD>
<title>Checking requirements...</title>
</HEAD>

<BODY>

<TABLE border=0 cellpadding=2 cellspacing=2 width=70%>
<TR bgcolor="#CCCCCC">
<TH align=left>Option</TH>
<TH>Required</TH>
<TH>Currently</TH>
<TH>&nbsp;Status&nbsp;</TH>
<TH align=left>Comments</TH>
</TR>

<TR bgcolor=#FFFFFF>
<TD>Operation system</TD>
<TD align=center>-</TD>
<TD align=center>
<?php
list($os_type, $tmp) = split(" ", php_uname());
echo $os_type;
?>
</TD>
<TD align=center><font color=#00CC00><b>OK</b></font></TD>
<TD>&nbsp;</TD>
</TR>
<?php
#
# Display results in the HTML format
#
foreach ($CHECK_REQUIREMENTS["req_vars"] as $k=>$v) {
	$CHECK_REQUIREMENTS["warn"] = "";
	$CHECK_REQUIREMENTS["status"] = "";
	$CHECK_REQUIREMENTS["msg"] = "&nbsp;";
	if ($CHECK_REQUIREMENTS["req_vars"][$k]["trigger"]) {
		switch ($k) {
			case "PHP version":
				$CHECK_REQUIREMENTS["status"] = "Failed";
				$CHECK_REQUIREMENTS["msg"] = "PHP upgrade is needed";
				break;
			case "disabled functions list":
				$CHECK_REQUIREMENTS["status"] = "Warning";
				$CHECK_REQUIREMENTS["msg"] = "Some functionality may be lost";
				break;
			case "upload_max_filesize":
				$CHECK_REQUIREMENTS["status"] = "Warning";
				$CHECK_REQUIREMENTS["msg"] = "May be too low";
				break;
			case "register_globals":
				$CHECK_REQUIREMENTS["status"] = "Warning";
				$CHECK_REQUIREMENTS["msg"] = "Emulation is used";
				break;
			default:
				$CHECK_REQUIREMENTS["status"] = "Failed";
				$CHECK_REQUIREMENTS["msg"] = "Please check php.ini to correct problem";
		}
	}
	if ($CHECK_REQUIREMENTS["status"] == "Failed")
		$CHECK_REQUIREMENTS["warn"] = " style=\"FONT-WEIGHT:bold;COLOR:#CC0000\"";
	elseif ($CHECK_REQUIREMENTS["status"] == "Warning")
	        $CHECK_REQUIREMENTS["warn"] = " style=\"FONT-WEIGHT:bold;COLOR:#0000CC\"";
	$i = $i?0:1;
	echo "<TR bgcolor=#".($i?"EEEEEE":"FFFFFF").">";
	echo "<TD".$CHECK_REQUIREMENTS["warn"].">".$v["option"]."&nbsp;&nbsp;</TD>";
	echo "<TD align=center".$CHECK_REQUIREMENTS["warn"].">".$CHECK_REQUIREMENTS["req_vars"][$k]["req_val"]."</TD>";
	echo "<TD align=center".$CHECK_REQUIREMENTS["warn"].">".$v["real_val"]."</TD>";
	echo "<TD align=center".$CHECK_REQUIREMENTS["warn"].">".($CHECK_REQUIREMENTS["warn"]?$CHECK_REQUIREMENTS["status"]:"<font color=#00CC00><b>OK</b></font>")."</TD>";
	echo "<TD>".$CHECK_REQUIREMENTS["msg"]."</TD>";
	echo "</TR>";
}
?>
</TABLE>

<?php
if ($CHECK_REQUIREMENTS["show_details"]) {
?>
<BR>Please contact your host administrators and ask them to correct PHP-settings for your site according to the requirements above.<BR>
<BR>
<?php
}

@include_once "./top.inc.php";

?>

<TABLE border=0 cellpadding=2 cellspacing=2 width=70%>
<TR bgcolor="#CCCCCC">
<TH align=left>Directory</TH>
<TH>Permissions</TH>
<TH>Required</TH>
<TH align=left>Comments</TH>
</TR>

<TR>
<TD align=left> (root) <?php echo $xcart_dir; ?></TD>
<TD align=center><?php echo sprintf("%o",fileperms($xcart_dir)); ?></TD>
<TD align=center>xx755</TD>
<TD align=left></TD>
</TR>

<TR bgcolor=#EEEEEE>
<TD align=left> (customer) <?php echo DIR_CUSTOMER; ?></TD>
<TD align=center><?php echo sprintf("%o",fileperms($xcart_dir.DIR_CUSTOMER)); ?></TD>
<TD align=center>xx755</TD>
<TD align=left></TD>
</TR>

<TR>
<TD align=left> (admin) <?php echo DIR_ADMIN; ?></TD>
<TD align=center><?php echo sprintf("%o",fileperms($xcart_dir.DIR_ADMIN)); ?></TD>
<TD align=center>xx755</Td>
<TD align=left></TD>
</TR>

<TR bgcolor=#EEEEEE>
<TD align=left> (provider) <?php echo DIR_PROVIDER; ?></TD>
<TD align=center><?php echo sprintf("%o",fileperms($xcart_dir.DIR_PROVIDER)); ?></TD>
<TD align=center>xx755</TD>
<TD align=left></TD>
</TR>

<TR>
<TD align=left> (partner) <?php echo DIR_PARTNER; ?></TD>
<TD align=center><?php echo sprintf("%o",fileperms($xcart_dir.DIR_PARTNER)); ?></TD>
<TD align=center>xx755</TD>
<TD align=left></TD>
</TR>

</TABLE>
<br>
</BODY>

</HTML>

<?php
}

#
# Destroy temporary array
#
unset($CHECK_REQUIREMENTS);
?>
