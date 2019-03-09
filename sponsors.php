<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";
	require_once "inc/class_users.php";
	require_once "inc/class_school.php";
	require_once "inc/class_schools.php";
	require_once "inc/class_banner.php";

	$all_schools = new schools;
	$all_schools->load_schools();

	$silver_sponsors = new users;
	$silver_sponsors->find_users("", "", "", "", "12");

	$sponsors = new users;
	$sponsors->find_users("", "", "", "", "11");
?>
<html>
<head>
<? require "inc/cssstyle.php"; ?>
<?
	$pagename = "$config_sponsors_page_name";
	$help_msg_name = "config_sponsors_help";
	$help_msg = "$config_sponsors_help";
	$help_width = "$config_sponsors_help_width";
	$help_height = "$config_sponsors_help_height";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>
                  <td width="640" align="left" valign="top">
<?	if (!empty($message))
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center><br>";
	if (!empty($config_sponsors_paragraph1)) {
		echo "$config_sponsors_paragraph1";
	}
?>
				<table border=0 cellpadding=0 cellspacing=0 width="100%">
					<tr valign="middle">
						<td align="center">
							<img src="images/SmartCar.jpg">
						</td>
					</tr>
				</table><BR>
<?
	# Select school sponsors.
#	echo "<H1>School Sponsors</H1><BR>";
	$sponsors = array();
	reset($all_schools->school_list);
	while (list($schoolid, $school) = each($all_schools->school_list)) {
		if (!array_key_exists($school->sponsor_user_id, $sponsors))
		{
			if ($all_schools->sponsor_half_banner($schoolid))
			{
			?>
				<table border=0 cellpadding=0 cellspacing=0 width="100%">
					<tr valign="middle">
						<td align="center">
							<? if ($all_schools->sponsor_url($schoolid)) echo "<a href=\"".$all_schools->sponsor_url($schoolid)."\" target=\"sponsor\">"; ?>
							<img src="<?=(empty($HTTPS)?$http_location:$https_location).$all_schools->sponsor_half_banner($schoolid);?>" border=0 alt="<?=$all_schools->school_name($schoolid);?> sponsored by <?=$all_schools->sponsor_name($schoolid);?>">
							<? if ($all_schools->sponsor_url($schoolid)) echo "</a>"; ?>
						</td>
					</tr>
				</table><BR>
			<? } elseif ($all_schools->sponsor_banner($schoolid))
			{
			?>
				<table border=0 cellpadding=0 cellspacing=0 width="100%">
					<tr valign="middle">
						<td align="center">
							<? if ($all_schools->sponsor_url($schoolid)) echo "<a href=\"".$all_schools->sponsor_url($schoolid)."\" target=\"sponsor\">"; ?>
							<img src="<?=(empty($HTTPS)?$http_location:$https_location).$all_schools->sponsor_banner($schoolid);?>" border=0 alt="<?=$all_schools->school_name($schoolid);?> sponsored by <?=$all_schools->sponsor_name($schoolid);?>">
							<? if ($all_schools->sponsor_url($schoolid)) echo "</a>"; ?>
						</td>
					</tr>
				</table><BR>
			<? } elseif ($all_schools->sponsor_name($schoolid) != "") { ?>
				<table border=0 cellpadding=0 cellspacing=0 width="100%">
					<tr valign="middle">
						<td align="center">
							<? if ($all_schools->sponsor_url($schoolid)) echo "<a href=\"".$all_schools->sponsor_url($schoolid)."\" target=\"sponsor\">"; ?>
							<H1><?=$all_schools->school_name($schoolid);?> sponsored by <?=$all_schools->sponsor_name($schoolid);?></H1>
							<? if ($all_schools->sponsor_url($schoolid)) echo "</a>"; ?>
						</td>
					</tr>
				</table><BR>
			<? }
			$sponsors[$school->sponsor_user_id] = $school->sponsor_user_id;
		}
	}
	# Display Silver Sponsors
	reset($silver_sponsors->user_list);
