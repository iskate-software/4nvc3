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
// The total Revenue analysis which rolls up all of the species into a single report.
// Average invoice per doctor is also calculated. The Doctor file is the source of the data,
// .

$DOCTAB1 = "DROP TABLE IF EXISTS DOCTAB" ;
$DOCAVG1 = mysql_query($DOCTAB1, $tryconnection) or die(mysql_error()) ;

$DOCTAB2 = "CREATE  TABLE DOCTAB (DOCTOR VARCHAR(40), K9 FLOAT(8,2), FEL FLOAT(8,2), EQ FLOAT(8,2), BOV FLOAT(8,2),
            CAP FLOAT(8,2), PORC FLOAT(8,2), AV FLOAT(8,2), OTHER FLOAT(8,2), INVOICES INT(7), TOTAL FLOAT(9,2), AVERAGE FLOAT(9.2))" ;
$DOCAVG2 = mysql_query($DOCTAB2, $tryconnection) or die(mysql_error()) ;

$DOCTAB3 =  "INSERT INTO DOCTAB  (DOCTOR)  (SELECT DISTINCT INVORDDOC FROM MSALES JOIN DOCTOR ON INVORDDOC = DOCTOR ) ORDER BY PRIORITY " ;
$DOCAVG3 = mysql_query($DOCTAB3, $tryconnection) or die(mysql_error()) ;
$DOCTAB3A = "UPDATE DOCTAB SET K9= 0, FEL = 0, EQ = 0, BOV = 0, CAP = 0, PORC = 0, AV = 0, OTHER = 0, INVOICES = 0, TOTAL = 0, AVERAGE = 0" ;
$DOCAVG3A = mysql_query($DOCTAB3A, $tryconnection) or die(mysql_error()) ;
 
$DOCTAB4 = "SELECT INVORDDOC,SUM(INVTOT) AS INVTOT,INVLGSM FROM MSALES WHERE INVDECLINE <> 1 AND INVTOT IS NOT NULL GROUP BY INVORDDOC, INVLGSM" ;
$DOCAVG4 = mysql_query($DOCTAB4, $tryconnection) or die(mysql_error()) ;

$DOCTAB4A = "SELECT INVORDDOC,SUM(INVTOT) AS INVTOT FROM MSALES WHERE INVDECLINE <> 1 AND INVTOT IS NOT NULL GROUP BY INVORDDOC" ;
$DOCAVG4A = mysql_query($DOCTAB4A, $tryconnection) or die(mysql_error()) ;
            
$DOCTAB5 = "SELECT COUNT(DISTINCT INVNO) AS INVNO,INVORDDOC FROM MSALES GROUP BY INVORDDOC" ;
$DOCAVG5 = mysql_query($DOCTAB5, $tryconnection) or die(mysql_error()) ;

// do the species totals

while ($row_T = mysqli_fetch_assoc($DOCAVG4)) {
 $tot = $row_T['INVTOT'] ;
 $doc = $row_T['INVORDDOC'] ;
 $lgsm = $row_T['INVLGSM'] ;
 switch ($lgsm) {
  case  1 ;
   $DOCTAB6 = "UPDATE DOCTAB SET K9 = '$tot' WHERE DOCTOR = '$doc'  LIMIT 1" ;
   $DOCAVG6 = mysql_query($DOCTAB6, $tryconnection) or die(mysql_error()) ;
  case  2 ;
   $DOCTAB6 = "UPDATE DOCTAB SET FEL = '$tot' WHERE DOCTOR = '$doc'  LIMIT 1" ;
   $DOCAVG6 = mysql_query($DOCTAB6, $tryconnection) or die(mysql_error()) ;
   break;
  case  3 ;
   $DOCTAB6 = "UPDATE DOCTAB SET EQ = '$tot' WHERE DOCTOR = '$doc'  LIMIT 1" ;
   $DOCAVG6 = mysql_query($DOCTAB6, $tryconnection) or die(mysql_error()) ;
   break;
  case  4 ;
   $DOCTAB6 = "UPDATE DOCTAB SET BOV = '$tot' WHERE DOCTOR = '$doc' LIMIT 1 " ;
   $DOCAVG6 = mysql_query($DOCTAB6, $tryconnection) or die(mysql_error()) ;
   break;
  case  5 ;
   $DOCTAB6 = "UPDATE DOCTAB SET CAP = '$tot' WHERE DOCTOR = '$doc'  LIMIT 1" ;
   $DOCAVG6 = mysql_query($DOCTAB6, $tryconnection) or die(mysql_error()) ;
   break;
  case  6 ;
   $DOCTAB6 = "UPDATE DOCTAB SET PORC = '$tot' WHERE DOCTOR = '$doc'  LIMIT 1" ;
   $DOCAVG6 = mysql_query($DOCTAB6, $tryconnection) or die(mysql_error()) ;
   break;
  case  7 ;
   $DOCTAB6 = "UPDATE DOCTAB SET AV = '$tot' WHERE DOCTOR = '$doc'  LIMIT 1" ;
   $DOCAVG6 = mysql_query($DOCTAB6, $tryconnection) or die(mysql_error()) ;
   break;
  case  8 ;
   $DOCTAB6 = "UPDATE DOCTAB SET OTHER = '$tot' WHERE DOCTOR = '$doc'  LIMIT 1" ;
   $DOCAVG6 = mysql_query($DOCTAB6, $tryconnection) or die(mysql_error()) ;
   break;
   
   }
}
 
// The invoice totals

while ($row_S = mysqli_fetch_assoc($DOCAVG4A)) {
 $inv = $row_S['INVTOT'] ;
 $doc = $row_S['INVORDDOC'] ;

 $DOCTAB7 = "UPDATE DOCTAB SET TOTAL = TOTAL + '$inv' WHERE DOCTOR = '$doc' LIMIT 1" ;
 $DOCAVG7 = mysql_query($DOCTAB7, $tryconnection) or die(mysql_error()) ;
 
}

while ($row_T = mysqli_fetch_assoc($DOCAVG5)) {
 $inv = $row_T['INVNO'] ;
 $doc = $row_T['INVORDDOC'] ;
 $DOCTAB8 = "UPDATE DOCTAB SET INVOICES = '$inv' WHERE DOCTOR = '$doc' LIMIT 1" ;
 $DOCAVG8 = mysql_query($DOCTAB8, $tryconnection) or die(mysql_error()) ;
 
}

// and complete the table (averages)
$DOCTAB9 = "UPDATE DOCTAB SET AVERAGE = ROUND(TOTAL / INVOICES,2)" ;
$DOCAVG9 = mysql_query($DOCTAB9, $tryconnection) or die(mysql_error()) ;


?>