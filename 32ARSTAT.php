<?php
// ARSTAT program.
// This program prints out statements for individual or all clients. 
// The statement date is given by NOW().
// It asks for the ending dates for cash receipts and invoices which are to be
// included. This allows for retroactive printing of statements, excluding all
// invoices from $invdate on, and cash receipts from $cashdate onwards.
// As a result, statement can be sent late, but take into account payments which
// were received after the nominal month end. Or not. It depends on the clinic's protocols.
//
// Temporary tables TARCUST (arcusto), TARV (ararecv) and CASH (arcashr + cashdep) are created to
// minimize traffic loads, and prevent live updating of the records during the printout.
//
// First, it is conceivable that the client balance field in ARCUSTO may be incorrect. To account 
// for this, client balances are "Forced" to the totals of the receivables records. After that
// credit amounts are taken into account (it is assumed that the credit field in ARCUSTO is correct,
// so it can just be applied to the balance to give the true balance.)
// To keep subsequent joins minimalized, ARCUSTO is then extracted to a temporary file for 
// non zero balances.

mysql_select_db($database_tryconnection, $tryconnection);

$BALANCE1 = "DROP TEMPORARY TABLE IF EXISTS TAR1" ;
$BALANCE2 = "CREATE TEMPORARY TABLE TAR1 (CUSTNO FLOAT(7),COMPANY VARCHAR(50),INVDTE DATE, IBAL FLOAT(8,2)) 
            SELECT CUSTNO,COMPANY,INVDTE,SUM(IBAL) AS IBAL FROM ARARECV WHERE IBAL <> 0 AND INVDTE <= '$invdate[0]' GROUP BY CUSTNO ";
$BALANCE3 = "UPDATE ARCUSTO SET BALANCE = 0 WHERE BALANCE <> 0" ;
$BALANCE4 = "UPDATE ARCUSTO JOIN TAR1 USING (CUSTNO) SET ARCUSTO.BALANCE = TAR1.IBAL" ;
$BALANCE5 = "UPDATE ARCUSTO  SET BALANCE = BALANCE - CREDIT" ;
$BALANCE6 = "DROP TEMPORARY TABLE IF EXISTS TARCUST" ;
$BALANCE7 = "CREATE TEMPORARY TABLE TARCUST (CUSTNO FLOAT(7),TITLE VARCHAR(25),COMPANY VARCHAR (50), 
             CONTACT VARCHAR(50), ADDRESS1 VARCHAR(60), ADDRESS2 VARCHAR(60),CITY VARCHAR(50), STATE 
             CHAR(3), ZIP CHAR(12), COUNTRY VARCHAR(30)) SELECT CUSTNO,TITLE,COMPANY,CONTACT,ADDRESS1,
             ADDRESS2, STATE,ZIP, COUNTRY,CREDIT FROM ARCUSTO WHERE BALANCE <> 0 " ;
$Q_Balance1 = mysql_query($BALANCE1, $tryconnection) or die(mysql_error());
$Q_Balance2 = mysql_query($BALANCE2, $tryconnection) or die(mysql_error());
$Q_Balance3 = mysql_query($BALANCE3, $tryconnection) or die(mysql_error());
$Q_Balance4 = mysql_query($BALANCE4, $tryconnection) or die(mysql_error());
$Q_Balance5 = mysql_query($BALANCE5, $tryconnection) or die(mysql_error());
$Q_Balance6 = mysql_query($BALANCE6, $tryconnection) or die(mysql_error());
$Q_Balance7 = mysql_query($BALANCE7, $tryconnection) or die(mysql_error());

// Then, all the cash records are gathered from ARCASHR and CASHDEP and 
// summarised for each client. This allows both for removing payments on receivables
// if the clinic chooses to backdate the statements, and for showing the total payment
// received that month on each statement.


$CASH1 = "DROP TEMPORARY TABLE IF EXISTS CASH"
$CASH2 = "CREATE TEMPORARY TABLE CASH SELECT * FROM ARCASHR  ORDER BY CUSTNO,INVNO,INVDTE ASC" ;
$CASH3 = "INSERT INTO CASH SELECT * FROM CASHDEP "; 
$Q_Cash1 = mysql_query($CASH1, $tryconnection) or die(mysql_error());
$Q_Cash2 = mysql_query($CASH2, $tryconnection) or die(mysql_error());
$Q_Cash3 = mysql_query($CASH3, $tryconnection) or die(mysql_error());

// The receivables are then selected using the $indate variable to exclude any late records.

