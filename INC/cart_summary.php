 <?
  	if (isset($User_ID)) {
?>
	<table width="100%" cellspacing=0 cellpadding=1 border=0>
		<TR>
			<TD Align='Center'>
<?
if ($user->cart_item_count() > 0) {
echo "<a href=\"$http_location"."cart.php".(!empty($HTTPS) ? "?uniqueid=".htmlentities(urlencode($uniqueid)):"")."\">";
}
echo "<img border=0 src=\"images/".(($user->cart_item_count() == 0) ? "cart_empty.gif" : "cart_full.gif")."\">";
if ($user->cart_item_count() > 0) {
echo "</a>";
} ?>
			</TD>
			<TD><font size="-1"><? echo (($user->cart_item_count() == 0) ? "Empty" : "$".sprintf("%01.2f", $user->cart_item_total())); ?></font></TD>
		</TR>
	</table>
<?
  	}
?>