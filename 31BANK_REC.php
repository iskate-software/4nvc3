<?php
session_start();
require_once('../../tryconnection.php');
include("../../ASSETS/tax.php");

if (!empty($_GET['startdate'])){
$startdate=$_GET['startdate'];
}
else {
$startdate='00/00/0000';
}
$stdum = $startdate ;
mysql_select_db($database_tryconnection, $tryconnection);
$startdate="SELECT STR_TO_DATE('$startdate','%m/%d/%Y')";
$startdate=mysql_query($startdate, $tryconnection) or die(mysql_error());
$startdate=mysqli_fetch_array($startdate);

if (!empty($_GET['enddate'])){
$enddate=$_GET['enddate'];
}
else {
$enddate=date('m/d/Y');
}
$enddum = $enddate ;
$enddate="SELECT STR_TO_DATE('$enddate','%m/%d/%Y')";
$enddate=mysql_query($enddate, $tryconnection) or die(mysql_error());
$enddate=mysqli_fetch_array($enddate);
$taxname=taxname($database_tryconnection, $tryconnection, date('m/d/Y')); 

$gethosp="SELECT HOSPNAME FROM CRITDATA" ;
$Query_hosp = mysql_query($gethosp, $tryconnection) or die(mysql_error()) ;
$row_hosp = mysqli_fetch_array($Query_hosp) ;
$hospname = $row_hosp['HOSPNAME'] ;

$drop1 = "DROP TEMPORARY TABLE IF EXISTS DEBIT" ;
$ex1 = mysql_query($drop1, $tryconnection) or die(mysql_error()) ;
$drop2 = "DROP TEMPORARY TABLE IF EXISTS CHEQUE" ;
$ex2 = mysql_query($drop2, $tryconnection) or die(mysql_error()) ;
$drop3 = "DROP TEMPORARY TABLE IF EXISTS VISA" ;
$ex3 = mysql_query($drop3, $tryconnection) or die(mysql_error()) ;
$drop4 = "DROP TEMPORARY TABLE IF EXISTS MASTERC" ;
$ex4 = mysql_query($drop4, $tryconnection) or die(mysql_error()) ;
$drop5 = "DROP TEMPORARY TABLE IF EXISTS CASH" ;
$ex5 = mysql_query($drop5, $tryconnection) or die(mysql_error()) ;
$drop6 = "DROP TEMPORARY TABLE IF EXISTS CELL" ;
$ex6 = mysql_query($drop6, $tryconnection) or die(mysql_error()) ;
$drop7 = "DROP TEMPORARY TABLE IF EXISTS AMEX" ;
$ex7 = mysql_query($drop7, $tryconnection) or die(mysql_error()) ;

$Q1 = "DROP TABLE IF EXISTS  TEMPBANK" ;
$exQ1 = mysql_query($Q1, $tryconnection) or die(mysql_error()) ;

$Q2 = "DROP TEMPORARY TABLE IF EXISTS HOLDING" ;
$exQ2 = mysql_query($Q2, $tryconnection) or die(mysql_error()) ;

$Q3 = "CREATE table TEMPBANK LIKE BANKREC " ;
$exQ3 = mysql_query($Q3, $tryconnection) or die(mysql_error()) ;

$Q4 = "CREATE TEMPORARY TABLE HOLDING LIKE CASHDEP " ;
$exQ4 = mysql_query($Q4, $tryconnection) or die(mysql_error()) ;

$Q5 = "INSERT INTO HOLDING SELECT * FROM ARYCASH  WHERE DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]'" ;
$exQ5 = mysql_query($Q5, $tryconnection) or die(mysql_error()) ;

$Q6 = "INSERT INTO HOLDING SELECT * FROM LASTCASH WHERE DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]'" ;
$exQ6 = mysql_query($Q6, $tryconnection) or die(mysql_error()) ;

$Q7 = "INSERT INTO HOLDING SELECT * FROM CASHDEP  WHERE DTEPAID >= '$startdate[0]' AND DTEPAID <= '$enddate[0]'" ;
$exQ7 = mysql_query($Q7, $tryconnection) or die(mysql_error()) ;

$Q8 = "INSERT INTO TEMPBANK (DATEOF) SELECT DISTINCT DTEPAID FROM HOLDING ORDER BY DTEPAID ASC" ;
$exQ8 = mysql_query($Q8, $tryconnection) or die(mysql_error()) ;

$Q9 = "CREATE TEMPORARY TABLE DEBIT LIKE BANKREC" ;
$exQ9 = mysql_query($Q9, $tryconnection) or die(mysql_error()) ;

$Q10 = "INSERT INTO DEBIT (DATEOF,DEBIT) SELECT DTEPAID, SUM(AMTPAID) FROM HOLDING WHERE REFNO = 'DCRD' GROUP BY DTEPAID"  ;
$exQ10 = mysql_query($Q10, $tryconnection) or die(mysql_error()) ;

