<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

       var subwindow;
       var lnk;

function display_help(help_id, section_name, wwidth, wheight)
{
   	subwindow = window.open("<? echo "$http_location/" ?>help.php?helpid="+help_id+"&section="+section_name, "_Help", "toolbar=no,scrollbars=yes,resizable=yes,width="+wwidth+",height="+wheight)
   	if (!subwindow.opener)
   	{
   		subwindow.opener = window
   	}
}

function display_msg(url, wwidth, wheight)
{
	if (subwindow && !subwindow.closed)
    {
    	subwindow.focus();
    }
   	subwindow = window.open(url, "_Msg", "toolbar=no,scrollbars=yes,resizable=yes,width="+wwidth+",height="+wheight)
   	if (!subwindow.opener)
   	{
   		subwindow.opener = window
   	}
}

function display_report(urllink) {
    window2 = window.open(urllink, "_blank", "toolbar=no,scrollbars=yes,resizable=yes") ;
    }
//-->
</script>
<script language="JavaScript1.2">
// ---------------------------- //
// General JavaScript Library:  //
// Author : Beji Joseph         //
// ---------------------------- //
       var subwindow;
       var lnk;

       function showHelp(lnk)
       {
       if (!subwindow || subwindow.closed)
       {
		subwindow =  window.open(lnk,"help","height=400,width=450,menubar=no,toolbar=no,resizable=no,scrollbars=yes,alwaysRaised")
                        if (!subwindow.opener)
                        {
                        subwindow.opener = window
                        }
                }
        else
                {
                subwindow.focus();
                window.open(lnk,"help");
                }
        }

function inValidChar(passedVal)
{
	if (passedVal == "\f")
		return true;
	else
	{
		if (passedVal == "\n")
			return true;
		else
		{
			if(passedVal == "\r")
				return true;
			else
			{
				if(passedVal == "\t")
					return true;
			}
		}
	}
	return false;
}

function hasValue(passedVal, bInValidCharCheck)
{
	var charHold;

	if (ReplaceInString(passedVal, " ", "") == '') return false;
	if (ReplaceInString(ReplaceInString(ReplaceInString(ReplaceInString(passedVal, "\f", ""), "\r", ""), "\n", ""), "\t", "") == '') return false;

	if(bInValidCharCheck)
		for (i=0; i<passedVal.length; i++)
		{
			charHold = passedVal.charAt(i);
			if (inValidChar(charHold)) return false;
		}
	return true;
}

