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

mysqli_select_db($tryconnection, $database_tryconnection);
$startdate="SELECT STR_TO_DATE('$startdate','%m/%d/%Y')";
$startdate=mysqli_query($tryconnection, $startdate) or die(mysqli_error($mysqli_link));
$startdate=mysqli_fetch_array($startdate);

if (!empty($_GET['enddate'])){
$enddate=$_GET['enddate'];
}
else {
$enddate=date('m/d/Y');
}

$enddate="SELECT STR_TO_DATE('$enddate','%m/%d/%Y')";
$enddate=mysqli_query($tryconnection, $enddate) or die(mysqli_error($mysqli_link));
$enddate=mysqli_fetch_array($enddate);

$taxname=taxname($database_tryconnection, $tryconnection, date('m/d/Y')); 

// first, make a temporary copy of the client file as is now, and all the invoices and cash receipts which have come in since the cut-off date.

$search_ARCUSTO1 = "DROP TEMPORARY TABLE  IF EXISTS CUSTBAL" ;
$search_ARCUSTO2 = "CREATE TEMPORARY TABLE CUSTBAL (CUSTNO INTEGER(7), TITLE VARCHAR(25), COMPANY VARCHAR(50), CONTACT VARCHAR(50), CAREA CHAR(4), PHONE CHAR(8), CITY VARCHAR(50), CREDIT FLOAT(8,2), BALANCE FLOAT(8,2))" ;
$search_ARCUSTO3 = "INSERT INTO CUSTBAL (CUSTNO,TITLE,COMPANY,CONTACT,CAREA,PHONE,CITY,CREDIT,BALANCE) SELECT  CUSTNO,TITLE,COMPANY,CONTACT,CAREA,PHONE,CITY,CREDIT,BALANCE FROM ARCUSTO ORDER BY CUSTNO" ;
$search_ARINVOI1 = "DROP TEMPORARY TABLE IF EXISTS INVOICES" ;
$search_ARINVOI2 = "CREATE TEMPORARY TABLE INVOICES (CUSTNO INTEGER(7),INVDTE DATE,ITOTAL FLOAT(8,2))" ;
$search_ARINVOI3 = "INSERT INTO INVOICES (CUSTNO INTEGER(7),INVDTE DATE,ITOTAL FLOAT(8,2)) SELECT  CUSTNO,DATE_FORMAT(INVDTE, '%m/%d/%Y') AS INVDTE,ITOTAL FROM ARINVOI WHERE INVDTE >= '$startdate[0]'  ORDER BY CUSTNO ASC";
$search_ARINVOI4 = "INSERT INTO INVOICES (CUSTNO INTEGER(7),INVDTE DATE,ITOTAL FLOAT(8,2)) SELECT  CUSTNO,DATE_FORMAT(INVDTE, '%m/%d/%Y') AS INVDTE,ITOTAL FROM INVLAST WHERE INVDTE >= '$startdate[0]'  ORDER BY CUSTNO ASC";
$search_ARINVOI5 = "SELECT CUSTNO,ITOTAL FROM INVOICES" ;
$search_ARCASH1 = "DROP TEMPORARY TABLE IF EXISTS EXTRACASH" ;
$search_ARCASH2 = "CREATE TEMPORARY TABLE EXTRACASH (CUSTNO INTEGER(7),DTEPAID DATE, AMTPAID FLOAT(8,2))" ;
$search_ARCASH3 = "INSERT INTO EXTRACASH SELECT CUSTNO, DATE_FORMAT(DTEPAID, '%m/%d/%Y') AS DTEPAID FROM ARCASHR, AMTPAID WHERE DTEPAID >= '$startdate[0]' ORDER BY CUSTNO";
$search_ARCASH4 = "INSERT INTO EXTRACASH SELECT CUSTNO, DATE_FORMAT(DTEPAID, '%m/%d/%Y') AS DTEPAID FROM CASHDEP, AMTPAID WHERE DTEPAID >= '$startdate[0]' ORDER BY CUSTNO";
$search_ARCASH5 = "INSERT INTO EXTRACASH SELECT CUSTNO, DATE_FORMAT(DTEPAID, '%m/%d/%Y') AS DTEPAID FROM LASTCASH, AMTPAID WHERE DTEPAID >= '$startdate[0]' ORDER BY CUSTNO";
$search_AR CASH6 = "SELECT CUSTNO,AMTPAID FROM EXTRACASH" ;

