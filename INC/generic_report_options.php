				<font size="+1" color="<?=$color_table_hdg_font;?>">GENERIC REPORT</font>
				<?	include "inc/box_middle.htm";	?>
				<table>
				<Form Name="GenericReport" Method="POST">
					<tr>
						<td align="right">Report Title</td>
						<td align="left">
							<input type='text' name='report_title' size='30'>
						</td>
					</tr>

					<tr>
						<td align="right">SQL Statement</td>
						<td align="left">
							<textarea name='sql' rows='10' cols='60'>
							</textarea>
						</td>
					</tr>
					<tr>
						<td align="left" colspan="2">
							<input type="hidden" name="reportno" value="<?=$reportno;?>">
							<input type="submit" class="nicebtns" name="submit" value="Run Report">
						</td>
					</tr>
				</FORM>
				</table>
