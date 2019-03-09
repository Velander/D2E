<?	require "inc/db_inc.php";
	require_once "inc/class_cart_item.php";
	require_once "inc/class_user.php";
	require_once "inc/class_project.php";
	require_once "inc/class_donation.php";
	require_once "inc/class_school.php";
	require_once "inc/class_schools.php";
	require_once "inc/class_district.php";
	require_once "inc/class_districts.php";
	require_once "inc/class_grade_level.php";
	require_once "inc/class_grade_levels.php";
	require_once "inc/class_project_type.php";
	require_once "inc/class_project_types.php";
	require_once "inc/class_affiliation.php";
	require_once "inc/class_affiliations.php";
	require_once "inc/class_state.php";
	require_once "inc/class_states.php";
	require_once "inc/class_country.php";
	require_once "inc/class_countries.php";
	require_once "inc/class_payment_type.php";
	require_once "inc/class_payment_types.php";
	require_once "inc/func.php";
	require_once "inc/func_https_ssleay.php";
	require_once "inc/class_authorizenet.php";
	require_once "inc/class_banner.php";

#require "smarty.php";
#require "config.php";
#require "customer/auth.php";

if ($debug) {
	echo "loading user<br>\n";
	flush();
}
	$user = new user();
	$user->load_user($User_ID);

if ($debug) {
	echo "loading schools<br>\n";
	flush();
}
	$schools = new schools();
	$schools->load_schools();

	$districts = new districts();
	$districts->load_districts();

	$district = new district();

if ($debug) {
	echo "loading grade_levels<br>\n";
	flush();
}
	$gradelevels = new grade_levels();
	$gradelevels->load_grade_levels();

if ($debug) {
	echo "loading states<br>\n";
	flush();
}
	$states = new states();
	$states->load_states();

	$countries = new countries();
	$countries->load_countries();

if ($debug) {
	echo "loading project_types<br>\n";
	flush();
}
	$projecttypes = new project_types();
	$projecttypes->load_project_types();

	$payment_types = new payment_types();
	$payment_types->load_payment_types();

	$user_affiliations = new affiliations();
	$user_affiliations->load_affiliations($User_ID);

	$authorize_net = new authorizenet();
	$message	= $_GET["message"];

	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$review				= $_POST["review"];
		$search_arg			= $_POST["earch_arg"];
		$Submit 			= $_POST["Submit"];
		$f_cartid			= $_POST["f_cartid"];
		$f_donationamt		= $_POST["f_donationamt"];
		$f_amtneeded		= $_POST["f_amtneeded"];
		$removeid			= $_POST["removeid"];
		if ($debug) {
			echo "Posting<br>\n";
			flush();
		}
		if ($Submit == "Checkout") {
			echo "<script type=\"text/javascript\">\nlocation.href='$https_location"."donation.php?review=D".(empty($HTTPS) ? "&uniqueid=".htmlentities(urlencode($uniqueid)):"")."'\n</script>\n";
		}
		if ($Submit == "Continue Shopping") {
#			echo "<script type=\"text/javascript\">\nlocation.href='$https_location"."donation_search.php?".(empty($HTTPS) ? "&uniqueid=".htmlentities(urlencode($uniqueid)):"")."'\n</script>\n";
			echo "<script type=\"text/javascript\">\nlocation.href='$http_location"."donation_search.php".$search_arg."'\n</script>\n";
		}
		if ($Submit == "Update Changes") {
			# Change contents of the Cart
			$i = 0;
			while ($f_cartid[$i]) {
				$user->change_cart_item($f_cartid[$i], $f_donationamt[$i], $f_amtneeded[$i]);
				$i += 1;
			}
		}
	} elseif ($removeid) {
		$user->remove_cart_item($removeid);
	}
?>
<html>
<head>
<?
	include "inc/cssstyle.php";
	$pagename = "$config_cart_page_name";
	$help_msg_name = "config_cart_help";
	$help_msg = "$config_cart_help";
	$help_width = "$config_cart_help_width";
	$help_height = "$config_cart_help_height";
	require "inc/title.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? require "inc/jscript.inc"; ?>
</head>
<? require "inc/body_begin.inc"; ?>
<? include "inc/nav.php"; ?>

	             <TD width="640" align="left" valign="top">
