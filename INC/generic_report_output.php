<?
					# Generic Report Output
					if ($results = mysql_query(stripcslashes($sql)))
					{
						echo "<font size='+1' color='$color_table_hdg_font'>".stripcslashes ($report_title)."</font>\n";
						include "inc/box_middle.htm";
?>
						<table border=0 cellpassing=2>
							<tr>
<?
							/* get column header info */
							$i = 0;
							while ($i < mysql_num_fields($results))
							{
								$meta = mysql_fetch_field($results, $i);
								$cols = array();
								if (!$meta)
								{
									echo "<td>COL$i</td>\n";
								} else {
									/* First column of header name indicates alignment: > right, < left, ^ center */
									$colhdr = $meta->name;
									if (substr($colhdr,0,1) == ">") {
										$cols[$i] = "RIGHT";
										$colhdr = substr($colhdr, 1);
									} elseif  (substr($colhdr,0,1) == "<") {
										$cols[$i] = "LEFT";
										$colhdr = substr($colhdr, 1);
									} elseif  (substr($colhdr,0,1) == "^") {
										$cols[$i] = "CENTER";
										$colhdr = substr($colhdr, 1);
									} else
										$cols[$i] = "LEFT";
?>
									<td align="<?=$cols[$i];?>"><b><?=$colhdr;?></b></td>
<?
								}
								$i += 1;
							}
?>
							</tr>
<?
						while($row = mysql_fetch_array($results))
						{
							echo "<tr>";
							/* get column header info */
							$i = 0;
							while ($i < mysql_num_fields($results)) {
?>
								<td align='<?=$cols[$i];?>'><?=$row[$i];?></td>
<?
								$i += 1;
							}
?>
							</tr>
<?
						}
						echo "</table>";
					} else
						echo "<b>Error Occured: ".mysql_error()."<br>$sql<br>";
?>