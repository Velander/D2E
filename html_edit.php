<!--
#################################################################################
##
## HTML Text Editing Component for hosting in Web Pages
## Copyright (C) 2001  Ramesys (Contracting Services) Limited
##
## This library is free software; you can redistribute it and/or
## modify it under the terms of the GNU Lesser General Public
## License as published by the Free Software Foundation; either
## version 2.1 of the License, or (at your option) any later version.
##
## This library is distributed in the hope that it will be useful,
## but WITHOUT ANY WARRANTY; without even the implied warranty of
## MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
## Lesser General Public License for more details.
##
## You should have received a copy of the GNU LesserGeneral Public License
## along with this program; if not a copy can be obtained from
##
##    http://www.gnu.org/copyleft/lesser.html
##
## or by writing to:
##
##    Free Software Foundation, Inc.
##    59 Temple Place - Suite 330,
##    Boston,
##    MA  02111-1307,
##    USA.
##
## Original Developer:
##
##	Austin David France
##	Ramesys (Contracting Services) Limited
##	Mentor House
##	Ainsworth Street
##	Blackburn
##	Lancashire
##	BB1 6AY
##	United Kingdom
##  email: Austin.France@Ramesys.com
##
## Home Page:    http://richtext.sourceforge.net/
## Support:      http://richtext.sourceforge.net/
##
#################################################################################
-->
<?
include_once "inc/db_inc.php";
include_once "inc/admin_user.php";
if ($user->type_id < 40) {
	echo "<script type=\"text/javascript\">\nlocation.href='../admin.php?message=".htmlentities(urlencode("Support Login Required"))."'\n</script>";
}
if (!empty($_POST["text"])) {
    // Update your database here
    $_POST["text"]=str_replace($config_htmledit_image_path_prefix,"",$_POST["text"]);
    if (!$db_link->query("Update config set value = '" . $_POST["text"] . "' where name = '$field_name'"))
		$error_message = "Database Error: Error updating the database: ".mysqli_error()."<BR>";
	else
		echo "<script type=\"text/javascript\">\nlocation.href='../config_edit.php'\n</script>";
} else {
?>
<HTML>
<HEAD>
<TITLE>Donate2Educate Content Edit</TITLE>
<?	include "inc/cssstyle.php"; ?>
<META content="HTML 4.0" name=vs_targetSchema>
<META content="Microsoft FrontPage 4.0" name=GENERATOR>
</HEAD>
<BODY leftMargin=0 topMargin=0 scroll="no" style="border:0">

<?
	if (!empty($error_message))
		echo "<B>$error_message</B><BR>";
	// Get your HTML from the datbase here
	if (!$results = $db_link->query("Select value, category, prompt from config where name = '$field_name'"))
		echo "Error occured reading $field_name from the configuration table!<BR>";
	else
		list($strHTML, $category, $prompt) = mysqli_fetch_row($results);

	// Create an instance of the editor
	?>
	<table border=0>
	<tr><td>Category</td><td><?=$category;?></td></tr>
	<tr><td>Prompt</td><td><?=$prompt;?></td></tr>
	<tr><td width=100>&nbsp;</td><td align='center'>
	<object id="richedit" style="BACKGROUND-COLOR: buttonface" data="editor/rte/richedit.html"
	width="665" height="300" type="text/x-scriptlet" VIEWASTEXT>
		</object>

	<? // Dummy form used to communicate text to/from the server ?>
	<form id="theForm" method="post">
	<textarea name="text" style="display:none" rows="1" cols="20"><?=$strHTML?></textarea>
	<input type="hidden" name="to" value="">
	<input type="hidden" name="cc" value="">
	<input type="hidden" name="subject" value="">
	<input type="hidden" name="field_name" value="<?=$field_name;?>">
	</form>

	<? // Glue to populate the editor with HTML from database ?>
	<SCRIPT language="JavaScript" event="onload" for="window">
		richedit.options = "history=no;source=yes";
		//richedit.addField("to", "To", 128, theForm.to.value);
		//richedit.addField("cc", "Cc", 128, theForm.cc.value);
		//richedit.addField("subject", "Subject", 128, theForm.subject.value);
		richedit.docHtml = theForm.text.innerText;
	</SCRIPT>

	<? // Glue to submit the updated HTML to the server ?>
	<SCRIPT language="JavaScript" event="onscriptletevent(name, eventData)" for="richedit">
		if (name == "post") {
	    	//theForm.to.value = richedit.getValue("to");
			//theForm.cc.value = richedit.getValue("cc");
			//theForm.subject.value = richedit.getValue("subject");
			theForm.text.value = eventData;
			theForm.submit();
		}
	</SCRIPT>
	</td></tr></table>
	<?
}
?>
</BODY>
</HTML>
