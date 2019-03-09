<?
include "inc/db_inc.php";
require_once "inc/class_cart_item.php";
require_once "inc/class_user.php";
require_once "inc/admin_user.php";
require_once "inc/func.php";

$mode		= $_GET["mode"];
$message	= $_GET["message"];

if ($user->type_id < 40) {
	echo "<script type=\"text/javascript\">\nlocation.href='".$http_location."index.php?message=".htmlentities(urlencode("Support Login Required"))."'\n</script>";
}
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$newname		= $_POST["newname"];
	$newprompt		= $_POST["newprompt"];
	$newvalue		= $_POST["newvalue"];
	$newsortorder	= $_POST["newsortorder"];
	$newtype		= $_POST["newtype"];
	$newcategory	= $_POST["newcategory"];
	$safe_admin		= $_POST["safe_admin"];

    if ($submit=="Add Value" && !empty($newname))
    {
        $sql = "insert config (name, prompt, value, sortorder, type, category) values ('$newname', '$newprompt', '$newvalue', '$newsortorder', '$newtype', '$newcategory')";
        $db_link->query($sql);
        if (mysqli_errno != 0)
        	$message = "<b>Update Error Occured: ".mysqli_error."</b><br>";
        else
        	$message = "$newname field added.";
        $mode = $newcategory;
    } else {
        if ($safe_admin) safe_mode_msg(true);
        # First need to turn off any checkboxs since they will only be found if they are turned on.
        $sql = "update config set value='N' where type='checkbox' and category = '$f_mode'";
        $db_link->query($sql) or die ($mysqli_error_msg);
        while (list($key,$val) = each($_POST))
        {
            if (substr($key,0,7) == "remove_") {
				$key = substr($key,7);
				$db_link->query("update config set value='' where name='".$key."'") || die ("$mysqli_error_msg");
            } else {
                #if ($[$key."_crypted"] == "Y")
                #	if ($[$key."_orig"] != $val)
                #		$db_link->query("update config set value='".addslashes(text_crypt($val))."' where name='".$key."'") || die ("$mysqli_error_msg");
                #else
                $sql = "update config set value='".($val=="on" ? "Y" : $val)."' where name='".$key."'";
                if (!$db_link->query($sql))
                	$message .= "$key failed to update: ".mysqli_error."<BR>";
            }
        }
    }
}
?>
<html>
<head>
<? include "inc/cssstyle.php"; ?>
<?
	$pagename = "Admin";
	require "inc/title.php";
?>
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
</head>
<body bgcolor="<? echo $cl_doc_bg ?>">
<tr>
<!-- main frame here -->
<table width="100%" height="100%" cellpadding="10">
<?
	if (!empty($message)) {
		echo "<tr><td>\n";
		include "inc/box_begin.htm";
		echo "<center><b><font color='$color_error_message'>".stripslashes($message)."</font></b></center>";
		include "inc/box_end.htm";
		echo "</td></tr>\n";
	}
?>
<tr>
<td valign="top">
<div align="left"><a name=\"Top\"><font color="<?=$cl_header;?>" size="+1"><b>Edit Configuration</b></a></font>&nbsp;&nbsp;<Font size="+1"><B>|</B></Font>&nbsp;&nbsp;<a href="<?=$http_location;?>index.php"><font color="<?=$cl_header;?>" size="+1"><b>Home</b></font></a><br></div>
<hr>
<?
$c_result = $db_link->query("select distinct category from config order by category");
$cnt = mysqli_num_rows($c_result);
$contents = array();
$i = 0;
$l = 1;
if (!$mode) $mode = $f_mode;
# Display a list of the categories
while (list($cat) = mysqli_fetch_row($c_result)) {
    if (empty($mode)) $mode = $cat;
    $contents[$i][] = "<a href=\"config_edit.php?mode=$cat\">$cat</a>";
    if ($l == round($cnt/4)) {
        $i += 1;
        $l = 1;
    } else
        $l += 1;
}

