<?php
session_start();
require_once('../../tryconnection.php');

mysql_select_db($database_tryconnection, $tryconnection);

$LIMIT1 = "SELECT FIRSTINV FROM PREFER LIMIT 1" ;
$DOIT1 = mysql_query($LIMIT1, $tryconnection ) or die(mysql_error()) ;
$FIRSTINV = mysqli_fetch_array($DOIT1);

$LIMIT2 = "SELECT LASTINV FROM PREFER LIMIT 1" ;
$DOIT2 = mysql_query($LIMIT2, $tryconnection ) or die(mysql_error()) ;
$LASTINV = mysqli_fetch_array($DOIT2);

$query_closedate="SELECT STR_TO_DATE('$_GET[closedate]','%m/%d/%Y')";
$closedate= mysql_unbuffered_query($query_closedate, $tryconnection) or die(mysql_error());
$closedate=mysqli_fetch_array($closedate);

$closemonth ="SELECT DATE_FORMAT('$closedate[0]', '%D %M %Y') " ;
$clm = mysql_query($closemonth, $tryconnection) or die(mysql_error()) ;
$clm1 = mysqli_fetch_array($clm) ;
$clm2 = $clm1[0] ;
/*
$SETUP1 = "DROP TEMPORARY TABLE IF EXISTS MSALES" ;
$PREP1 = mysql_query($SETUP1, $tryconnection) or die(mysql_error()) ;

$SETUP2 = "CREATE TEMPORARY TABLE MSALES LIKE SALESCAT" ;
$PREP2 = mysql_query($SETUP2, $tryconnection) or die(mysql_error()) ;

// $SETUP3 = "INSERT INTO MSALES SELECT * FROM SALESCAT WHERE INVREVCAT <> 0 AND INVREVCAT <> 91 AND INVDTE <= '$closedate[0]' AND INVNO >='$FIRSTINV[0]' AND INVNO <= '$LASTINV[0]' AND INVDECLINE=0"  ; 
$SETUP3 = "INSERT INTO MSALES SELECT * FROM SALESCAT WHERE INVREVCAT <> 0 AND INVREVCAT <> 91 AND INVDTE <= '$closedate[0]'  AND INVDECLINE=0"  ;

$PREP3 = mysql_query($SETUP3, $tryconnection) or die(mysql_error()) ;
*/
// Finally, the total Revenue analysis which rolls up all of the above into a single report.


// now pick out all the doctors who actually invoiced this month, and check it against the master list.

$DOCAVG4 = "SELECT DISTINCT INVORDDOC FROM MSALES " ;
$DOCAVG5 = mysql_query($DOCAVG4, $tryconnection) or die(mysql_error()) ;

/* First, the master loop which will refresh the reference table until there are no more new doctors.


while ($row = mysql_fetch_row($DOCAVG5) {
 $NEW = 1 ;
  while ($row1 = mysql_fetch_row($DOCAVG3) {
    if ($row[0] = $row1[0]) {
     $NEW = 0 ;
     break ;
    }
   if ($NEW = 1) {
    $DOCAVG6 = "INSERT INTO DOCTAB SET DOCTOR = $row[0],INVOICES = 0, TOTAL = 0, AVERAGE = 0 " ;
    $DOCAVG7 = mysql_query($DOCAVG6, $tryconnection) or die(mysql_error()) ;
   }
  }
}
*/
$query_TOTREV = "SELECT INVMAJ, INVREVCAT, INVORDDOC, SUM(INVTOT) AS INVTOT FROM MSALES GROUP BY INVREVCAT, INVORDDOC WITH ROLLUP" ;
$TOTREV = mysql_query($query_TOTREV, $tryconnection) or die(mysql_error()) ;
$row_TOTREV = mysqli_fetch_assoc($TOTREV) ;
/*
 For this one, the interpretation of INVREVCAT comes from REVCAT, with the following additions:
 	90 = GST
 	92 = PST
 	95 = Casual Sales
 	96 = Discounts
 	97 = Cancelled from previous months
 	98 = Service Charges
 	99 = Summary Invoices
*/

  $query_CRITDATA = "SELECT HGST,HOGST,HGSTDATE,HTAXNAME,HOTAXNAME FROM CRITDATA LIMIT 1" ;
  $CRITDATA = mysql_query($query_CRITDATA, $tryconnection) or die(mysql_error());
  $row_CRITDATA = mysqli_fetch_assoc($CRITDATA);
  $date2 = date("Y-m-d",time());
  if ($date2 >= $row_CRITDATA['HGSTDATE']) {
   $GSTNAME = $row_CRITDATA['HTAXNAME'] ;
   $GSTRATE = $row_CRITDATA['HGST'] ;
  }
  else {
   $GSTNAME = $row_CRITDATA['HOTAXNAME'] ;
   $GSTRATE = $row_CRITDATA['HOGST'] ;
   }


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style TYPE="text/css">
p.breakhere {page-break-before:always}
</style>
<title>REVENUE ANALYSIS SUMMARY</title>
<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />

<script type="text/javascript">

</script>

</head>

<body onload="window.print();" style="background-color:#FFFFFF; overflow:auto;">
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" style="top:0px; left:0px; position:absolute ">
  <tr>
    <td class="Verdana12" align="center">
<!--   <div id="irresults">-->      
<!--
<div id-"Canine">
<table width="732" border="0" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="none" style="page-break-before:always;">
  <tr height="10">
    <td height="35" colspan="7" align="center" valign="middle" class="Verdana14B"><script type="text/javascript">document.write(localStorage.hospname);</script><br />
