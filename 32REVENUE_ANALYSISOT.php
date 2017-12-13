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
/*
$SETUP1 = "DROP TABLE IF EXISTS MSALES" ;
$PREP1 = mysql_query($SETUP1, $tryconnection) or die(mysql_error()) ;

$SETUP2 = "CREATE TABLE MSALES LIKE SALESCAT" ;
$PREP2 = mysql_query($SETUP2, $tryconnection) or die(mysql_error()) ;

// $SETUP3 = "INSERT INTO MSALES SELECT * FROM SALESCAT WHERE INVREVCAT <> 0 AND INVREVCAT <> 91 AND INVDTE <= '$closedate[0]' AND INVNO >='$FIRSTINV[0]' AND INVNO <= '$LASTINV[0]' AND INVDECLINE=0"  ; 
$SETUP3 = "INSERT INTO MSALES SELECT * FROM SALESCAT WHERE INVREVCAT <> 0 AND INVREVCAT <> 91 AND INVDTE <= '$closedate[0]'  AND INVDECLINE=0"  ;
$PREP3 = mysql_query($SETUP3, $tryconnection) or die(mysql_error()) ;
*/
// first, the otherses....

$CANINE = "SELECT INVMAJ, INVORDDOC, SUM(INVTOT) AS INVTOT FROM MSALES WHERE INVMAJ < 90 AND INVLGSM = 8 GROUP BY INVMAJ, INVORDDOC WITH ROLLUP" ;
$CANPRT = mysqli_query($tryconnection, $CANINE) or die(mysqli_error($mysqli_link)) ;
$row_CANINE = mysqli_fetch_assoc($CANPRT) ;


$CANDET = "SELECT INVMAJ,INVTNO,INVDESC, INVORDDOC, SUM(INVTOT) AS INVTOT FROM MSALES WHERE INVMAJ < 90 AND INVLGSM = 8 GROUP BY INVMAJ,INVTNO,INVORDDOC WITH ROLLUP" ;
$CANDETPRT = mysqli_query($tryconnection, $CANDET) or die(mysqli_error($mysqli_link)) ;
$row_CANDET = mysqli_fetch_assoc($CANDETPRT) ;



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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style TYPE="text/css">
 p.breakhere {page-break-before:always}
</style>
<title>REVENUE ANALYSISOT</title>
<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />

<script type="text/javascript">

</script>

</head>

<body onload="window.print();" style="background-color:#FFFFFF; overflow:auto;">
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" style="top:0px; left:0px; position:absolute ">
  <tr>
    <td class="Verdana12" align="center">
<!--   <div id="irresults">-->      
<div id-"Other">
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
    <td class="Verdana12B"><?php if ($row_CANINE['INVMAJ']!=$xxx) { 
	$query_TFF="SELECT TTYPE FROM VETCAN WHERE TSPECIES='8' AND TCATGRY='".$row_CANINE['INVMAJ']."' LIMIT 1";
	$TFF = mysqli_query($tryconnection, $query_TFF) or die(mysqli_error($mysqli_link));
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

<!--<h2> Details </h2> -->
<div style="p.breakhere" id-"Other Detail">
<table width="832" border="0" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="none" style="page-break-before:always;">
  <tr height="10">
    <td height="35" colspan="8" align="center" valign="middle" class="Verdana14B"><script type="text/javascript">document.write(localStorage.hospname);</script><br />
Detailed Revenue Analysis (
  <?php echo $clm2; ?>
  )</td>
  </tr>
  <tr height="5" bgcolor="#000000" class="Verdana14Bwhite">
    <td align="center">OTHER</td>
    <td align="left"></td>
    <td align="left"></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"></td>
    <td align="center">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td width="90" height="0"></td>
    <td width="150"></td>
    <td width="200"></td>
    <td width="200"></td>
    <td width=""></td>
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
    <td></td>
  </tr>
  
  <?php $xyz = '    ' ;
   do {  ?>
  <tr>
    <td height="18"></td>
    <td class="Verdana12B"><?php if ($row_CANDET['INVMAJ']!=$xxx) { 
	$query_TFF="SELECT TTYPE,TDESCR FROM VETCAN WHERE TSPECIES='8' AND TCATGRY='".$row_CANDET['INVMAJ']."' LIMIT 1";
	$TFF = mysqli_query($tryconnection, $query_TFF) or die(mysqli_error($mysqli_link));
	$row_TFF = mysqli_fetch_assoc($TFF);
	echo $row_TFF['TTYPE'];
	$lookup = 0 ;
	 if (strpos($row_TFF['TDESCR'] , 'Lookup') != 0 ) {
	 $lookup = 1 ;
	 }
	} ?></td>
	<td <?php if (empty($row_CANDET['INVDESC'])) {echo "align='right' class='Verdana12B'";} else if (empty($row_CANDET['INVTNO'])){
	echo "align='right' class='Verdana13B'";
	} ?>><?php 
	if (!empty($row_CANDET['INVTNO']) && $row_CANDET['INVTNO'] != $xyz) {
	  if ($lookup == 1) {if ($row_CANDET['INVTNO'] == 1) {echo 'Lookup' ;} else {echo $row_CANDET['INVDESC']; }} 
	  else {echo $row_CANDET['INVDESC'] ;}
	$xyz = $row_CANDET['INVTNO'];
	} 
	else if (empty($row_CANDET['INVTNO'])){
	echo "TOTAL";
	} ?> </td>
    <td <?php if (empty($row_CANDET['INVORDDOC'])) {echo "align='right' class='Verdana12B'";} else if (empty($row_CANDET['INVMAJ'])){
	echo "align='right' class='Verdana13B'";
	} ?>>
	
	<?php 
	if (!empty($row_CANDET['INVORDDOC'])) {
	echo $row_CANDET['INVORDDOC'];
	}/* 
	else if (empty($row_CANDET['INVMAJ'])){
	echo "TOTAL";
	} */
	else if (!empty($row_CANDET['INVTNO'])){
	
	echo "Subtotal";
	} ?>
    
    </td>
    <td align="right" <?php 
	if (empty($row_CANDET['INVORDDOC'])) {echo "class='Verdana13B'" ;} 
	else { echo "align='right' class='Verdana13'";} ?>>
	
	<?php
	if (empty($row_CANDET['INVORDDOC'])) {
	echo "<hr size='1' style='margin:0;' width='90'/>";
	} 
	echo number_format($row_CANDET['INVTOT'],2); ?>
    </td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <?php 
  
  if (empty($row_CANDET['INVORDDOC'])) {
  echo '<tr>
    <td height="15"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>';
  } 
   $xxx = $row_CANDET['INVMAJ'];} while ($row_CANDET = mysqli_fetch_assoc($CANDETPRT));  ?>
</table>
</div>
    </td>
  </tr>
</table>



</body>
</html>
