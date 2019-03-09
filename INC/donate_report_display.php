<?
				# Donation Audit Report
				$sql = "Select p.project_id, p.project_name, p.required_by_date, u.first_name, u.last_name, ";
				$sql .= "p.amount_needed, dp.amount donation_amount, p.completed_date, p.funds_dispersed, ";
				$sql .= "p.funds_dispersed_date, p.funds_dispersed_amount, d.contact_flag, d.gift_first_name, d.gift_last_name ";
				$sql .= "from project p ";
				$sql .= "inner join donation_project dp on dp.project_id = p.project_id ";
				$sql .= "inner join donation d on dp.donation_id = d.donation_id and d.Payment_Authorized = 'Y' ";
				$sql .= "inner join user u on u.user_id = d.user_id ";
				$where = "d.payment_received = 'Y'";
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
				$sql .= " order by p.project_id";
				echo "$sql<BR>";
				if ($results = mysql_query($sql)) {
				} else
					echo "<b>Error Occured: ".mysql_error()."<br>$sql<br>";
				echo "<font size='+1' color='$color_table_hdg_font'>DONATION AUDIT REPORT</font>\n";
				include "inc/box_middle.htm";
?>
				<table border=0 cellpassing=2>
					<tr>
						<td valign='bottom'><b>Proj ID</b></td>
						<td valign='bottom'><b>Project Name</b></td>
						<td align='center' valign='bottom'><b>Date<br>Expires</b></td>
						<td align='center' valign='bottom'><b>Req Amount</b></td>
						<td align='left' valign='bottom'><b>Donor</b></td>
						<td align='center' valign='bottom'><b>Donation</b></td>
						<td align='center' valign='bottom'><b>% Funded</b></td>
						<td align='center' valign='bottom'><b>Funds Complete</b></td>
						<td align='center' valign='bottom'><b>Funds Dispersed</b></td>
						<td align='center' valign='bottom'><b>Date Dispersed</b></td>
					</tr>
<?
				$prevprojectid = 0;
				$donation_total = 0;
				while(list($projectid,$projectname,$reqdate,$fname,$lname,$amount,$donation,$datecomplete,$dispersed,$dispersed_date,$dispersed_amount,$contact_flag,$gift_first_name,$gift_last_name) = mysql_fetch_row($results)) {
					if (($prevprojectid != $projectid) && ($prevprojectid != 0)) {
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
<? 					$donation_total = 0;
					} ?>
					<tr>
						<td align='center' valign='top'><?= (($projectid == $prevprojectid) ? "&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;" :
								"$projectid</td><td align='left' valign='top'>$projectname</td><td align='center' valign='top'>".(!empty($reqdate)?date("m/d/y",strtotime($reqdate)):"&nbsp;")."</td><td align='right' valign='top'>".sprintf("%01.2f", $amount));?>
						</td>
						<td align='left' valign='top'>
<?					if ($contact_flag == "G")
						echo "$gift_first_name $gift_last_name";
					elseif ($contact_flag == "A")
						echo "Anonymous";
					else
						echo "$fname $lname";
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