Revenue Analysis (
  <?php echo $clm2; ?>
  )</td>
  </tr>
  <tr height="5" bgcolor="#000000" class="Verdana14Bwhite">
    <td align="center">CANINE</td>
    <td align="left"></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"></td>
    <td align="center">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td width="140" height="0"></td>
    <td width="150"></td>
    <td width="200"></td>
    <td width="100"></td>
    <td width=""></td>
    <td width=""></td>
    <td width=""></td>
  </tr>
  <tr class="Verdana14B">
    <td height="10" class="Verdana14B" align="center"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  <?php do {  ?>
  <tr>
    <td height="18"></td>
    <td class="Verdana12B"><?php if ($row_CANINE['INVMAJ']!=$xxx) { 
	$query_TFF="SELECT TTYPE FROM VETCAN WHERE TSPECIES='1' AND TCATGRY='".$row_CANINE['INVMAJ']."' LIMIT 1";
	$TFF = mysql_query($query_TFF, $tryconnection) or die(mysql_error());
	$row_TFF = mysqli_fetch_assoc($TFF);
	echo $row_TFF['TTYPE'];
	} ?></td>
    <td <?php if (empty($row_CANINE['INVORDDOC'])) {echo "align='right' class='Verdana12B'";} else if (empty($row_CANINE['INVMAJ'])){
	echo "align='right' class='Verdana13B'";
	} ?>>
	
	<?php 
	if (!empty($row_CANINE['INVORDDOC'])) {
	echo $row_CANINE['INVORDDOC'];
	} 
	else if (empty($row_CANINE['INVMAJ'])){
	echo "TOTAL";
	}
	else {
	echo "Subtotal";
	} ?>
    
    </td>
    <td align="right" <?php if (empty($row_CANINE['INVORDDOC'])) {echo "class='Verdana12B'";} else if (empty($row_CANINE['INVMAJ'])){
	echo "class='Verdana13B'";
	}?>>
	
	<?php 
	if (empty($row_CANINE['INVORDDOC'])) {
	echo "<hr size='1' style='margin:0;' width='90'/>";
	} 
	echo number_format($row_CANINE['INVTOT'],2); ?>
    </td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <?php 
  
  if (empty($row_CANINE['INVORDDOC'])) {
  echo '<tr>
    <td height="15"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>';
  } 
   $xxx = $row_CANINE['INVMAJ'];} while ($row_CANINE = mysqli_fetch_assoc($CANPRT));  ?>
</table>
</div>
<!      >  
<div id-"Feline">
<h2>  </h2>
<p class="breakhere">
<table width="732" border="0" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="none" style="page-break-before:always;">
  <tr height="10">
    <td height="34" colspan="7" align="center" valign="middle" class="Verdana14B"><script type="text/javascript">document.write(localStorage.hospname);</script><br />
Revenue Analysis (
  <?php echo $clm2; ?>
  )</td>
  </tr>
  <tr height="5" bgcolor="#000000" class="Verdana14Bwhite">
    <td align="center">FELINE</td>
    <td align="left"></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"></td>
    <td align="center">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td width="140" height="0"></td>
    <td width="150"></td>
    <td width="200"></td>
    <td width="100"></td>
    <td width=""></td>
    <td width=""></td>
    <td width=""></td>
  </tr>
  <tr class="Verdana14B">
    <td height="10" class="Verdana14B" align="center"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  <?php do {  ?>
  <tr>
    <td height="18"></td>
    <td class="Verdana12B"><?php if ($row_FELINE['INVMAJ']!=$xxx) { 
	$query_TFF="SELECT TTYPE FROM VETCAN WHERE TSPECIES='2' AND TCATGRY='".$row_FELINE['INVMAJ']."' LIMIT 1";
	$TFF = mysql_query($query_TFF, $tryconnection) or die(mysql_error());
	$row_TFF = mysqli_fetch_assoc($TFF);
	echo $row_TFF['TTYPE'];
	} ?></td>
    <td <?php if (empty($row_FELINE['INVORDDOC'])) {echo "align='right' class='Verdana12B'";} else if (empty($row_FELINE['INVMAJ'])){
	echo "align='right' class='Verdana13B'";
	} ?>>
	
	<?php 
	if (!empty($row_FELINE['INVORDDOC'])) {
	echo $row_FELINE['INVORDDOC'];
	} 
	else if (empty($row_FELINE['INVMAJ'])){
	echo "TOTAL";
	}
	else {
	echo "Subtotal";
	} ?>
    
    </td>
    <td align="right" <?php if (empty($row_FELINE['INVORDDOC'])) {echo "class='Verdana12B'";} else if (empty($row_FELINE['INVMAJ'])){
	echo "class='Verdana13B'";
	}?>>
	
	<?php 
	if (empty($row_FELINE['INVORDDOC'])) {
	echo "<hr size='1' style='margin:0;' width='90'/>";
	} 
	echo number_format($row_FELINE['INVTOT'],2); ?>
    </td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <?php 
  
  if (empty($row_FELINE['INVORDDOC'])) {
  echo '<tr>
    <td height="15"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>';
  } 
   $xxx = $row_FELINE['INVMAJ'];} while ($row_FELINE = mysqli_fetch_assoc($FELPRT));  ?>


      </table>
</div>
<h2> </h2>
<table width="732" border="0" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="none" style="page-break-before:always;">
  <tr height="10">
    <td height="34" colspan="7" align="center" valign="middle" class="Verdana14B"><script type="text/javascript">document.write(localStorage.hospname);</script><br />
