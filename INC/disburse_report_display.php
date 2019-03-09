<?
	$prevdistrict = "";
	$prevschool = "";
	$prevprojectid = 0;
	$prevamount = 0;
	$prevreqamount = 0;
	$d_total = 0;
	$s_total = 0;
				# Donation Audit Report
				$sql  = "Select d.district_name, d.receives_funds, s.school_name, t.first_name teacher_fname, ";
				$sql .= "t.last_name teacher_lname, ";
				$sql .= "du.first_name donor_fname, du.last_name donor_lname, du.company donor_company, du.street donor_street, du.city donor_city, du.state donor_state, du.zip donor_zip, ";
				$sql .= "dn.contact_flag, dn.gift_first_name, dn.gift_last_name, dn.gift_street, dn.gift_city, dn.gift_state, dn.gift_zip, ";
				$sql .= "p.project_id, p.project_name, p.amount_needed, ";
				$sql .= "case when md.donation_date is null then dn.donation_date else md.donation_date end donation_date,  ";
				$sql .= "dp.amount ";
				$sql .= "from project p ";
				$sql .= "inner join user t on t.user_id = p.submitted_user_id ";
				$sql .= "inner join school s on s.school_id = p.school_id ";
				$sql .= "inner join district d on d.district_id = s.district_id ";
				$sql .= "inner join donation_project dp on dp.project_id = p.project_id ";
				$sql .= "inner join donation dn on dn.donation_id = dp.donation_id ";
				$sql .= "inner join user du on du.user_id = dn.user_id ";
				$sql .= "left join donation md on md.donation_id = dp.matching_donation_id ";
				$sql .= "left join user mdu on mdu.user_id = md.user_id ";
				$where = "dn.payment_received = 'Y'";
				if ($f_schoolid != 0)
					if (empty($where)) $where .= " and ";
					$where = "p.school_id = '$f_schoolid'";
				if ($f_gradelevel != 0) {
					if (empty($where)) $where .= " and ";
					$where .= "p.grade_level_id = '$f_gradelevel'";
				}
				if ($f_projecttype != 0) {
					if (empty($where)) $where .= " and ";
					$where .= "p.project_type_id = '$f_projecttype'";
				}
				if (!empty($where)) $sql .= " where $where";
				$sql .= "order by d.district_name, s.school_name, t.last_name, t.first_name, p.project_id"; ";
				echo "$sql<BR>";
				if ($results = mysql_query($sql)) {
				} else
					echo "<b>Error Occured: ".mysql_error()."<br>$sql<br>";
				echo "<font size='+1' color='$color_table_hdg_font'>DISBURSEMENT REPORT</font>\n";
				include "inc/box_middle.htm";
				$prevprojectid = 0;
				$donation_total = 0;
				$reqamount = $row[amount_needed];
				$amount = $row[amount];
				while($row = mysql_fetch_assoc($results)) {
					if ($row[school_name] != $prevschool) {
						if ($prevschool)
							echo "<tr><td></td><td>$prevschool Total</td><td>$s_total</td></tr>\n";
						$s_total = 0;
					}
					if ($row[district_name] != $prevdistrict) {
						if ($prevdistrict)
							echo "$prevdistrict Total: $d_total";
						$d_total = 0;
					}
					if (($prevprojectid != $row[project_id]) && ($prevprojectid != 0)) {
?>
						<tr>
						<td align='center'><b><?=$prevprojectid;?></b></td>
						<td align='left'><b>Total</b></td>
						<td><b><?=sprintf("%01.2f",$prevreqamount);?></b></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<? if ($donation_total >= $reqamount) { ?>
						<td><b>Fully Funded</b></td>
						<? } else { ?>
						<td><b>Partially Funded</b></td>
						<? } ?>
						<td>&nbsp;</td>
						<td align='right' valign='top'><b><?=sprintf("%01.2f",$donation_total);?></b></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						</tr>
<? 						$donation_total = 0;
					}

					if ($row[school_name] != $prevschool)
						echo "<tr><td colspan=9><h2>$row[school_name]</h2></td></tr>";
					$prevschool = $row[school_name];

					if ($row[district_name] != $prevdistrict)
						echo "<tr><td colspan=9><h1>$row[district_name]</h1></td></tr>\n";
					$prevdistrict = $row[district_name];
?>
<tr>
						<td align='center' valign='top'><?= (($projectid == $prevprojectid) ? "&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;" :
								"$projectid</td><td align='left' valign='top'>$projectname</td><td align='center' valign='top'>".(!empty($reqdate)?date("m/d/y",strtotime($reqdate)):"&nbsp;")."</td><td align='right' valign='top'>".sprintf("%01.2f", $amount));?>
						</td>
						<td align='left' valign='top'>
<?					if ($contact_flag == "G") {
						echo "$row[gift_first_name] $row[gift_last_name]";
						if ($row[gift_street])
							echo "\n$row[gift_street]";
						if ($row[gift_city] || $row[gift_state] || $row[gift_zip])
							echo "\n$row[gift_city] $row[gift_state] $row[gift_zip]");
					}
					elseif ($contact_flag == "A")
						echo "Anonymous";
					else {
						echo "$row[donor_fname] $row[donor_lname]";
						if ($row[donor_street])
							echo ("\n$row[donor_street]");
						if ($row(donor_city) || $row[donor_state] || $row[donor_zip])
						,	echo ("\n$row(donor_city) $row[donor_state] $row[donor_zip]");
					}
?>
						</td>
						<td align='right' valign='top'><?=sprintf("%01.2f",$donation);?></td>
						<td align='right' valign='top'><?=sprintf("%01.2f",$donation/$amount*100);?></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
<?					$prevprojectid = $projectid;
					$prevamount = $amount;
					$prevreqamount = $reqamount;
					$donation_total += $donation;
				}
				if (($prevprojectid != 0)) {
?>
					<tr>
						<td align='center'><b><?=$prevprojectid;?></b></td>
						<td><b>Total</b></td>
						<td>&nbsp;</td>
						<td align='right' valign='top'><b><?=sprintf("%01.2f",$prevamount);?></b></td>
						<td>&nbsp;</td>
						<td align='right' valign='top'><b><?=sprintf("%01.2f",$donation_total);?></b></td>
						<td align='right' valign='top'><b><?=sprintf("%01.2f",$donation_total/$prevamount*100);?></b></td>
						<td align='center' valign='top'><b><?=(empty($datecomplete)?"&nbsp;":date("m/d/y",strtotime($datecomplete)));?></b></td>
						<td align='center' valign='top'><b><?=($dispersed == "Y" ? sprintf("%01.2f",$dispersed_amount) : "&nbsp;");?></b></td>
						<td align='left' valign='top'><b><?=($dispersed == "Y" ? date("m/d/y",strtotime($dispersed_date)) : "&nbsp;");?></b></td>
					</tr>
					<tr><td colspan='10'>&nbsp;</td></tr>
<?				} ?>
				</table>
<?
				mysql_free_result($results);
?>