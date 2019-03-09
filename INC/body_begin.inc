<?
require_once "inc/class_school.php";
require_once "inc/class_schools.php";
$allschools = new schools;
$allschools->load_donation_schools();
?>
<body bgcolor="#FFFFFF">
<table width="700" border="0" align="center" cellpadding="2" cellspacing="0" bordercolor="#FFFFFF" bgcolor="#FFFFFF">
  <tr valign='top'>
    <td valign="top">
      <div align="top">
        <table width="698" height="100%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr valign='top'>
<?
echo "          <td width=\"595\" colspan=\"2\" align=\"left\" valign=\"top\" background=\"images/background.gif\">\n";
echo "				<table width=\"100%\" height=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"5\">\n";
echo "					<tr height=100 align=\"left\" valign=\"top\">\n";
echo "          			<td height=\"100\" colspan=2>\n";
echo "						<table width=\"746\" height=\"92\" background=\"images/D2E_banner.png\">";
echo "						<tr><td valign=\"top\" align=\"center\">\n";
echo "						</td></td></table>\n";
echo "					</td>	</tr>\n";
echo "					<tr align=\"left\" valign=\"middle\">\n";
echo "          			<td colspan=2><table width='100%' border=0 cellpadding=0 cellspacing=0><tr><td align='left' valign='middle'>\n";
echo "						<FORM ACTION=\"..\\\">";
echo "  	        			<font size='-1'>";
?>
							<select name='q_school_id' id='schoolid' onchange="window.open(this.options[this.selectedIndex].value,'_top')">
								<option value=''>Quick Search by School</option>
<?
		reset($allschools->school_list);
		while (list($schoolid, $school) = each($allschools->school_list)) {
			if ($school->inactive != "Y") {
				echo "\t\t\t\t\t\t\t\t<Option value='donation_search.php?f_school_id=".$school->school_id."&f_district_id=".$school->district_id."'>$school->school_name</option>\n";
			}
		}
?>
								</select>&nbsp;&nbsp;<a href='donation_search.php'><u>Advanced Search</u></a></font></form>
<?
echo "					</td>";
echo "          			<td align=right>\n";
echo "  	        			<font size='-1'>".date("F j, Y h:m A",time()-10900)."</font>\n";
echo "					</td></tr></table>\n";
echo "					</td></tr>\n";
?>
                <tr valign='top'>