<?
	include "inc/banner_ads.php";
	if (!empty($config_cart_paragraph1)) {
		include "inc/box_begin.htm";
		echo "$config_cart_paragraph1\n";
		include "inc/box_end.htm";
	}
	if (!empty($message)) {
		include "inc/box_begin.htm";
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center>\n";
		include "inc/box_end.htm";
	}
?>
	<table width=100% cellspacing=1 cellpadding=1>
		<TR>
			<TD align=left width=50% bgcolor="<?=$color_table_hdg_bg;?>">
				<table width="100%" align="center" border=0 cellpadding=5 cellspacing=0>
					<TR>
						<TD bgcolor="<?=$color_table_hdg_bg;?>" align="left">
							<Font Size="+1" color="<?=$color_table_hdg_font;?>">Cart Contents</font>
						</TD>
					</TR>
				</table>
				<table width="100%" align="center" border=0 cellpadding=5 cellspacing=0>
					<TR height=235>
						<TD bgcolor="<?=$color_table_col_bg;?>">
							<form method="POST" ACTION="cart.php">
							<input type="hidden" name="uniqueid" value="<?=$uniqueid;?>">
							<TABLE ALIGN="left" BORDER=0 CELLSPACING=1 CELLPADDING=0 WIDTH="100%">
								<TR ALIGN="left" VALIGN="middle"><TD colspan=3>
<?
  	if (isset($User_ID)) {
  		include $path_root."inc/box_begin.htm";
?>
	<table width="100%" cellspacing=0 cellpadding=1 border=0 align=left>
		<TR>
			<TD Align='Center' colspan=5><b>Project Details</b></TD>
		</TR><TR>
			<TD Align='Left' Valign='Bottom'><b>Project Name</b></TD>
			<TD Align='Right' Valign='Bottom'><b>Funds<BR>Needed</b></TD>
			<TD Align='Right' Valign='Bottom'><b>Donation<BR>Amount</b></TD>
			<TD></TD>
		</TR>
<?
	reset($user->cart_item_list);
	$total = 0;
	while (list($cartid, $cartitem) = each($user->cart_item_list)) {
		$project = new project();
		if ($project->load_project($cartitem->project_id)) {
			$total += $cartitem->donation_amount;
			$districtid = $schools->school_district_id($project->school_id);
			$district = new district();
			$district->load_district($districtid);
			$checks_payable_to = $districts->district_administrator($schools->school_district_id($f_schoolid));
			$district_fax_number = $districts->district_administrator($schools->school_district_id($f_schoolid));
?>

		<TR>
			<TD Align='Left' Valign='Top'><?=$project->project_name;?><INPUT TYPE='hidden' NAME='f_cartid[]' VALUE='<?=$cartid;?>'></TD>
			<TD Align='Right' Valign='Top'><?=sprintf("%01.2f", ($project->amount_needed - $project->amount_donated()));?><INPUT TYPE='hidden' NAME='f_amtneeded[]' VALUE='<?=($project->amount_needed - $project->amount_donated());?>'></TD>
			<TD Align='Right' Valign='Top'><INPUT TYPE='text' NAME='f_donationamt[]' SIZE='6' VALUE='<?=sprintf("%01.2f", $cartitem->donation_amount);?>'></TD>
			<TD Width="1%" Align='Center' Valign='Top'>&nbsp;<a href="cart.php?removeid=<?=$project->project_id;?>"><font size="-2"><b>Remove</b></font></TD>
			</TR>
		</TR>
<?
		}
	}
?>
		<TR>
			<TD Align='Left' Valign='Top' Colspan='2'><b>Total Donation Amount</b></TD>
			<TD Align='Right' Valign='Top'><b><?=sprintf("%01.2f", $total);?></b>&nbsp;&nbsp;</TD></TR>
		</TR>
	</table>
<?
  		include $path_root."inc/box_end.htm";
  	}
?>

								<TR ALIGN="CENTER" VALIGN="middle">
									<TD>
										<input Type="Hidden" Name="search_arg" Value="<?=$search_arg;?>">
										<Input Type="Submit" Name="Submit" class="nicebtns" Value="Continue Shopping">
										<Input Type="Submit" Name="Submit" class="nicebtns" Value="Checkout">
										<Input Type="Submit" Name="Submit" class="nicebtns" Value="Update Changes">
									</TD>
									</Form>
								</TR>
							</TABLE>
						</TD>
					</TR>
				</TABLE>
			</TD>
		</TR>
	</table>
<? require "inc/body_end.inc"; ?>
</html>
