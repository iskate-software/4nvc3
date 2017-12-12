<?php 

echo ' started ' ;
session_start();
require_once('../../tryconnection.php');
mysql_select_db($database_tryconnection, $tryconnection);
$query_CLASSES = "SELECT CLASSID, CLASS, REGITM3, VAR3, ROUNDER1, MINPRICE1, REGITM6, VAR6, ROUNDER4, MINPRICE4 FROM FORMULA1 ORDER BY CLASS";
$CLASSES = mysql_query($query_CLASSES, $tryconnection) or die(mysql_error());
$row_CLASSES = mysql_fetch_assoc($CLASSES);

echo ' While I use up space which should not be needed Looking for check ';
  $_SESSION["class"] = "x" ;


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>APPLY PRICE CLASSES</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->


<script type="text/javascript">

function bodyonload(){
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+150,toppos+40);
}

function bodyonunload() {
  self.close() ; 
  localStorage.setItem('class','getElementById("class")' ;)
}

//function putInClass(x){
//opener.document.getElementById('class').value=x;
//self.close();
//}

</script>


<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="GET" action="ACTUALLY_APPLY.php" name="classes" id="classes" style="position:absolute; top:0px; left:0px;">
    <table height="553" width="500" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
      <tr height="10" class="Verdana11Bwhite">
        <td width="" bgcolor="#000000">&nbsp;</td>
        <td width="" align="center" bgcolor="#000000">Class</td>
        <td width="70" align="center" bgcolor="#000000">Formula</td>
        <td width="" align="center" bgcolor="#000000">Rounding</td>
        <td align="center" bgcolor="#000000">Min. Price</td>
      </tr>
	  <tr>
      	<td colspan="5">
<div style="height:490px; overflow:auto;">    
<table  width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" bordercolor="#CCCCCC" frame="below" rules="rows">      <tr>
        <td width="35"></td>
        <td width="20" height='0'></td>
        <td width="200" height='0'></td>
        <td width="100" height='0'></td>
        <td height='0'></td>
      </tr>
  <?php do { ?>
    <tr class="Verdana12" id="<?php echo $row_CLASSES['CLASSID']; ?>"  onmouseover="document.getElementById(this.id).style.cursor='pointer'" bgcolor="#EEEEEE" title="<?php echo $row_CLASSES['CLASS']; ?>: Package Price">
      <td height="18"></td>
    <td align="left" class="Verdana12B" ><input type="radio" name="class" value ="<?php echo $row_CLASSES['CLASS']; ?>"/><?php echo $row_CLASSES['CLASS']; ?> </td>
    <td align="left">Cost / Pack Qty * <?php echo $row_CLASSES['REGITM3']; ?></td>
    <td align="center">
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
	</td>
    <td align="center"><?php echo $row_CLASSES['MINPRICE1']; ?></td>
  </tr>
    <tr class="Verdana12" id="<?php echo $row_CLASSES['CLASSID']; ?>x"   onmouseover="document.getElementById(this.id).style.cursor='pointer'" title="<?php echo $row_CLASSES['CLASS']; ?>: Unit Price">
      <td height="18"></td>
    <td align="center"><?php echo $row_CLASSES['']; ?></td>
    <td align="center">Cost / Pack Qty * <?php echo $row_CLASSES['REGITM6']; ?></td>
    <td align="center">
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
    </td>
    <td align="center"><?php echo $row_CLASSES['MINPRICE4']; ?></td>
  </tr>
    <?php } while ($row_CLASSES = mysql_fetch_assoc($CLASSES)); ?>
</table>
</div>
		</td>
      </tr>      
      <tr>
        <td colspan="5" align="center" class="ButtonsTable">
          <input name="apply" type="submit" class="button" id="apply" value="APPLY"  />
          <input name="cancel" type="reset" class="button" id="cancel" value="CANCEL" onclick="self.close();" /></td>
      </tr>
      <input type="hidden" name="check" value="1"  />
     
    </table>
    </form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
