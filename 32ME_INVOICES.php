<?php 
session_start();
require_once('../../tryconnection.php'); 

mysqli_select_db($tryconnection, $database_tryconnection);

//$taxname=taxname($database_tryconnection, $tryconnection, date('m/d/Y')); 

$query_closedate="SELECT STR_TO_DATE('$_GET[closedate]','%m/%d/%Y')";
$closedatexx= mysql_unbuffered_query($query_closedate, $tryconnection) or die(mysqli_error($mysqli_link));
$closedate=mysqli_fetch_array($closedatexx);


$closemonth ="SELECT DATE_FORMAT('$closedate[0]', '%D %M %Y') " ;
$clm = mysqli_query($tryconnection, $closemonth) or die(mysqli_error($mysqli_link)) ;
$clm1 = mysqli_fetch_array($clm) ;

$clm2 = $clm1[0] ;

$query_TAX = "SELECT HTAXNAME, HOTAXNAME, HGSTNO, DATE_FORMAT(HGSTDATE,'%m/%d/%Y') AS HGSTDATE FROM CRITDATA";
$TAX = mysqli_query($tryconnection, $query_TAX) or die(mysqli_error($mysqli_link));
$row_TAX = mysqli_fetch_array($TAX);
$nametax = $row_TAX['HTAXNAME'] ;
/*
$LIMIT1 = "SELECT FIRSTINV FROM PREFER LIMIT 1" ;
$DOIT1 = mysql_query($LIMIT1, $tryconnection ) or die(mysql_error()) ;
$FIRSTINV = mysql_fetch_array($DOIT1);

$LIMIT2 = "SELECT LASTINV FROM PREFER LIMIT 1" ;
$DOIT2 = mysql_query($LIMIT2, $tryconnection ) or die(mysql_error()) ;
$LASTINV = mysql_fetch_array($DOIT2);

$SETUP1 = "DROP  TABLE IF EXISTS ARTEMPI" ;
$SETUP2 = "CREATE TABLE ARTEMPI LIKE ARINVOI" ;
$SETUP3 = "INSERT INTO ARTEMPI SELECT * FROM ARINVOI WHERE INVDTE <= '$closedate[0]' " ;
$DOIT3 = mysql_query($SETUP1, $tryconnection ) or die(mysql_error()) ;
$DOIT4 = mysql_query($SETUP2, $tryconnection ) or die(mysql_error()) ;
$DOIT5 = mysql_query($SETUP3, $tryconnection ) or die(mysql_error()) ;
*/
///* Searches for the month's cancels' totals. 
//   * The cancels are of two types. Current are +ve, with "CANC" in the refno.
//   *                               Past due are -ve with "CANC" in the refno
//   * So they have to be extracted separately, then combined.
//   * They also have to be separated from the true, current credit invoices. Refno does this.
//   * Start with the current month cancels if positive, prev if negative and invno = sc.
//   Define the searches:
//*/

$search_CURCAN = "SELECT SUM(ITOTAL) AS Total_CURCAN FROM ARTEMPI WHERE UPPER(REFNO) = 'CANC.' AND INVNO <> 'CANCLD' AND ITOTAL > 0" ;
$ICANCC = mysqli_query($tryconnection, $search_CURCAN) or die(mysqli_error($mysqli_link)) ;
$row_ICANCC = mysqli_fetch_array($ICANCC) ;

$search_CANCSC = "SELECT SUM(ITOTAL) AS Total_CANSC FROM ARTEMPI WHERE UPPER(REFNO) = 'CANC.' AND INVNO = '0000' AND ITOTAL < 0" ;
$ICANCSC = mysqli_query($tryconnection, $search_CANCSC) or die(mysqli_error($mysqli_link)) ;
$row_ICANCSC = mysqli_fetch_array($ICANCSC) ;

$search_CANCNG = "SELECT SUM(ITOTAL) AS Total_CANCNG FROM ARTEMPI WHERE UPPER(REFNO) = 'CANC.' AND INVNO <> '0000' AND INVNO <> 'CANCLD' AND ITOTAL < 0" ;
$CANCNG = mysqli_query($tryconnection, $search_CANCNG) or die(mysqli_error($mysqli_link)) ;

$search_PSTCC = "SELECT SUM(PTAX) AS Total_PSTCC FROM ARTEMPI WHERE UPPER(REFNO) = 'CANC.'" ;
$IPSTCC = mysqli_query($tryconnection, $search_PSTCC) or die(mysqli_error($mysqli_link)) ;

