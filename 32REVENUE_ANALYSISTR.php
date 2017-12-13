<?php
session_start();

require_once('../../tryconnection.php');

mysqli_select_db($tryconnection, $database_tryconnection);


$LIMIT1 = "SELECT FIRSTINV FROM PREFER LIMIT 1" ;
$DOIT1 = mysqli_query($tryconnection, $LIMIT1) or die(mysqli_error($mysqli_link)) ;
$FIRSTINV = mysqli_fetch_array($DOIT1);

$LIMIT2 = "SELECT LASTINV FROM PREFER LIMIT 1" ;
$DOIT2 = mysqli_query($tryconnection, $LIMIT2) or die(mysqli_error($mysqli_link)) ;
$LASTINV = mysqli_fetch_array($DOIT2);

$query_closedate="SELECT STR_TO_DATE('$_GET[closedate]','%m/%d/%Y')";
$closedate= mysql_unbuffered_query($query_closedate, $tryconnection) or die(mysqli_error($mysqli_link));
$closedate=mysqli_fetch_array($closedate);

$closemonth ="SELECT DATE_FORMAT('$closedate[0]', '%D %M %Y') " ;
$clm = mysqli_query($tryconnection, $closemonth) or die(mysqli_error($mysqli_link)) ;
$clm1 = mysqli_fetch_array($clm) ;
$clm2 = $clm1[0] ;
// The total Revenue analysis which rolls up all of the species into a single report.


$query_TOTREV = "SELECT INVMAJ, INVREVCAT, INVORDDOC, SUM(INVTOT) AS INVTOT,PRIORITY FROM MSALES RIGHT JOIN DOCTOR ON MSALES.INVORDDOC = DOCTOR.DOCTOR GROUP BY INVREVCAT, PRIORITY WITH ROLLUP" ;

//$query_TOTREV = "SELECT INVMAJ, INVREVCAT, INVORDDOC, SUM(INVTOT) AS INVTOT FROM MSALES GROUP BY INVREVCAT, INVORDDOC WITH ROLLUP" ;
//$query_TOTREV = "SELECT INVMAJ, INVREVCAT, INVORDDOC, SUM(INVTOT) AS INVTOT FROM MSALES GROUP BY INVREVCAT, INVORDDOC" ;
$TOTREV = mysqli_query($tryconnection, $query_TOTREV) or die(mysqli_error($mysqli_link)) ;
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
  $CRITDATA = mysqli_query($tryconnection, $query_CRITDATA) or die(mysqli_error($mysqli_link));
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
$subtot = 0.00 ;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style TYPE="text/css">
p.breakhere {page-break-before:always}
</style>
<title>REVENUE ANALYSIS TOTAL REVENUE</title>
<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />

<script type="text/javascript">

</script>

</head>

<body onload="window.print();" style="background-color:#FFFFFF; overflow:auto;">
 
<div id="Total">
<!--
<h2>  </h2>
<p class="breakhere">
<table width="732" border="0" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="none" style="page-break-before:always;">
-->
<table width="732" border="0" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="none" >
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
  
  <?php  do {  ?>
  <tr>
    <td height="18"></td>
    <td class="Verdana12B">
	
	<?php 
	if ($row_TOTREV['INVREVCAT']!=$xxx) { 
	$query_TFF="SELECT TTYPE FROM REVCAT WHERE TCATGRY='".$row_TOTREV['INVREVCAT']."' LIMIT 1";
	$TFF = mysqli_query($tryconnection, $query_TFF) or die(mysqli_error($mysqli_link));
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
	else if ($row_TOTREV['INVREVCAT']==96 && empty($row_TOTREV['INVORDDOC'])){
	echo "DISCOUNTS";
	}
	else if ($row_TOTREV['INVREVCAT']==98 && empty($row_TOTREV['INVORDDOC'])){
	echo "SERVICE CHARGES";
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
	 if ($row_TOTREV['PRIORITY']=== NULL){
	  echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Subtotal</b>';}
	 else {
	  echo $row_TOTREV['INVORDDOC'];
	  $subtot = $subtot + $row_TOTREV['INVTOT'] ;
	 }
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
   
   } $xxx = $row_TOTREV['INVREVCAT'];}  while ($row_TOTREV = mysqli_fetch_assoc($TOTREV));  ?>


</table>
</div>
</td>
  </tr>
</table>



</body>
</html>
