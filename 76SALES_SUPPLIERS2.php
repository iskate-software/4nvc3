<?php 
session_start();
require_once('../../tryconnection.php');

mysqli_select_db($tryconnection, $database_tryconnection);

$get_supplier = "SELECT DISTINCT SUPPLIER FROM ARINVT ORDER BY TRIM(SUPPLIER) ASC" ;
$query_supplier = mysqli_query($tryconnection, $get_supplier) or die(mysqli_error($mysqli_link)) ;
$row_supplier = mysqli_fetch_assoc($query_supplier) ;


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />

<title>SALES_SUPPLIERS</title>


<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>


<script type="text/javascript">

function bodyonload()
{
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+180,toppos+160);
document.supplier_search.supplier.focus();
}

</script>

</head>

<body onload="bodyonload()" onunload="bodyonunload()">

<form method="get" action="SUPPLIER_LIST.php" name="supplier_search" target="mainWin" style="position:absolute; top:0px; left:0px; background-color:#FFFFFF;">

<table width="400" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td height="70" class="Verdana12">&nbsp;</td>
    </tr>
  <tr>
    <td height="30" align="center" class="Verdana12">
      <label>Supplier:
      <select name="supplier" id="supplier" >
      <?php while ($row_supplier = mysqli_fetch_assoc($query_supplier) ) {
      echo ' <option value="'.$row_supplier['SUPPLIER'].'"'; if ($row_supplier['SUPPLIER'] == 'VPC'){ echo ' selected="selected"';} echo '>'.$row_supplier['SUPPLIER']. '</option>' ;
      } ?></select></label> </td>
    </tr>
  <tr>
    <td height="30" align="center" valign="top" class="Verdana11Grey">(Please select required supplier (each has to be done individually))</td>
    </tr>
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
