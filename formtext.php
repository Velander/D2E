<?
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		echo "Field first name = $firstname<BR>";
		echo "Field first name = $lastname<BR>";
	}
?>
<HTML>
<BODY>
<FORM METHOD='POST'>
First Name: <INPUT TYPE='text' name='firstname'><BR>
Last Name: <INPUT TYPE='text' name='lastname'><BR>
<input type='submit' value='submit'>
</form>
</BODY>