$search_GSTCC = "SELECT SUM(TAX) AS Total_GSTCC FROM ARTEMPI WHERE UPPER(REFNO) = 'CANC.'" ;
$IGSTCC = mysqli_query($tryconnection, $search_GSTCC) or die(mysqli_error($mysqli_link)) ;

///* Searches for the previous month's totals. */

$search_OLDCAN = "SELECT SUM(ITOTAL) AS Total_OLDCAN FROM ARTEMPI WHERE UPPER(REFNO) = 'CANC.' AND INVNO <> 'CANCLD' " ;
$ICANCO = mysqli_query($tryconnection, $search_OLDCAN) or die(mysqli_error($mysqli_link)) ;
$row_ICANCO = mysqli_fetch_array($ICANCO) ;

$search_OCANSC = "SELECT SUM(ITOTAL) AS Total_OCANSC FROM ARTEMPI WHERE UPPER(REFNO) = 'CANC.' AND INVNO = '0000' " ;
$OCANSC = mysqli_query($tryconnection, $search_OCANSC) or die(mysqli_error($mysqli_link)) ;
$row_OCANSC = mysqli_fetch_array($OCANSC) ;

$search_PSTOC = "SELECT SUM(PTAX) AS Total_PSTOC FROM ARTEMPI WHERE INVNO = 'CANCLD'" ;
$IPSTCO = mysqli_query($tryconnection, $search_PSTOC) or die(mysqli_error($mysqli_link)) ;
$row_IPSTCO = mysqli_fetch_array($IPSTCO) ;

$search_GSTOC = "SELECT SUM(TAX) AS Total_GSTOC FROM ARTEMPI WHERE INVNO = 'CANCLD'" ;
$IGSTCO = mysqli_query($tryconnection, $search_GSTOC) or die(mysqli_error($mysqli_link)) ;
$row_IGSTCO = mysqli_fetch_array($IGSTCO) ;

///* Now the current month's tax totals. */

$search_GST = "SELECT SUM(TAX) AS Total_IGST FROM ARTEMPI WHERE INSTR(REFNO,'CANC') = 0 " ;
$IGST   = mysqli_query($tryconnection, $search_GST) or die(mysqli_error($mysqli_link)) ;

$search_PST = "SELECT SUM(PTAX) AS Total_IPST FROM ARTEMPI WHERE INSTR(REFNO,'CANC') = 0 " ;
$IPST   = mysqli_query($tryconnection, $search_PST) or die(mysqli_error($mysqli_link)) ;

$search_ITOTAL = "SELECT SUM(ITOTAL) AS Total_IGROSS FROM ARTEMPI WHERE INSTR(REFNO,'CANC') = 0 " ;
$ITOTAL   = mysqli_query($tryconnection, $search_ITOTAL) or die(mysqli_error($mysqli_link)) ;


///* Delete all the cancelled from the raw data, as the totals have been extracted, and will be presented in 
//the summary at the foot of the printout. then organize it chronologically and summarize it by day.
//Also, count the invoices for later insertion into PRACTICE.*/

$M_invo1 = "DELETE FROM ARTEMPI WHERE INSTR(REFNO,'CANC') <> 0 " ;
$Doit1 = mysqli_query($tryconnection, $M_invo1) or die(mysqli_error($mysqli_link)) ;

$M_invo2 = "UPDATE ARTEMPI SET COMPANY = 'Totals this date'" ;
$Doit2 = mysqli_query($tryconnection, $M_invo2) or die(mysqli_error($mysqli_link)) ;

$M_invo3 = "SELECT COMPANY, COUNT(COMPANY) AS MINV FROM ARTEMPI" ; 
$invo3 = mysqli_query($tryconnection, $M_invo3) or die(mysqli_error($mysqli_link)) ;
$MINV = mysqli_fetch_array($invo3);

$M_invo4 = "SELECT COMPANY, SUM(ITOTAL) AS MTINVSL FROM ARTEMPI" ;
$invo4 = mysqli_query($tryconnection, $M_invo4) or die(mysqli_error($mysqli_link)) ;
$MTINVSL = mysqli_fetch_array($invo4);

$M_invo5 = "SELECT COMPANY, SUM(TAX) AS MTGST FROM ARTEMPI" ;
$invo5 = mysqli_query($tryconnection, $M_invo5) or die(mysqli_error($mysqli_link)) ;
$MTGST = mysqli_fetch_array($invo5);

