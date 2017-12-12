<?php 
session_start();
require_once('../../tryconnection.php');
include("../../ASSETS/age.php");

$classid=$_GET['classid'];
mysql_select_db($database_tryconnection, $tryconnection);
$query_CLASSES = "SELECT CLASSID, CLASS, CONCAT(REGITM1,' ',REGOPR1,' ',REGITM2,' ' ,REGOPR2, ' ' , REGITM3) AS VIEWFORM1, 
  REGITM1, REGOPR1, REGITM2, REGOPR2, REGITM3, ROUNDER1,MINPRICE1, CONCAT(REGITM4,' ',REGOPR4,' ',REGITM5,' ' ,REGOPR5, ' ',REGITM6) AS VIEWFORM4, 
  ROUNDER4, MINPRICE4,REGITM6, MEMO1, MEMO2 FROM FORMULA1 WHERE CLASSID = $classid LIMIT 1";
$CLASSES = mysql_query($query_CLASSES, $tryconnection) or die(mysql_error());
$row_CLASSES = mysql_fetch_assoc($CLASSES);


if (isset($_POST['save']) && $classid=='0'){

// $viewform1 = "Cost / Pack Qty * " .$_POST['var3'];
// $viewform4 = "Cost / Pack Qty * " .$_POST['var6'];
  $viewform1 = $row_CLASSES['REGITM1'] . ' ' .$row_CLASSES['REGOPR1'] . ' ' . $row_CLASSES['REGITM2'] . ' ' . $row_CLASSES['REGOPR2']. ' ' . $_POST['VAR3'] ;
  $viewform2 = $row_CLASSES['REGITM4'] . ' ' .$row_CLASSES['REGOPR4'] . ' ' . $row_CLASSES['REGITM5'] . ' ' . $row_CLASSES['REGOPR5']. ' ' . $_POST['VAR6'] ;
  
 $insert_FORMULA1="INSERT INTO FORMULA1 (CLASS, VIEWFORM1, REGITM1, REGOPR1, REGITM2, REGOPR2, REGITM3, VAR1, VAR2,VAR3, REGITM6,ROUNDER1,         
 ROUNDER4, MEMO1, MEMO2, MINPRICE1, MINPRICE4,VIEWFORM4,REGITM4, REGOPR4,REGITM5,REGOPR5,VAR4,VAR5,VAR6,FLDCOUNT4) 
 VALUES ('$_POST[class]','".mysql_real_escape_string($viewform1)."','COST','/','PKGQTY', '*','$_POST[var3]','MCOST','MPKGQTY', '$_POST[var3]','$_POST[var6]','$_POST[rounder1]',
 '$_POST[rounder4]','".mysql_real_escape_string($_POST['memo1'])."','".mysql_real_escape_string($_POST['memo2'])."','$_POST[minprice1]','$_POST[minprice4]', '".mysql_real_escape_string($viewform4)."',
 'COST', '/', 'PKGQTY','*','MCOST','MPKGQTY', '$_POST[var6]','3' )";
 $FORMULA1=mysql_query($insert_FORMULA1, $tryconnection) or die(mysql_error());
 header('Location:CLASS_REPORT.php');
}
else if (isset($_POST['save']) && $classid !='0'){

 $viewform1 = $row_CLASSES['VIEWFORM1'];
 $viewform4 = $row_CLASSES['VIEWFORM4'];

 $update_FORMULA1="UPDATE FORMULA1 SET REGITM3 ='$_POST[var3]', VAR3 = '$_POST[var3]', REGITM6 ='$_POST[var6]', VAR6 ='$_POST[var6]', ROUNDER1='$_POST[rounder1]', ROUNDER4='$_POST[rounder4]', MEMO1='".mysql_real_escape_string($_POST['memo1'])."', 
 MEMO2='".mysql_real_escape_string($_POST['memo2'])."', VIEWFORM1 = '".mysql_real_escape_string($viewform1)."', VIEWFORM4 = '".mysql_real_escape_string($viewform4)."', MINPRICE1='$_POST[minprice1]', MINPRICE4='$_POST[minprice4]' WHERE CLASSID='$classid' LIMIT 1";
 $FORMULA1=mysql_query($update_FORMULA1, $tryconnection) or die(mysql_error());
 header('Location:CLASS_REPORT.php');
}
else if (isset($_POST['delete']) && $classid!='0'){
$delete_FORMULA1="DELETE FROM FORMULA1 WHERE CLASSID='$classid' LIMIT 1";
$FORMULA1=mysql_query($delete_FORMULA1, $tryconnection) or die(mysql_error());
$optimize_FORMULA1="OPTIMIZE TABLE FORMULA1";
$FORMULA2=mysql_query($optimize_FORMULA1, $tryconnection) or die(mysql_error());
header('Location:CLASS_REPORT.php');
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title><?php if($classid=='0') {echo "ADD NEW";} else {echo "EDIT";} ?> CLASS <?php echo $row_CLASSES['CLASS']; ?></title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<style type="text/css">
<!--

#shadow {
	background-color: #556453;
	width: 296px;
	height: auto;
}
#shadowedtable {
	position: relative;
	width: 294px;
	height: auto;
	left: -4px;
	top: -4px;
	background-color:#FFFFFF;
	border: solid #556453 thin;
}
-->
</style>

