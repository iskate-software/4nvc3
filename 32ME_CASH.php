<?php 
session_start();
require_once('../../tryconnection.php'); 

mysqli_select_db($tryconnection, $database_tryconnection);


$query_closedate="SELECT STR_TO_DATE('$_GET[closedate]','%m/%d/%Y')";
$closedate= mysql_unbuffered_query($query_closedate, $tryconnection) or die(mysqli_error($mysqli_link));
$closedate=mysqli_fetch_array($closedate);

$closemonth ="SELECT DATE_FORMAT('$closedate[0]', '%D %M %Y') " ;
$clm = mysqli_query($tryconnection, $closemonth) or die(mysqli_error($mysqli_link)) ;
$clm1 = mysqli_fetch_array($clm) ;

$clm2 = $clm1[0] ;
/*


$SETUP3 = "DROP TABLE IF EXISTS ARTEMPC" ;
$DOIT3 = mysql_query($SETUP3, $tryconnection) or die(mysql_error()) ;

$SETUP4 = "CREATE TABLE ARTEMPC LIKE CASHDEP" ;
$DOIT4 = mysql_query($SETUP4, $tryconnection) or die(mysql_error()) ;


$SETUP5 = "INSERT INTO ARTEMPC SELECT * FROM CASHDEP WHERE DTEPAID <= '$closedate[0]'";
$DOIT5 = mysql_query($SETUP5, $tryconnection) or die(mysql_error()) ;
$SETUP6 = "INSERT INTO ARTEMPC SELECT * FROM ARCASHR WHERE DTEPAID <= '$closedate[0]'";
$DOIT6 = mysql_query($SETUP6, $tryconnection) or die(mysql_error()) ;
*/

$search_CASH = "SELECT SUM(AMTPAID) AS Total_Cash FROM ARTEMPC WHERE INSTR(UPPER(REFNO),'CASH') <> 0 ";
$CASH = mysqli_query($tryconnection, $search_CASH) or die(mysqli_error($mysqli_link)) ;
$row_CASH=mysqli_fetch_array($CASH);

$search_CHQ = "SELECT SUM(AMTPAID) AS Total_CHQ FROM ARTEMPC WHERE INSTR(UPPER(REFNO),'CHEQUE') <> 0 OR INSTR(UPPER(REFNO),'CHQ') <> 0 ";
$CHQ = mysqli_query($tryconnection, $search_CHQ) or die(mysqli_error($mysqli_link)) ;
$row_CHQ=mysqli_fetch_array($CHQ);

$search_VISA = "SELECT SUM(AMTPAID) AS Total_VISA FROM ARTEMPC WHERE INSTR(UPPER(REFNO),'VISA') <> 0 ";
$VISA = mysqli_query($tryconnection, $search_VISA) or die(mysqli_error($mysqli_link)) ;
$row_VISA=mysqli_fetch_array($VISA);

$search_MCRD = "SELECT SUM(AMTPAID) AS Total_MCRD FROM ARTEMPC WHERE INSTR(UPPER(REFNO),'MC') <> 0 OR INSTR(UPPER(REFNO),'M/C') <> 0  ";
$MCRD = mysqli_query($tryconnection, $search_MCRD) or die(mysqli_error($mysqli_link)) ;
$row_MCRD=mysqli_fetch_array($MCRD);

$search_AMEX = "SELECT SUM(AMTPAID) AS Total_AMEX FROM ARTEMPC WHERE INSTR(UPPER(REFNO),'AMEX') <> 0 ";
$AMEX = mysqli_query($tryconnection, $search_AMEX) or die(mysqli_error($mysqli_link)) ;
$row_AMEX=mysqli_fetch_array($AMEX);

$search_DCRD = "SELECT SUM(AMTPAID) AS Total_DCRD FROM ARTEMPC WHERE INSTR(UPPER(REFNO),'DCRD') <> 0 ";
$DCRD = mysqli_query($tryconnection, $search_DCRD) or die(mysqli_error($mysqli_link)) ;
$row_DCRD=mysqli_fetch_array($DCRD);

$search_DINE = "SELECT SUM(AMTPAID) AS Total_DINE FROM ARTEMPC WHERE INSTR(UPPER(REFNO),'DINE') <> 0 ";
$DINE = mysqli_query($tryconnection, $search_DINE) or die(mysqli_error($mysqli_link)) ;
$row_DINE=mysqli_fetch_array($DINE);

$search_GE = "SELECT SUM(AMTPAID) AS Total_GE FROM ARTEMPC WHERE INSTR(UPPER(REFNO),'GE') <> 0 ";
$GE = mysqli_query($tryconnection, $search_GE) or die(mysqli_error($mysqli_link)) ;
$row_GE=mysqli_fetch_array($GE);

$search_CELL = "SELECT SUM(AMTPAID) AS Total_CELL FROM ARTEMPC WHERE INSTR(UPPER(REFNO),'CELL') <> 0 ";
$CELL = mysqli_query($tryconnection, $search_CELL) or die(mysqli_error($mysqli_link)) ;
$row_CELL=mysqli_fetch_array($CELL);

$search_PND = "SELECT SUM(AMTPAID) AS Total_PND FROM ARTEMPC WHERE UPPER(REFNO) = 'PND' OR UPPER(REFNO) = 'POUND' ";
$PND = mysqli_query($tryconnection, $search_PND) or die(mysqli_error($mysqli_link)) ;
$row_PND=mysqli_fetch_array($PND);


