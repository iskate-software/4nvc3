<?php 
session_start();
require_once('../../tryconnection.php'); 

mysql_select_db($database_tryconnection, $tryconnection);

if (isset($_POST['check1'])) {
 $closewindow= "self.close();" ;
 $_SESSION['genmsg'] = $_POST['gcommtext'] ;
}
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>STATEMENT MESSAGE</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">

function bodyonload()
{
<?php echo $closewindow; ?>
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+90,toppos+80);
document.forms[0].gcommtext.focus();
}


function countchar(){
var chars=document.forms[0].gcommtext.value.length;
document.getElementById('maxnum').innerText=chars;
	if (chars>1000){
	alert('I am sorry, but your comment is too long. It\'s not my fault.');
	document.forms[0].gcommtext.value=document.forms[0].gcommtext.value.substr(0,999);	
	}
}

</script>

<style type="text/css">
<!--
.Labels2{
font-family:Arial, Helvetica, sans-serif;
}
-->
</style>



<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="stmtgmess" id="" class="FormDisplay" style="position:absolute; top:0px; left:0px;">

<table id="linstructionsuser" width="600" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td height="40" colspan="5" align="center" valign="bottom" class="Verdana12B">Please enter <?php if ($_GET['msg']=='gen') {echo "GENERAL broadcasted message";} else {echo "message #".$_GET['msg']." and select clients to target";} ?>:
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <input type="button" name="view" id="view" value="VIEW" onclick="window.open('../../INVOICE/COMMENTS/COMMENTS_LIST.php?path=TFF','_blank')" />    </td>
  </tr>
  <tr>
    <td colspan="5" align="center">
    <!--the petname variable is here just to make it work-->
    <input type="hidden" name="petname" id="petname" size="6" value="" />
    <input type="hidden" name="tautocomm" id="tautocomm" size="6" value="" />
    <textarea name="gcommtext" id="gcommtext" cols="70" rows="10" wrap="virtual" class="commentarea" onkeyup="countchar()"></textarea>    </td>
  </tr>
  <tr>
    <td height="35" colspan="5" align="center" valign="top" class="Verdana11Grey"># of characters: <span id="maxnum"></span> (max 1000)&nbsp;&nbsp;<input type="button" value="CLEAR" onclick="document.stmtgmess.gcommtext.value='';"  />    </td>
    </tr>
  <tr <?php if ($_GET['msg']=='gen') {echo "class='hidden'";}?>>
    <td height="1" colspan="5" align="left" class="Verdana12Blue"><hr size="1" style="margin:0px;" color="#0000FF" /></td>
    </tr>
  <tr <?php if ($_GET['msg']=='gen') {echo "class='hidden'";}?>>
    <td width="42" height="25" align="left" class="Verdana12Blue"></td>
    <td width="138" align="left" class="Verdana12Blue"><label>
      <input type="checkbox" name="checkbox" id="checkbox" />
      Canine Clients</label>      </td>
    <td width="138" align="left" class="Verdana12Blue">
    <label><input type="checkbox" name="checkbox2" id="checkbox2" />
    Feline Clients</label>    </td>
    <td width="138" align="left" class="Verdana12Blue">
    <label><input type="checkbox" name="checkbox2" id="checkbox2" />
    Equine Clients</label>    </td>
    <td width="138" align="left" class="Verdana12Blue">
    <label><input type="checkbox" name="checkbox2" id="checkbox2" />
    Bovine Clients</label>    </td>
  </tr>
  <tr <?php if ($_GET['msg']=='gen') {echo "class='hidden'";}?>>
    <td height="25" align="left" class="Verdana12Blue"></td>
    <td align="left" class="Verdana12Blue">
    <label><input type="checkbox" name="checkbox2" id="checkbox2" />Caprine Clients</label>    </td>
    <td align="left" class="Verdana12Blue">
    <label><input type="checkbox" name="checkbox2" id="checkbox2" />
    Porcine Clients</label>    </td>
    <td align="left" class="Verdana12Blue">
    <label><input type="checkbox" name="checkbox2" id="checkbox2" />
    Avian Clients</label>    </td>
    <td align="left" class="Verdana12Blue">
    <label><input type="checkbox" name="checkbox2" id="checkbox2" />
    Other Clients</label>    </td>
  </tr>
  <tr <?php if ($_GET['msg']=='gen') {echo "class='hidden'";}?>>
    <td height="1" colspan="5" align="left" class="Verdana12Blue"><hr size="1" style="margin:0px;" color="#0000FF" /></td>
    </tr>
  <tr <?php if ($_GET['msg']=='gen') {echo "class='hidden'";}?>>
    <td height="1" colspan="5" align="left" class="Verdana12Blue"><hr size="1" style="margin:1px;" color="#000000" /></td>
    </tr>
  <tr <?php if ($_GET['msg']=='gen') {echo "class='hidden'";}?>>
    <td height="25" align="left" class="Verdana12Blue"></td>
    <td align="left" class="Verdana12B">
    <label><input type="checkbox" name="checkbox2" id="checkbox2" />
    Current Clients</label>    </td>
    <td align="left" class="Verdana12B">
    <label><input type="checkbox" name="checkbox2" id="checkbox2" />
    Over 30 Days</label>    </td>
    <td align="left" class="Verdana12B">
    <label><input type="checkbox" name="checkbox2" id="checkbox2" />
    Over 60 Days</label>    </td>
    <td align="left" class="Verdana12B"></td>
  </tr>
  <tr <?php if ($_GET['msg']=='gen') {echo "class='hidden'";}?>>
    <td height="25" align="left" class="Verdana12Blue"></td>
    <td align="left" class="Verdana12B">
    <label><input type="checkbox" name="checkbox2" id="checkbox2" />
    Over 90 Days</label>    </td>
    <td align="left" class="Verdana12B">
    <label><input type="checkbox" name="checkbox2" id="checkbox2" />
    Over 120 Days</label>    </td>
    <td colspan="2" align="left" class="Verdana12Pink"><label>
      <input type="radio" name="radio" id="radio" value="radio" />
      Only selected aging</label><label>
      <input type="radio" name="radio" id="radio" value="radio" />
      To Any Clients</label></td>
    </tr>
  <tr <?php if ($_GET['msg']=='gen') {echo "class='hidden'";}?>>
    <td height="1" colspan="5" align="left" class="Verdana12Blue"><hr size="1" style="margin:0px;" color="#000000" /></td>
    </tr>
  <tr>
    <td height="26" colspan="5" align="center">To force a line break use '<' and '/br' and '>' without the quotes or the word "and".</td>
  </tr>
  <tr>
    <td colspan="5" align="center" class="ButtonsTable">
    <input type="submit" class="button" name="save" id="save" value="SAVE" />
    <input type="button" class="button" name="button2" id="button2" value="CLOSE" onclick="self.close();" /></td>
  </tr>
</table>


<input type="hidden" name="check1" value="1"  />


</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
