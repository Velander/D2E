<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<HTML>
<HEAD>
<TITLE>Funds 4 Kids</TITLE>
<META NAME="Generator" CONTENT="TextPad 4.6">
<META NAME="Author" CONTENT="?">
<META NAME="Keywords" CONTENT="?">
<META NAME="Description" CONTENT="?">
</HEAD>

<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#FF0000" VLINK="#800000" ALINK="#FF00FF" BACKGROUND="?">
<?
	require "inc/db_inc.php";
	require "inc/class_districts.php";
	require "inc/class_district.php";
?>
<TABLE ALIGN="left" BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH="100%">
<TR ALIGN="left" VALIGN="middle">
	<TD><B>District ID</B></TD>
	<TD><B>District Name</B></TD>
</TR>

<?
	$ndistricts = new districts();
	$ndistricts->load_districts();
	$i = 0;
	while ($i < $ndistricts->count()) {
		$ndistrict = $ndistricts->district($i);
		echo "<tr><td>$ndistrict->district_id</td><td>$ndistrict->district_name</td></tr>";
	}
?>
</TABLE>
</BODY>
</HTML>