Revenue Analysis (
  <?php echo $clm2; ?>
  )</td>
  </tr>
  <tr height="5" bgcolor="#000000" class="Verdana14Bwhite">
    <td align="center">EQUINE</td>
    <td align="left"></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"></td>
    <td align="center">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td width="140" height="0"></td>
    <td width="150"></td>
    <td width="200"></td>
    <td width="100"></td>
    <td width=""></td>
    <td width=""></td>
    <td width=""></td>
  </tr>
  <tr class="Verdana14B">
    <td height="10" class="Verdana14B" align="center"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  <?php do {  ?>
  <tr>
    <td height="18"></td>
    <td class="Verdana12B"><?php if ($row_EQUINE['INVMAJ']!=$xxx) { 
	$query_TFF="SELECT TTYPE FROM VETCAN WHERE TSPECIES='3' AND TCATGRY='".$row_EQUINE['INVMAJ']."' LIMIT 1";
	$TFF = mysql_query($query_TFF, $tryconnection) or die(mysql_error());
	$row_TFF = mysqli_fetch_assoc($TFF);
	echo $row_TFF['TTYPE'];
	} ?></td>
    <td <?php if (empty($row_EQUINE['INVORDDOC'])) {echo "align='right' class='Verdana12B'";} else if (empty($row_EQUINE['INVMAJ'])){
	echo "align='right' class='Verdana13B'";
	} ?>>
	
	<?php 
	if (!empty($row_EQUINE['INVORDDOC'])) {
	echo $row_EQUINE['INVORDDOC'];
	} 
	else if (empty($row_EQUINE['INVMAJ'])){
	echo "TOTAL";
	}
	else {
	echo "Subtotal";
	} ?>
    
    </td>
    <td align="right" <?php if (empty($row_EQUINE['INVORDDOC'])) {echo "class='Verdana12B'";} else if (empty($row_EQUINE['INVMAJ'])){
	echo "class='Verdana13B'";
	}?>>
	
	<?php 
	if (empty($row_EQUINE['INVORDDOC'])) {
	echo "<hr size='1' style='margin:0;' width='90'/>";
	} 
	echo number_format($row_EQUINE['INVTOT'],2); ?>
    </td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <?php 
  
  if (empty($row_EQUINE['INVORDDOC'])) {
  echo '<tr>
    <td height="15"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>';
  } 
   $xxx = $row_EQUINE['INVMAJ'];} while ($row_EQUINE = mysqli_fetch_assoc($EQPRT));  ?>


      </table>
  
  
<table width="732" border="0" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="none" style="page-break-before:always;">
  <tr height="10">
    <td height="35" colspan="7" align="center" valign="middle" class="Verdana14B"><script type="text/javascript">document.write(localStorage.hospname);</script><br />
Revenue Analysis (
  <?php echo $clm2; ?>
  )</td>
  </tr>
  <tr height="5" bgcolor="#000000" class="Verdana14Bwhite">
    <td align="center">BOVINE</td>
    <td align="left"></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"></td>
    <td align="center">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td width="140" height="0"></td>
    <td width="150"></td>
    <td width="200"></td>
    <td width="100"></td>
    <td width=""></td>
    <td width=""></td>
    <td width=""></td>
  </tr>
  <tr class="Verdana14B">
    <td height="10" class="Verdana14B" align="center"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  <?php do {  ?>
  <tr>
    <td height="18"></td>
    <td class="Verdana12B"><?php if ($row_BOVINE['INVMAJ']!=$xxx) { 
	$query_TFF="SELECT TTYPE FROM VETCAN WHERE TSPECIES='4' AND TCATGRY='".$row_BOVINE['INVMAJ']."' LIMIT 1";
	$TFF = mysql_query($query_TFF, $tryconnection) or die(mysql_error());
	$row_TFF = mysqli_fetch_assoc($TFF);
	echo $row_TFF['TTYPE'];
	} ?></td>
    <td <?php if (empty($row_BOVINE['INVORDDOC'])) {echo "align='right' class='Verdana12B'";} else if (empty($row_BOVINE['INVMAJ'])){
	echo "align='right' class='Verdana13B'";
	} ?>>
	
	<?php 
	if (!empty($row_BOVINE['INVORDDOC'])) {
	echo $row_BOVINE['INVORDDOC'];
	} 
	else if (empty($row_BOVINE['INVMAJ'])){
	echo "TOTAL";
	}
	else {
	echo "Subtotal";
	} ?>
    
    </td>
    <td align="right" <?php if (empty($row_BOVINE['INVORDDOC'])) {echo "class='Verdana12B'";} else if (empty($row_BOVINE['INVMAJ'])){
	echo "class='Verdana13B'";
	}?>>
	
	<?php 
	if (empty($row_BOVINE['INVORDDOC'])) {
	echo "<hr size='1' style='margin:0;' width='90'/>";
	} 
	echo number_format($row_BOVINE['INVTOT'],2); ?>
    </td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <?php 
  
  if (empty($row_BOVINE['INVORDDOC'])) {
  echo '<tr>
    <td height="15"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>';
  } 
   $xxx = $row_BOVINE['INVMAJ'];} while ($row_BOVINE = mysqli_fetch_assoc($BOVPRT));  ?>
</table>

   
<table width="732" border="0" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="none" style="page-break-before:always;">
  <tr height="10">
    <td height="35" colspan="7" align="center" valign="middle" class="Verdana14B"><script type="text/javascript">document.write(localStorage.hospname);</script><br />