$M_invo6 = "SELECT COMPANY, SUM(TAX) AS MTPST FROM ARTEMPI" ;
$invo6 = mysqli_query($tryconnection, $M_invo6) or die(mysqli_error($mysqli_link)) ;
$MTPST = mysqli_fetch_array($invo6);

$M_invo7 = "ALTER TABLE ARTEMPI ADD INDEX IDAYOF (INVDTE) ";
//$invo7 = mysql_query($M_invo7, $tryconnection) or die(mysql_error()) ;


$Sum_INVMONTH = "SELECT DATE_FORMAT(INVDTE, '%m/%d/%Y') AS INVDTE, COMPANY, SUM(ITOTAL) AS ITOTAL, SUM(TAX) AS TAX, SUM(PTAX) AS PTAX FROM ARTEMPI GROUP BY INVDTE ";
$INVMONTH = mysqli_query($tryconnection, $Sum_INVMONTH) or die(mysqli_error($mysqli_link)) ;
$row_INVMONTH = mysqli_fetch_array($INVMONTH);

$query_SUM = "SELECT SUM(ITOTAL) AS ITOTAL, SUM(TAX) AS TAX, SUM(PTAX) AS PTAX, SUM(IBAL) AS IBAL, SUM(AMTPAID) AS AMTPAID FROM ARTEMPI";
$SUM = mysqli_query($tryconnection, $query_SUM) or die(mysqli_error($mysqli_link)) ;
$row_SUM = mysqli_fetch_array($SUM);



?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>MONTH END INVOICE REGISTER</title>
<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />

<script type="text/javascript">

</script>

</head>

<body onload="window.print();" style="background-color:#FFFFFF;">
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" style="top:0px; left:0px; position:absolute; overflow:auto;">
  <tr height="10">
    <td height="34" colspan="6" align="center" class="Verdana14B"><script type="text/javascript">document.write(localStorage.hospname);</script></td>
  </tr>
    </tr>
    <tr id="prtpurpose">
    <td colspan="8" height="15" align="center" class="Verdana13">Month End Invoice Register for Closing of <?php echo $clm2 ; ?></td>
    </tr>
  <tr height="10" bgcolor="#000000" class="Verdana11Bwhite">
    <td width="160" align="center">Date</td>
    <td width="170" align="left"></td>
    <td width="100" align="center">Amount</td>
    <td width="100" align="center">PST</td>
    <td width="100" align="center"><?php echo $nametax; ?></td>
    <td width="100" align="center">Total</td>
    <td width=""></td>
  </tr>
  <tr>
    <td colspan="6" class="Verdana12" align="center">