#	echo "<H1>Silver Sponsors</H1><BR>";
	while (list($sponsorid, $sponsor) = each($silver_sponsors->user_list)) {
		if (!array_key_exists($sponsorid, $sponsors))
		{
			if ($sponsor->half_banner_link) {
	?>
			<table border=0 cellpadding=0 cellspacing=0 width="100%">
				<tr valign="middle">
					<td align="center">
						<? if ($sponsor->url) echo "<a href=\"".$sponsor->url."\" target=\"sponsor\">"; ?>
						<img src="<?=(empty($HTTPS)?$http_location:$https_location).$sponsor->half_banner_link;?>" border=0 alt="<?=$sponsor->name;?>">
						<? if ($sponsor->url) echo "</a>"; ?>
					</td>
				</tr>
			</table><BR>
	<?
			$sponsors[$sponsorid] = $sponsorid;
			} elseif ($sponsor->banner_link) {
	?>
			<table border=0 cellpadding=0 cellspacing=0 width="100%">
				<tr valign="middle">
					<td align="center">
						<? if ($sponsor->url) echo "<a href=\"".$sponsor->url."\" target=\"sponsor\">"; ?>
						<img src="<?=(empty($HTTPS)?$http_location:$https_location).$sponsor->banner_link;?>" border=0 alt="<?=$sponsor->name;?>">
						<? if ($sponsor->url) echo "</a>"; ?>
					</td>
				</tr>
			</table><BR>
	<?
			$sponsors[$sponsorid] = $sponsorid;
			}
		}
	}
	# Display Sponsors
	reset($sponsors->user_list);
#	echo "<H1>Sponsors</H1><BR>";
	while (list($sponsorid, $sponsor) = each($sponsors->user_list)) {
		if (!array_key_exists($sponsorid, $sponsors))
		{
			if ($sponsor->half_banner_link) {
	?>
			<table border=0 cellpadding=0 cellspacing=0 width="100%">
				<tr valign="middle">
					<td align="center">
						<? if ($sponsor->url) echo "<a href=\"".$sponsor->url."\" target=\"sponsor\">"; ?>
						<img src="<?=(empty($HTTPS)?$http_location:$https_location).$sponsor->half_banner_link;?>" border=0 alt="<?=$sponsor->name;?>">
						<? if ($sponsor->url) echo "</a>"; ?>
					</td>
				</tr>
			</table><BR>
	<?
			$sponsors[$sponsorid] = $sponsorid;
			} elseif ($sponsor->banner_link) {
	?>
			<table border=0 cellpadding=0 cellspacing=0 width="100%">
				<tr valign="middle">
					<td align="center">
						<? if ($sponsor->url) echo "<a href=\"".$sponsor->url."\" target=\"sponsor\">"; ?>
						<img src="<?=(empty($HTTPS)?$http_location:$https_location).$sponsor->banner_link;?>" border=0 alt="<?=$sponsor->name;?>">
						<? if ($sponsor->url) echo "</a>"; ?>
					</td>
				</tr>
			</table><BR>
	<?
			$sponsors[$sponsorid] = $sponsorid;
			}
		}
	}
	# Display Ad banners
#	echo "<H1>Sponsors</H1><BR>";
	$banner = new banner;
	$bannerlist = $banner->all_banners();

	while (list($bannerid, $bannnerdate) = each($bannerlist)) {
		$banner->load_banner($bannerid);
		$sponsorid = $banner->user_id;
		if (!array_key_exists($sponsorid, $sponsors))
		{
			$sponsor = new user;
			$sponsor->load_user($sponsorid);
			if ($sponsor->half_banner_link) {
	?>
			<table border=0 cellpadding=0 cellspacing=0 width="100%">
				<tr valign="middle">
					<td align="center">
						<? if ($sponsor->url) echo "<a href=\"".$sponsor->url."\" target=\"sponsor\">"; ?>
						<img src="<?=(empty($HTTPS)?$http_location:$https_location).$sponsor->half_banner_link;?>" border=0 alt="<?=$sponsor->name;?>">
						<? if ($sponsor->url) echo "</a>"; ?>
					</td>
				</tr>
			</table><BR>
	<?
			$sponsors[$sponsorid] = $sponsorid;
			} elseif ($sponsor->banner_link) {
	?>
			<table border=0 cellpadding=0 cellspacing=0 width="100%">
				<tr valign="middle">
					<td align="center">
						<? if ($sponsor->url) echo "<a href=\"".$sponsor->url."\" target=\"sponsor\">"; ?>
						<img src="<?=(empty($HTTPS)?$http_location:$https_location).$sponsor->banner_link;?>" border=0 alt="<?=$sponsor->name;?>">
						<? if ($sponsor->url) echo "</a>"; ?>
					</td>
				</tr>
			</table><BR>
	<?
			$sponsors[$sponsorid] = $sponsorid;
			}
		}
	}
?>
                  </td>
<? require "inc/body_end.inc"; ?>
</html>