$INVOICE1 = "DROP TEMPORARY TABLE IF EXISTS TARV" ;
$INVOICE2 = "CREATE TEMPORARY TABLE TARV SELECT * FROM ARARECV WHERE INVDTE <= $invdate[0] ORDER BY CUSTNO" ;
$Q_Invoice1 = mysql_query($INVOICE1, $tryconnection) or die(mysql_error());
$Q_Invoice2 = mysql_query($INVOICE2, $tryconnection) or die(mysql_error());

// If the run is being backdated to the last month end, the above selection looks after everything 
// but the overdated payments in both the receivables file (TARV) and the cash file (CASH). 
// They have to be removed.
// First, all payments made on old invoices after the cash cut-off date have to be removed from TARV, 
// then all payments for invoices after the invoice cut-off date have to be trashed from CASH.
if ($cashdate[0] > $invdate[0] || $invdate[0] < date('Y-m-d')) {
  $TARV1 = "UPDATE TARV JOIN CASH USING (CUSTNO,INVNO) SET TARV.IBAL = TARV.IBAL - CASH.AMTPAID WHERE 
            CASH.DTEPAID > '$cashdate[0]'";
  $Q_Tarv1 = mysql_query($TARV1, $tryconnection) or die(mysql_error());
  $CASH4 = "DELETE FROM CASH WHERE INVDTE > '$invdate[0]' ";
  $Q_Cash4 = mysql_query($CASH4, $tryconnection) or die(mysql_error());
}
// Finally, we have clean data. So, work through the temporary client file, extracting the appropriate
// data.
// Start by preparing statement summary totals
  $GCurrent = 0 ;
  $GOver_30 = 0 ;
  $GOver_60 = 0 ;
  $GOver_90 = 0 ;
  $GOver_120 = 0 ;
  $Curdate = YEAR($invdte) + MONTH($invdte) * 12 ;
$CLIENTS = "SELECT  CUSTNO,TITLE,COMPANY,CONTACT,ADDRESS1,
             ADDRESS2, STATE, ZIP, COUNTRY, CREDIT FROM TARCUST" ;
$Q_Client = mysql_query($CLIENTS, $tryconnection( or die(mysql_error()) ;
while($row = mysql_fetch_assoc($Q_Client)) {
  // Check for cash
  $pay2date = 0 ;
  $Is_Cash = "SELECT SUM(AMTPAID) FROM CASH WHERE CUSTNO = $row['CUSTNO']" ;
  $Q_Cash5 = mysql_query($Is_Cash, $tryconnection) or die(mysql_error()) ;
  $any = mysql_fetch_array($Q_Cash,MYSQL_NUM) ;
  if ($any) {
    $pay2date = $any ;
  }
  // Prepare the aging data
  $Current = 0 ;
  $Over_30 = 0 ;
  $Over_60 = 0 ;
  $Over_90 = 0 ;
  $Over_120 = 0 ;
  // now get the receivables
  $RECEIVABLES = 'SELECT INVNO,INVDTE,PONUM,ITOTAL,AMTPAID,IBAL FROM TARV WHERE CUSTNO = $row['CUSTNO'] ORDER BY INVDTE  ';
  $Q_Recv = mysql_query($RECEIVABLES, $tryconnection) or die(mysql_error()) ;
  
  // Then print the Statement Heading, 
  
  // The client mailing address
  
  // Cash paid this month if any
//   use $pay2date if not zero.......  
  // and the detailed invoice amounts. Do the aging as you go.
  while($row1 = mysql_fetch_assoc($Q_Recv) {
  if $Curdate - (YEAR($row1['invdte']) + 12*MONTH($row1['invdte']) ) = 0 ) {
    $Current = $Current + $row1['ibal'] ;
    $GCurrent = $GCurrent + $row1['ibal'] ;
  }
  if $Curdate - (YEAR($row1['invdte']) + 12*MONTH($row1['invdte']) ) = 1 ) {
    $Over_30 = $Over_30 + $row1['ibal'] ;
    $GOver_30 = $GOver_30 + $row1['ibal'] ;
  }
  if $Curdate - (YEAR($row1['invdte']) + 12*MONTH($row1['invdte']) ) = 2 ) {
    $Over_60 = $Over_60 + $row1['ibal'] ;
    $GOver_60 = $GOver_60 + $row1['ibal'] ;
  }
  if $Curdate - (YEAR($row1['invdte']) + 12*MONTH($row1['invdte']) ) = 3 ) {
    $Over_90 = $Over_90 + $row1['ibal'] ;
    $GOver_90 = $GOver_90 + $row1['ibal'] ;
  }
  if $Curdate - (YEAR($row1['invdte']) + 12*MONTH($row1['invdte']) ) > 3 ) {
    $Over_120 = $Over_120 + $row1['ibal'] ;
    $GOver_120 = $Over_120 + $row1['ibal'] ;
  }
  }
}

?>