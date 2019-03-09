<html>
<body>
	<form method="POST" action="https://www.paypal.com/cgi-bin/webscr">
	First name<input name="first_name" type="text" size="50" value="Eric"><br>
	Last Name<input name="last_name" type="text" size="50" value="Vaterlaus"><br>
	Address<input name="address1" type="text" size="50" value="PO Box 14"><br>
	City<input name="city" type="text" size="50" value="Colton"><br>
	State<input name="state" type="text" size="50" value="OR"><br>
	Zip<input name="zip" type="text" size="50" value="97017"><br>
	Country<input name="country" type="text" size="50" value="US"><br>
	Notify_url<input name="notify_url" type="text" size="50" value="http://www.donate2educate.org/paypal_complete.php"><br>
	Image_url<input name="image_url" type="text" size="50" value="http://www.donate2educate.org/images/banner_paypal.jpg"><br>
	No_Shipping<input name="no_shipping" type="text" size="50" value="1"><br>
	No_Note<input name="no_note" type="text" size="50" value="1"><br>
	Return<input name="return" type="text" size="50" value="http://www.donate2educate.org/paypal_complete.php?cmd=complete&donateid=TESTDONATION"><br>
	rm<input name="rm" type="text" size="50" value="2"><br>
	cbt<input name="cbt" type="text" size="50" value="Complete Donation"><br>
	cancel_return<input name="cancel_return" type="text" size="50" value="http://www.donate2educate.org/paypal_complete.php?cmd=cancel&donateid=TESTDONATION"><br>
	cmd<input name="cmd" type="text" size="50" value="_cart"><br>
	upload<input name="upload" type="text" size="50" value="1"><br>
	business<input name="business" type="text" size="50" value="donate@donate2educate.org"><br>
	invoice<input name="invoice" type="text" size="50" value="TESTDONATION"><br>
<?
	for($item_idx=1; $item_idx<=3; $item_idx++) {
		echo "Item<input name=\"item_name_$item_idx\" size=\"50\" value=\"Request $item_idx\">&nbsp;&nbsp;";
		echo "Price<input name=\"amount_$item_idx\" size=\"50\" value=\"".(3.45*$item_idx)."\"><br>";
	}
	echo "Item<input name=\"item_name_$item_idx\" size=\"50\" value=\"Payment Processing Fees\">&nbsp;&nbsp;";
	echo "Price<input name=\"amount_$item_idx\" size=\"50\" value=\"".($item_idx*.25)."\"><br>";
?>
	<input type="Submit" Name="PayPal">
	</form>
</body>
</html>
