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
# $Id: smarty.php,v 1.17.2.4 2003/09/29 12:32:49 svowl Exp $
#

umask(0);

#
# If we are in subdir of X-Cart dir, then include with '../'
# else include with './'
#
if (!@include("../Smarty-2.5.0/Smarty.class.php"))
	@include("./Smarty-2.5.0/Smarty.class.php");

#
# Get absolute path
#
define('BASEDIR', realpath(dirname(__FILE__)));

#
# Smarty object for processing html templates
#
$smarty = new Smarty;

#
# Store all compiled templates to the single directory
#
$smarty->use_sub_dirs = false;

$smarty->template_dir = BASEDIR.DIRECTORY_SEPARATOR."skin1";
$smarty->compile_dir = BASEDIR.DIRECTORY_SEPARATOR."templates_c";
$smarty->config_dir = BASEDIR.DIRECTORY_SEPARATOR."skin1";
$smarty->cache_dir = BASEDIR.DIRECTORY_SEPARATOR."cache";
$smarty->secure_dir = BASEDIR.DIRECTORY_SEPARATOR."skin1";
$smarty->debug_tpl="file:debug_templates.tpl";

if( !is_dir($smarty->compile_dir) && !file_exists($smarty->compile_dir) )
	@mkdir($smarty->compile_dir);

if( !is_writable($smarty->compile_dir) || !is_dir($smarty->compile_dir) ){
	echo "Can't write template cache in the directory: <b>".$smarty->compile_dir."</b>.<br>Please check if it exists, and have writable permissions.";
	exit;
}

$file_temp_dir=$smarty->compile_dir;

$smarty->assign("ImagesDir","../skin1/images");
$smarty->assign("SkinDir","../skin1");

#
# Smarty object for processing mail templates
#
$mail_smarty = $smarty;

#
# WARNING :
# Please ensure that you have no whitespaces / empty lines below this message.
# Adding a whitespace or an empty line below this line will cause a PHP error.
#
?>