Revenue Analysis (
  <?php echo $clm2; ?>
  )</td>
  </tr>
  <tr height="5" bgcolor="#000000" class="Verdana14Bwhite">
    <td align="center">CAPRINE/OVINE</td>
    <td align="left"></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"></td>
    <td align="center">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td width="140" height="0"></td>
    <td width="150"></td>
    <td width="200"></td>
    <td width="100"></td>
    <td width=""></td>
    <td width=""></td>
    <td width=""></td>
  </tr>
  <tr class="Verdana14B">
    <td height="10" class="Verdana14B" align="center"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  <?php do {  ?>
  <tr>
    <td height="18"></td>
    <td class="Verdana12B"><?php if ($row_CAPRINE['INVMAJ']!=$xxx) { 
	$query_TFF="SELECT TTYPE FROM VETCAN WHERE TSPECIES='5' AND TCATGRY='".$row_CAPRINE['INVMAJ']."' LIMIT 1";
	$TFF = mysql_query($query_TFF, $tryconnection) or die(mysql_error());
	$row_TFF = mysqli_fetch_assoc($TFF);
	echo $row_TFF['TTYPE'];
	} ?></td>
    <td <?php if (empty($row_CAPRINE['INVORDDOC'])) {echo "align='right' class='Verdana12B'";} else if (empty($row_CAPRINE['INVMAJ'])){
	echo "align='right' class='Verdana13B'";
	} ?>>
	
	<?php 
	if (!empty($row_CAPRINE['INVORDDOC'])) {
	echo $row_CAPRINE['INVORDDOC'];
	} 
	else if (empty($row_CAPRINE['INVMAJ'])){
	echo "TOTAL";
	}
	else {
	echo "Subtotal";
	} ?>
    
    </td>
    <td align="right" <?php if (empty($row_CAPRINE['INVORDDOC'])) {echo "class='Verdana12B'";} else if (empty($row_CAPRINE['INVMAJ'])){
	echo "class='Verdana13B'";
	}?>>
	
	<?php 
	if (empty($row_CAPRINE['INVORDDOC'])) {
	echo "<hr size='1' style='margin:0;' width='90'/>";
	} 
	echo number_format($row_CAPRINE['INVTOT'],2); ?>
    </td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <?php 
  
  if (empty($row_CAPRINE['INVORDDOC'])) {
  echo '<tr>
    <td height="15"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>';
  } 
   $xxx = $row_CAPRINE['INVMAJ'];} while ($row_CAPRINE = mysqli_fetch_assoc($CAPRPRT));  ?>
</table>


   
<table width="732" border="0" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="none" style="page-break-before:always;">
  <tr height="10">
    <td height="35" colspan="7" align="center" valign="middle" class="Verdana14B"><script type="text/javascript">document.write(localStorage.hospname);</script><br />
Revenue Analysis (
  <?php echo $clm2; ?>
  )</td>
  </tr>
  <tr height="5" bgcolor="#000000" class="Verdana14Bwhite">
    <td align="center">PORCINE</td>
    <td align="left"></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"></td>
    <td align="center">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td width="140" height="0"></td>
    <td width="150"></td>
    <td width="200"></td>
    <td width="100"></td>
    <td width=""></td>
    <td width=""></td>
    <td width=""></td>
  </tr>
  <tr class="Verdana14B">
    <td height="10" class="Verdana14B" align="center"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  <?php do {  ?>
  <tr>
    <td height="18"></td>
    <td class="Verdana12B"><?php if ($row_PORCINE['INVMAJ']!=$xxx) { 
	$query_TFF="SELECT TTYPE FROM VETCAN WHERE TSPECIES='6' AND TCATGRY='".$row_PORCINE['INVMAJ']."' LIMIT 1";
	$TFF = mysql_query($query_TFF, $tryconnection) or die(mysql_error());
	$row_TFF = mysqli_fetch_assoc($TFF);
	echo $row_TFF['TTYPE'];
	} ?></td>
    <td <?php if (empty($row_PORCINE['INVORDDOC'])) {echo "align='right' class='Verdana12B'";} else if (empty($row_PORCINE['INVMAJ'])){
	echo "align='right' class='Verdana13B'";
	} ?>>
	
	<?php 
	if (!empty($row_PORCINE['INVORDDOC'])) {
	echo $row_PORCINE['INVORDDOC'];
	} 
	else if (empty($row_PORCINE['INVMAJ'])){
	echo "TOTAL";
	}
	else {
	echo "Subtotal";
	} ?>
    
    </td>
    <td align="right" <?php if (empty($row_PORCINE['INVORDDOC'])) {echo "class='Verdana12B'";} else if (empty($row_PORCINE['INVMAJ'])){
	echo "class='Verdana13B'";
	}?>>
	
	<?php 
	if (empty($row_PORCINE['INVORDDOC'])) {
	echo "<hr size='1' style='margin:0;' width='90'/>";
	} 
	echo number_format($row_PORCINE['INVTOT'],2); ?>
    </td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <?php 
  
  if (empty($row_PORCINE['INVORDDOC'])) {
  echo '<tr>
    <td height="15"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>';
  } 
   $xxx = $row_PORCINE['INVMAJ'];} while ($row_PORCINE = mysqli_fetch_assoc($PORCPRT));  ?>
</table>

  
<table width="732" border="0" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="none" style="page-break-before:always;">
  <tr height="10">
    <td height="35" colspan="7" align="center" valign="middle" class="Verdana14B"><script type="text/javascript">document.write(localStorage.hospname);</script><br />
