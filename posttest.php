<HTML>
<BODY>
<?
	//echo $HTTP_SERVER_VARS("DOCUMENT_ROOT")."<BR>";
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		echo "Address = ".$address1;
	}
?>
<Form method='post' action='posttest.php'>
Address 1 <input type=text name='address1' width='23'><br>
Address 2 <input type=text name='address2' width='23'><br>
Address 3 <input type=text name='address3' width='23'><br>
Address 4 <input type=text name='address4' width='23'><br>
<input name='submit' type='submit' value='submit'>
</FORM>
</body>
</html>
