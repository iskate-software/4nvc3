<?php

session_start() ;
require_once('../../tryconnection.php');
mysql_select_db($database_tryconnection, $tryconnection);

// get the appropriate dates 

$query_closedate="SELECT STR_TO_DATE('$_GET[closedate]','%m/%d/%Y')";
$closedate= mysql_unbuffered_query($query_closedate, $tryconnection) or die(mysql_error());
$closedate=mysql_fetch_array($closedate);

$closemonth ="SELECT DATE_FORMAT('$closedate[0]', '%M %Y') " ;
$clm = mysql_query($closemonth, $tryconnection) or die(mysql_error()) ;
$clm1 = mysql_fetch_array($clm) ;
$clm2 = $clm1[0] ;

$DOC_SPLIT = "SELECT * FROM DOCTAB1" ;
$get_doc = mysql_query($DOC_SPLIT, $tryconnection) or die(mysql_error()) ;

$CRITDATA = "SELECT HOSPNAME FROM CRITDATA LIMIT 1" ;
$get_crit = mysql_query($CRITDATA, $tryconnection) or die(mysql_error()) ;
$row_Crit = mysql_fetch_assoc($get_crit) ;
$hospname = $row_Crit['HOSPNAME'] ;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=1.5" />
<title>Doctor Split</title>
<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<style type="text/css">

#prtclosebuttons{
display:block;
} 

body {
	font: 100% Verdana, Arial, Helvetica, sans-serif;
	background: #FFFFFF;
	margin: 0; 
	<!--  its good practice to zero the margin and padding of the body element to account for differing browser defaults-->
	padding: 0;
	text-align: center; <!-- this centers the container in IE 5* browsers. The text is then set to the left aligned default in the #container selector -->
	color: #000000;
}
.oneColElsCtr #container {
	width: 46em;
	background: #FFFFFF;
	margin: 0 auto; <!-- the auto margins (in conjunction with a width) center the page -->
	border: 1px solid #000000;
	text-align: right; <!-- this overrides the text-align: center on the body element. -->
}
.oneColElsCtr #mainContent {
	padding: 0 2px; <!-- remember that padding is the space inside the div box and margin is the space outside the div box -->
}
.Doctor {
	color: #000000; 
	font-family: "Verdana";
	font-size: 10px; 
	text-decoration: none;
    text-align: left;
}
.h4 {text-align: center;}


</style>
<link rel="stylesheet" type="text/css" href="../../ASSETS/print.css" media="print"/>
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>
</head>

<!-- -->

</style></head>

<body  onload="window.print();"  class="oneColElsCtr">

<div id="container">
  <div id="mainContent">
    <h4 class="h4"><?php echo $hospname .' REVENUE ANALYSIS </br> DOCTOR SPLITS '. $clm2 ; ?></h4>
    <p>&nbsp;</p>
    <table width="98%" border="1" cellspacing="1" cellpadding="1">
      <tr>
        <td width="10%" class="Doctor">Doctor</td>
        <td width="9%" class="Verdana10">Canine</td>
        <td width="9%" class="Verdana10">Feline</td>
        <td width="8%" class="Verdana10">Equine</td>
        <td width="8%" class="Verdana10">Bovine</td>
        <td width="8%" class="Verdana10">Caprine</td>
        <td width="8%" class="Verdana10">Porcine</td>
        <td width="8%" class="Verdana10">Avian</td>
        <td width="8%" class="Verdana10">Other</td>
        <td width="10%" class="Verdana10">Total</td>
        <td width="7%" class="Verdana10">Invoices</td>
        <td width="7%" class="Verdana10">Average</td>
      </tr>
      <?php 
      $ktot = 0 ;
      $ftot = 0 ;
      $etot = 0 ;
      $btot = 0 ;
      $ctot = 0 ;
      $ptot = 0 ;
      $atot = 0 ;
      $otot = 0 ;
      $ttot = 0 ;
      $itot = 0 ;
      while ($row_doc = mysql_fetch_assoc($get_doc)) {
       echo '
       <tr>
        <td class="Doctor">'.$row_doc['DOCTOR'].'</td>
        <td class="Verdana10">&nbsp;'.$row_doc['CAN9'].'</td>
        <td class="Verdana10">&nbsp;'.$row_doc['FEL'].'</td>
        <td class="Verdana10">&nbsp;'.$row_doc['EQ'].'</td>
        <td class="Verdana10">&nbsp;'.$row_doc['BOV'].'</td>
        <td class="Verdana10">&nbsp;'.$row_doc['CAP'].'</td>
        <td class="Verdana10">&nbsp;'.$row_doc['PORC'].'</td>
        <td class="Verdana10">&nbsp;'.$row_doc['AV'].'</td>
        <td class="Verdana10">&nbsp;'.$row_doc['OTHER'].'</td>
        <td class="Verdana10">&nbsp;'.$row_doc['TOTAL'].'</td>  
        <td class="Verdana10">&nbsp;'.$row_doc['INVOICES'].'</td>
        <td class="Verdana10">&nbsp;'.$row_doc['AVERAGE'].'</td>    </tr>' ; 
        
        $ktot = $ktot + $row_doc['CAN9'];
        $ftot = $ftot + $row_doc['FEL'];
        $etot = $etot + $row_doc['EQ'];
        $btot = $btot + $row_doc['BOV'];
        $ctot = $ctot + $row_doc['CAP'];
        $ptot = $ptot + $row_doc['PORC'];
        $atot = $atot + $row_doc['AV'];
        $otot = $otot + $row_doc['OTHER'];
        $ttot = $ttot + $row_doc['TOTAL'];
        $itot = $itot + $row_doc['INVOICES'];
        }
        $avtot = round($ttot / $itot,2) ;
        setlocale(LC_MONETARY, 'en_US');
       echo '<tr>
        <td class="Verdana10Bold">&nbsp;Totals</td>
        <td class="Verdana10Bold">&nbsp;'.money_format('%^(!#7n',$ktot).'</td>
        <td class="Verdana10Bold">&nbsp;'.money_format('%^(!#7n',$ftot).'</td>
        <td class="Verdana10Bold">&nbsp;'.money_format('%^(!#7n',$etot).'</td>
        <td class="Verdana10Bold">&nbsp;'.money_format('%^(!#7n',$btot).'</td>
        <td class="Verdana10Bold">&nbsp;'.money_format('%^(!#7n',$ctot).'</td>
        <td class="Verdana10Bold">&nbsp;'.money_format('%^(!#7n',$ptot).'</td>
        <td class="Verdana10Bold">&nbsp;'.money_format('%^(!#7n',$atot).'</td>
        <td class="Verdana10Bold">&nbsp;'.money_format('%^(!#7n',$otot).'</td>
        <td class="Verdana10Bold">&nbsp;'.money_format('%^(!#7n',$ttot).'</td>
        <td class="Verdana10Bold">&nbsp;'.$itot.'</td>
        <td class="Verdana10Bold">&nbsp;'.money_format('%^(!#7n',$avtot).'</td>
      </tr>' ; ?>
    </table>
    <h2>&nbsp;</h2>
    <p>&nbsp;</p>
	<!-- end #mainContent --></div>
<!-- end #container --></div>
</body>
</html>