$Q11 = "UPDATE TEMPBANK RIGHT JOIN DEBIT ON TEMPBANK.DATEOF = DEBIT.DATEOF SET TEMPBANK.DEBIT = DEBIT.DEBIT" ;
$exQ11 = mysql_query($Q11, $tryconnection) or die(mysql_error()) ;

$Q12 = "CREATE TEMPORARY TABLE CHEQUE LIKE BANKREC" ;
$exQ12 = mysql_query($Q12, $tryconnection) or die(mysql_error()) ;

$Q13 = "INSERT INTO CHEQUE (DATEOF, CHQ) SELECT DTEPAID, SUM(AMTPAID) FROM HOLDING WHERE REFNO = 'CHEQUE' GROUP BY DTEPAID" ;
$exQ13 = mysql_query($Q13, $tryconnection) or die(mysql_error()) ;

$Q14 = "UPDATE TEMPBANK RIGHT JOIN CHEQUE ON TEMPBANK.DATEOF = CHEQUE.DATEOF SET TEMPBANK.CHQ = CHEQUE.CHQ" ;
$exQ14 = mysql_query($Q14, $tryconnection) or die(mysql_error()) ;

$Q15 = "CREATE TEMPORARY TABLE VISA LIKE BANKREC" ;
$exQ15 = mysql_query($Q15, $tryconnection) or die(mysql_error()) ;

$Q16 = "INSERT INTO VISA (DATEOF, VISA) SELECT DTEPAID, SUM(AMTPAID) FROM HOLDING WHERE REFNO = 'VISA' GROUP BY DTEPAID" ;
$exQ16 = mysql_query($Q16, $tryconnection) or die(mysql_error()) ;

$Q17 = "UPDATE TEMPBANK RIGHT JOIN VISA ON TEMPBANK.DATEOF = VISA.DATEOF SET TEMPBANK.VISA = VISA.VISA" ;
$exQ17 = mysql_query($Q17, $tryconnection) or die(mysql_error()) ;

$Q18 = "CREATE TEMPORARY TABLE MASTERC LIKE BANKREC" ;
$exQ18 = mysql_query($Q18, $tryconnection) or die(mysql_error()) ;

$Q19 = "INSERT INTO MASTERC (DATEOF, MC) SELECT DTEPAID, SUM(AMTPAID) FROM HOLDING WHERE REFNO = 'MC' GROUP BY DTEPAID" ;
$exQ19 = mysql_query($Q19, $tryconnection) or die(mysql_error()) ;

$Q20 = "UPDATE TEMPBANK RIGHT JOIN MASTERC ON TEMPBANK.DATEOF = MASTERC.DATEOF SET TEMPBANK.MC = MASTERC.MC" ;
$exQ20 = mysql_query($Q20, $tryconnection) or die(mysql_error()) ;

$Q21 = "CREATE TEMPORARY TABLE CASH LIKE BANKREC ";
$exQ21 = mysql_query($Q21, $tryconnection) or die(mysql_error()) ;

$Q22 = "INSERT INTO CASH (DATEOF, CASH) SELECT DTEPAID, SUM(AMTPAID) FROM HOLDING WHERE REFNO = 'CASH' GROUP BY DTEPAID" ;
$exQ22 = mysql_query($Q22, $tryconnection) or die(mysql_error()) ;

$Q23 = "UPDATE TEMPBANK RIGHT JOIN CASH ON TEMPBANK.DATEOF = CASH.DATEOF SET TEMPBANK.CASH = CASH.CASH" ;
$exQ23 = mysql_query($Q23, $tryconnection) or die(mysql_error()) ;

$Q24 = "CREATE TEMPORARY TABLE CELL LIKE BANKREC ";
$exQ24 = mysql_query($Q24, $tryconnection) or die(mysql_error()) ;

$Q25 = "INSERT INTO CASH (DATEOF, CELL) SELECT DTEPAID, SUM(AMTPAID) FROM HOLDING WHERE REFNO = 'CELL' GROUP BY DTEPAID" ;
$exQ25 = mysql_query($Q25, $tryconnection) or die(mysql_error()) ;

$Q26 = "UPDATE TEMPBANK RIGHT JOIN CELL ON TEMPBANK.DATEOF = CELL.DATEOF SET TEMPBANK.CELL = CELL.CELL" ;
$exQ26 = mysql_query($Q26, $tryconnection) or die(mysql_error()) ;

$Q27 = "DROP TABLE IF EXISTS ABANKREC" ;
$exQ27 = mysql_query($Q27, $tryconnection) or die(mysql_error()) ;

$Q28 = "CREATE TABLE ABANKREC SELECT * FROM TEMPBANK GROUP BY DATEOF" ;
$exQ28 = mysql_query($Q28, $tryconnection) or die(mysql_error()) ;

$Q29 = "SELECT * FROM ABANKREC ORDER BY DATEOF" ;
$exQ29 = mysql_query($Q29, $tryconnection) or die(mysql_error()) ;

$Q30 = "SELECT SUM(DEBIT) AS DEBITTOT FROM ABANKREC" ;
$exQ30 = mysql_query($Q30, $tryconnection) or die(mysql_error()) ;
$row_debit = mysqli_fetch_array($exQ30) ;
$debittot = $row_debit['DEBITTOT'] ;

