<?php 
session_start();
require_once('../../tryconnection.php');


mysql_select_db($database_tryconnection, $tryconnection);


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />

<title>REPORT BY EXPIRY SEARCH</title>


<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>


<script type="text/javascript">

function bodyonload()
{
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+180,toppos+160);
document.expiry_search.seq.focus();
}

function bodyonunload() 
{
self.close() ;
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;

</script>

</head>

<body onload="bodyonload()" onunload="bodyonunload()">


<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;" >
<tr><td id="ds_calclass"></td></tr>
</table>
<script type="text/javascript" src="../../ASSETS/calendar.js"></script>

<form method="get" action="REPORT_BY_EXPIRY_RESULTS.php" name="expiry_search" target="mainWin" style="position:absolute; top:0px; left:0px; background-color:#FFFFFF;">

<table width="400" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td height="70" class="Verdana12">&nbsp;</td>
    </tr>
  <tr>
    <td height="30" align="center" class="Verdana12">Expiry Cut-Off Date: 
      <input name="cutdate" id="cutdate" type="text" class="Input" size="10" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onclick="ds_sh(this);" value="<?php echo date('m/d/Y'); ?>" title="MM/DD/YYYY"/></td>
    </tr>  <!--
  <tr>
    <td height="30" align="center" valign="top" class="Verdana11Grey">(Leave blank for all)</td>
    </tr> -->
  <tr>
    <td height="70" class="Verdana12">&nbsp;</td>
    </tr>  
  <tr class="ButtonsTable">
    <td align="center">
    	<input name="display" type="submit" class="button" id="display" value="DISPLAY"/>
        <input name="close" type="button" class="button" id="close" value="CLOSE" onclick="self.close();"/>
    </td>
   </tr>
</table>

</form>
</body>
</html>
