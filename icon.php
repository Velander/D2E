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
# $Id: icon.php,v 1.19.2.3 2003/06/24 10:43:23 svowl Exp $
#
# Show image by categoryid
#

require "./smarty.php";
require "./config.php";

if ($tmp) {

	x_session_register("file_upload_data");

	if (!empty($file_upload_data["file_path"]) && $file_upload_data["id"]==$categoryid && $file_upload_data["imtype"]=="C") {

		$file_path = $file_upload_data["file_path"];

		if ($file_upload_data["source"] == "U") {
			$file_size = 100000;
		}
		else
			$file_size = filesize($file_path);
			
		if ($fd = @fopen($file_path, "rb")) {
			$image_out = fread($fd, $file_size);
			fclose($fd);
		}
	}

}

if (empty($image_out)) {

	$result = db_query("SELECT image, image_path, image_type FROM $sql_tbl[icons] WHERE categoryid='$categoryid'");
	if (db_num_rows($result))
		list($image, $image_path, $image_type) = db_fetch_row($result);

	db_free_result($result);

	if ($config["Images"]["icons_location"] == "DB") {
		if (!empty($image))
			$image_out = $image;
		else
			$no_image_db = true;
	}

	if ($config["Images"]["icons_location"] == "FS" || $no_image_db) {

		if (!empty($image_path)) {
	        if ($fd = @fopen($image_path, "rb")) {
				fclose($fd);
				header("Content-type: $image_type");
				readfile($image_path);
				exit;
			}
		}
	}

}

if (!empty($image_out)) {
	header("Content-type: $image_type");
	echo $image_out;
} else {
	header("Content-type: image/gif");
	readfile($default_icon);
}

?>