Revenue Analysis (
  <?php echo $clm2; ?>
  )</td>
  </tr>
  <tr height="5" bgcolor="#000000" class="Verdana14Bwhite">
    <td align="center">AVIAN</td>
    <td align="left"></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"></td>
    <td align="center">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td width="140" height="0"></td>
    <td width="150"></td>
    <td width="200"></td>
    <td width="100"></td>
    <td width=""></td>
    <td width=""></td>
    <td width=""></td>
  </tr>
  <tr class="Verdana14B">
    <td height="10" class="Verdana14B" align="center"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
/*think it is here  */
  <?php do {  ?>
  <tr>
    <td height="18"></td>
    <td class="Verdana12B"><?php if ($row_AVIAN['INVMAJ']!=$xxx) { 
	$query_TFF="SELECT TTYPE FROM VETCAN WHERE TSPECIES='7' AND TCATGRY='".$row_AVIAN['INVMAJ']."' LIMIT 1";
	$TFF = mysql_query($query_TFF, $tryconnection) or die(mysql_error());
	$row_TFF = mysqli_fetch_assoc($TFF);
	echo $row_TFF['TTYPE'];
	} ?></td>
    <td <?php if (empty($row_AVIAN['INVORDDOC'])) {echo "align='right' class='Verdana12B'";} else if (empty($row_AVIAN['INVMAJ'])){
	echo "align='right' class='Verdana13B'";
	} ?>>
	
	<?php 
	if (!empty($row_AVIAN['INVORDDOC'])) {
	echo $row_AVIAN['INVORDDOC'];
	} 
	else if (empty($row_AVIAN['INVMAJ'])){
	echo "TOTAL";
	}
	else {
	echo "Subtotal";
	} ?>
    
    </td>
    <td align="right" <?php if (empty($row_AVIAN['INVORDDOC'])) {echo "class='Verdana12B'";} else if (empty($row_AVIAN['INVMAJ'])){
	echo "class='Verdana13B'";
	}?>>
	
	<?php 
	if (empty($row_AVIAN['INVORDDOC'])) {
	echo "<hr size='1' style='margin:0;' width='90'/>";
	} 
	echo number_format($row_AVIAN['INVTOT'],2); ?>
    </td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <?php 
  
  if (empty($row_AVIAN['INVORDDOC'])) {
  echo '<tr>
    <td height="15"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>';
  } 
   $xxx = $row_AVIAN['INVMAJ'];} while ($row_AVIAN = mysqli_fetch_assoc($AVPRT));  ?>
</table>
*/
   
<table width="732" border="0" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="none" style="page-break-before:always;">
  <tr height="10">
    <td height="35" colspan="7" align="center" valign="middle" class="Verdana14B"><script type="text/javascript">document.write(localStorage.hospname);</script><br />
Revenue Analysis (
  <?php echo $clm2; ?>
  )</td>
  </tr>
  <tr height="5" bgcolor="#000000" class="Verdana14Bwhite">
    <td align="center">OTHER</td>
    <td align="left"></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"></td>
    <td align="center">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td width="140" height="0"></td>
    <td width="150"></td>
    <td width="200"></td>
    <td width="100"></td>
    <td width=""></td>
    <td width=""></td>
    <td width=""></td>
  </tr>
  <tr class="Verdana14B">
    <td height="10" class="Verdana14B" align="center"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  <?php do {  ?>
  <tr>
    <td height="18"></td>
    <td class="Verdana12B"><?php if ($row_OTHER['INVMAJ']!=$xxx) { 
	$query_TFF="SELECT TTYPE FROM VETCAN WHERE TSPECIES='8' AND TCATGRY='".$row_OTHER['INVMAJ']."' LIMIT 1";
	$TFF = mysql_query($query_TFF, $tryconnection) or die(mysql_error());
	$row_TFF = mysqli_fetch_assoc($TFF);
	echo $row_TFF['TTYPE'];
	} ?></td>
    <td <?php if (empty($row_OTHER['INVORDDOC'])) {echo "align='right' class='Verdana12B'";} else if (empty($row_OTHER['INVMAJ'])){
	echo "align='right' class='Verdana13B'";
	} ?>>
	
	<?php 
	if (!empty($row_OTHER['INVORDDOC'])) {
	echo $row_OTHER['INVORDDOC'];
	} 
	else if (empty($row_OTHER['INVMAJ'])){
	echo "TOTAL";
	}
	else {
	echo "Subtotal";
	} ?>
    
    </td>
    <td align="right" <?php if (empty($row_OTHER['INVORDDOC'])) {echo "class='Verdana12B'";} else if (empty($row_OTHER['INVMAJ'])){
	echo "class='Verdana13B'";
	}?>>
	
	<?php 
	if (empty($row_OTHER['INVORDDOC'])) {
	echo "<hr size='1' style='margin:0;' width='90'/>";
	} 
	echo number_format($row_OTHER['INVTOT'],2); ?>
    </td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <?php 
  
  if (empty($row_OTHER['INVORDDOC'])) {
  echo '<tr>
    <td height="15"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>';
  } 
   $xxx = $row_OTHER['INVMAJ'];} while ($row_OTHER = mysqli_fetch_assoc($OTHPRT));  ?>
   
</table>

</div>
<div id="Total">
<h2>  </h2>
<p class="breakhere">
<table width="732" border="0" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="none" style="page-break-before:always;">
  <tr height="10">
    <td height="34" colspan="7" align="center" valign="middle" class="Verdana14B"><script type="text/javascript">document.write(localStorage.hospname);</script><br />