$Q31 = "SELECT SUM(CASH) AS CASHTOT FROM ABANKREC" ;
$exQ31 = mysql_query($Q31, $tryconnection) or die(mysql_error()) ;
$row_cash = mysqli_fetch_array($exQ31) ;
$cashtot = $row_cash['CASHTOT'] ;

$Q32 = "SELECT SUM(CHQ) AS CHQTOT FROM ABANKREC" ;
$exQ32 = mysql_query($Q32, $tryconnection) or die(mysql_error()) ;
$row_chq = mysqli_fetch_array($exQ32) ;
$chqtot = $row_chq['CHQTOT'] ;

$Q33 = "SELECT SUM(VISA) AS VISATOT FROM ABANKREC" ;
$exQ33 = mysql_query($Q33, $tryconnection) or die(mysql_error()) ;
$row_visa = mysqli_fetch_array($exQ33) ;
$visatot = $row_visa['VISATOT'] ;

$Q34 = "SELECT SUM(MC) AS MCTOT FROM ABANKREC" ;
$exQ34 = mysql_query($Q34, $tryconnection) or die(mysql_error()) ;
$row_mc = mysqli_fetch_array($exQ34) ;
$mctot = $row_mc['MCTOT'] ;

$Q35 = "SELECT SUM(AMEX) AS AMEXTOT FROM ABANKREC" ;
$exQ35 = mysql_query($Q35, $tryconnection) or die(mysql_error()) ;
$row_amex = mysqli_fetch_array($exQ35) ;
$amextot = $row_amex['AMEXTOT'] ;

$Q36 = "SELECT SUM(CELL) AS CELLTOT FROM ABANKREC" ;
$exQ36 = mysql_query($Q36, $tryconnection) or die(mysql_error()) ;
$row_cell = mysqli_fetch_array($exQ36) ;
$celltot = $row_cell['CELLTOT'] ;

$grandtot = $debittot + $cashtot + $chqtot + $visatot + $mctot + $amextot + $celltot ;


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
</head>

<body>
<div id="Bank_Rec"><table width="95%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><div align="center" class="Verdana13B"><?php echo $hospname ; ?></div></td>
  </tr>
  <tr>
    <td><div align="center" class="Verdana12B">Bank Reconciliation</div></td>
  </tr>
  <tr>
    <td><div align="center" class="Verdana11"><?php echo $startdate[0] .' to '. $enddate[0];?></div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="95%" border="0" cellspacing="0" cellpadding="0">
  <tr  style="Verdana12">
    <td width="12%" align="center">Date</td>
    <td width="11%" align="right">Cash</td>
    <td width="11%" align="right">Cheque</td>
    <td width="11%" align="right">Debit</td>
    <td width="11%" align="right">Visa</td>
    <td width="11%" align="right">M/C</td>
    <td width="10%" align="right">Amex</td>
    <td width="11%" align="right">Cell</td>
    <td width="12%" align="right">Total</td>
  </tr>
  <?php while ($row_BANK = mysqli_fetch_array($exQ29)) {
  $thistot = $row_BANK['CASH'] + $row_BANK['CHQ'] + $row_BANK['DEBIT'] + $row_BANK['VISA'] + $row_BANK['MC'] + $row_BANK['AMEX'] + $row_BANK['CELL'] ;
  echo '<tr> 
    <td align="right">'.$row_BANK['DATEOF'].'</td>
    <td align="right">'.$row_BANK['CASH'].'</td>
    <td align="right">'.$row_BANK['CHQ'].'</td>
    <td align="right">'.$row_BANK['DEBIT'].'</td>
    <td align="right">'.$row_BANK['VISA'].'</td>
    <td align="right">'.$row_BANK['MC'].'</td>
    <td align="right">'.$row_BANK['AMEX'].'</td>
    <td align="right">'.$row_BANK['CELL'].'</td>
    <td align="right">'.$thistot.'</td>
  </tr>' ;
   } //while $row_BANK = mysql_fetch_array($exQ29)
   echo 
  '<tr>
   <td>&nbsp;</td>
   </tr>
   <tr>
    <td>Totals</td>
    <td align="right">'.$cashtot.'</td>
    <td align="right">'.$chqtot.'</td>
    <td align="right">'.$debittot.'</td>
    <td align="right">'.$visatot.'</td>
    <td align="right">'.$mctot.'</td>
    <td align="right">'.$amextot.'</td>
    <td align="right">'.$celltot.'</td>
    <td align="right">'.$grandtot.'</td>
  </tr>' ;?>
  <tr id="buttons">
    <td align="center" class="ButtonsTable" colspan="9">
    <input name="button2" type="button" class="button" id="button2" value="FINISHED" onclick="document.location='REPORTS_DIRECTORY.php'" />
    <input name="button3" type="button" class="button" id="button3" value="PRINT" onclick="window.print();" />
    <input name="button" type="button" class="button" id="button" value="CANCEL" onclick="history.back()"/></td>
  </tr>
</table>
</div>
</body>
</html>