function emailCheck(emailField)
{
var invalidChars = " /:,;"
var atPos,tmp
var strEmailText
if (!checkDBQuote(emailField)){
	return false;
}
strEmailText = emailField.value
   for (i=0; i<invalidChars.length; i++) {

      badChar = invalidChars.charAt(i)
      if (strEmailText.indexOf(badChar,0) > -1) {
	     alert(MSG_GEN_EntValdEmlAddr);
         emailField.focus()
         return false;
      }
   }

	atPos = strEmailText.indexOf("@",1)
	if (atPos == -1) {
		alert(MSG_GEN_PlsEntValdEmlAddr);
        emailField.focus()
        return false;
	}

	if (strEmailText.indexOf("@",atPos+1) > -1) {
		alert(MSG_GEN_PlsEntValdEmlAddr);
        emailField.focus()
        return false;
	}

	if (strEmailText.indexOf("..") > -1) {
	    alert(MSG_GEN_PlsEntValdEmlAddr);
        emailField.focus()
        return false;
	}
	if (strEmailText.indexOf("@.") > -1) {
	   alert(MSG_GEN_PlsEntValdEmlAddr);
        emailField.focus()
        return false;
	}


	periodPos = strEmailText.indexOf(".",atPos)

	if (periodPos == -1) {
	alert(MSG_GEN_PlsEntValdEmlAddr);
        emailField.focus()
        return false;
	}

	if (periodPos+3 > strEmailText.length)   {
	alert(MSG_GEN_PlsEntValdEmlAddr);
        emailField.focus()
        return false;
	}

	if (strEmailText.lastIndexOf(".") == (strEmailText.length - 1)) {
	   alert(MSG_GEN_PlsDelPrdFrmEndOfYrEmlAddr);
        emailField.focus()
        return false;
	}

	if (strEmailText.indexOf(".") == 0) {
	   alert(MSG_GEN_PlsDelPrdFrmBgnOfYrEmlAddr);
        emailField.focus()
        return false;
	}
return true;
}
function ReplaceInString(str, strFind, strReplace)
{
   // Replaces strFind in str with strReplace and returns replaced text string
   // 04-04-2003 Venkat

   var returnStr = str;
   var start = returnStr.indexOf(strFind);
   while (start>=0)
   {
     returnStr = returnStr.substring(0,start) + strReplace + returnStr.substring(start+strFind.length,returnStr.length);
     start = returnStr.indexOf(strFind,start+strReplace.length);
   }
   return returnStr;
}
function isSwitchSolo(cardType, cardNbr) {

var result    = false;

var switchRules  = new Array("490302,490309,18,1","490335,490339,18,1","491101,491102,16,1","491174,491182,18,1","493600,493699,19,1","564182,564182,16,2","633300,633300,16,0","633301,633301,19,1","633302,633349,16,0","675900,675900,16,0","675901,675901,19,1","675902,675904,16,0","675905,675905,19,1","675906,675917,16,0","675918,675918,19,1","675919,675937,16,0","675938,675940,18,1","675941,675949,16,0","675950,675962,19,1","675963,675997,16,0","675998,675998,19,1","675999,675999,16,0");
var soloRules    = new Array("633450,633453,16,0","633454,633457,16,0","633458,633460,16,0","633461,633461,18,1","633462,633472,16,0","633473,633473,18,1","633474,633475,16,0","633476,633476,19,1","633477,633477,16,0","633478,633478,18,1","633479,633480,16,0","633481,633481,19,1","633482,633489,16,0","633490,633493,16,1","633494,633494,18,1","633495,633497,16,2","633498,633498,19,1","633499,633499,18,1","676700,676700,16,0","676701,676701,19,1","676702,676702,16,0","676703,676703,18,1","676704,676704,16,0","676705,676705,19,1","676706,676707,16,2","676708,676711,16,0","676712,676715,16,0","676716,676717,16,0","676718,676718,19,1","676719,676739,16,0","676740,676740,18,1","676741,676749,16,0","676750,676762,19,1","676763,676769,16,0","676770,676770,19,1","676771,676773,16,0","676774,676774,18,1","676775,676778,16,0","676779,676779,18,1","676780,676781,16,0","676782,676782,18,1","676783,676794,16,0","676795,676795,18,1","676796,676797,16,0","676798,676798,19,1","676799,676799,16,0");

    switch(cardType) {
       case "SO": thisRules = soloRules;
                   break;
       case "SW": thisRules = switchRules;
                   break;
       default  : thisRules = new Array();
                   break;
    }

    var ndx;
    var ruleDetails;
    var done = false;
    for(ndx = 0 ; ndx < thisRules.length && !done ; ++ndx) {
        thisRule    = thisRules[ndx];
        ruleDetails = thisRule.split(",");

        var hiPrefix        = ruleDetails[0];
        var loPrefix        = ruleDetails[1];
        var valLength       = ruleDetails[2];
        var issueLength     = ruleDetails[3];
        var startDateLength = ruleDetails[4];

        var cardPrefix = cardNbr.substr(0,hiPrefix.length);
        if(cardPrefix >= hiPrefix && cardPrefix <= loPrefix) {
            if(cardNbr.length == valLength) {
                result = true;
                done   = true;
            }
        }
    }

	return(result);
}

/*
* This function cheks the checksum of CreditCard
* @param cc - the string with credit card number
* @param accepted - the array of allowed CC types
* if(accepted==null) { all CC types are allowed }
*/

