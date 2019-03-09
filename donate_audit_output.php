<?
				# Donation Audit Report
				$sql = "Select p.school_id, p.project_id, p.project_name, p.submitted_user_id, p.required_by_date, u.first_name, u.last_name, p.amount_needed, d.donation_amount, d.donation_date, d.gift_first_name, d.gift_last_name, p.completed_date, p.funds_dispersed, p.funds_dispersed_date, p.funds_dispersed_amount, d.contact_flag, d.refund_flag, p.date_receipts_received, p.date_thankyous_sent".
				" from project p inner join donation d on p.project_id = d.project_id inner join user u on u.user_id = d.user_id";
				$where = ""; "d.payment_received = 'Y'";
				if ($f_schoolid != 0) {
					if (!empty($where)) $where .= " and ";
					$where .= "p.school_id = '$f_schoolid'";
				}
				if ($f_gradelevel != 0) {
					if (!empty($where)) $where .= " and ";
					$where .= "p.grade_level_id = '$f_gradelevel'";
				}
				if ($f_projecttype != 0) {
					if (!empty($where)) $where .= " and ";
					$where .= "p.project_type_id = '$f_projecttype'";
				}
				if (($min_date != "") && ($mdate = strtotime($min_date))) {
					if (!empty($where)) $where .= " and ";
					$where .= "d.donation_date >= '".date("Y-m-d", $mdate)."'";
				}
				if (($max_date != "") && ($mdate = strtotime($max_date))) {
					if (!empty($where)) $where .= " and ";
					$where .= "d.donation_date <= '".date("Y-m-d", $mdate)."'";
				}
				if (!empty($where)) $sql .= " where $where";
				$sql .= " order by p.school_id, p.project_id";
				if ($results = $db_link->query($sql)) {
				} else
					echo "<b>Error Occured: ".mysqli_error()."<br>$sql<br>";
				echo "<font size='+1' color='$color_table_hdg_font'>DONATION AUDIT REPORT</font>\n";
				include "inc/box_middle.htm";
?>
				<table border=0 cellpassing=2>