<script type="text/javascript">
function bodyonload(){
	if (sessionStorage.filetype!='0'){
	document.getElementById('inuse').innerText=sessionStorage.fileused;
	}
	else {
	document.getElementById('inuse').innerHTML="&nbsp;";
	}
}


function countchar(){
var chars=document.forms[0].memo1.value.length;
document.getElementById('maxnum1').innerText=chars;
	if (chars>255){
	alert('I am sorry, but your comment is too long. It\'s not my fault.');
	document.forms[0].memo1.value=document.forms[0].memo1.value.substr(0,254);	
	}

var chars=document.forms[0].memo2.value.length;
document.getElementById('maxnum2').innerText=chars;
	if (chars>255){
	alert('I am sorry, but your comment is too long. It\'s not my fault.');
	document.forms[0].memo2.value=document.forms[0].memo2.value.substr(0,254);	
	}
}

</script>

<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form action="" class="FormDisplay" method="post" name="price"  style="position:absolute; top:0px; left:0px;">
  <table width="500" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
  <tr>
    <td height="30" align="left" valign="bottom" class="Verdana13">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Class Name: <span class="Verdana20B"><?php if($classid=='0') {echo '<input type="text" name="class" id="class" class="Input" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" size="3" maxlength="2" value="'.$row_CLASSES['CLASS'].'" />';} else {echo $row_CLASSES['CLASS'];} ?></span></td>
  </tr>
  <tr>
    <td height="30" align="center" class="Verdana13B">PACKAGE PRICE</td>
  </tr>
  <tr>
    <td height="140" align="center" valign="top"><table width="90%" border="1" cellspacing="0" cellpadding="0" class="table">
      <tr>
        <td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td height="5" colspan="3"></td>
            </tr>
            <tr>
              <td width="9%" class="Verdana11">&nbsp;</td>
              <td width="19%" height="30" class="Verdana12">Markup</td>
              <td width="72%" height="25" class="Verdana11"><input type="text" name="var3" id="var3" class="Inputright" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" size="7" maxlength="10" value="<?php echo $row_CLASSES['REGITM3']; ?>"/></td>
            </tr>
            <tr>
              <td class="Verdana11">&nbsp;</td>
              <td height="30" class="Verdana12">Min. Price</td>
              <td height="25" class="Verdana11"><input type="text" name="minprice1" id="class5" class="Inputright" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" size="7" maxlength="10" value="<?php echo $row_CLASSES['MINPRICE1']; ?>"/></td>
            </tr>
            <tr>
              <td class="Verdana11">&nbsp;</td>
              <td height="30" class="Verdana12">Rounding</td>
              <td height="25" class="Verdana11"><select name="rounder1" id="rounder1">
                  <option value="<?php echo $row_CLASSES['ROUNDER1']; ?>" selected="selected">
                  <?php 
				switch ($row_CLASSES['ROUNDER1']) {
					case 0.01:
						echo "Cent";
						break;
					case 0.05:
						echo "Nickel";
						break;
					case 0.10:
						echo "Dime";
						break;
					case 0.25:
						echo "Quarter";
						break;
					case 0.50:
						echo "Half Dollar";
						break;					
					case 1.00:
						echo "Dollar";
						break;
				} 
				
				?>
                    </option>
                  <option value="0.01">Cent</option>
                  <option value="0.05">Nickel</option>
                  <option value="0.10">Dime</option>
                  <option value="0.25">Quarter</option>
                  <option value="0.50">Half Dollar</option>
                  <option value="1.00">Dollar</option>
                </select>
              </td>
            </tr>
            <tr>
              <td class="Verdana11">&nbsp;</td>
              <td height="30" class="Verdana12">Comment</td>
              <td rowspan="3" class="Verdana11Grey"><textarea name="memo1" cols="35" rows="3" class="commentarea" id="textarea2" onkeyup="countchar()"><?php echo $row_CLASSES['MEMO1']; ?></textarea>
                  <br  />
                # of characters: <span id="maxnum1"></span> (max 255)</td>
            </tr>
            <tr>
              <td class="Verdana11">&nbsp;</td>
              <td height="25" class="Verdana12">&nbsp;</td>
            </tr>
            <tr>
              <td class="Verdana11">&nbsp;</td>
              <td height="25" class="Verdana12">&nbsp;</td>
            </tr>
            <tr>
              <td height="5" colspan="3"></td>
            </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="16" align="center" class="Verdana13B"></td>
  </tr>
  <tr>
    <td height="30" align="center" class="Verdana13B">UNIT PRICE</td>
  </tr>
  <tr>
    <td height="140" align="center" valign="top"><table width="90%" border="1" cellspacing="0" cellpadding="0" class="table" bgcolor="#FFFFFF">
      <tr>
        <td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td height="5" colspan="3"></td>
            </tr>
            <tr>
              <td width="9%" class="Verdana11">&nbsp;</td>
              <td width="19%" height="30" class="Verdana12">Markup</td>
              <td width="72%" height="25" class="Verdana11"><input type="text" name="var6" id="var6" class="Inputright" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" size="7" maxlength="10" value="<?php echo $row_CLASSES['REGITM6']; ?>"/></td>
            </tr>
            <tr>
              <td class="Verdana11">&nbsp;</td>
              <td height="30" class="Verdana12">Min. Price</td>
              <td height="25" class="Verdana11"><input type="text" name="minprice4" id="minprice4" class="Inputright" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" size="7" maxlength="10" value="<?php echo $row_CLASSES['MINPRICE4']; ?>"/></td>
            </tr>
            <tr>
              <td class="Verdana11">&nbsp;</td>
              <td height="30" class="Verdana12">Rounding</td>
              <td height="25" class="Verdana11"><select name="rounder4" id="rounder4">
                <?php echo $row_CLASSES['ROUNDER4']; ?>
                  <option value="<?php echo $row_CLASSES['ROUNDER4']; ?>" selected="selected">
                  <?php 
				switch ($row_CLASSES['ROUNDER4']) {
					case 0.01:
						echo "Cent";
						break;
					case 0.05:
						echo "Nickel";
						break;
					case 0.10:
						echo "Dime";
						break;
					case 0.25:
						echo "Quarter";
						break;
					case 0.50:
						echo "Half Dollar";
						break;					
					case 1.00:
						echo "Dollar";
						break;
				} 
				
				?>
                    </option>
                  <option value="0.01">Cent</option>
                  <option value="0.05">Nickel</option>
                  <option value="0.10">Dime</option>
                  <option value="0.25">Quarter</option>
                  <option value="0.50">Half Dollar</option>
                  <option value="1.00">Dollar</option>
                </select>
              </td>
            </tr>
            <tr>
              <td class="Verdana11">&nbsp;</td>
              <td height="30" class="Verdana12">Comment</td>
              <td rowspan="3" class="Verdana11Grey"><textarea name="memo2" cols="35" rows="3" class="commentarea" id="textarea" onkeyup="countchar()"><?php echo $row_CLASSES['MEMO2']; ?></textarea>
                  <br  />
                # of characters: <span id="maxnum2"></span> (max 255)</td>
            </tr>
            <tr>
              <td class="Verdana11">&nbsp;</td>
              <td height="25" class="Verdana12">&nbsp;</td>
            </tr>
            <tr>
              <td class="Verdana11">&nbsp;</td>
              <td height="25" class="Verdana12">&nbsp;</td>
            </tr>
            <tr>
              <td height="5" colspan="3"></td>
            </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="40" align="center" valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td align="center" class="ButtonsTable">
        <input name="check" type="button" class="button" id="check" value="CHECK" />
        <input name="save" type="submit" class="button" id="save" value="SAVE" />
        <input name="delete" type="button" class="button" id="delete" value="DELETE" />
        <input name="cancel" type="button" class="button" id="ok" value="CANCEL" onclick="history.back();" />    </td>
  </tr>
 </table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