Total Revenue Analysis (
  <?php echo $clm2; ?>
  )</td>
  </tr>
  <tr height="10" bgcolor="#000000" class="Verdana11Bwhite">
    <td align="center"></td>
    <td align="left"></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"></td>
    <td align="center">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td width="140" height="0"></td>
    <td width="150"></td>
    <td width="200"></td>
    <td width="100"></td>
    <td width=""></td>
    <td width=""></td>
    <td width=""></td>
  </tr>
  
  <?php do {  ?>
  <tr>
    <td height="18"></td>
    <td class="Verdana12B">
	
	<?php 
	if ($row_TOTREV['INVREVCAT']!=$xxx) { 
	$query_TFF="SELECT TTYPE FROM REVCAT WHERE TCATGRY='".$row_TOTREV['INVREVCAT']."' LIMIT 1";
	$TFF = mysql_query($query_TFF, $tryconnection) or die(mysql_error());
	$row_TFF = mysqli_fetch_assoc($TFF);
	echo $row_TFF['TTYPE'];
	} 
	else if ($row_TOTREV['INVREVCAT']==90 && empty($row_TOTREV['INVORDDOC'])){
	echo $GSTNAME;
	}
	else if ($row_TOTREV['INVREVCAT']==92 && empty($row_TOTREV['INVORDDOC'])){
	echo "PST";
	}
	else if ($row_TOTREV['INVREVCAT']==95 && empty($row_TOTREV['INVORDDOC'])){
	echo "CASUAL SALES";
	}
	else if ($row_TOTREV['INVREVCAT']==99 && empty($row_TOTREV['INVORDDOC'])){
	echo "SUMMARY INVOICES";
	}
	//else {echo $row_TOTREV['INVMAJ'];}
	
	?></td>
    <td <?php if (empty($row_TOTREV['INVORDDOC'])) {echo "align='right' class='Verdana12B'";} else if (empty($row_TOTREV['INVREVCAT'])){
	echo "align='right' class='Verdana13B'";
	} ?>>
	
	<?php 
	if (!empty($row_TOTREV['INVORDDOC']) && $row_TOTREV['INVREVCAT']<90) {
	echo $row_TOTREV['INVORDDOC'];
	} 
	else if (empty($row_TOTREV['INVREVCAT'])){
	echo "TOTAL";
	}
	else if ($row_TOTREV['INVREVCAT']<90){
	echo "Subtotal";
	} ?>    </td>
    <td align="right" <?php if (empty($row_TOTREV['INVORDDOC'])) {echo "class='Verdana12B'";} else if (empty($row_TOTREV['INVREVCAT'])){
	echo "class='Verdana13B'";
	}?>>
	
	<?php 
	if (empty($row_TOTREV['INVORDDOC']) && $row_TOTREV['INVREVCAT']<90) {
	echo "<hr size='1' style='margin:0;' width='90'/>";
	echo number_format($row_TOTREV['INVTOT'],2);
	} 
	else if (!empty($row_TOTREV['INVORDDOC']) && $row_TOTREV['INVREVCAT']>=90) {
	echo "";
	} 
	else {
	echo number_format($row_TOTREV['INVTOT'],2);
	} 
	?>    </td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <?php 
  
  if (empty($row_TOTREV['INVORDDOC'])) {
  echo '<tr>
    <td height="15"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>';
  } 
   $xxx = $row_TOTREV['INVREVCAT'];} while ($row_TOTREV = mysqli_fetch_assoc($TOTREV));  ?>


</table>
</div>
  
-->  <!--
<div id-"Summary">
<h2> </h2>
<p class="breakhere">

<table width="732" border="0" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="none" style="page-break-before:always;"> -->
<table width="732" border="0" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="none" >
  <tr height="10">
    <td height="40" colspan="7" align="center" valign="middle" class="Verdana14B"><script type="text/javascript">document.write(localStorage.hospname);</script><br />
Revenue Summary (
  <?php echo $clm2; ?>
  )</td>
  </tr>
  <tr height="7" bgcolor="#000000" class="Verdana11Bwhite">
    <td align="center"></td>
    <td align="left"></td>
    <td align="center"></td>
    <td align="center"></td>
    <td align="center"></td>
    <td align="center"></td>
    <td></td>
  </tr>
  <tr>
    <td width="186" height="0"></td>
    <td width="133"></td>
    <td width="107"></td>
    <td width="154"></td>
    <td width="33"></td>
    <td width="19"></td>
    <td width="100"></td>
  </tr>
<tr>
    <td height="30" colspan="7" align="center" class="Verdana14B"><u>CURRENT PERIOD</u></td>
    </tr>
