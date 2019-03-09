<?

# INPUT:

# $method		[string: POST|GET]

# $url			[string]
#	user:password@www.vasia.ru:443/path/to/script.asp

# $data			[array]
#	$data[] = "parametr=value";

# $join			[string]
#	$join = "\&";

# $cookie		[array]
#	$cookie = "parametr=value";

# $conttype		[string]
#	$conttype = "text/xml";

# $referer		[string]
#	$referer = "http://www.vasia.ru";

# $cert			[string]
#	$cert = "../certs/demo-cert.pem";

# $kcert		[string]
#	$keyc = "../certs/demo-keycert.pem";

# $rhead		[string]
#	$rhead = "...";

# $rbody		[string]
#	$rbody = "...";

function func_https_request($method, $url, $data="", $join="&", $cookie="", $conttype="application/x-www-form-urlencoded", $referer="", $cert="", $kcert="")
{
	global $config;

	if(!empty($config_perl_binary))
		$execline = $config_perl_binary." ";
	else $execline = "";

	$execline.= "/s101-2/home6/donate2educate/inc/netssleay.pl";

	if(($method!="POST") && ($method!="GET"))
			return array("0","HTTPS: Invalid method");

	if(!preg_match("/^(https?:\/\/)(.*\@)?([a-z0-9_\.\-]+):(\d+)(\/.*)$/Ui",$url,$m))
			return array("0","HTTPS: Invalid URL");
	elseif(!empty($m[2]))
	{
		preg_match("/^(.*)@$/",$m[2],$o);
		$url=$m[1].base64_encode($o[1])."@".$m[3].":".$m[4].$m[5];
	}

	# Set GET method flag
	$execline.= " POST=".($method=="GET" ? 0 : 1 );

	# Combine REQUEST string
	if($data)
		{
			if($join)
			{	foreach($data as $k=>$v)
					{
						list($a,$b)=split("=",trim($v),2);
						$data[$k]=$a."=".urlencode($b);
					}
			}
			$execline.= " DATA=\"".join($join,$data)."\"";
		}

	# Add SSL Certificate
	if($cert)
		$execline.= " CERT=\"".$cert."\"";

	# Add SSL Key-Certificate
	if($kcert)
		$execline.= " KEY=\"".$kcert."\"";

	# Add Content-Type...
	if($conttype != "application/x-www-form-urlencoded")
	{
		$execline.=" HEADT=\"".addslashes($conttype)."\"";
	}

	# Add referer...
	if($referer != "")
	{
		$execline.=" HEADR=\"".addslashes($referer)."\"";
	}

	# Add cookie...
	if($cookie != "")
	{
		$execline.=" COOKIE=\"".addslashes($cookie)."\"";
	}
	$execline.=" URL=\"".$url."\" 2>&1";
	#echo "$execline<br>";

    exec($execline, $rbody);

    return split("ENDOFHEADERFORXCART",join("\n",$rbody),2);
}

?>
