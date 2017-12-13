<?php 
session_start();
require_once('../../tryconnection.php');
mysql_select_db($database_tryconnection, $tryconnection);
$query_CLASSES = "SELECT CLASSID, CLASS, CONCAT(REGITM1,' ',REGOPR1,' ',REGITM2,' ' ,REGOPR2, ' ' , REGITM3) AS VIEWFORM1, 
  REGITM1, REGOPR1, REGITM2, REGOPR2, REGITM3, ROUNDER1,MINPRICE1, CONCAT(REGITM4,' ',REGOPR4,' ',REGITM5,' ' ,REGOPR5, ' ',REGITM6) AS VIEWFORM4, 
  ROUNDER4, MINPRICE4,REGITM6,MEMO1,MEMO2 FROM FORMULA1 ORDER BY CLASS";
$CLASSES = mysql_query($query_CLASSES, $tryconnection) or die(mysql_error());
$row_CLASSES = mysqli_fetch_assoc($CLASSES);
//$totalRows_CLASSES = mysql_num_rows($CLASSES);
$row_CLASSES = mysqli_fetch_assoc($CLASSES);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>PRICE CLASSES</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->


<script type="text/javascript">

function bodyonload(){
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+75,toppos+40);
}

function putInClass(x){
opener.document.getElementById('class').value=x;
self.close();
}

</script>


<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="classes" id="classes" style="position:absolute; top:0px; left:0px;">
    <table height="553" width="700" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
      <tr height="10" class="Verdana11Bwhite">
        <td width="" bgcolor="#000000">&nbsp;</td>
        <td width="" align="left" bgcolor="#000000">&nbsp;&nbsp;&nbsp;&nbsp;Class</td>
        <td width="70" align="left" bgcolor="#000000">&nbsp;&nbsp;&nbsp;Formula</td>
        <td width="" align="left" bgcolor="#000000">Rounding</td>
        <td align="left" bgcolor="#000000">Min. Price</td>
        <td align="center" bgcolor="#000000">Comment</td>
      </tr>
	  <tr>
      	<td colspan="6">
<div style="height:490px; overflow:auto;">    
<table  width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" bordercolor="#CCCCCC" frame="below" rules="rows">      <tr>
        <td width="35"></td>
        <td width="20" height='0'></td>
        <td width="200" height='0'></td>
        <td width="100" height='0'></td>
        <td width="200" height='0'></td>
        <td height='0'></td>
      </tr>
     <!--onclick="putInClass('<?php echo $row_CLASSES['CLASS']; ?>')"-->
  <?php do { ?>
    <tr class="Verdana12" id="<?php echo $row_CLASSES['CLASSID']; ?>"  onclick="document.location='ADD_EDIT_CLASS.php?classid=<?php echo $row_CLASSES['CLASSID']; ?>';" onmouseover="document.getElementById(this.id).style.cursor='pointer'" bgcolor="#EEEEEE" title="<?php echo $row_CLASSES['CLASS']; ?>: Package Price">
      <td height="18"></td>
    <td align="center"><?php echo $row_CLASSES['CLASS']; ?></td>
    <td align="center">Cost / Pack Qty * <?php echo $row_CLASSES['REGITM3']; ?></td>
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
    <td align="center"><?php echo $row_CLASSES['MINPRICE1']; ?></td>
    <td align="left"><?php echo $row_CLASSES['MEMO1']; ?></td>
  </tr>
    <tr class="Verdana12" id="<?php echo $row_CLASSES['CLASSID']; ?>x"  onclick="document.location='ADD_EDIT_CLASS.php?classid=<?php echo $row_CLASSES['CLASSID']; ?>';" onmouseover="document.getElementById(this.id).style.cursor='pointer'" title="<?php echo $row_CLASSES['CLASS']; ?>: Unit Price">
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
    <td align="left"><?php echo $row_CLASSES['MEMO2']; ?></td>
  </tr>
    <?php } while ($row_CLASSES = mysqli_fetch_assoc($CLASSES)); ?>
</table>
</div>
		</td>
      </tr>      
      <tr>
        <td colspan="6" align="center" class="ButtonsTable">
          <input name="add" type="button" class="button" id="add" value="ADD NEW" onclick="document.location='ADD_EDIT_CLASS.php?classid=0';"/>
        <input name="cancel" type="reset" class="button" id="cancel" value="CANCEL" onclick="self.close();" /></td>
      </tr>
    </table>
    </form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