function isCreditCard(cc,accepted) {

  cc=String(cc);
  if(cc.length<4 || cc.length>30) return false;

  // Start the Mod10 checksum process...
  var checksum=0;

  // Add even digits in even length strings or odd digits in odd length strings.
  for (var location=1-(cc.length%2); location<cc.length; location+=2) {
    var digit=parseInt(cc.substring(location,location+1));
    if(isNaN(digit)) return false;
    checksum+=digit;
  }

  // Analyze odd digits in even length strings
  // or even digits in odd length strings.
  for (var location=(cc.length%2); location<cc.length; location+=2) {
    var digit=parseInt(cc.substring(location,location+1));
    if(isNaN(digit)) return false;
    if(digit<5) checksum+=digit*2;
    else checksum+=digit*2-9;
  }

  if(checksum%10!=0) return false;

  if(accepted!=null) {
    var checkPresent = false;
    var accepted_array = new Array("Diners Club","American Express","JCB","Diners Club","American Express","Diners Club","Carte Blanche","Visa","MasterCard","Australian BankCard","Discover/Novus","Switch","Solo");

    for (var i in accepted_array) {
		if(accepted[0] == accepted_array[i])
			checkPresent = true;
	}

    var type="not";
	var accept=false;

    if(checkPresent == true) {
      var t=parseInt(cc.substring(0,4)), l=cc.length;

      if(t>=3000 && t<3060 && l==14) type="Diners Club";
      else if(isSwitchSolo("SW",cc.substring(0, cc.length))) type="Switch";
	  else if(isSwitchSolo("SO",cc.substring(0, cc.length))) type="Solo";
      else if(t>=3400 && t<3500 && l==15) type="American Express";
      else if(t>=3528 && t<3590 && l==16) type="JCB";
      else if(t>=3600 && t<3700 && l==14) type="Diners Club";
      else if(t>=3700 && t<3800 && l==15) type="American Express";
      else if(t>=3800 && t<3890 && l==14) type="Diners Club";
      else if(t>=3890 && t<3900 && l==14) type="Carte Blanche";
      else if(t>=4000 && t<5000 && (l==13 || l==16)) type="Visa";
      else if(t>=5100 && t<5600 && l==16) type="MasterCard";
      else if(t==5610 && l==16) type="Australian BankCard";
      else if(t==6011 && l==16) type="Discover/Novus";
	  else type="not";	// accepted and recognized types are not equal
    }
	else {
	  // we don't know this card's type so pass it as correct
	  return true;
	}

	if (accepted[0]==type)  accept=true;
	else accept = false;

	return accept;
  }

  return true;
}


function checkCCNumber(field_cc,field_accepted) {

  var card_types=new Array();
      card_types["VISA"]="Visa";
      card_types["MC"]="MasterCard";
      card_types["AMEX"]="American Express";

  var cc=field_cc.value;
  var accepted=null;
  if(field_accepted!=null) {
    accepted=new Array(card_types[field_accepted.value]);
  }
  if (isCreditCard(cc,accepted)) {
    return true;
  } else {

	alert("Credit Card checksum is invalid! Please correct");

	field_cc.focus();
	field_cc.select();
	return false;
  }
}

/*
* This function checks CVV2 field
*/

function checkCVV2(cvv2,cc) {

  var card_cvv2=new Array();
      card_cvv2["VISA"]="1";
      card_cvv2["MC"]="1";
      card_cvv2["AMEX"]="1";


  var num=cc.value;

if (card_cvv2[num]=='')
  	return true;
  cvv2 = cvv2.value;
  cvv2 = String(cvv2);

  if(cvv2.length==0) {
    alert("CVV2 is empty");
    return false;
  }
  if(cvv2.length!=3 && cvv2.length!=4) {
    alert("CVV2 isn't correct");
    return false;
  }

  for (var location=0; location<cvv2.length; location++) {
    var digit=parseInt(cvv2.substring(location,location+1));

    if(isNaN(digit)) {
	  alert("CVV2 must be a number");
	  return false;
	}
  }


  return true;

}

/*
* This function checks expiration CC date
*/