<?
				$prevprojectid = 0;
				$prevschoolid = 0;
				$donation_total = 0;
				$predispersed_amount = 0;

				while(list($schoolid,$projectid,$projectname,$submitted_user_id,$reqdate,$fname,$lname,$amount,$donation,$donationdate,$gift_fname,$gift_lname,$datecomplete,$dispersed,$dispersed_date,$dispersed_amount,$contact_flag,$refund_flag,$date_receipts_received,$date_thankyous_sent) = mysqli_fetch_row($results)) {
					if (($prevprojectid != $projectid) && ($prevprojectid != 0))
					{
?>
					<tr>
						<td align='center'><b><?=$prevprojectid;?></b></td>
						<td><b>Total</b></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align='right' valign='top'><b><?=sprintf("%01.2f",$prevamount);?></b></td>
						<td>&nbsp;</td>
						<td align='right' valign='top'><b><?=sprintf("%01.2f",$donation_total);?></b></td>
						<td align='right' valign='top'><b>&nbsp;</b></td>
						<td align='right' valign='top'><b><?=sprintf("%01.0f",$donation_total/$prevamount*100);?>%</b></td>
						<td align='center' valign='top'><b><?=(empty($prevdatecomplete)?"&nbsp;":date("m/d/y",strtotime($prevdatecomplete)));?></b></td>
						<td align='center' valign='top'><b><?=($prevdispersed_amount > 0 ? sprintf("%01.2f",$prevdispersed_amount) : "&nbsp;");?></b></td>
						<td align='center' valign='top'><b><?=($prevdispersed_amount > 0 ? date("m/d/y",strtotime($prevdispersed_date)) : "&nbsp;");?></b></td>
					</tr>
					<tr><td colspan='12'><hr></td></tr>
<? 					$donation_total = 0;
					$prevdispersed_amount = 0;
					$prevdispersed = "N";
					}
					if ($schoolid != $prevschoolid) {
?>
					<tr>
						<td valign='bottom' colspan='12'><b>School: <?=$schools->school_name($schoolid);?></b></td>
					</tr>
					<tr>
						<td valign='bottom'><b>Proj<br>ID</b></td>
						<td valign='bottom'><b><br>Project Name</b></td>
						<td valign='bottom'><b>Submitted<br>By</b></td>
						<td align='center' valign='bottom'><b>Date<br>Expires</b></td>
						<td align='center' valign='bottom'><b>Req<br>Amount</b></td>
						<td align='left' valign='bottom'><b><br>Donor</b></td>
						<td align='center' valign='bottom'><b><br>Donation</b></td>
						<td align='center' valign='bottom'><b>Refund<br>Flag</b></td>
						<td align='center' valign='bottom'><b><br>Date</b></td>
						<td align='center' valign='bottom'><b>%<br>Funded</b></td>
						<td align='center' valign='bottom'><b>Funds<br>Complete</b></td>
						<td align='center' valign='bottom'><b>Funds<br>Dispersed</b></td>
						<td align='center' valign='bottom'><b>Date<br>Dispersed</b></td>
					</tr>
<?
						$prevschoolid = $schoolid;
					}
					if ($contact_flag == "G") {
						$donor_name = $gift_fname." ".$gift_lname;
					} elseif ($contact_flag == "A") {
						$donor_name = "Anonymous";
					} else
						$donor_name = $fname." ".$lname;
					if ($projectid != $prevprojectid)
						$submitted_by = $users->user_name($submitted_user_id);
?>
					<tr>
						<td align='center' valign='top'><?= (($projectid == $prevprojectid) ? "&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;" :
								"<a href=\"proposal.php?projectid=$projectid\" target=\"proposal\">$projectid</a></td><td align='left' valign='top'>$projectname</td><td>$submitted_by</td><td align='center' valign='top'>".(!empty($reqdate)?date("m/d/y",strtotime($reqdate)):"&nbsp;")."</td><td align='right' valign='top'>".sprintf("%01.2f", $amount));?>
						</td>
						<td align='left' valign='top'><?="$donor_name";?></td>
						<td align='right' valign='top'><?=sprintf("%01.2f",$donation);?></td>
						<td align='center' valign='top'><?="$refund_flag";?></td>
						<td align='right' valign='top'><?=date("m/d/y",strtotime($donationdate));?></td>
						<td align='right' valign='top'><?=sprintf("%01.0f",$donation/$amount*100);?>%</td>
						<td align='center' valign='bottom'><?=($datecomplete==""? "&nbsp" : date("m/d/y",strtotime($datecomplete)));?></td>
						<td align='center' valign='bottom'>&nbsp</td>
						<td align='center' valign='bottom'>&nbsp</td>
					</tr>
<?					$prevprojectid = $projectid;
					$prevamount = $amount;
					$prevdatecomplete = $datecomplete;
					$prevdispersed_amount = $dispersed_amount;
					$prevdispersed = $dispersed;
					$donation_total += $donation;
				}
				if (($prevprojectid != 0)) {
?>
					<tr>
						<td align='center'><b><?=$prevprojectid;?></b></td>
						<td><b>Total</b></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align='right' valign='top'><b><?=sprintf("%01.2f",$prevamount);?></b></td>
						<td>&nbsp;</td>
						<td align='right' valign='top'><b>&nbsp;</b></td>
						<td align='right' valign='top'><b><?=sprintf("%01.2f",$donation_total);?></b></td>
						<td align='right' valign='top'><b>&nbsp;</b></td>
						<td align='right' valign='top'><b><?=sprintf("%01.0f",$donation_total/$prevamount*100);?>%</b></td>
						<td align='center' valign='top'><b><?=(empty($prevdatecomplete)?"&nbsp;":date("m/d/y",strtotime($prevdatecomplete)));?></b></td>
						<td align='center' valign='top'><b><?=($prevdispersed_amount > 0 ? sprintf("%01.2f",$prevdispersed_amount) : "&nbsp;");?></b></td>
						<td align='center' valign='top'><b><?=($prevdispersed_amount > 0 ? date("m/d/y",strtotime($prevdispersed_date)) : "&nbsp;");?></b></td>
					</tr>
<?				} ?>
				</table>
<?
				mysqli_free_result($results);
?>