<!--   <div id="irresults">-->      
<table width="732" border="1" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="rows">
  <tr>
    <td width="160" height="0"></td>
    <td width=""></td>
    <td width="100"></td>
    <td width="100"></td>
    <td width="100"></td>
    <td width="100"></td>
    <td width="30"></td>
  </tr>
        <?php 
  
  do {
  echo '
  <tr>
    <td align="center" class="Verdana13">'.$row_INVMONTH['INVDTE'].'</td>
    <td class="Verdana13">'.substr($row_INVMONTH['COMPANY'],0,29).'</td>
    <td align="right" class="Verdana13">'.number_format(($row_INVMONTH['ITOTAL']-$row_INVMONTH['TAX']),2).'</td>
    <td align="right" class="Verdana13">'.number_format($row_INVMONTH['PTAX'],2).'</td>
    <td align="right" class="Verdana13">'.number_format($row_INVMONTH['TAX'],2).'</td>
    <td align="right" class="Verdana13">'.number_format($row_INVMONTH['ITOTAL'],2).'</td>
    <td align="right" class="Verdana13"></td>
  </tr>';
  }
  while ($row_INVMONTH=mysqli_fetch_assoc($INVMONTH));
  
  ?>
  <tr class="Verdana13B">
    <td height="25"></td>
    <td>**GRAND TOTAL**</td>
    <td align="right"><?php echo number_format(($row_SUM['ITOTAL']-$row_SUM['TAX']),2); ?></td>
    <td align="right"><?php echo number_format($row_SUM['PTAX'],2); ?></td>
    <td align="right"><?php echo number_format($row_SUM['TAX'],2); ?></td>
    <td align="right"><?php echo number_format($row_SUM['ITOTAL'],2); ?></td>
    <td></td>
  </tr>
      </table>
    <!--</div>-->
        <table width="60%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="47" colspan="4" align="center" valign="bottom" class="Verdana13BBlue">Invoice Register Summary</td>
          </tr>
          <tr>
            <td width="22%" height="1"></td>
            <td height="1" colspan="2"><hr  /></td>
            <td width="24%" height="1"></td>
          </tr>
          <tr>
            <td height="18" class="Verdana12">&nbsp;</td>
            <td width="28%" class="Verdana12">Invoice Amount</td>
            <td width="26%" align="right" class="Verdana12"><?php echo number_format(($row_SUM['ITOTAL']-$row_SUM['TAX']),2); ?></td>
            <td class="Verdana12">&nbsp;</td>
          </tr>
          <tr>
            <td height="18" class="Verdana12">&nbsp;</td>
            <td class="Verdana12">PST Amount</td>
            <td align="right" class="Verdana12"><?php echo number_format($row_SUM['PTAX'],2); ?></td>
            <td class="Verdana12">&nbsp;</td>
          </tr>
          <tr>
            <td height="18" class="Verdana12">&nbsp;</td>
            <td class="Verdana12">HST Amount</td>
            <td align="right" class="Verdana12"><?php echo number_format($row_SUM['TAX'],2); ?></td>
            <td class="Verdana12">&nbsp;</td>
          </tr>
          <tr>
            <td height="18" class="Verdana12">&nbsp;</td>
            <td class="Verdana12">New Invoices</td>
            <td align="right" class="Verdana12"><?php echo number_format($row_SUM['ITOTAL'],2); ?></td>
            <td class="Verdana12">&nbsp;</td>
          </tr>
          <tr>
            <td height="18" class="Verdana12">&nbsp;</td>
            <td class="Verdana12">On Account</td>
            <td align="right" class="Verdana12"><?php echo number_format($row_SUM['IBAL'],2); ?></td>
            <td class="Verdana11">&nbsp;*</td>
          </tr>
          <tr>
            <td height="18" class="Verdana12">&nbsp;</td>
            <td class="Verdana12">Payments</td>
            <td align="right" class="Verdana12"><?php echo number_format($row_SUM['AMTPAID'],2); ?></td>
            <td class="Verdana11">&nbsp;*&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*Info only</td>
          </tr>
          <tr>
            <td height="18" class="Verdana12">&nbsp;</td>
            <td class="Verdana12">Current Cancels</td>
            <td align="right" class="Verdana12"><?php echo (0-$row_ICANCC[0]); ?></td>
            <td class="Verdana11">&nbsp;*</td>
          </tr>
          <tr>
            <td height="18" class="Verdana12">&nbsp;</td>
            <td class="Verdana12">Past Cancels</td>
            <td align="right" class="Verdana12"><?php echo ($row_ICANCO[0]-$row_IGSTCO[0]-$row_IPSTCO[0]-$row_OCANSC[0]); ?></td>
            <td class="Verdana12">&nbsp;</td>
          </tr>
          <tr>
            <td height="18" class="Verdana12">&nbsp;</td>
            <td class="Verdana12">HST Cancelled</td>
            <td align="right" class="Verdana12"><?php echo $row_IGSTCO[0]; ?></td>
            <td class="Verdana12">&nbsp;</td>
          </tr>
          <tr>
            <td height="18" class="Verdana12">&nbsp;</td>
            <td class="Verdana12">PST Cancelled</td>
            <td align="right" class="Verdana12"><?php echo $row_IPSTCO[0]; ?></td>
            <td class="Verdana12">&nbsp;</td>
          </tr>

          <tr>
            <td height="1"></td>
            <td height="1" colspan="2"><hr  /></td>
            <td height="1"></td>
          </tr>
          <tr>
            <td height="22" valign="top" class="Verdana13B">&nbsp;</td>
            <td height="22" valign="top" class="Verdana13B">Net Revenue</td>
            <td height="22" align="right" valign="top" class="Verdana13B"><?php //setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_NET[0] + $row_TAX[0] + $row_PST[0]); ?>
            <?php echo number_format(($row_SUM['ITOTAL']-$row_ICANCO[0]-$row_OCANSC[0]),2); ?></td>
            <td height="22" valign="top" class="Verdana13B">&nbsp;</td>
          </tr>
      </table></td>
  </tr>
</table>



</body>
</html>