$ARCUSTO1 = mysqli_query($tryconnection, $search_ARCUSTO1) or die(mysqli_error($mysqli_link)) ;
$ARCUSTO2 = mysqli_query($tryconnection, $search_ARCUSTO2) or die(mysqli_error($mysqli_link)) ;
$ARCUSTO3 = mysqli_query($tryconnection, $search_ARCUSTO3) or die(mysqli_error($mysqli_link)) ;
$ARINVOI1=mysqli_query($tryconnection, $search_ARINVOI1) or die(mysqli_error($mysqli_link));
$ARINVOI2=mysqli_query($tryconnection, $search_ARINVOI2) or die(mysqli_error($mysqli_link));
$ARINVOI3=mysqli_query($tryconnection, $search_ARINVOI3) or die(mysqli_error($mysqli_link));
$ARINVOI4=mysqli_query($tryconnection, $search_ARINVOI4) or die(mysqli_error($mysqli_link));
$ARINVOI5=mysqli_query($tryconnection, $search_ARINVOI5) or die(mysqli_error($mysqli_link));
$CASH1=mysqli_query($tryconnection, $search_ARCASH1) or die(mysqli_error($mysqli_link));
$CASH2=mysqli_query($tryconnection, $search_ARCASH2) or die(mysqli_error($mysqli_link));
$CASH3=mysqli_query($tryconnection, $search_ARCASH3) or die(mysqli_error($mysqli_link));
$CASH4=mysqli_query($tryconnection, $search_ARCASH4) or die(mysqli_error($mysqli_link));
$CASH5=mysqli_query($tryconnection, $search_ARCASH5) or die(mysqli_error($mysqli_link));
$CASH6=mysqli_query($tryconnection, $search_ARCASH6) or die(mysqli_error($mysqli_link));

// now take out from the client file balances all the invoices which are beyond the selection date, and add the cash in to get the retroactive data.
$select_CLIENT = "SELECT DISTINCT CUSTNO FROM INVOICES" ;
$TAKEAWAY = mysqli_query($tryconnection, $select_CLIENT) or die(mysqli_error($mysqli_link)) ;
$row_CLIENT = mysqli_fetch_assoc($TAKEAWAY) ;
$invcust = array() ;
do {
$invcust[] = $row_CLIENT['CUSTNO'] ;
} while ($row_CLIENT=mysqli_fetch_assoc($TAKEAWAY)) 
foreach ($invcust as $invcust2 {
       $update_CUSTBAL = "UPDATE CUSTBAL SET BALANCE = BALANCE - $row_ARINVOI['ITOTAL'] WHERE CUSTNO = '$invcust2' ";
       $C_U = mysqli_query($tryconnection, $update_CUSTBAL) or die(mysqli_error($mysqli_link)) ;
       }

while ($row_CASH=mysqli_fetch_assoc($CASH6)) {
       $update_CUSTBAL = "UPDATE CUSTBAL SET BALANCE = BALANCE + $row_CASH['AMTPAID'] WHERE CUSTNO = $row_CASH['CUSTNO'] ";
       $C_U2 = mysqli_query($tryconnection, $update_CUSTBAL) or die(mysqli_error($mysqli_link)) ;
       }

// and finally

$CLIENT = "SELECT COMPANY,CONTACT,CAREA,PHONE,BALANCE,CREDIT FROM CUSTBAL WHERE BALANCE <> 0 OR CREDIT <> 0 ORDER BY COMPANY,CONTACT ASC" ;
$get_CLIENT = mysqli_query($tryconnection, $CLIENT) or die(mysqli_error($mysqli_link)) ;
$row_CLIENT = mysqli_fetch_assoc($get_CLIENT) ;


?>