$M_cash1 = "UPDATE ARTEMPC SET COMPANY = 'Totals this date'" ;
$Doit7 = mysqli_query($tryconnection, $M_cash1) or die(mysqli_error($mysqli_link)) ;

$M_cash2 = "SELECT COMPANY, SUM(AMTPAID) AS MTCASHSL FROM ARTEMPC WHERE REFNO <> 'Corrn.' ";
$Doit8 = mysqli_query($tryconnection, $M_cash2) or die(mysqli_error($mysqli_link)) ;
$MTCASHSL = mysqli_fetch_array($Doit8);

$M_cash3 = "ALTER TABLE ARTEMPC ADD INDEX CDAYOF (DTEPAID)" ;
//$Doit9 = mysql_query($M_cash3, $tryconnection) or die(mysql_error()) ;


$Sum_CASHMONTH = "SELECT DATE_FORMAT(DTEPAID, '%m/%d/%Y') AS DTEPAID, COMPANY, SUM(AMTPAID) AS AMTPAID FROM ARTEMPC  WHERE REFNO <> 'Corrn.' GROUP BY DTEPAID ";
$Get_CASHMONTH = mysqli_query($tryconnection, $Sum_CASHMONTH) or die(mysqli_error($mysqli_link)) ;
$row_CASHMONTH = mysqli_fetch_array($Get_CASHMONTH);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>MONTH END CASH RECEIPTS REGISTER</title>
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
    </tr>
    <tr id="prtpurpose">
    <td colspan="8" height="15" align="center" class="Verdana13">Month End Cash Receipts Register for Closing of <?php echo $clm2 ; ?></td>
    </tr>
  <tr height="10" bgcolor="#000000" class="Verdana11Bwhite">
    <td width="160" align="center">Date Paid</td>
    <td width="170" align="left"></td>
    <td width="100" align="center">&nbsp;</td>
    <td width="100" align="center">&nbsp;</td>
    <td width="100" align="center">Payment</td>
    <td width="100" align="center">&nbsp;</td>
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
    <td align="center" class="Verdana13">'.$row_CASHMONTH['DTEPAID'].'</td>
    <td class="Verdana13">'.substr($row_CASHMONTH['COMPANY'],0,29).'</td>
    <td align="right" class="Verdana13"></td>
    <td align="right" class="Verdana13"></td>
    <td align="right" class="Verdana13">'.number_format($row_CASHMONTH['AMTPAID'],2).'</td>
    <td align="right" class="Verdana13"></td>
    <td align="right" class="Verdana13"></td>
  </tr>';
  }
  while ($row_CASHMONTH=mysqli_fetch_assoc($Get_CASHMONTH));
  
  ?>
      </table>
    <table width="60%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="55" colspan="4" align="center" valign="bottom" class="Verdana13BBlue">Cash Receipts Summary</td>
      </tr>
      <tr>
        <td height="1"></td>
        <td height="1" colspan="2"><hr  /></td>
        <td height="1"></td>
      </tr>
      <tr>
        <td width="22%" height="18" class="Verdana12">&nbsp;</td>
        <td width="28%" class="Verdana12">Cash</td>
        <td width="26%" align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_CASH[0]); ?></td>
        <td width="24%" class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td height="18" class="Verdana12">&nbsp;</td>
        <td class="Verdana12">Cheques</td>
        <td align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_CHQ[0]); ?></td>
        <td class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td height="18" class="Verdana12">&nbsp;</td>
        <td class="Verdana12">Visa</td>
        <td align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_VISA[0]); ?></td>
        <td class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td height="18" class="Verdana12">&nbsp;</td>
        <td class="Verdana12">Master Card</td>
        <td align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_MCRD[0]); ?></td>
        <td class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td height="18" class="Verdana12">&nbsp;</td>
        <td class="Verdana12">Amex</td>
        <td align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_AMEX[0]); ?></td>
        <td class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td height="18" class="Verdana12">&nbsp;</td>
        <td class="Verdana12">Debit Card</td>
        <td align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_DCRD[0]); ?></td>
        <td class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td height="18" class="Verdana12">&nbsp;</td>
        <td class="Verdana12">Diners Club</td>
        <td align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_DINE[0]); ?></td>
        <td class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td height="18" class="Verdana12">&nbsp;</td>
        <td class="Verdana12">GE Credit Card</td>
        <td align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_GE[0]); ?></td>
        <td class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td height="18" class="Verdana12">&nbsp;</td>
        <td class="Verdana12">Cell</td>
        <td align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_CELL[0]); ?></td>
        <td class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td height="18" class="Verdana12">&nbsp;</td>
        <td class="Verdana12">Pound</td>
        <td align="right" class="Verdana12"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_PND[0]); ?></td>
        <td class="Verdana12">&nbsp;</td>
      </tr>
      <tr>
        <td height="1"></td>
        <td height="1" colspan="2"><hr  /></td>
        <td height="1"></td>
      </tr>
      <tr>
        <td height="22" valign="top" class="Verdana13B">&nbsp;</td>
        <td height="22" valign="top" class="Verdana13B">Grand Total</td>
        <td height="22" align="right" valign="top" class="Verdana13B"><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%(#10n',$row_CASH[0]+$row_CHQ[0]+$row_DCRD[0]+$row_VISA[0]+$row_MCRD[0]+$row_AMEX[0]+$row_DINE[0]+$row_GE[0]+$row_CELL[0]+$row_PND[0]); ?></td>
        <td height="22" valign="top" class="Verdana13B">&nbsp;</td>
      </tr>
    </table>
    <!--</div>--></td>
  </tr>
</table>

</body>
</html>