<tr>
  <td height="18"></td>
  <td height="18" colspan="2">Amount Invoiced Through Invoicing</td>
  <td height="18" align="right">
  <?php 
  $query_SUMMARY1 = "SELECT SUM(INVTOT) AS INVTOT FROM MSALES WHERE INVREVCAT < 90" ;
  $SUMMARY1 = mysql_query($query_SUMMARY1, $tryconnection) or die(mysql_error());
  $row_1 = mysqli_fetch_assoc($SUMMARY1);
  echo number_format($row_1['INVTOT'],2);
  ?>  </td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18">Casual Sales</td>
  <td height="18"></td>
  <td height="18" align="right">
  <?php 
  $query_SUMMARY2 = "SELECT SUM(INVTOT) AS INVTOT FROM MSALES WHERE INVREVCAT = 95" ;
  $SUMMARY2 = mysql_query($query_SUMMARY2, $tryconnection) or die(mysql_error());
  $row_2 = mysqli_fetch_assoc($SUMMARY2);
  echo number_format($row_2['INVTOT'],2);
  ?>  </td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18" align="right" class="Verdana12B">Subtotal</td>
  <td height="18" align="right" class="Verdana12B"><?php 
  echo number_format($row_2['INVTOT']+$row_1['INVTOT'],2);
  ?></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18">&nbsp;</td>
  <td height="18"></td>
  <td height="18" align="right">&nbsp;</td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18">Summary Invoices</td>
  <td height="18"></td>
  <td height="18" align="right"><span class="Verdana12">
    <?php 
  $query_SUMMARY3 = "SELECT SUM(INVTOT) AS INVTOT FROM MSALES WHERE INVREVCAT = 99 ;" ;
  $SUMMARY3 = mysql_query($query_SUMMARY3, $tryconnection) or die(mysql_error());
  $row_3 = mysqli_fetch_assoc($SUMMARY3);
  echo number_format($row_3['INVTOT'],2);
  ?>
  </span></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18">Service Charges</td>
  <td height="18"></td>
  <td height="18" align="right"><?php 
  $query_SUMMARY4 = "SELECT SUM(INVTOT) AS INVTOT FROM MSALES WHERE INVREVCAT = 98 " ;
  $SUMMARY4 = mysql_query($query_SUMMARY4, $tryconnection) or die(mysql_error());
  $row_4 = mysqli_fetch_assoc($SUMMARY4);
  echo number_format($row_4['INVTOT'],2);
  ?></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18">Discounts</td>
  <td height="18"></td>
  <td height="18" align="right"><?php 
  $query_SUMMARY5 = "SELECT SUM(INVTOT) AS INVTOT FROM MSALES WHERE INVREVCAT = 96" ;
  $SUMMARY5 = mysql_query($query_SUMMARY5, $tryconnection) or die(mysql_error());
  $row_5 = mysqli_fetch_assoc($SUMMARY5);
  echo number_format($row_5['INVTOT'],2);
  ?></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18" align="right" class="Verdana12B">Subtotal</td>
  <td height="18" align="right" class="Verdana12B">
  <?php 
  echo number_format($row_3['INVTOT']+$row_4['INVTOT']+$row_5['INVTOT'],2);
  ?></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18" align="right"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18" class="Verdana13B"></td>
  <td height="18" colspan="2" class="Verdana13B">Total Sales and Other Revenue</td>
  <td height="18" align="right" class="Verdana13B"><?php 
  echo number_format($row_1['INVTOT']+$row_2['INVTOT']+$row_3['INVTOT']+$row_4['INVTOT']+$row_5['INVTOT'],2);
  ?></td>
  <td height="18" class="Verdana13B"></td>
  <td height="18" class="Verdana13B"></td>
  <td height="18" class="Verdana13B"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18" align="right"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18"><?php echo $GSTNAME ;?> Invoiced</td>
  <td height="18"></td>
  <td height="18" align="right"><?php 
  $query_SUMMARY6 = "SELECT SUM(INVTOT) AS INVTOT FROM MSALES WHERE INVREVCAT = 90 AND INVDESC <> 'CANCELLED' " ;
  $SUMMARY6 = mysql_query($query_SUMMARY6, $tryconnection) or die(mysql_error());
  $row_6 = mysqli_fetch_assoc($SUMMARY6);
  echo number_format($row_6['INVTOT'],2);
  ?></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18">PST Invoiced</td>
  <td height="18"></td>
  <td height="18" align="right"><?php 
  $query_SUMMARY7 = "SELECT SUM(INVTOT) AS INVTOT FROM MSALES WHERE INVREVCAT = 92" ;
  $SUMMARY7 = mysql_query($query_SUMMARY7, $tryconnection) or die(mysql_error());
  $row_7 = mysqli_fetch_assoc($SUMMARY7);
  echo number_format($row_7['INVTOT'],2);
  ?></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18" align="right" class="Verdana12B">Subtotal</td>
  <td height="18" align="right" class="Verdana12B"><?php 
  echo number_format($row_6['INVTOT']+$row_7['INVTOT'],2);
  ?></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18" align="right"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18" colspan="2" class="Verdana13B">Total Sales Including Taxes</td>
  <td height="18" align="right" class="Verdana13B"><?php 
  echo number_format($row_1['INVTOT']+$row_2['INVTOT']+$row_3['INVTOT']+$row_4['INVTOT']+$row_5['INVTOT']+$row_6['INVTOT']+$row_7['INVTOT'],2);
  ?></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18" align="right"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18" align="right"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="35" colspan="7" align="center" class="Verdana14B"><u>PREVIOUS PERIOD</u></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18">Cancelled Invoices</td>
  <td height="18"></td>
  <td height="18" align="right"><?php 
  $query_SUMMARY8 = "SELECT SUM(INVTOT) AS INVTOT FROM MSALES WHERE INVREVCAT = 97 AND INVNO != 0 " ;
  $SUMMARY8 = mysql_query($query_SUMMARY8, $tryconnection) or die(mysql_error());
  $row_8 = mysqli_fetch_assoc($SUMMARY8);
  echo number_format($row_8['INVTOT'],2);
  ?></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18" colspan="2">Cancelled Service Charges</td>
  <td height="18" align="right"><?php 
  $query_SUMMARY9 = "SELECT SUM(INVTOT) AS INVTOT FROM MSALES WHERE INVREVCAT = 98 AND INVNO = 0 AND INVDESC = 'CANCELLED'" ;
  $SUMMARY9 = mysql_query($query_SUMMARY9, $tryconnection) or die(mysql_error());
  $row_9 = mysqli_fetch_assoc($SUMMARY9);
  echo number_format($row_9['INVTOT'],2);
  ?></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18" colspan="2">Cancelled <?php echo $GSTNAME ;?> (Adjustments)</td>
  <td height="18" align="right"><?php 
  $query_SUMMARYA = "SELECT SUM(INVTOT) AS INVTOT FROM MSALES WHERE INVREVCAT = 90 AND INVTOT < 0 AND INVDESC = 'CANCELLED' " ;
  $SUMMARYA = mysql_query($query_SUMMARYA, $tryconnection) or die(mysql_error());
  $row_10 = mysqli_fetch_assoc($SUMMARYA);
  echo number_format($row_10['INVTOT'],2);
  ?></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18" colspan="2">Cancelled PST</td>
  <td height="18" align="right"><?php 
  $query_SUMMARYB = "SELECT SUM(INVTOT) AS INVTOT FROM MSALES WHERE INVREVCAT = 92 AND INVTOT < 0" ;
  $SUMMARYB = mysql_query($query_SUMMARYB, $tryconnection) or die(mysql_error());
  $row_11 = mysqli_fetch_assoc($SUMMARYB);
  echo number_format($row_11['INVTOT'],2);
  ?></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18" align="right" class="Verdana12B">Subtotal</td>
  <td height="18" align="right" class="Verdana12B"><?php 
  $query_SUMMARYC = "SELECT SUM(INVTOT) AS INVTOT FROM MSALES WHERE INVREVCAT < 90" ;
  $SUMMARYC = mysql_query($query_SUMMARYC, $tryconnection) or die(mysql_error());
  $row_SUMMARYC = mysqli_fetch_assoc($SUMMARYC);
  echo number_format($row_8['INVTOT']+$row_9['INVTOT']+$row_10['INVTOT']+$row_11['INVTOT'],2);
  ?></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18" align="right"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18" class="Verdana13B"></td>
  <td height="18" class="Verdana13B">Net Revenue</td>
  <td height="18" class="Verdana13B"></td>
  <td height="18" align="right" class="Verdana13B"><?php 
  echo number_format($row_1['INVTOT']+$row_2['INVTOT']+$row_3['INVTOT']+$row_4['INVTOT']+$row_5['INVTOT']+$row_6['INVTOT']+$row_7['INVTOT']+$row_8['INVTOT']+$row_9['INVTOT']+$row_10['INVTOT']+$row_11['INVTOT'],2);
  ?></td>
  <td height="18" class="Verdana13B"></td>
  <td height="18" class="Verdana13B"></td>
  <td height="18" class="Verdana13B"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18" align="right"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="35" colspan="7" align="center" class="Verdana14B"><u><?php echo $GSTNAME ;?> FIGURES FOR POSTING</u></td>