function checkExpirationDate(expiration_date) {
  ed = expiration_date.value;
  ed = String(ed);

  for (var location=0; location<ed.length; location++) {
    var digit=parseInt(ed.substring(location,location+1));
    if(isNaN(digit)) {
	  alert("Date must be a number");
	  return false;
  }

  }
  if(ed.length !=4) {
    alert("Expiration date's format isn't correct");
    return false;
  }

  mm=ed.substring(0,2);
  if(mm<"01" || mm>"12") {
    alert("Month of expiration date isn't correct");
    return false;
  }
  yy=ed.substring(2,4);
  // need to change "03" to variable

  if(yy<"03") {
    alert("Is this card expired?");
    return false;
  }
  return true;
}
//<a href="javascript: if(checkCCNumber(document.checkout_form.card_number,document.checkout_form.card_type) && checkExpirationDate(document.checkout_form.card_expire)  && checkCVV2(document.checkout_form.card_cvv2,document.checkout_form.card_type)) document.checkout_form.submit()"><font class=FormButton>Submit order <img src="../skin1/images/GOboutonWhiteBkg.gif" width=28 height=13 border=0 align=top></font></a>
</script>
<script language="javascript" type="text/javascript">
<!--
// Get the HTTP Object
function getHTTPObject()
{
	if (window.ActiveXObject) return new ActiveXObject("Microsoft.XMLHTTP");
		else if (window.XMLHttpRequest) return new XMLHttpRequest();
	else
	{
		alert("Your browser does not support AJAX.");
		return null;
	}
}
// Change the value of the outputText field
function setSchoolList()
{
	if(httpObject.readyState == 4)
	{
		var combo = document.getElementById('f_school_id');
		combo.options.length = 0;
		var response = httpObject.responseText;
		var items = response.split(";");
		var count = items.length;
		for (var i=0;i<count;i++)
		{
			var options = items[i].split("-");
			combo.options[i] = new Option(options[0],options[1]);
		}
	}
}
function setCategoryList()
{
	if(httpObject.readyState == 4)
	{
		var combo = document.getElementById('f_project_type_id');
		combo.options.length = 0;
		var response = httpObject.responseText;
		var items = response.split(";");
		var count = items.length;
		for (var i=0;i<count;i++)
		{
			var options = items[i].split("-");
			combo.options[i] = new Option(options[0],options[1]);
		}
	}
}
function setCategoryList2()
{
	if(httpObject2.readyState == 4)
	{
		var combo = document.getElementById('f_project_type_id');
		combo.options.length = 0;
		var response = httpObject2.responseText;
		var items = response.split(";");
		var count = items.length;
		for (var i=0;i<count;i++)
		{
			var options = items[i].split("-");
			combo.options[i] = new Option(options[0],options[1]);
		}
	}
}
function changeDist()
{
	httpObject = getHTTPObject();
	if (httpObject != null)
	{
		httpObject.open("GET", "getDistSchools.php?district_id="+document.getElementById('f_district_id').value, true);
		httpObject.onreadystatechange = setSchoolList;
		httpObject.send(null);
	}
	httpObject2 = getHTTPObject();
	if (httpObject2 != null)
	{
		httpObject2.open("GET", "getSchoolCategory.php?district_id="+document.getElementById('f_district_id').value+"&school_id=ALL", true);
		httpObject2.onreadystatechange = setCategoryList2;
		httpObject2.send(null);
	}
}
function changeSch()
{
	httpObject = getHTTPObject();
	if (httpObject != null)
	{
		httpObject.open("GET", "getSchoolCategory.php?district_id="+document.getElementById('f_district_id').value+"&school_id="+document.getElementById('f_school_id').value, true);
		httpObject.onreadystatechange = setCategoryList;
		httpObject.send(null);
	}
}
//-->
function checksubmit(submitbtn){
submitbtn.form.submit()
checksubmit=blocksubmit
return false
}

function blocksubmit(){
if (typeof formerrormsg!="undefined")
alert(formerrormsg)
return false
}
</script>
<link rel="alternate" type="application/rss+xml" title="RSS" href="http://www.donate2educate.org/rss.php">
