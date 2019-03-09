<?
function url_to_link($text) {
	$reg_exUrl = "/((((http|https|ftp|ftps)\:\/\/)|www\.)[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,4}(\/\S*)?)/";

	return preg_replace( $reg_exUrl, "<a href=\"$1\" target=\"new\">Webpage</a> ", $text );
	}

$sample = "Go to http://www.veland.com/ now!<BR>Then go to https://www.novelupdates.com/reading-list/?list=4<BR> and read a story.<BR>";
?>
<HTML>
<BODY>
This is a test.<BR>
<?=url_to_link($sample);?><BR>
This is only a test.<BR>
</BODY>
</HTML>