mysqli_free_result($c_result);
reset($contents);
echo "<TABLE  ALIGN=\"left\" BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">\n";
while (list($i, $cat) = each($contents[0])) {
    echo "<TR><TD>$cat</TD>";
    list($i, $cat) = each($contents[1]);
    echo "<TD>$cat</TD>";
    list($i, $cat) = each($contents[2]);
    echo "<TD>$cat</TD>";
    list($i, $cat) = each($contents[3]);
    echo "<TD>$cat</TD></TR>\n";
}
echo "<TR><TD colspan=4>\n";
echo "<form action=\"config_edit.php\" method=\"POST\">";
echo "<table border=\"0\" width=\"95%\">";
$c_result = $db_link->query("select name, prompt, value, sortorder, type, category from config order by category, sortorder");
$prev_category = "";
while ($row = mysqli_fetch_row($c_result)) {
	if ($row[5] == $mode) {
		if ($prev_category <> $mode) {
			echo "<input type='hidden' name='f_mode' value='$mode'>\n";
			echo "<tr><td colspan=\"3\"><hr><a name=\"$row[5]\"><b>$row[5]</b></a></td></tr>";
			$prev_category = $mode;
		}
		echo "<tr><td width='20'>&nbsp;</td><td nowrap>$row[1]&nbsp;&nbsp;<small>($row[3])</small>:<br><small>&nbsp;&nbsp;[$row[0]]</small>\n";
		echo "</td><td>\n";
		if($row[4]=="text")
			echo "<input type=\"text\" maxlength=\"255\" size=\"70\" name=\"$row[0]\" value=\"$row[2]\">\n";
		elseif ($row[4]=="textarea")
			echo "<textarea name=\"$row[0]\" rows=\"5\" cols=\"83\">$row[2]</textarea>\n";
		elseif ($row[4]=="html") {
			echo "<textarea id=\"$row[0]\" name=\"$row[0]\" rows=\"5\" cols=\"60\">$row[2]</textarea>\n";
?>
                        <script type="text/javascript">
                        //<![CDATA[

                            // This call can be placed at any point after the
                            // <textarea>, or inside a <head><script> in a
                            // window.onload event handler.

                            // Replace the <textarea id="editor"> with an CKEditor
                            // instance, using default configurations.
                            CKEDITOR.replace( '<?=$row[0];?>',
                            {width : 640,
                            forcePasteAsPlainText : true,
                            toolbar_Full : [
                                ['Source','-','Templates'],
                                ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
                                ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
                                ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
                                ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
                                ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
                                ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
                                ['Link','Unlink','Anchor'],
                                ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
                                '/',
                                ['Styles','Format','Font','FontSize'],
                                ['TextColor','BGColor'],
                                ['Maximize', 'ShowBlocks','-','About']
                            ]
                            });

                        //]]>
                        </script>
<?
#			echo "<table cellspacing=1 bgcolor=\"#000000\" width='100%'><TR><TD>\n<table width='100%' bgcolor=\"#FFFFFF\"><TR><TD>$row[2]&nbsp;</TD></TR></TABLE>\n</TD></TR></TABLE>\n";
#			echo "<A href=\"html_edit.php?field_name=".urlencode($row[0])."\"><b>Edit HTML</b></A>&nbsp;&nbsp;<input type=\"checkbox\" name=\"remove_".$row[0]."\" value=\"1\"><b>Clear Text</b>\n";
		} elseif ($row[4]=="checkbox")
			echo "<input type=checkbox name=\"$row[0]\" ".($row[2]=="Y" ? "checked" : "").">\n";
	}
}
mysqli_free_result($c_result);
?>
</table>
<hr>
<b><a name="Save"><input type="submit" class="nicebtns" value="Update values"></a></b>
</form>
<?
echo "<form action=\"config_edit.php\" method=\"POST\">";
echo "<hr>\n";
echo "<table border=\"0\" width=\"95%\">";
		echo "<tr><td colspan=\"3\"><input type='text' name='newcategory' value='$mode' size=20>&nbsp;";
    	echo "Type: <select name='newtype'>\n";
    	echo "			<option>text</option>\n";
    	echo "			<option>textarea</option>\n";
    	echo "			<option>checkbox</option>\n";
    	echo "			<option>html</option>\n";
    	echo "			</select>\n";
    	echo "Variable Name:<input type='text' name='newname' size=20>\n";
		echo "</td></tr>\n";
	    echo "<tr><td width='20'>&nbsp;</td><td nowrap><input type='text' name='newprompt' value='' size=20> : (<input type='text' name='newsortorder' size='4'>)</td><td>";
    	echo "<input type=\"text\" maxlength=\"255\" size=\"40\" name=\"newvalue\" value=\"\">\n";
    	echo "</td></tr>\n";
?>
</table>
<b><input name='submit' type="submit" class="nicebtns" value="Add Value"></b>
</form>
<hr>
</td>
</tr>
</table>
</td></tr></table>
<!-- /main frame -->
</td>
<?
$dont_display_lc = 1;
include "inc/bottom.php";
?>
</body>
</html>