</tr>
<tr>

  <td height="18"></td>
  <td height="18" colspan="2">Total <?php echo $GSTNAME ;?> Taxable Supplies</td>
  <td height="18" align="right"><?php
  echo number_format($row_6['INVTOT']/($GSTRATE/100),2);
  ?></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18" colspan="2"> <?php echo $GSTNAME ; ?> Invoiced </td>
  <td height="18" align="right"><?php 
  echo number_format($row_6['INVTOT'],2);
  ?></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18" colspan="2">Cancelled <?php echo $GSTNAME ;?> (Adjustments)</td>
  <td height="18" align="right"><?php 
  echo number_format($row_10['INVTOT'],2);
  ?></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18" align="right"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18" class="Verdana13B"></td>
  <td height="18" class="Verdana13B">Net <?php echo $GSTNAME ?> Payable</td>
  <td height="18" class="Verdana13B"></td>
  <td height="18" align="right" class="Verdana13B"><?php 
  echo number_format($row_6['INVTOT']+$row_10['INVTOT'],2);
  ?></td>
  <td height="18" class="Verdana13B"></td>
  <td height="18" class="Verdana13B"></td>
  <td height="18" class="Verdana13B"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18" align="right"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18" align="right"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="35" colspan="7" align="center" class="Verdana14B"><u>PST FIGURES FOR POSTING</u></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18">Total Sales</td>
  <td height="18"></td>
  <td height="18" align="right">
  <?php 
  echo number_format(($row_1['INVTOT']+$row_2['INVTOT']+$row_3['INVTOT']+$row_4['INVTOT']+$row_5['INVTOT']),2);
  ?></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18" colspan="2">Total PST Taxable Supplies</td>
  <td height="18" align="right"><?php 
  echo number_format($row_7['INVTOT']/(8/100),2);
  ?></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18">PST Invoiced</td>
  <td height="18"></td>
  <td height="18" align="right"><?php 
  echo number_format($row_7['INVTOT'],2);
  ?></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18" colspan="2">Cancelled PST (Adjustments)</td>
  <td height="18" align="right"><?php 
  echo number_format($row_11['INVTOT'],2);
  ?></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18" align="right"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18" class="Verdana13B"></td>
  <td height="18" class="Verdana13B">Net PST Payable</td>
  <td height="18" class="Verdana13B"></td>
  <td height="18" align="right" class="Verdana13B"><?php 
  echo number_format($row_7['INVTOT']+$row_11['INVTOT'],2);
  ?></td>
  <td height="18" class="Verdana13B"></td>
  <td height="18" class="Verdana13B"></td>
  <td height="18" class="Verdana13B"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
<tr>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
  <td height="18"></td>
</tr>
      </table>
</div>
    <!--</div>--></td>
  </tr>
</table>



</body>
</html>
